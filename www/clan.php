<?
require('init.php');
require('session.php');

$clanId = $_GET['clanId'];
$force = $_GET['force'];
$sort = $_GET['sort'];
$sort = isset($sort) ? $sort : 'trophies_desc';
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
	$apiMembers = refreshClanInfo($clan, isset($force));
	if($apiMembers === false){
		$apiMembers = array();
	}
}catch(noResultFoundException $e){
	$clan = new clan();
	$clan->create($clanId);
	$apiMembers = refreshClanInfo($clan, isset($force));
	if($apiMembers === false){
		$clan->delete();
		$_SESSION['curError'] = 'Clan Tag was not found in Clash of Clans.';
		header('Location: /clans.php');
		exit;
	}
	$clanId = $clan->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /clans.php');
	exit;
}

$members = $clan->getMembers(true, $sort);
$wars = $clan->getMyWars();
$war = $wars[0];
$lootReport = $clan->getLootReports()[0];
$userHasAccessToUpdateClan = userHasAccessToUpdateClan($clan);
$canGenerateLootReport = $userHasAccessToUpdateClan && $clan->canGenerateLootReport(weekAgo());
if(isset($loggedInUserClan) && $loggedInUserClan->get('id') == $clanId){
	$requests = $clan->getRequests();
}else{
	$requests = array();
}
$canRequest = $clan->canRequestAccess();

