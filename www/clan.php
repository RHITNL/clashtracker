<?
require(__DIR__ . '/../config/functions.php');

$clanId = $_GET['clanId'];
try{
	$clan = new clan($clanId);
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clans.php');
	exit;
}

$members = $clan->getMyActiveClanMembers();
$members = sortPlayersByRank($members);
$pastMembers = $clan->getMyPastClanMembers();
$wars = $clan->getMyWars();
$war = $wars[0];

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/clans.php">Clans</a></li>
		<li class="active"><?=$clan->get('name');?></li>
	</ol>
	<?require('showMessages.php');?>
	<h1 ><?=$clan->get('name');?></h1><br>
	<div class="well col-md-12">
		<div class="col-md-6">
			<label for="warsWon" class="col-sm-4 control-label">Wars Won:</label>
			<div class="col-sm-8 text-right" id="warsWon">
				<p><?=$clan->getNumWarsWon();?></p>
			</div>
			<label for="members" class="col-sm-4 control-label">Members:</label>
			<div class="col-sm-8 text-right" id="members">
				<p><?=count($members);?></p>
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
			<p><?=$clan->get('description');?></p>
		</div>
	</div>
	<div class="col-md-12">
		<div class="col-md-6">
			<a type="button" class="btn btn-success" href="/editClan.php?clanId=<?=$clan->get('id');?>">Edit</a>
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
					<th>Actions</th>
					<th class="text-right">Player Tag</th>
				</tr>
			</thead>
			<tbody>
				<?foreach ($members as $member) {?>
					<tr style="cursor: pointer;" onclick="clickRow('player.php?playerId=<?=$member->get("id");?>&clanId=<?=$clan->get('id');?>');">
						<td><?=$member->get('name');?></td>
						<td><?=rankFromCode($member->get('rank'));?></td>
						<td>
							<?if($member->get('rank') != 'LE'){?>
								<a type="button" class="btn btn-sm btn-success" href="/processClanManage.php?clanId=<?=$clan->get('id')?>&playerId=<?=$member->get('id');?>&action=promote">Promote</a>
							<?}
							if($member->get('rank') != 'ME'){?>
								<a type="button" class="btn btn-sm btn-warning" href="/processClanManage.php?clanId=<?=$clan->get('id')?>&playerId=<?=$member->get('id');?>&action=demote">Demote</a>
							<?}?>
							<a type="button" class="btn btn-sm btn-info" href="/processClanManage.php?clanId=<?=$clan->get('id')?>&playerId=<?=$member->get('id');?>&action=leave">Leave</a>
							<?if($member->get('rank') != 'LE'){?>
								<a type="button" class="btn btn-sm btn-danger" href="/processClanManage.php?clanId=<?=$clan->get('id')?>&playerId=<?=$member->get('id');?>&action=kick">Kick</a>
							<?}?>
						</td>
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
	<?}
	if(count($pastMembers)>0){?>
		<div class="col-md-12">
			<div class="col-md-6">
				<button type="button" class="btn btn-success" data-toggle="collapse" data-target="#pastMembers" aria-expanded="true" aria-controls="pastMembers">Past Members</button>
				<br><br>
			</div>
		</div>
		<div class="collapse" id="pastMembers">
			<h3>Past Members</h3>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Name</th>
						<th>Method of Leaving</th>
						<?if(count($members)<50){?>
							<th>Action</th>
						<?}?>
						<th class="text-right">Player Tag</th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($pastMembers as $member) {?>
						<tr style="cursor: pointer;" onclick="clickRow('player.php?playerId=<?=$member->get("id");?>&clanId=<?=$clan->get('id');?>');">
							<td><?=$member->get('name');?></td>
							<td><?=rankFromCode($member->get('rank', $clan->get('id')));?></td>
							<?if(count($members)<50){?>
								<td>
									<a type="button" class="btn btn-sm btn-success" href="/processClanManage.php?clanId=<?=$clan->get('id')?>&playerId=<?=$member->get('id');?>&action=rejoin">Rejoin</a>
								</td>
							<?}?>
							<td class="text-right"><?=$member->get('tag');?></td>
						</tr>
					<?}?>
				</tbody>
			</table>
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