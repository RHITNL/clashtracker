<?
require('init.php');
require('session.php');

$clanId = $_GET['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
	if(!userHasAccessToUpdateClan($clan)){
		$_SESSION['curError'] = NO_ACCESS;
		header('Location: /clan.php?clanId=' . $clanId);
		exit;
	}
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clans.php');
	exit;
}

$members = $clan->getMembers();

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/clans.php">Clans</a></li>
		<li><a href="/clan.php?clanId=<?=$clan->get('id');?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
		<li class="active"><?=htmlspecialchars($clan->get('name'));?> Loot</li>
	</ol>
	<?require('showMessages.php');?>
	<h1><?=$clan->get('name');?> Loot</h1><br>
	<form class="form-inline" action="/processRecordLoot.php" method="POST">
		<?if(count($members)>5){?>
			<div class="col-md-12 text-right">
				<button type="cancel" class="btn btn-default text-right" onclick="return showRecordLootButton();">Cancel</button>
				<button type="submit" class="btn btn-success text-right">Save</button>
			</div>
		<?}?>
		<input hidden name="type" value="multiple">
		<input hidden name="clanId" value="<?=$clan->get('id');?>">
		<?foreach ($members as $member) {
			if(userHasAccessToUpdatePlayer($member)){?>
				<h4><?=htmlspecialchars($member->get('name'));?></h4>
				<?$gold = $member->getGold();
				$elixir = $member->getElixir();
				$darkElixir = $member->getDarkElixir();?>
				<div class="col-md-3">
					<div class="form-group">
						<label for="gold<?=$member->get('id');?>">Gold</label>
						<input type="number" class="form-control" id="gold<?=$member->get('id');?>" name="gold<?=$member->get('id');?>" placeholder="<?=(count($gold)>0) ? $gold[0]['statAmount'] : '0';?>">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="elixir<?=$member->get('id');?>">Elixir</label>
						<input type="number" class="form-control" id="elixir<?=$member->get('id');?>" name="elixir<?=$member->get('id');?>" placeholder="<?=(count($elixir)>0) ? $elixir[0]['statAmount'] : '0';?>">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="darkElixir<?=$member->get('id');?>">Dark Elixir</label>
						<input type="number" class="form-control" id="darkElixir<?=$member->get('id');?>" name="darkElixir<?=$member->get('id');?>" placeholder="<?=(count($darkElixir)>0) ? $darkElixir[0]['statAmount'] : '0';?>">
					</div>
				</div><br><br><br>
			<?}
		}?>
		<div class="col-md-12 text-right">
			<button type="cancel" class="btn btn-default text-right" onclick="return showRecordLootButton();">Cancel</button>
			<button type="submit" class="btn btn-success text-right">Save</button>
		</div>
	</form>
</div>
<?
require('footer.php');