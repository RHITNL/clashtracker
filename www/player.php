<?
require('init.php');
require('session.php');

$playerId = $_GET['playerId'];
try{
	$player = new player($playerId);
	$playerId = $player->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No player with id ' . $playerId . ' found.';
	header('Location: /players.php');
	exit;
}

$clanId = $_GET['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$clan = null;
}

$attacks = $player->getAttacks();
$defences = $player->getDefences();
$wars = $player->getWars();
$attackStats = array(3=>0, 2=>0, 1=>0, 0=>0, 'DNA' => (count($wars)*2 - count($attacks)));
$defenceStats = array(3=>0, 2=>0, 1=>0, 0=>0);
$totalAttackStars = 0;
$totalDefenceStars = 0;
if(count($wars)>0){
	$warsAvailable = true;
	foreach ($attacks as $attack) {
		$attackStats[$attack['totalStars']]++;
		$totalAttackStars += $attack['totalStars'];
	}
	foreach ($defences as $defence) {
		$defenceStats[$defence['totalStars']]++;
		$totalDefenceStars += $defence['totalStars'];
	}
}else{
	$warsAvailable = false;
}
$averageAttackStars = ($totalAttackStars==0) ? 0 : $totalAttackStars/count($attacks);
$averageDefenceStars = ($totalDefenceStars==0) ? 0 : $totalDefenceStars/count($defences);

$gold = $player->getGold();
$elixir = $player->getElixir();
$oil = $player->getDarkElixir();
$goldAvailable = count($gold)>1;
$elixirAvailable = count($elixir)>1;
$oilAvailable = count($oil)>1;
$lootAvailable = ($goldAvailable||$elixirAvailable||$oilAvailable);

$goldPastYear = $player->getGold(yearAgo());
$elixirPastYear = $player->getElixir(yearAgo());
$oilPastYear = $player->getDarkElixir(yearAgo());
$goldAvailablePastYear = count($goldPastYear)>1;
$elixirAvailablePastYear = count($elixirPastYear)>1;
$oilAvailablePastYear = count($oilPastYear)>1;
$lootAvailablePastYear = (($goldAvailablePastYear||$elixirAvailablePastYear||$oilAvailablePastYear) && (count($gold)!=count($goldPastYear)||count($elixir)!=count($elixirPastYear)||count($oil)!=count($oilPastYear)));

$goldPastMonth = $player->getGold(monthAgo());
$elixirPastMonth = $player->getElixir(monthAgo());
$oilPastMonth = $player->getDarkElixir(monthAgo());
$goldAvailablePastMonth = count($goldPastMonth)>1;
$elixirAvailablePastMonth = count($elixirPastMonth)>1;
$oilAvailablePastMonth = count($oilPastMonth)>1;
$lootAvailablePastMonth = ($goldAvailablePastMonth||$elixirAvailablePastMonth||$oilAvailablePastMonth);

$goldPastWeek = $player->getGold(weekAgo());
$elixirPastWeek = $player->getElixir(weekAgo());
$oilPastWeek = $player->getDarkElixir(weekAgo());
$goldAvailablePastWeek = count($goldPastWeek)>1;
$elixirAvailablePastWeek = count($elixirPastWeek)>1;
$oilAvailablePastWeek = count($oilPastWeek)>1;
$lootAvailablePastWeek = ($goldAvailablePastWeek||$elixirAvailablePastWeek||$oilAvailablePastWeek);

$playerClan = $player->getMyClan();
$playerClans = $player->getMyClans();

$largestValue = 0;
foreach ($gold as $loot) {
	if($largestValue < $loot['lootAmount']){
		$largestValue = $loot['lootAmount'];
	}
}
foreach ($elixir as $loot) {
	if($largestValue < $loot['lootAmount']){
		$largestValue = $loot['lootAmount'];
	}
}
foreach ($oil as $loot) {
	if($largestValue < $loot['lootAmount']*100){
		$largestValue = $loot['lootAmount']*100;
	}
}

