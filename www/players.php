<?
require(__DIR__ . '/../config/functions.php');

$players = array();
try{
	$players = player::getPlayers();
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li class="active">Players</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Players</h1><br>
	<a type="button" class="btn btn-success" href="/addPlayer.php">Add Player</a><br><br>
	<?if(count($players)>0){?>
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
	<?}else{?>
		<div class="alert alert-info" role="alert">
			<strong>Oh no!</strong> There's no players in our records. You can start by adding one above.
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