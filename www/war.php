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
	$clan1 = new clan($clanId);
	$clanId = $clan1->get('id');
}else{
	$clan1 = new clan($war->get('firstClanId'));
	$clanId = null;
}
$clan2 = new clan($war->getEnemy($clan1->get('id')));

$clan1Players = $war->getMyWarPlayers($clan1->get('id'));
$clan2Players = $war->getMyWarPlayers($clan2->get('id'));

$clan1Stars = $war->getClanStars($clan1->get('id'));
$clan1Attacks = $war->getAttacks($clan1->get('id'));
$clan1AttacksUsed = count($clan1Attacks);
$clan1AttacksLeft = ($war->get('size') * 2) - $clan1AttacksUsed;
$clan1AttacksWon = 0;
$clan1AttacksLost = 0;
foreach ($clan1Attacks as $attack) {
	if($attack['totalStars'] > 0){
		$clan1AttacksWon++;
	}else{
		$clan1AttacksLost++;
	}
}
$clan1BestAttacks = array(3=>0, 2=>0, 1=>0, 0=>0);
foreach ($clan2Players as $defender) {
	$defences = $war->getPlayerDefences($defender->get('id'));
	$starsAgainst = 0;
	foreach ($defences as $defence) {
		if($defence['totalStars'] > $starsAgainst){
			$starsAgainst = $defence['totalStars'];
		}
	}
	$clan1BestAttacks[$starsAgainst]++;
}
$clan1CanAddMore = count($clan1Players) < $war->get('size');

$clan2Stars = $war->getClanStars($clan2->get('id'));
$clan2Attacks = $war->getAttacks($clan2->get('id'));
$clan2AttacksUsed = count($clan2Attacks);
$clan2AttacksLeft = ($war->get('size') * 2) - $clan2AttacksUsed;
$clan2AttacksWon = 0;
$clan2AttacksLost = 0;
foreach ($clan2Attacks as $attack) {
	if($attack['totalStars'] > 0){
		$clan2AttacksWon++;
	}else{
		$clan2AttacksLost++;
	}
}
$clan2BestAttacks = array(3=>0, 2=>0, 1=>0, 0=>0);
foreach ($clan1Players as $defender) {
	$defences = $war->getPlayerDefences($defender->get('id'));
	$starsAgainst = 0;
	foreach ($defences as $defence) {
		if($defence['totalStars'] > $starsAgainst){
			$starsAgainst = $defence['totalStars'];
		}
	}
	$clan2BestAttacks[$starsAgainst]++;
}
$clan2CanAddMore = count($clan2Players) < $war->get('size');

$warAttacks = array_reverse($war->getAttacks());

function getPlayerAttacks($playerId, $type='attack'){
	global $warAttacks;
	$warAttacks = array_reverse($warAttacks);
	$attacks = array();
	foreach ($warAttacks as $attack) {
		if($type == 'attack' && $attack['attackerId'] == $playerId){
			$attacks[] = $attack;
		}
		if($type == 'defence' && $attack['defenderId'] == $playerId){
			$attacks[] = $attack;
		}
	}
	return $attacks;
}

