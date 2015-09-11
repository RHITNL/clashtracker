<?
require(__DIR__ . '/../config/functions.php');

$query = $_POST['query'];

try{
	$clans = clan::searchClans($query);
}catch(Exception $e){
	$clans = array();
	$_SESSION['curError'] = $e->getMessage();
}

try{
	$players = player::searchPlayers($query);
}catch(Exception $e){
	$players = array();
	$_SESSION['curError'] .= $e->getMessage();
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li class="active">Search Results</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Search Results</h1>
	<h5>Search for "<?=$query;?>"...</h5>
	<?if(count($clans)>0){
		if(count($clans)==1){?>
			<h4>Found <?=count($clans);?> clan matching.</h4>
		<?}else{?>
			<h4>Found <?=count($clans);?> clans matching.</h4>
		<?}?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Clan name</th>
						<th>Wars Won</th>
						<th>Members</th>
						<th>Type</th>
						<th>War Frequency</th>
						<th>Required Trophies</th>
						<th class="text-right">Clan Tag</th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($clans as $clan) {?>
						<tr style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=$clan->get("id");?>');">
							<td><?=$clan->get('name');?></td>
							<td><?=$clan->getNumWarsWon();?></td>
							<td><?=$clan->getNumMembers();?></td>
							<td><?=clanTypeFromCode($clan->get('clanType'));?></td>
							<td><?=warFrequencyFromCode($clan->get('warFrequency'));?></td>
							<td><?=$clan->get('minimumTrophies');?></td>
							<td class="text-right"><?=$clan->get('tag');?></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	<?}
	if(count($players)>0){
		if(count($players)==1){?>
			<h4>Found <?=count($players);?> player matching.</h4>
		<?}else{?>
			<h4>Found <?=count($players);?> players matching.</h4>
		<?}?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Name</th>
						<th>Clan Name</th>
						<th>Clan Rank</th>
						<th class="text-right">Player Tag</th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($players as $player) {?>
						<tr style="cursor: pointer;">
							<td onclick="clickRow('player.php?playerId=<?=$player->get("id");?>');"><?=$player->get('name');?></td>
							<?$clan = $player->getMyClan();
							if(isset($clan)){?>
								<td onclick="clickRow('clan.php?clanId=<?=$clan->get("id");?>');"><?=$clan->get('name');?></td>
							<?}else{?>
								<td onclick="clickRow('player.php?playerId=<?=$player->get("id");?>');"></td>
							<?}?>
							<td onclick="clickRow('player.php?playerId=<?=$player->get("id");?>');"><?=rankFromCode($player->get('rank'));?></td>
							<td class="text-right" onclick="clickRow('player.php?playerId=<?=$player->get("id");?>');"><?=$player->get('tag');?></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	<?}
	if(count($clans)==0&&count($players)==0){?>
		<div class="alert alert-info" role="alert">
			<strong>Oh no!</strong> We couldn't find anything in our records matching your search.
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