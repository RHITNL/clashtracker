<?
require('init.php');
require('session.php');

$clanId = $_GET['clanId'];
refreshClanInfo($clanId);
try{
	$clan = new clan($clanId);
	$clan->load();
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clans.php');
	exit;
}

$members = $clan->getCurrentMembers();
$members = sortPlayersByTrophies($members);
$wars = $clan->getMyWars();
$war = $wars[0];

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/clans.php">Clans</a></li>
		<li class="active"><?=htmlspecialchars($clan->get('name'));?></li>
	</ol>
	<?require('showMessages.php');?>
	<h1><?=htmlspecialchars($clan->get('name'));?></h1><br>
	<div class="well col-md-12">
		<div class="col-md-6">
			<label for="warsWon" class="col-sm-4 control-label">Wars Won:</label>
			<div class="col-sm-8 text-right" id="warsWon">
				<p><?=$clan->get('warWins');?></p>
			</div>
			<label for="members" class="col-sm-4 control-label">Members:</label>
			<div class="col-sm-8 text-right" id="members">
				<p><?=$clan->get('members');?>/50</p>
			</div>
			<label for="type" class="col-sm-4 control-label">Type:</label>
			<div class="col-sm-8 text-right" id="type">
				<p><?=clanTypeFromCode($clan->get('clanType'));?></p>
			</div>
			<label for="minimumTrophies" class="col-sm-4 control-label">Required Trophies:</label>
			<div class="col-sm-8 text-right" id="minimumTrophies">
				<p><?=$clan->get('minimumTrophies');?></p>
			</div>
			<label for="warFrequency" class="col-sm-4 control-label">War Frequency:</label>
			<div class="col-sm-8 text-right" id="warFrequency">
				<p><?=warFrequencyFromCode($clan->get('warFrequency'));?></p>
			</div>
			<label for="clanTag" class="col-sm-4 control-label">Clan Tag:</label>
			<div class="col-sm-8 text-right" id="clanTag">
				<p><?=$clan->get('tag');?></p>
			</div>
		</div>
		<div class="col-md-6">
			<p><?=htmlspecialchars($clan->get('description'));?></p>
		</div>
	</div>
	<div class="col-md-12">
		<div class="col-md-6">
			<?if(count($members)<50){?>
				<a type="button" class="btn btn-success" href="/addPlayer.php?clanId=<?=$clan->get('id');?>">Add Member</a>
			<?}
			if(count($members)>0){?>
				<a type="button" class="btn btn-success" href="/recordClanLoot.php?clanId=<?=$clan->get('id');?>">Record Loot</a>
			<?}
			if(count($members)>=10){?>
				<a type="button" class="btn btn-success" href="/addWar.php?clanId=<?=$clan->get('id');?>">Add War</a>
			<?}
			if(isset($war)){?>
				<a type="button" class="btn btn-success" href="/war.php?warId=<?=$war->get('id');?>&clanId=<?=$clan->get('id');?>">Most Recent War</a>
			<?}
			if(count($wars)>1){?>
				<a type="button" class="btn btn-success" href="/wars.php?clanId=<?=$clan->get('id');?>">War Log</a>
			<?}?>
			<br><br>
		</div>
	</div>
	<?if(count($members)>0){?>
		<h3>Clan Members</h3>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Name</th>
					<th>Rank</th>
					<th>Trophies</th>
					<th>Troops donated:</th>
					<th>Troops received:</th>
					<th class="text-right">Player Tag</th>
				</tr>
			</thead>
			<tbody>
				<?foreach ($members as $member) {?>
					<tr style="cursor: pointer;" onclick="clickRow('player.php?playerId=<?=$member->get("id");?>&clanId=<?=$clan->get('id');?>');">
						<td><?=htmlspecialchars($member->get('name'));?></td>
						<td><?=rankFromCode($member->get('rank'));?></td>
						<td><?=$member->get('trophies');?></td>
						<td><?=$member->get('donations');?></td>
						<td><?=$member->get('received');?></td>
						<td class="text-right"><?=$member->get('tag');?></td>
					</tr>
				<?}?>
			</tbody>
		</table>
	<?}else{?>
		<h6>&nbsp;</h6>
		<div class="alert alert-info">
			<strong>Oh no!</strong> There's no members currently in this clan. You can start by adding one above.
		</div>
	<?}?>
</div>
<script type="text/javascript">
function clickRow(href){
	window.document.location = href;
}
</script>
<?
require('footer.php');