$sorts = array(
	'name' => 'name',
	'trophies' => 'trophies_desc',
	'donations' => 'donations_desc',
	'received' => 'received_desc',
	'rank' => 'rank');
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
		<li><a href="/clans.php">Clans</a></li>
		<li class="active"><?=htmlspecialchars($clan->get('name'));?></li>
	</ol>
	<?require('showMessages.php');?>
	<h1>
		<?$url = $clan->get('badgeUrl');
		if(strlen($url)>0){?>
			<img src="<?=$url;?>">
		<?}?>
		<?=htmlspecialchars($clan->get('name'));?>
	</h1><br>
	<div class="well col-md-12">
		<div class="col-md-6">
			<label for="clanPoints" class="col-xs-8 control-label">Total Points:</label>
			<div class="col-xs-4 text-right" id="clanPoints">
				<p><?=$clan->get('clanPoints');?></p>
			</div>
			<label for="warsWon" class="col-xs-8 control-label">Wars Won:</label>
			<div class="col-xs-4 text-right" id="warsWon">
				<p><?=$clan->get('warWins');?></p>
			</div>
			<label for="members" class="col-xs-8 control-label">Members:</label>
			<div class="col-xs-4 text-right" id="members">
				<p><?=$clan->get('members');?>/50</p>
			</div>
			<label for="type" class="col-xs-4 control-label">Type:</label>
			<div class="col-xs-8 text-right" id="type">
				<p><?=clanTypeFromCode($clan->get('clanType'));?></p>
			</div>
			<label for="minimumTrophies" class="col-xs-8 control-label">Required Trophies:</label>
			<div class="col-xs-4 text-right" id="minimumTrophies">
				<p><?=$clan->get('minimumTrophies');?></p>
			</div>
			<label for="warFrequency" class="col-xs-8 control-label">War Frequency:</label>
			<div class="col-xs-4 text-right" id="warFrequency">
				<p><?=warFrequencyFromCode($clan->get('warFrequency'));?></p>
			</div>
			<label for="location" class="col-xs-6 control-label">Location:</label>
			<div class="col-xs-6 text-right" id="location">
				<p><?=$clan->get('location');?></p>
			</div>
			<label for="clanTag" class="col-xs-7 control-label">Clan Tag:</label>
			<div class="col-xs-5 text-right" id="clanTag">
				<p><?=$clan->get('tag');?></p>
			</div>
		</div>
		<div class="col-md-6">
			<p><?=htmlspecialchars($clan->get('description'));?></p>
		</div>
	</div>
	<?if((count($members)>0 && $userHasAccessToUpdateClan) || count($wars)>0){?>
		<div class="col-md-12">
			<div class="col-md-12">
				<?if(count($members)>0 && $userHasAccessToUpdateClan){?>
					<a type="button" class="btn btn-success" href="/recordClanLoot.php?clanId=<?=$clanId;?>">Record Loot</a>
				<?}
				if($canGenerateLootReport){?>
					<a type="button" class="btn btn-success" href="/processGenerateLootReport.php?clanId=<?=$clanId;?>">Generate Loot Report</a>
				<?}
				if(isset($lootReport)){?>
					<a type="button" class="btn btn-success" href="/lootReport.php?lootReportId=<?=$lootReport->get('id');?>">Loot Report</a>
				<?}
				if(count($members)>=10 && $userHasAccessToUpdateClan){?>
					<a type="button" class="btn btn-success" href="/addWar.php?clanId=<?=$clanId;?>">Add War</a>
				<?}
				if(isset($war)){?>
					<a type="button" class="btn btn-success" href="/war.php?warId=<?=$war->get('id');?>&clanId=<?=$clanId;?>">Most Recent War</a>
				<?}
				if(count($wars)>1){?>
					<a type="button" class="btn btn-success" href="/warStats.php?clanId=<?=$clanId;?>">War Stats</a>
				<?}
				if(count($wars)>1){?>
					<a type="button" class="btn btn-success" href="/wars.php?clanId=<?=$clanId;?>">War Log</a>
				<?}
				if(count($requests)>0){?>
					<a type="button" class="btn btn-success" href="/clanRequests.php?clanId=<?=$clanId;?>">Clan Requests</a>
				<?}
				if($canRequest){?>
					<a type="button" class="btn btn-success" href="/requestClanAccess.php?clanId=<?=$clanId;?>">Request Access</a>
				<?}?>
				<br><br>
			</div>
		</div>
	<?}if(count($members) > 0 || count($apiMembers) > 0){?>
		<h3>Clan Members</h3>
		<?if(count($members) > 0){?>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th></th>
							<th style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=$clanId;?>&sort=<?=$sorts['name'];?>');"><i class="fa fa-sort"></i>&nbsp;Name</th>
							<th style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=$clanId;?>&sort=<?=$sorts['rank'];?>');"><i class="fa fa-sort"></i>&nbsp;Rank</th>
							<th style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=$clanId;?>&sort=<?=$sorts['trophies'];?>');"><i class="fa fa-sort"></i>&nbsp;Trophies</th>
							<th style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=$clanId;?>&sort=<?=$sorts['donations'];?>');"><i class="fa fa-sort"></i>&nbsp;Troops donated:</th>
							<th style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=$clanId;?>&sort=<?=$sorts['received'];?>');"><i class="fa fa-sort"></i>&nbsp;Troops received:</th>
							<th class="text-right">Player Tag</th>
						</tr>
					</thead>
					<tbody>
						<?foreach ($members as $member) {?>
							<tr style="cursor: pointer;" onclick="clickRow('player.php?playerId=<?=$member->get("id");?>&clanId=<?=$clanId;?>');">
								<td width="20">
									<?$url = $member->get('leagueUrl');
									if(strlen($url)>0){?>
										<img src="<?=$url;?>" height="20" width="20">
									<?}?>
								</td>
								<td><?=htmlspecialchars($member->get('name'));?></td>
								<td><?=rankFromCode($member->get('rank'));?></td>
								<td><i class="fa fa-trophy" style="color: gold;"></i>&nbsp;<?=$member->get('trophies');?></td>
								<td><?=$member->get('donations');?></td>
								<td><?=$member->get('received');?></td>
								<td class="text-right"><?=$member->get('tag');?></td>
							</tr>
						<?}?>
					</tbody>
				</table>
			</div>
		<?}if(count($apiMembers) > 0){?>
			<div class="alert alert-info">
				<strong>Oh no!</strong> The following members are not saved in our system. We need their <strong>player tag</strong> for a unique identifier. <?if($userHasAccessToUpdateClan){print "Please input them below and hit <strong>save</strong>.";}?>
			</div>
			<form class="form-horizontal" action="/processAddMembersFromApi.php" method="POST">
				<?if($userHasAccessToUpdateClan){?>
					<div class="col-md-12 text-right">
						<button type="submit" class="btn btn-success text-right">Save</button>
					</div>
				<?}?>
				<input hidden name="clanId" value="<?=$clanId;?>"></input>
				<div class="table-responsive col-md-12">
					<table class="table table-hover">
						<thead>
							<tr>
								<th></th>
								<th>Name</th>
								<th>Rank</th>
								<th>Trophies</th>
								<th>Troops donated:</th>
								<th>Troops received:</th>
								<?if($userHasAccessToUpdateClan){?>
									<th class="text-right">Player Tag</th>
								<?}?>
							</tr>
						</thead>
						<tbody>
							<?foreach ($apiMembers as $apiMember) {?>
								<tr>
									<td width="20">
										<?$url = $apiMember->league->iconUrls->small;
										if(strlen($url)>0){?>
											<img src="<?=$url;?>" height="20" width="20">
										<?}?>
									</td>
									<td><?=htmlspecialchars($apiMember->name);?></td>
									<td><?=rankFromCode(convertRank($apiMember->role));?></td>
									<td><i class="fa fa-trophy" style="color: gold;"></i>&nbsp;<?=$apiMember->trophies;?></td>
									<td><?=$apiMember->donations;?></td>
									<td><?=$apiMember->donationsReceived;?></td>
									<?if($userHasAccessToUpdateClan){?>
										<td class="text-right">
											<input type="text" class="form-control input-sm text-right" id="playerTags[]" name="playerTags[]" placeholder="<?=randomTag();?>"></input>
											<input hidden id="names[]" name="names[]" value="<?=htmlspecialchars($apiMember->name);?>"></input>
										</td>
									<?}?>
								</tr>
							<?}?>
						</tbody>
					</table>
				</div>
			</form>
		<?}?>
	<?}else{?>
		<h6>&nbsp;</h6>
		<div class="alert alert-info">
			<strong>Oh no!</strong> There's no members currently in this clan.
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