$isEditable = $war->isEditable();

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<?if(isset($clanId)){?>
			<li><a href="/clans.php">Clans</a></li>
			<li><a href="/clan.php?clanId=<?=$clan1->get('id');?>"><?=$clan1->get('name');?></a></li>
			<li><a href="/wars.php?clanId=<?=$clan1->get('id');?>">Wars</a></li>
		<?}else{?>
			<li><a href="/wars.php">Wars</a></li>
		<?}?>
		<li class="active"><?=$clan1->get('name');?> vs. <?=$clan2->get('name');?></li>
	</ol>
	<?require('showMessages.php');?>
	<div class="visible-lg-block">
		<div class="col-sm-6 text-center"><h1><?=$clan1->get('name');?></h1></div>
		<div class="col-sm-6 text-center"><h1><?=$clan2->get('name');?></h1></div>
	</div>
	<div class="col-sm-12 text-center"><h2><i class="fa fa-star" style="color: gold;"></i> <?=$clan1Stars;?> - <?=$clan2Stars;?> <i class="fa fa-star" style="color: gold;"></i></h2></div>
	<div class="col-md-6">
		<h2 class="hidden-lg"><?=$clan1->get('name');?></h2>
		<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<h3 class=" panel-title">Attack Totals</h3>
			</div>
			<div class="panel-body">
				<p class="col-sm-6 text-center">Attacks Used</p>
				<p class="col-sm-6 text-center"><?=$clan1AttacksUsed;?></p>
				<br>
				<p class="col-sm-6 text-center">Attacks Won</p>
				<p class="col-sm-6 text-center"><?=$clan1AttacksWon;?></p>
				<br>
				<p class="col-sm-6 text-center">Attacks Lost</p>
				<p class="col-sm-6 text-center"><?=$clan1AttacksLost;?></p>
				<br>
				<p class="col-sm-6 text-center">Attacks Remaining</p>
				<p class="col-sm-6 text-center"><?=$clan1AttacksLeft;?></p>
			</div>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<h3 class=" panel-title">Best Attacks</h3>
			</div>
			<div class="panel-body">
				<p class="col-sm-6 text-center">3 Stars</p>
				<p class="col-sm-6 text-center"><?=$clan1BestAttacks[3];?></p>
				<br>
				<p class="col-sm-6 text-center">2 Stars</p>
				<p class="col-sm-6 text-center"><?=$clan1BestAttacks[2];?></p>
				<br>
				<p class="col-sm-6 text-center">1 Star</p>
				<p class="col-sm-6 text-center"><?=$clan1BestAttacks[1];?></p>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<h2 class="hidden-lg"><?=$clan2->get('name');?></h2>
		<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<h3 class=" panel-title">Attack Totals</h3>
			</div>
			<div class="panel-body">
				<p class="col-sm-6 text-center">Attacks Used</p>
				<p class="col-sm-6 text-center"><?=$clan2AttacksUsed;?></p>
				<br>
				<p class="col-sm-6 text-center">Attacks Won</p>
				<p class="col-sm-6 text-center"><?=$clan2AttacksWon;?></p>
				<br>
				<p class="col-sm-6 text-center">Attacks Lost</p>
				<p class="col-sm-6 text-center"><?=$clan2AttacksLost;?></p>
				<br>
				<p class="col-sm-6 text-center">Attacks Remaining</p>
				<p class="col-sm-6 text-center"><?=$clan2AttacksLeft;?></p>
			</div>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading text-center">
				<h3 class=" panel-title">Best Attacks</h3>
			</div>
			<div class="panel-body">
				<p class="col-sm-6 text-center">3 Stars</p>
				<p class="col-sm-6 text-center"><?=$clan2BestAttacks[3];?></p>
				<br>
				<p class="col-sm-6 text-center">2 Stars</p>
				<p class="col-sm-6 text-center"><?=$clan2BestAttacks[2];?></p>
				<br>
				<p class="col-sm-6 text-center">1 Star</p>
				<p class="col-sm-6 text-center"><?=$clan2BestAttacks[1];?></p>
			</div>
		</div>
	</div>
	<?if(count($clan1Attacks) > 0 || count($clan2Attacks) > 0){?>
		<div class="col-md-12">
			<ul class="nav nav-pills" role="tablist">
				<li id="warPlayersTab" role="presentation" class="active">
					<a style="cursor: pointer;">War Players</a>
				</li>
				<li id="warAttacksTab" role="presentation">
					<a style="cursor: pointer;">War Events</a>
				</li>
			</ul>
		</div>
	<?}?>
	<div id="warPlayers" class="col-md-12">
		<br>
		<div class="col-md-6">
			<h2 class="hidden-lg"><?=$clan1->get('name');?></h2>
			<div class="col-md-12">
				<?if($clan1CanAddMore && $isEditable){
					if(isset($clanId)){?>
						<a type="button" class="btn btn-success" href="/addWarPlayer.php?warId=<?=$war->get('id');?>&addClanId=<?=$clan1->get('id');?>&clanId=<?=$clanId;?>">Add Players</a><br><br>
					<?}else{?>
						<a type="button" class="btn btn-success" href="/addWarPlayer.php?warId=<?=$war->get('id');?>&addClanId=<?=$clan1->get('id');?>">Add Players</a><br><br>
					<?}
					if(count($clan1Players) > 0){?>
						<div class="alert alert-warning" role="alert">
							There's not enough players in the war for this clan yet. You can add more by clicking the button above.
						</div>
					<?}
				}?>
			</div>
			<div class="col-md-12">
				<?if(count($clan1Players) > 0){?>
					<table class="table table-hover">
						<thead>
							<tr>
								<?if($isEditable){?>
									<th></th>
								<?}?>
								<th>Player</th>
								<th>First Attack</th>
								<th>Second Attack</th>
								<th>Defence</th>
								<?if($isEditable){?>
									<th></th>
								<?}?>
							</tr>
						</thead>
						<tbody>
							<?foreach ($clan1Players as $player) {
								$playerAttacks = getPlayerAttacks($player->get('id'));
								$firstAttack = $playerAttacks[0];
								$secondAttack = $playerAttacks[1];
								$playerDefences = getPlayerAttacks($player->get('id'), 'defence');
								$starsAgainst = -1;
								$rank = $war->getPlayerRank($player->get('id'));
								foreach ($playerDefences as $defence) {
									if($defence['totalStars'] > $starsAgainst){
										$starsAgainst = $defence['totalStars'];
									}
								}?>
								<tr style="cursor: pointer;" onclick="clickRow('player.php?playerId=<?=$player->get("id");?>');">
									<?if($isEditable){?>
										<td style="line-height: 1;">
											<?if(isset($clanId)){
												if($rank!=1){?>
													<a href="/processUpdateWarRank.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&action=up&clanId=<?=$clanId;?>" style="color: black;">
														<i class="fa fa-caret-up"></i><br>
													</a>
												<?}
												if($rank!=$war->get('size')){?>
													<a href="/processUpdateWarRank.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&action=down&clanId=<?=$clanId;?>" style="color: black;">
														<i class="fa fa-caret-down"></i><br>
													</a>
												<?}
											}else{
												if($rank!=1){?>
													<a href="/processUpdateWarRank.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&action=up" style="color: black;">
														<i class="fa fa-caret-up"></i><br>
													</a>
												<?}
												if($rank!=$war->get('size')){?>
													<a href="/processUpdateWarRank.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&action=down" style="color: black;">
														<i class="fa fa-caret-down"></i><br>
													</a>
												<?}
											}?>
										</td>
									<?}?>
									<td><?=$rank . '. ' . $player->get('name');?></td>
									<td>
										<?if(isset($firstAttack)){
											for($i=$firstAttack['totalStars']-$firstAttack['newStars'];$i>0;$i--){?>
												<i class="fa fa-star" style="color: silver;"></i>
											<?}
											for($i=$firstAttack['newStars'];$i>0;$i--){?>
												<i class="fa fa-star" style="color: gold;"></i>
											<?}
											for($i=$firstAttack['totalStars'];$i<3;$i++){?>
												<i class="fa fa-star-o" style="color: silver;"></i>
											<?}
										}else{
											if(count($clan2Players) > 0 && $isEditable){
												if(isset($clanId)){?>
													<a type="button" class="btn btn-xs btn-success" href="/addWarAttack.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&clanId=<?=$clanId;?>">Add Attack</a>
												<?}else{?>
													<a type="button" class="btn btn-xs btn-success" href="/addWarAttack.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>">Add Attack</a>
												<?}
											}
										}?>
									</td>
									<td>
										<?if(isset($secondAttack)){
											for($i=$secondAttack['totalStars']-$secondAttack['newStars'];$i>0;$i--){?>
												<i class="fa fa-star" style="color: silver;"></i>
											<?}
											for($i=$secondAttack['newStars'];$i>0;$i--){?>
												<i class="fa fa-star" style="color: gold;"></i>
											<?}
											for($i=$secondAttack['totalStars'];$i<3;$i++){?>
												<i class="fa fa-star-o" style="color: silver;"></i>
											<?}
										}elseif(isset($firstAttack)){
											if(count($clan2Players) > 0 && $isEditable){
												if(isset($clanId)){?>
													<a type="button" class="btn btn-xs btn-success" href="/addWarAttack.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&clanId=<?=$clanId;?>">Add Attack</a>
												<?}else{?>
													<a type="button" class="btn btn-xs btn-success" href="/addWarAttack.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>">Add Attack</a>
												<?}
											}
										}?>
									</td>
									<td>
										<?if($starsAgainst==3){?>
											<i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star" style="color: gold;"></i>
										<?}elseif($starsAgainst==2){?>
											<i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star-o" style="color: silver;"></i>
										<?}elseif($starsAgainst==1){?>
											<i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star-o" style="color: silver;"></i> <i class="fa fa-star-o" style="color: silver;"></i>
										<?}elseif($starsAgainst==0){?>
											<i class="fa fa-star-o" style="color: silver;"></i> <i class="fa fa-star-o" style="color: silver;"></i> <i class="fa fa-star-o" style="color: silver;"></i>
										<?}?>
									</td>
									<?if($isEditable){?>
										<td>
											<?if(isset($clanId)){?>
												<a type="button" class="btn btn-xs btn-danger" href="/processRemoveWarPlayer.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&clanId=<?=$clanId;?>" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="Click to remove this player from the war.">&times;</a>
											<?}else{?>
												<a type="button" class="btn btn-xs btn-danger" href="/processRemoveWarPlayer.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="Click to remove this player from the war.">&times;</a>
											<?}?>
										</td>
									<?}?>
								</tr>
							<?}?>
						<tbody>
					</table>
				<?}else{?>
					<div class="alert alert-info" role="alert">
						<strong>On no!</strong> There's no players in the war for this clan. You can start by adding some above.
					</div>
				<?}?>
			</div>
		</div>
		<div class="col-md-6">
			<h2 class="hidden-lg"><?=$clan2->get('name');?></h2>
			<div class="col-md-12">
				<?if($clan2CanAddMore && $isEditable){
					if(isset($clanId)){?>
						<a type="button" class="btn btn-success" href="/addWarPlayer.php?warId=<?=$war->get('id');?>&addClanId=<?=$clan2->get('id');?>&clanId=<?=$clanId;?>">Add Players</a><br><br>
					<?}else{?>
						<a type="button" class="btn btn-success" href="/addWarPlayer.php?warId=<?=$war->get('id');?>&addClanId=<?=$clan2->get('id');?>">Add Players</a><br><br>
					<?}
					if(count($clan2Players) > 0){?>
						<div class="alert alert-warning" role="alert">
							There's not enough players in the war for this clan yet. You can add more by clicking the button above.
						</div>
					<?}
				}?>
			</div>
			<div class="col-md-12">
				<?if(count($clan2Players) > 0){?>
					<table class="table table-hover">
						<thead>
							<tr>
								<?if($isEditable){?>
									<th></th>
								<?}?>
								<th>Player</th>
								<th>First Attack</th>
								<th>Second Attack</th>
								<th>Defence</th>
								<?if($isEditable){?>
									<th></th>
								<?}?>
							</tr>
						</thead>
						<tbody>
							<?foreach ($clan2Players as $player) {
								$playerAttacks = getPlayerAttacks($player->get('id'));
								$firstAttack = $playerAttacks[0];
								$secondAttack = $playerAttacks[1];
								$playerDefences = getPlayerAttacks($player->get('id'), 'defence');
								$starsAgainst = -1;
								$rank = $war->getPlayerRank($player->get('id'));
								foreach ($playerDefences as $defence) {
									if($defence['totalStars'] > $starsAgainst){
										$starsAgainst = $defence['totalStars'];
									}
								}?>
								<tr style="cursor: pointer;" onclick="clickRow('player.php?playerId=<?=$player->get("id");?>');">
									<?if($isEditable){?>
										<td style="line-height: 1;">
											<?if(isset($clanId)){
												if($rank!=1){?>
													<a href="/processUpdateWarRank.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&action=up&clanId=<?=$clanId;?>" style="color: black;">
														<i class="fa fa-caret-up"></i><br>
													</a>
												<?}
												if($rank!=$war->get('size')){?>
													<a href="/processUpdateWarRank.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&action=down&clanId=<?=$clanId;?>" style="color: black;">
														<i class="fa fa-caret-down"></i><br>
													</a>
												<?}
											}else{
												if($rank!=1){?>
													<a href="/processUpdateWarRank.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&action=up" style="color: black;">
														<i class="fa fa-caret-up"></i><br>
													</a>
												<?}
												if($rank!=$war->get('size')){?>
													<a href="/processUpdateWarRank.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&action=down" style="color: black;">
														<i class="fa fa-caret-down"></i><br>
													</a>
												<?}
											}?>
										</td>
									<?}?>
									<td><?=$rank . '. ' . $player->get('name');?></td>
									<td>
										<?if(isset($firstAttack)){
											for($i=$firstAttack['totalStars']-$firstAttack['newStars'];$i>0;$i--){?>
												<i class="fa fa-star" style="color: silver;"></i>
											<?}
											for($i=$firstAttack['newStars'];$i>0;$i--){?>
												<i class="fa fa-star" style="color: gold;"></i>
											<?}
											for($i=$firstAttack['totalStars'];$i<3;$i++){?>
												<i class="fa fa-star-o" style="color: silver;"></i>
											<?}
										}else{
											if(count($clan1Players) > 0 && $isEditable){
												if(isset($clanId)){?>
													<a type="button" class="btn btn-xs btn-success" href="/addWarAttack.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&clanId=<?=$clanId;?>">Add Attack</a>
												<?}else{?>
													<a type="button" class="btn btn-xs btn-success" href="/addWarAttack.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>">Add Attack</a>
												<?}
											}
										}?>
									</td>
									<td>
										<?if(isset($secondAttack)){
											for($i=$secondAttack['totalStars']-$secondAttack['newStars'];$i>0;$i--){?>
												<i class="fa fa-star" style="color: silver;"></i>
											<?}
											for($i=$secondAttack['newStars'];$i>0;$i--){?>
												<i class="fa fa-star" style="color: gold;"></i>
											<?}
											for($i=$secondAttack['totalStars'];$i<3;$i++){?>
												<i class="fa fa-star-o" style="color: silver;"></i>
											<?}
										}elseif(isset($firstAttack)){
											if(count($clan1Players) > 0 && $isEditable){
												if(isset($clanId)){?>
													<a type="button" class="btn btn-xs btn-success" href="/addWarAttack.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&clanId=<?=$clanId;?>">Add Attack</a>
												<?}else{?>
													<a type="button" class="btn btn-xs btn-success" href="/addWarAttack.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>">Add Attack</a>
												<?}
											}
										}?>
									</td>
									<td>
										<?if($starsAgainst==3){?>
											<i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star" style="color: gold;"></i>
										<?}elseif($starsAgainst==2){?>
											<i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star-o" style="color: silver;"></i>
										<?}elseif($starsAgainst==1){?>
											<i class="fa fa-star" style="color: gold;"></i> <i class="fa fa-star-o" style="color: silver;"></i> <i class="fa fa-star-o" style="color: silver;"></i>
										<?}elseif($starsAgainst==0){?>
											<i class="fa fa-star-o" style="color: silver;"></i> <i class="fa fa-star-o" style="color: silver;"></i> <i class="fa fa-star-o" style="color: silver;"></i>
										<?}?>
									</td>
									<?if($isEditable){?>
										<td>
											<?if(isset($clanId)){?>
												<a type="button" class="btn btn-xs btn-danger" href="/processRemoveWarPlayer.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>&clanId=<?=$clanId;?>" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="Click to remove this player from the war.">&times;</a>
											<?}else{?>
												<a type="button" class="btn btn-xs btn-danger" href="/processRemoveWarPlayer.php?warId=<?=$war->get('id');?>&playerId=<?=$player->get('id');?>" data-toggle="popover" data-trigger="hover" data-placement="left" data-content="Click to remove this player from the war.">&times;</a>
											<?}?>
										</td>
									<?}?>
								</tr>
							<?}?>
						<tbody>
					</table>
				<?}else{?>
					<div class="alert alert-info" role="alert">
						<strong>On no!</strong> There's no players in the war for this clan. You can start by adding some above.
					</div>
				<?}?>
			</div>
		</div>
	</div>
	<div id="warAttacks" class="col-md-12 hidden">
		<div class="col-md-12">
			<table class="table table-hover">
				<thead>
					<tr>
						<th class="text-left"><h3><?=$clan1->get('name');?></h3></th>
						<?if($isEditable){?>
							<th class="text-center">Actions</th>
						<?}?>
						<th class="text-right"><h3><?=$clan2->get('name');?></h3></th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($warAttacks as $attack) {
						$attacker = new player($attack['attackerId']);
						$defender = new player($attack['defenderId']);
						$attackerRank = $war->getPlayerRank($attacker->get('id'));
						$defenderRank = $war->getPlayerRank($defender->get('id'));
						$attackerClanId = $attack['attackerClanId'];
						$totalStars = $attack['totalStars'];
						$newStars = $attack['newStars'];?>
						<tr>
							<?if($attackerClanId == $clan1->get('id')){?>
								<td class="text-left"><strong><?=$attackerRank . '. ' . $attacker->get('name');?></strong>&nbsp;<i class="fa fa-star"></i><br>
									<?for($i=$totalStars-$newStars;$i>0;$i--){?>
										<i class="fa fa-star" style="color: silver;"></i>
									<?}
									for($i=$newStars;$i>0;$i--){?>
										<i class="fa fa-star" style="color: gold;"></i>
									<?}
									for($i=$totalStars;$i<3;$i++){?>
										<i class="fa fa-star-o" style="color: silver;"></i>
									<?}?>
								</td>
								<?if($isEditable){?>
									<td class="text-center">
										<?if(isset($clanId)){?>
											<a type="button" class="btn btn-sm btn-success" href="/editWarAttack.php?warId=<?=$war->get('id');?>&attackerId=<?=$attacker->get('id');?>&defenderId=<?=$defender->get('id');?>&clanId=<?=$clanId;?>">Edit</a>
											<a type="button" class="btn btn-sm btn-danger" href="/processRemoveWarAttack.php?warId=<?=$war->get('id');?>&attackerId=<?=$attacker->get('id');?>&defenderId=<?=$defender->get('id');?>&clanId=<?=$clanId;?>" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to remove this attack from the war.">&times;</a>
										<?}else{?>
											<a type="button" class="btn btn-sm btn-success" href="/editWarAttack.php?warId=<?=$war->get('id');?>&attackerId=<?=$attacker->get('id');?>&defenderId=<?=$defender->get('id');?>">Edit</a>
											<a type="button" class="btn btn-sm btn-danger" href="/processRemoveWarAttack.php?warId=<?=$war->get('id');?>&attackerId=<?=$attacker->get('id');?>&defenderId=<?=$defender->get('id');?>" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to remove this attack from the war.">&times;</a>
										<?}?>
									</td>
								<?}?>
								<td class="text-right"><i class="fa fa-shield"></i>&nbsp;<strong><?=$defenderRank . '. ' . $defender->get('name');?></strong><br>
									<?if($totalStars==0){?>
										<i>Defended</i>
									<?}else{?>
										<i>Defeat</i>
									<?}?>
								</td>
							<?}else{?>
								<td class="text-left"><strong><?=$defenderRank . '. ' . $defender->get('name');?></strong>&nbsp;<i class="fa fa-shield"></i><br>
									<?if($totalStars==0){?>
										<i>Defended</i>
									<?}else{?>
										<i>Defeat</i>
									<?}?>
								</td>
								<?if($isEditable){?>
									<td class="text-center">
										<?if(isset($clanId)){?>
											<a type="button" class="btn btn-sm btn-success" href="/editWarAttack.php?warId=<?=$war->get('id');?>&attackerId=<?=$attacker->get('id');?>&defenderId=<?=$defender->get('id');?>&clanId=<?=$clanId;?>">Edit</a>
											<a type="button" class="btn btn-sm btn-danger" href="/processRemoveWarAttack.php?warId=<?=$war->get('id');?>&attackerId=<?=$attacker->get('id');?>&defenderId=<?=$defender->get('id');?>&clanId=<?=$clanId;?>" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to remove this attack from the war.">&times;</a>
										<?}else{?>
											<a type="button" class="btn btn-sm btn-success" href="/editWarAttack.php?warId=<?=$war->get('id');?>&attackerId=<?=$attacker->get('id');?>&defenderId=<?=$defender->get('id');?>">Edit</a>
											<a type="button" class="btn btn-sm btn-danger" href="/processRemoveWarAttack.php?warId=<?=$war->get('id');?>&attackerId=<?=$attacker->get('id');?>&defenderId=<?=$defender->get('id');?>" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to remove this attack from the war.">&times;</a>
										<?}?>
									</td>
								<?}?>
								<td class="text-right"><i class="fa fa-star"></i>&nbsp;<strong><?=$attackerRank . '. ' . $attacker->get('name');?></strong><br>
									<?for($i=$totalStars-$newStars;$i>0;$i--){?>
										<i class="fa fa-star" style="color: silver;"></i>
									<?}
									for($i=$newStars;$i>0;$i--){?>
										<i class="fa fa-star" style="color: gold;"></i>
									<?}
									for($i=$totalStars;$i<3;$i++){?>
										<i class="fa fa-star-o" style="color: silver;"></i>
									<?}?></td>
								</td>
							<?}?>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
function clickRow(href){
	window.document.location = href;
}
$('#warAttacksTab').on('click', function(){
	$('#warPlayersTab').removeClass('active');
	$('#warAttacksTab').addClass('active');
	$('#warPlayers').addClass('hidden');
	$('#warAttacks').removeClass('hidden');
});
$('#warPlayersTab').on('click', function(){
	$('#warPlayersTab').addClass('active');
	$('#warAttacksTab').removeClass('active');
	$('#warPlayers').removeClass('hidden');
	$('#warAttacks').addClass('hidden');
});
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();   
});
</script>
<?
require('footer.php');