$userHasAccessToUpdatePlayer = userHasAccessToUpdatePlayer($player);

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<?if(isset($clan)){?>
			<li><a href="/clans.php">Clans</a></li>
			<li><a href="/clan.php?clanId=<?=$clan->get('id');?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
		<?}else{?>
			<li><a href="/players.php">Players</a></li>
		<?}?>
		<li class="active"><?=htmlspecialchars($player->get('name'));?></li>
	</ol>
	<?require('showMessages.php');?>
	<div class="col-md-12">
		<div class="col-md-6">
			<h1>
				<?$url = $player->get('leagueUrl');
				if(strlen($url)>0){?>
					<img src="<?=$url;?>">
				<?}?>
				<?=htmlspecialchars($player->get('name'));?>
			</h1>
		</div>
		<div class="col-md-6 text-right">
			<?if($userHasAccessToUpdatePlayer){?>
				<div id="editNameButtonDiv">
					<button type="button" class="btn btn-primary" onclick="showEditNameForm();">Edit Name</button>
				</div>
				<div id="editNameFormDiv" hidden>
					<form class="form-inline" action="/processEditName.php" method="POST">
						<input hidden name="playerId" value="<?=$player->get('id');?>"></input>
						<?if(isset($clan)){?>
							<input hidden name="clanId" value="<?=$clan->get('id');?>"></input>
						<?}?>
						<div class="form-group">
							<label for="name">Name </label>
							<input type="text" class="form-control" id="name" name="name" placeholder="<?=htmlspecialchars($player->get('name'));?>">
						</div>
						<button type="cancel" class="btn btn-default text-right" onclick="return showEditNameButton();">Cancel</button>
						<button type="submit" class="btn btn-primary text-right">Save</button>
					</form>
				</div>
			<?}?>
		</div>
		<div class="col-md-12">
			<?if(isset($clan)){?>
				<h6><?=rankFromCode($player->get('rank', $clan->get('id')));?></h6>
			<?}else{?>
				<h6><?=rankFromCode($player->get('rank'));?></h6>
			<?}?>
		</div>
	</div>
	<?if($warsAvailable){?>
		<div class="col-md-12">
			<h3><i class="fa fa-star" style="color: gold;"></i>&nbsp;Wars</h3>
			<div class="col-md-12">
				<div class="col-md-12">
					<div class="col-md-3 text-center">
						<h4>Attacks</h4><br>
						<canvas id="attackPie"></canvas>
					</div>
					<?if(count($defences)>0){?>
						<div class="col-md-3 text-center">
							<h4>Defences</h4><br>
							<canvas id="defencePie"></canvas>
						</div>
					<?}?>
					<div class="jumbotron col-md-6">
						<label class="col-sm-8">Average <i class="fa fa-star" style="color: gold;"></i> per attack</label>
						<div class="col-sm-4 text-right">
							<p><?=round($averageAttackStars, 2);?></p>
						</div>
						<label class="col-sm-8">Average <i class="fa fa-star" style="color: gold;"></i> per defence</label>
						<div class="col-sm-4 text-right">
							<p><?=round($averageDefenceStars, 2);?></p>
						</div>
					</div>
				</div>
			</div>
		</div><br>
	<?}?>
	<div class="col-md-12">
		<h3><i class="fa fa-coins" style="color: gold;"></i>&nbsp;Loot</h3>
		<?if($userHasAccessToUpdatePlayer){?>
			<div class="col-md-12">
				<div id="recordLootButtonDiv" class="col-md-12">
					<button type="button" class="btn btn-primary" onclick="showRecordLootForm();">Record Loot</button>
				</div>
				<div id="recordLootDiv" hidden class="col-md-12">
					<form class="form-inline" action="/processRecordLoot.php" method="POST">
						<input hidden name="type" value="single"></input>
						<input hidden name="playerId" value="<?=$player->get('id');?>"></input>
						<?if(isset($clan)){?>
							<input hidden name="clanId" value="<?=$clan->get('id');?>"></input>
						<?}?>
						<div class="col-md-3">
							<div class="form-group">
								<label for="gold">Gold </label>
								<input type="number" class="form-control" id="gold" name="gold" placeholder="<?=(count($gold)>0) ? $gold[0]['lootAmount'] : '0';?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="elixir">Elixir </label>
								<input type="number" class="form-control" id="elixir" name="elixir" placeholder="<?=(count($elixir)>0) ? $elixir[0]['lootAmount'] : '0';?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="darkElixir">Dark Elixir </label>
								<input type="number" class="form-control" id="darkElixir" name="darkElixir" placeholder="<?=(count($oil)>0) ? $oil[0]['lootAmount'] : '0';?>">
							</div>
						</div>
						<div class="col-md-3">
							<button type="cancel" class="btn btn-default text-right" onclick="return showRecordLootButton();">Cancel</button>
							<button type="submit" class="btn btn-primary text-right">Save</button>
						</div>
					</form>
				</div>
			</div><br><br>
		<?}?>
		<div class="col-md-12">
			<?if($lootAvailable){?>
				<div class="col-md-12">
					<ul class="nav nav-pills" role="tablist">
						<?if($lootAvailable){?>
							<li id="allTimeTab" onclick="showLootGraph('allTime');" role="presentation" class="active">
								<a style="cursor: pointer;">All Time</a>
							</li>
						<?}
						if($lootAvailablePastYear){?>
							<li id="pastYearTab" onclick="showLootGraph('pastYear');" role="presentation">
								<a style="cursor: pointer;">Past Year</a>
							</li>
						<?}
						if($lootAvailablePastMonth){?>
							<li id="pastMonthTab" onclick="showLootGraph('pastMonth');" role="presentation">
								<a style="cursor: pointer;">Past Month</a>
							</li>
						<?}
						if($lootAvailablePastWeek){?>
							<li id="pastWeekTab" onclick="showLootGraph('pastWeek');" role="presentation">
								<a style="cursor: pointer;">Past Week</a>
							</li>
						<?}?>
					</ul>
				</div><br><br><br>
				<div class="col-md-12">
					<div id="lootLineChartDiv" class="col-md-6">
						<canvas id="lootLineChart" height="100px"></canvas>
					</div>
					<div class="col-md-1"></div>
					<div class="jumbotron col-md-5">
						<div id="allTimeAverage">
							<?if($goldAvailable){?>
								<label class="col-sm-4">Average&nbsp;<i class="fa fa-2x fa-coins" style="color: gold;"></i></label>
								<div class="col-sm-8 text-right">
									<p><?=number_format($player->getAverageGold(), 0, '.', ',') . '/week';?></p>
								</div>
							<?}
							if($elixirAvailable){?>
								<label class="col-sm-4">Average&nbsp;&nbsp;<i class="fa fa-2x fa-tint" style="color: #FF09F4;"></i></label>
								<div class="col-sm-8 text-right">
									<p><?=number_format($player->getAverageElixir(), 0, '.', ',') . '/week';?></p>
								</div>
							<?}
							if($oilAvailable){?>
								<label class="col-sm-4">Average&nbsp;&nbsp;<i class="fa fa-2x fa-tint"></i></label>
								<div class="col-sm-8 text-right">
									<p><?=number_format($player->getAverageDarkElixir(), 0, '.', ',') . '/week';?></p>
								</div>
							<?}?>
						</div>
						<?if($lootAvailablePastYear){?>
							<div id="pastYearAverage" hidden>
								<?if($goldAvailable){?>
									<label class="col-sm-4">Average&nbsp;<i class="fa fa-2x fa-coins" style="color: gold;"></i></label>
									<div class="col-sm-8 text-right">
										<p><?=number_format($player->getAverageGold(yearAgo()), 0, '.', ',') . '/week';?></p>
									</div>
								<?}
								if($elixirAvailable){?>
									<label class="col-sm-4">Average&nbsp;&nbsp;<i class="fa fa-2x fa-tint" style="color: #FF09F4;"></i></label>
									<div class="col-sm-8 text-right">
										<p><?=number_format($player->getAverageElixir(yearAgo()), 0, '.', ',') . '/week';?></p>
									</div>
								<?}
								if($oilAvailable){?>
									<label class="col-sm-4">Average&nbsp;&nbsp;<i class="fa fa-2x fa-tint"></i></label>
									<div class="col-sm-8 text-right">
										<p><?=number_format($player->getAverageDarkElixir(yearAgo()), 0, '.', ',') . '/week';?></p>
									</div>
								<?}?>
							</div>
						<?}
						if($lootAvailablePastMonth){?>
							<div id="pastMonthAverage" hidden>
								<?if($goldAvailable){?>
									<label class="col-sm-4">Average&nbsp;<i class="fa fa-2x fa-coins" style="color: gold;"></i></label>
									<div class="col-sm-8 text-right">
										<p><?=number_format($player->getAverageGold(monthAgo()), 0, '.', ',') . '/week';?></p>
									</div>
								<?}
								if($elixirAvailable){?>
									<label class="col-sm-4">Average&nbsp;&nbsp;<i class="fa fa-2x fa-tint" style="color: #FF09F4;"></i></label>
									<div class="col-sm-8 text-right">
										<p><?=number_format($player->getAverageElixir(monthAgo()), 0, '.', ',') . '/week';?></p>
									</div>
								<?}
								if($oilAvailable){?>
									<label class="col-sm-4">Average&nbsp;&nbsp;<i class="fa fa-2x fa-tint"></i></label>
									<div class="col-sm-8 text-right">
										<p><?=number_format($player->getAverageDarkElixir(monthAgo()), 0, '.', ',') . '/week';?></p>
									</div>
								<?}?>
							</div>
						<?}
						if($lootAvailablePastWeek){?>
							<div id="pastWeekAverage" hidden>
								<?if($goldAvailable){?>
									<label class="col-sm-4">Average&nbsp;<i class="fa fa-2x fa-coins" style="color: gold;"></i></label>
									<div class="col-sm-8 text-right">
										<p><?=number_format($player->getAverageGold(weekAgo()), 0, '.', ',') . '/week';?></p>
									</div>
								<?}
								if($elixirAvailable){?>
									<label class="col-sm-4">Average&nbsp;&nbsp;<i class="fa fa-2x fa-tint" style="color: #FF09F4;"></i></label>
									<div class="col-sm-8 text-right">
										<p><?=number_format($player->getAverageElixir(weekAgo()), 0, '.', ',') . '/week';?></p>
									</div>
								<?}
								if($oilAvailable){?>
									<label class="col-sm-4">Average&nbsp;&nbsp;<i class="fa fa-2x fa-tint"></i></label>
									<div class="col-sm-8 text-right">
										<p><?=number_format($player->getAverageDarkElixir(weekAgo()), 0, '.', ',') . '/week';?></p>
									</div>
								<?}?>
							</div>
						<?}?>
					</div>
				</div>
			<?}else{?>
				<div class="col-md-8">
					<div class="alert alert-info" role="alert">
						<strong>Oh no!</strong> We don't have enough records for this player's loot to display any stats. You can start by adding some above.
					</div>
				</div>
			<?}?>
		</div><br>
	</div><br>
	<?if(count($playerClans)>0){?>
		<div class="col-md-12">
			<?if(count($playerClans)>1){?>
				<h3><i class="fa fa-shield"></i>&nbsp;Clans</h3>
			<?}else{?>
				<h3><i class="fa fa-shield"></i>&nbsp;Clan</h3>
			<?}
			if(isset($playerClan)){?>
				<div class="jumbotron col-md-5">
					<label class="col-sm-4">Current Clan</label>
					<div class="col-sm-8 text-right" style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=$playerClan->get("id");?>');">
						<p>
							<?$url = $playerClan->get('badgeUrl');
							if(strlen($url)>0){?>
								<img src="<?=$url;?>" height="20" width="20">
							<?}?>
							<?=htmlspecialchars($playerClan->get('name'));?>
						</p>
					</div>
					<label class="col-sm-4">Current Rank</label>
					<div class="col-sm-8 text-right">
						<p><?=rankFromCode($player->get('rank'));?></p>
					</div>
					<label class="col-sm-4">Date Joined</label>
					<div class="col-sm-8 text-right">
						<p><?=date('j F, Y', strtotime($playerClan->playerJoined($player->get('id'))));?></p>
					</div>
				</div>
			<?}
			if((isset($playerClan)&&count($playerClans)>1)||(!isset($playerClan)&&count($playerClans)>0)){?>
				<div class="col-md-7">
					<?if((isset($playerClan)&&count($playerClans)>2) || (!isset($playerClan)&&count($playerClans)>1)){?>
						<h4>Previous Clans</h4>
					<?}else{?>
						<h4>Previous Clan</h4>
					<?}?>
					<table class="table table-hover">
						<thead>
							<tr>
								<th></th>
								<th>Name</th>
								<th>Clan Points</th>
								<th>Wars Won</th>
								<th class="text-right">Clan Tag</th>
							</tr>
						</thead>
						<tbody>
							<?foreach ($playerClans as $clan) {
								if((isset($playerClan) && $clan->get('id') != $playerClan->get('id')) || !isset($playerClan)){?>
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
										<td class="text-right"><?=$clan->get('tag');?></td>
									</tr>
								<?}
							}?>
						</tbody>
					</table>
				</div>
			<?}?>
		</div>
	<?}?>
