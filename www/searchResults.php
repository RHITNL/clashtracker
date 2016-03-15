<?
require('init.php');
require('session.php');

$query = $_GET['query'];
$api = $_GET['api'];

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


$apiClans = array();
if(isset($api)){
	$warFrequency = (strlen($_GET['warFrequency'])>0) ? $_GET['warFrequency'] : null;
	$minMembers = (strlen($_GET['minMembers'])>0) ? $_GET['minMembers'] : null;
	$maxMembers = (strlen($_GET['maxMembers'])>0) ? $_GET['maxMembers'] : null;
	$minClanLevel = (strlen($_GET['minClanLevel'])>0) ? $_GET['minClanLevel'] : null;
	$minClanPoints = (strlen($_GET['minClanPoints'])>0) ? $_GET['minClanPoints'] : null;
	$clanApi = new clanApi();
	try{
		$apiClans = $clanApi->searchClans($query, $warFrequency, $minMembers, $maxMembers, $minClanLevel, $minClanPoints);
	}catch(Exception $e){
		$_SESSION['curError'] = $e->getMessage();
	}
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li class="active">Search Results</li>
	</ol>
	<?require('showMessages.php');?>
	<h1 style="margin-bottom: 0px;">Search Results</h1>
	<h5 style="margin-top: 0px;">Search for "<?=$query;?>"...</h5>
	<?if(count($apiClans)>0){?>
		<!-- <form class="form-horizontal" action="/searchResults.php" method="GET">
			<div class="col-sm-6">
				<input hidden id="api" name="api" value="api"></input>
				<input hidden id="query" name="query" value="<?=$query;?>"></input>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="minMembers">Minimum Members:</label>
					<div class="col-sm-8">
						<input id="ex1Slider" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="50" data-slider-step="1" data-slider-value="<?=isset($minMembers) ? $minMembers : 0;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="description">War Frequency:</label>
					<div class="col-sm-8">
						<select class="form-control" id="warFrequency" name="warFrequency">
							<option value=""></option>
							<option  <?=($warFrequency == 'always') ? 'selected' : '';?> value="always">Always</option>
							<option  <?=($warFrequency == 'never') ? 'selected' : '';?> value="never">Never</option>
							<option  <?=($warFrequency == 'moreThanOncePerWeek') ? 'selected' : '';?> value="moreThanOncePerWeek">Twice a week</option>
							<option  <?=($warFrequency == 'oncePerWeek') ? 'selected' : '';?> value="oncePerWeek">Once a week</option>
							<option  <?=($warFrequency == 'lessThanOncePerWeek') ? 'selected' : '';?> value="lessThanOncePerWeek">Rarely</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
			</div>
			<div class="row">
				<div class="col-sm-12 btn-actions">
					<button type="submit" class="btn btn-success">Search</button>
				</div>
			</div>
		</form> -->
		<?if(count($apiClans)==1){?>
			<h4>Found <?=count($apiClans);?> clan matching in the Clash of Clans API.</h4>
		<?}elseif(count($apiClans)==50){?>
			<h4>Found <?=count($apiClans);?>+ clans matching in the Clash of Clans API. Showing top 50.</h4>
		<?}else{?>
			<h4>Found <?=count($apiClans);?> clans matching in the Clash of Clans API.</h4>
		<?}?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th></th>
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
					<?foreach ($apiClans as $apiClan) {?>
						<tr style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=ltrim($apiClan->tag, '#');?>');">
							<td width="20">
								<img src="<?=$apiClan->badgeUrls->small;?>" height="20" width="20">
							</td>
							<td><?=htmlspecialchars($apiClan->name);?></td>
							<td><?=$apiClan->warWins;?></td>
							<td><?=$apiClan->members;?></td>
							<td><?=clanTypeFromCode(convertType($apiClan->type));?></td>
							<td><?=warFrequencyFromCode(convertFrequency($apiClan->warFrequency));?></td>
							<td><?=$apiClan->requiredTrophies;?></td>
							<td class="text-right"><?=$apiClan->tag;?></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	<?}if(count($clans)>0){
		if(count($clans)==1){?>
			<h4>Found <?=count($clans);?> clan matching.</h4>
		<?}elseif(count($clans)==50){?>
			<h4>Found <?=count($clans);?>+ clans matching. Showing top 50.</h4>
		<?}else{?>
			<h4>Found <?=count($clans);?> clans matching.</h4>
		<?}?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th></th>
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
							<td width="20">
								<?$url = $clan->get('badgeUrl');
								if(strlen($url)>0){?>
									<img src="<?=$url;?>" height="20" width="20">
								<?}?>
							</td>
							<td><?=htmlspecialchars($clan->get('name'));?></td>
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
	<?}
	if(count($players)>0){
		if(count($players)==1){?>
			<h4>Found <?=count($players);?> player matching.</h4>
		<?}elseif(count($players)==50){?>
			<h4>Found <?=count($players);?>+ players matching. Showing top 50.</h4>
		<?}else{?>
			<h4>Found <?=count($players);?> players matching.</h4>
		<?}?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th></th>
						<th>Name</th>
						<th>Level</th>
						<th>Trophies</th>
						<th>Clan Name</th>
						<th>Clan Rank</th>
						<th class="text-right">Player Tag</th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($players as $player) {?>
						<tr style="cursor: pointer;">
							<td width="20">
								<?$url = $player->get('leagueUrl');
								if(strlen($url)>0){?>
									<img src="<?=$url;?>" height="20" width="20">
								<?}?>
							</td>
							<td onclick="clickRow('player.php?playerId=<?=$player->get("id");?>');"><?=htmlspecialchars($player->get('name'));?></td>
							<td onclick="clickRow('player.php?playerId=<?=$player->get("id");?>');"><i class="fa fa-certificate" style="color: #43BBE9;"></i>&nbsp;<?=$player->get('level');?></td>
							<td onclick="clickRow('player.php?playerId=<?=$player->get("id");?>');"><i class="fa fa-trophy" style="color: gold;"></i>&nbsp;<?=$player->get('trophies');?></td>
							<?$clan = $player->getMyClan();
							if(isset($clan)){?>
								<td onclick="clickRow('clan.php?clanId=<?=$clan->get("id");?>');">
									<?$url = $clan->get('badgeUrl');
									if(strlen($url)>0){?>
										<img src="<?=$url;?>" height="20" width="20">
									<?}?>
									<?=htmlspecialchars($clan->get('name'));?>
								</td>
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
	if(count($clans)==0&&count($players)==0&&count($apiClans)==0){?>
		<div class="alert alert-info" role="alert">
			<strong>Oh no!</strong> We couldn't find anything in our records matching your search<?if(!isset($api)){?>.<?}?>
			<?if(!isset($api)){?>
				Search the Clash of Clans API instead:&nbsp;
				<a type="button" class="btn btn-xs btn-success" href="/searchResults.php?query=<?=$query;?>&api=api">Search API</a>
			<?}else{?>
				or in the Clash of Clans API.
			<?}?>
		</div>
	<?}elseif(!isset($api)){?>
		<div class="alert alert-info" role="alert">
			Couldn't find what you're looking for? Search the Clash of Clans API instead:&nbsp;
			<a type="button" class="btn btn-xs btn-success" href="/searchResults.php?query=<?=$query;?>&api=api">Search API</a>
		</div>
	<?}elseif(count($apiClans)==0){?>
		<div class="alert alert-info" role="alert">
			<strong>Oh no!</strong> We couldn't find anything in the Clash of Clans API.
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