<?
require('init.php');
require('session.php');

$clanId = $_GET['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$clanId = null;
}

if(!isset($clanId)){
	$playerId = $_GET['playerId'];
	try{
		$player = new player($playerId);
		$playerId = $player->get('id');
	}catch(Exception $e){
		$playerId = null;
	}
}

$title = 'Wars';
try{
	if(isset($clanId)){
		$wars = $clan->getMyWars();
		$clanName = $clan->get('name');
		if($clanName[strlen($clanName)-1] == 's'){
			$clanName .= "'";
		}else{
			$clanName .= "'s";
		}
		$title = $clanName . ' ' . $title;
	}elseif(isset($playerId)){
		$wars = $player->getWars();
		$playerName = $player->get('name');
		if($playerName[strlen($playerName)-1] == 's'){
			$playerName .= "'";
		}else{
			$playerName .= "'s";
		}
		$title = $playerName . ' ' . $title;
	}else{
		$wars = war::getWars();
	}
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
}


require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<?if(isset($playerId)){?>
			<li><a href="/players.php">Players</a></li>
			<li><a href="/player.php?playerId=<?=$playerId?>"><?=htmlspecialchars($player->get('name'));?></a></li>
		<?}elseif(isset($clanId)){?>
			<li><a href="/clans.php">Clans</a></li>
			<li><a href="/clan.php?clanId=<?=$clanId?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
		<?}?>
		<li class="active">Wars</li>
	</ol>
	<?require('showMessages.php');?>
	<h1><?=htmlspecialchars($title);?></h1><br>
	<?if(count($wars)>0){?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>War</th>
						<th>Size</th>
						<th>Score</th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($wars as $war) {
						if(isset($playerId)){
							$clan = $war->getPlayerWarClan($playerId);
							$clanId = $clan->get('id');
						}
						if(isset($clanId)){?>
							<tr style="cursor: pointer;" onclick="clickRow('war.php?warId=<?=$war->get("id");?>&clanId=<?=$clanId;?>');">
						<?}else{?>
							<tr style="cursor: pointer;" onclick="clickRow('war.php?warId=<?=$war->get("id");?>');">
						<?}?>
							<?if(isset($clanId)){
								$clan1 = $clan;
								$clan2 = new clan($war->getEnemy($clanId));
							}else{
								$clan1 = $war->get('clan1');
								$clan2 = $war->get('clan2');
							}
							$name = htmlspecialchars($clan1->get('name')) . ' vs. ' . htmlspecialchars($clan2->get('name'));
							$score = $war->getClanStars($clan1) . ' - ' . $war->getClanStars($clan2);
							?>
							<td><?=$name;?></td>
							<td><?=$war->get('size');?>v<?=$war->get('size');?></td>
							<td><i class="fa fa-star" style="color: gold;"></i> <?=$score;?> <i class="fa fa-star" style="color: gold;"></i></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	<?}else{?>
		<div class="alert alert-info" role="alert">
			<?if(isset($clanId)){?>
				<strong>Oh no!</strong> There's no wars in our records for this clan. You can start by adding one on the clan's page.
			<?}elseif(isset($playerId)){?>
				<strong>Oh no!</strong> There's no wars in our records for this player.
			<?}else{?>
				<strong>Oh no!</strong> There's no wars in our records. You can start by adding one on a clan's page.
			<?}?>
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