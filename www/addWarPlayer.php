<?
require(__DIR__ . '/../config/functions.php');

$warId = $_GET['warId'];
try{
	$war = new war($warId);
	$warId = $war->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No war with id ' . $warId . ' found.';
	header('Location: /wars.php');
	exit;
}

$clanId = $_GET['clanId'];
if($war->isClanInWar($clanId)){
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
	$clanEnemy = new clan($war->getEnemy($clanId));
}else{
	$clanId = null;
}

if(!$war->isEditable()){
	$_SESSION['curError'] = 'This war is no longer editable.';
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
		exit;
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
		exit;
	}
}

$addClanId = $_GET['addClanId'];
if($war->isClanInWar($addClanId)){
	$addClan = new clan($addClanId);
	$clan1 = new clan($war->get('firstClanId'));
	$clan2 = new clan($war->get('secondClanId'));
}else{
	$_SESSION['curError'] = 'Clan not in selected war.';
	header('Location: /wars.php');
	exit;
}

$warPlayers = $war->getMyWarPlayers($addClan->get('id'));
$limit = $war->get('size') - count($warPlayers);
$allMembers = $addClan->getMyActiveClanMembers();
$members = array();
foreach ($allMembers as $member) {
	if(!$war->isPlayerInWar($member->get('id'))){
		$members[] = $member;
	}
}
for ($i=1; $i < count($members); $i++) {
	$j=$i;
	$member1Val = $members[$j]->warsSinceLastParticipated();
	$member2Val = $members[$j-1]->warsSinceLastParticipated();
	while($j>0 && $member1Val < $member2Val){
		$temp = $members[$j];
		$members[$j] = $members[$j-1];
		$members[$j-1] = $temp;
		$j--;
		if($j>0){
			$member1Val = $members[$j]->warsSinceLastParticipated();
			$member2Val = $members[$j-1]->warsSinceLastParticipated();
		}
	}
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<?if(isset($clanId)){?>
			<li><a href="/clans.php">Clans</a></li>
			<li><a href="/clan.php?clanId=<?=$clan->get('id');?>"><?=$clan->get('name');?></a></li>
			<li><a href="/wars.php?clanId=<?=$clan->get('id');?>">Wars</a></li>
			<li><a href="/war.php?warId=<?=$war->get('id');?>&clanId=<?=$clan->get('id');?>"><?=$clan->get('name');?> vs. <?=$clanEnemy->get('name');?></a></li>
		<?}else{?>
			<li><a href="/wars.php">Wars</a></li>
			<li><a href="/war.php?warId=<?=$war->get('id');?>"><?=$clan1->get('name');?> vs. <?=$clan2->get('name');?></a></li>
		<?}?>
		<li class="active">Add Players to War</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Add Players to War</h1><br>
	<div class="">
		<form class="form-horizontal" action="/processAddWarPlayer.php" method="POST">
			<input hidden name="warId" value="<?=$war->get('id');?>"></input>
			<input hidden name="addClanId" value="<?=$addClan->get('id');?>"></input>
			<?if(isset($clanId)){?>
				<input hidden name="clanId" value="<?=$clan->get('id');?>"></input>
			<?}?>
			<div class="col-md-12">
				<?if(count($members) > 0){?>
					<div class="col-md-6">
						<h4>Select Existing Members:</h4><br>
						<table class="table table-hover">
							<tbody>
								<?foreach ($members as $member) {?>
									<tr style="cursor: pointer;">
										<td onclick="selectMember(<?=$member->get('id');?>);">
											<div class="checkbox">
												<label>
													<input id="<?=$member->get('id');?>" type="checkbox" name="members[]" value="<?=$member->get('id');?>"><?=$member->get('name');?>
												</label>
											</div>
										</td>
									</tr>
								<?}?>
							</tbody>
						</table>
					</div>
				<?}
				if(count($allMembers)<50){?>
					<div class="col-md-6">
						<h4>Add New Member:</h4><br>
						<div class="form-group">
							<label class="col-sm-4 control-lable" for="name">Player Name:</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="name" name="name" placeholder="Angry Neeson 52" value="<?=$_SESSION['name'];?>"></input>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-lable" for="playerTag">Player Tag:</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="playerTag" name="playerTag" placeholder="#JKFH83J" value="<?=$_SESSION['playerTag'];?>"></input>
							</div>
						</div>
					</div>
				<?}?>
			</div>
			<div class="row">
				<div class="col-sm-12 text-right btn-actions">
					<br>
					<button type="submit" class="btn btn-default" name="cancel" value="cancel">Cancel</button>
					<button type="submit" class="btn btn-success" name="submit" value="submit">Submit</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
function selectMember(id){
	var checkbox = $('#' + id);
	if(!checkbox.is(':checked') && !checkbox.is(':disabled')){
		checkbox.prop('checked', true);
	}else{
		checkbox.prop('checked', false);
	}
	var limit = "<?=$limit;?>";
	if ($('input[type=checkbox]:checked').length >= limit) {
		$("input:checkbox:not(:checked)").each(function(){
			$(this).attr('disabled', true);
			$('#name').attr('disabled', true);
			$('#playerTag').attr('disabled', true);
		});
	}else{
		$("input:checkbox:not(:checked)").each(function(){
			$(this).attr('disabled', false);
			$('#name').attr('disabled', false);
			$('#playerTag').attr('disabled', false);
		});
	}
}
</script>
<?
require('footer.php');