</div>
<script type="text/javascript">
function clickRow(href){
	window.document.location = href;
}
var options;
$(document).ready(function() {
	var largestValue = <?=$largestValue;?>;
	var label;
	if(largestValue > 1000000000){
		label = "<%=Math.round(value/1000000)/1000 + 'B'%>";
	}else if(largestValue > 100000000){
		label = "<%=Math.round(value/100000)/10 + 'M'%>";
	}else if(largestValue > 10000000){
		label = "<%=Math.round(value/10000)/100 + 'M'%>";
	}else if(largestValue > 10000){
		label = "<%=Math.round(value/100)/10 + 'K'%>";
	}else{
		label = "<%=value%>";
	}
	options = {
		responsive : true,
		scaleType : "date",
		scaleDateTimeFormat : "mmm d, yyyy",
		scaleLabel : label,
		emptyDataMessage: "Oh no! We don't have enough records for this player's loot to display any stats. You can start by adding some above."
	};
	var warsAvailable = "<?=$warsAvailable;?>";
	if(warsAvailable){
		var ctx = $("#attackPie").get(0).getContext("2d");
		var data = [
			{
				value: <?=$attackStats[3];?>,
				color:"#419641",
				highlight: "#5CB85C",
				label: "3 Star Attacks"
			},
			{
				value: <?=$attackStats[2];?>,
				color: "#F8CC00",
				highlight: "#F8EB00",
				label: "2 Star Attacks"
			},
			{
				value: <?=$attackStats[1];?>,
				color: "#E15B00",
				highlight: "#E16F00",
				label: "1 Star Attacks"
			},
			{
				value: <?=$attackStats[0];?>,
				color: "#D0470D",
				highlight: "#E0470D",
				label: "0 Star Attacks"
			},
			{
				value: <?=$attackStats['DNA'];?>,
				color: "#C6000D",
				highlight: "#D6000D",
				label: "Did not attack"
			}
		]
		var attackPieChart = new Chart(ctx).Pie(data,{});
		var defenceAvailable = "<?=(count($defences)>0);?>";
		if(defenceAvailable){
			var ctx = $("#defencePie").get(0).getContext("2d");
			var data = [
				{
					value: <?=$defenceStats[3];?>,
					color:"#D0470D",
					highlight: "#E0470D",
					label: "3 Star Defence"
				},
				{
					value: <?=$defenceStats[2];?>,
					color: "#E15B00",
					highlight: "#E16F00",
					label: "2 Star Defence"
				},
				{
					value: <?=$defenceStats[1];?>,
					color: "#F8CC00",
					highlight: "#F8EB00",
					label: "1 Star Defence"
				},
				{
					value: <?=$defenceStats[0];?>,
					color: "#419641",
					highlight: "#5CB85C",
					label: "Defended"
				}
			]
			var defencePieChart = new Chart(ctx).Pie(data,{});
		}
	}
	showAllTimeGraph();
});
var lootChart;
function showAllTimeGraph(){
	var lootAvailable = "<?=$lootAvailable;?>";
	if(lootAvailable){
		var ctx = $("#lootLineChart").get(0).getContext("2d");
		var data = [
			<?if($goldAvailable){?>
				{
					label: 'Gold',
					strokeColor: 'gold',
					pointColor: 'gold',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($gold as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . $loot['lootAmount'] . " },\n\t\t\t\t\t";
						}?>
					]
				},
			<?}if($elixirAvailable){?>
				{
					label: 'Elixir',
					strokeColor: '#FF09F4',
					pointColor: '#FF09F4',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($elixir as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . $loot['lootAmount'] . " },\n\t\t\t\t\t";
						}?>
					]
				},
			<?}if($oilAvailable){?>
				{
					label: 'Dark Elixir (×100)',
					strokeColor: 'black',
					pointColor: 'black',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($oil as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . ($loot['lootAmount']*100) . " },\n\t\t\t\t\t";
						}?>
					]
				}
			<?}?>
		];
		lootChart = new Chart(ctx).Scatter(data, options);
	}
}
function showPastYearGraph(){
	var lootAvailablePastYear = "<?=$lootAvailablePastYear;?>";
	if(lootAvailablePastYear){
		var ctx = $("#lootLineChart").get(0).getContext("2d");
		var data = [
			<?if($goldAvailablePastYear){?>
				{
					label: 'Gold',
					strokeColor: 'gold',
					pointColor: 'gold',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($goldPastYear as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . $loot['lootAmount'] . " },\n\t\t\t\t\t";
						}?>
					]
				},
			<?}if($elixirAvailablePastYear){?>
				{
					label: 'Elixir',
					strokeColor: '#FF09F4',
					pointColor: '#FF09F4',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($elixirPastYear as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . $loot['lootAmount'] . " },\n\t\t\t\t\t";
						}?>
					]
				},
			<?}if($oilAvailablePastYear){?>
				{
					label: 'Dark Elixir (×100)',
					strokeColor: 'black',
					pointColor: 'black',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($oilPastYear as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . ($loot['lootAmount']*100) . " },\n\t\t\t\t\t";
						}?>
					]
				}
			<?}?>
		];
		lootChart = new Chart(ctx).Scatter(data, options);
	}
}
function showPastMonthGraph(){
	var lootAvailablePastMonth = "<?=$lootAvailablePastMonth;?>";
	if(lootAvailablePastMonth){
		var ctx = $("#lootLineChart").get(0).getContext("2d");
		var data = [
			<?if($goldAvailablePastMonth){?>
				{
					label: 'Gold',
					strokeColor: 'gold',
					pointColor: 'gold',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($goldPastMonth as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . $loot['lootAmount'] . " },\n\t\t\t\t\t";
						}?>
					]
				},
			<?}if($elixirAvailablePastMonth){?>
				{
					label: 'Elixir',
					strokeColor: '#FF09F4',
					pointColor: '#FF09F4',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($elixirPastMonth as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . $loot['lootAmount'] . " },\n\t\t\t\t\t";
						}?>
					]
				},
			<?}if($oilAvailablePastMonth){?>
				{
					label: 'Dark Elixir (×100)',
					strokeColor: 'black',
					pointColor: 'black',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($oilPastMonth as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . ($loot['lootAmount']*100) . " },\n\t\t\t\t\t";
						}?>
					]
				}
			<?}?>
		];
		lootChart = new Chart(ctx).Scatter(data, options);
	}
}
function showPastWeekGraph(){
	var lootAvailablePastWeek = "<?=$lootAvailablePastWeek;?>";
	if(lootAvailablePastWeek){
		var ctx = $("#lootLineChart").get(0).getContext("2d");
		var data = [
			<?if($goldAvailablePastWeek){?>
				{
					label: 'Gold',
					strokeColor: 'gold',
					pointColor: 'gold',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($goldPastWeek as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . $loot['lootAmount'] . " },\n\t\t\t\t\t";
						}?>
					]
				},
			<?}if($elixirAvailablePastWeek){?>
				{
					label: 'Elixir',
					strokeColor: '#FF09F4',
					pointColor: '#FF09F4',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($elixirPastWeek as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . $loot['lootAmount'] . " },\n\t\t\t\t\t";
						}?>
					]
				},
			<?}if($oilAvailablePastWeek){?>
				{
					label: 'Dark Elixir (×100)',
					strokeColor: 'black',
					pointColor: 'black',
					pointStrokeColor: '#fff',
					data: [
						<?foreach ($oilPastWeek as $loot) {
							echo "{ x: " . (strtotime($loot['dateRecorded'])*1000) . ", y: " . ($loot['lootAmount']*100) . " },\n\t\t\t\t\t";
						}?>
					]
				}
			<?}?>
		];
		lootChart = new Chart(ctx).Scatter(data, options);
	}
}
function showRecordLootForm(){
	$('#recordLootButtonDiv').hide();
	$('#recordLootDiv').show();
}
function showRecordLootButton(){
	$('#recordLootButtonDiv').show();
	$('#recordLootDiv').hide();
	return false;
}
function showEditNameForm(){
	$('#editNameButtonDiv').hide();
	$('#editNameFormDiv').show();
}
function showEditNameButton(){
	$('#editNameButtonDiv').show();
	$('#editNameFormDiv').hide();
	return false;
}
function showLootGraph(type){
	$('#allTimeAverage').hide();
	$('#pastYearAverage').hide();
	$('#pastMonthAverage').hide();
	$('#pastWeekAverage').hide();
	$('#allTimeTab').removeClass('active');
	$('#pastYearTab').removeClass('active');
	$('#pastMonthTab').removeClass('active');
	$('#pastWeekTab').removeClass('active');
	$('#' + type + 'Tab').addClass('active');
	var ctx = $("#lootLineChart").get(0).getContext("2d");
	lootChart.destroy();
	if(type == 'allTime'){
		showAllTimeGraph();
		$('#allTimeAverage').show();
	}else if(type == 'pastYear'){
		showPastYearGraph();
		$('#pastYearAverage').show();
	}else if(type == 'pastMonth'){
		showPastMonthGraph();
		$('#pastMonthAverage').show();
	}else if(type == 'pastWeek'){
		showPastWeekGraph();
		$('#pastWeekAverage').show();
	}
}
</script>
<?
require('footer.php');