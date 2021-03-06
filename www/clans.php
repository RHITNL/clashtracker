<?
require('init.php');
require('session.php');

$sort = $_GET['sort'];
$sort = isset($sort) ? $sort : 'clan_points_desc';

$clans = array();
try{
	$clans = Clan::getClans($sort);
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
}

$sorts = array(
	'name' => 'name',
	'clan_points' => 'clan_points_desc',
	'war_wins' => 'war_wins_desc',
	'members' => 'members_desc',
	'minimum_trophies' => 'minimum_trophies_desc');
if(strpos($sort, '_desc') !== FALSE){
	$sorts[str_replace('_desc', '', $sort)] = str_replace('_desc', '', $sort);
}else{
	$sorts[str_replace('_desc', '', $sort)] = str_replace('_desc', '', $sort) . '_desc';
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li class="active">Clans</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Clans</h1><br>
	<div class="col-md-12">
		<div id="addClanButtonDiv">
			<a type="button" class="btn btn-success" onclick="showAddClanForm();">Add Clan</a>
		</div>
		<div id="addClanFormDiv" hidden>
			<form class="form-inline" action="/processAddClan.php" method="POST">
				<div class="col-md-3">
					<div class="form-group">
						<label for="clanTag">Clan Tag:</label>
						<input type="text" class="form-control" id="clanTag" name="clanTag" placeholder="<?=randomTag();?>" value="<?=$_SESSION['clanTag'];?>">
					</div>
				</div>
				<div class="col-md-9 text-left">
					<button type="cancel" class="btn btn-default" onclick="return showAddClanButton();">Cancel</button>
					<button type="submit" class="btn btn-success" name="submit" value="submit">Add</button>
				</div>
			</form>
		</div>
	</div><br><br>
	<div class="col-md-12">
		<?if(count($clans)>0){?>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th></th>
							<th style="cursor: pointer;" onclick="clickRow('clans.php?sort=<?=$sorts['name'];?>');"><i class="fa fa-sort"></i>&nbsp;Clan name</th>
							<th style="cursor: pointer;" onclick="clickRow('clans.php?sort=<?=$sorts['clan_points'];?>');"><i class="fa fa-sort"></i>&nbsp;Clan Points</th>
							<th style="cursor: pointer;" onclick="clickRow('clans.php?sort=<?=$sorts['war_wins'];?>');"><i class="fa fa-sort"></i>&nbsp;Wars Won</th>
							<th style="cursor: pointer;" onclick="clickRow('clans.php?sort=<?=$sorts['members'];?>');"><i class="fa fa-sort"></i>&nbsp;Members</th>
							<th>Type</th>
							<th>War Frequency</th>
							<th style="cursor: pointer;" onclick="clickRow('clans.php?sort=<?=$sorts['minimum_trophies'];?>');"><i class="fa fa-sort"></i>&nbsp;Required Trophies</th>
							<th class="text-right">Clan Tag</th>
						</tr>
					</thead>
					<tbody>
						<?foreach ($clans as $clan) {?>
							<tr style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=$clan->get("id");?>');">
								<td width="20">
									<?$url = $clan->get('badgeUrl');
									if(strlen($url)>0){?>
										<img src="<?=$url;?>" height="20" width="20">
									<?}?>
								</td>
								<td><?=htmlspecialchars($clan->get('name'));?></td>
								<td><?=$clan->get('clanPoints');?></td>
								<td><?=$clan->get('warWins');?></td>
								<td><?=$clan->get('members');?></td>
								<td><?=clanTypeFromCode($clan->get('clanType'));?></td>
								<td><?=warFrequencyFromCode($clan->get('warFrequency'));?></td>
								<td><?=$clan->get('minimumTrophies');?></td>
								<td class="text-right"><?=$clan->get('tag');?></td>
							</tr>
						<?}?>
					</tbody>
				</table>
			</div>
		<?}else{?>
			<div class="alert alert-info" role="alert">
				<strong>Oh no!</strong> There's no clans in our records. You can start by adding one above.
			</div>
		<?}?>
	</div>
</div>
<script type="text/javascript">
function clickRow(href){
	window.document.location = href;
}
function showAddClanButton(){
	$('#addClanFormDiv').hide();
	$('#addClanButtonDiv').show();
	return false;
}
function showAddClanForm(){
	$('#addClanFormDiv').show();
	$('#addClanButtonDiv').hide();
	return false;
}
</script>
<?
require('footer.php');