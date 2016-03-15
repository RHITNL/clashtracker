<?
require('init.php');
require('session.php');

$query = $_GET['query'];
$api = $_GET['api'];

try{
	$players = player::searchPlayers($query);
}catch(Exception $e){
	$players = array();
	$_SESSION['curError'] .= $e->getMessage();
}


$apiClans = array();
if(isset($api)){
	$query = strlen($query)>3 ? $query : null;
	$warFrequency = (strlen($_GET['warFrequency'])>0) ? $_GET['warFrequency'] : null;
	$members = explode(',', $_GET['members']);
	if(isset($members) && count($members)==2){
		$minMembers = (min($members)>1) ? min($members) : null;
		$maxMembers = max($members);
	}else{
		$minMembers = null;
		$maxMembers = null;
	}
	$minClanLevel = (strlen($_GET['minClanLevel'])>0) ? $_GET['minClanLevel'] : null;
	$minClanLevel = ($minClanLevel > 1) ? $minClanLevel : null;
	$minClanPoints = (strlen($_GET['minClanPoints'])>0) ? $_GET['minClanPoints'] : null;
	$minClanPoints = ($minClanPoints > 0) ? $minClanPoints : 1;
	$clanApi = new clanApi();
	try{
		$apiClans = $clanApi->searchClans($query, $warFrequency, $minMembers, $maxMembers, $minClanLevel, $minClanPoints);
	}catch(Exception $e){
		$_SESSION['curError'] = $e->getMessage();
	}
}

try{
	$clans = clan::searchClans($query, $warFrequency, $minMembers, $maxMembers, $minClanLevel, $minClanPoints);
}catch(Exception $e){
	$clans = array();
	$_SESSION['curError'] = $e->getMessage();
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
	<h5 style="margin-top: 0px;">Search for "<?=$query;?>"...</h5><br>
	<?if(isset($api)){?>
		<form class="form-horizontal" action="/searchResults.php" method="GET">
			<div class="col-sm-6">
				<input hidden id="api" name="api" value="api"></input>
				<input hidden id="query" name="query" value="<?=$query;?>"></input>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="query">Clan Name:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="query" name="query" value="<?=$query;?>"></input>
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
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="minMembers">Members:</label>
					<div class="col-sm-8">
						<input style="width: 100%;" name="members" class="span2" id="ex1" data-slider-id='ex1Slider' type="text" data-slider-min="1" data-slider-max="50" data-slider-step="1" data-slider-value="[<?=isset($minMembers) ? $minMembers : 0;?>,<?=isset($maxMembers) ? $maxMembers : 50;?>]"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="minMembers">Minimum Clan Level:</label>
					<div class="col-sm-8">
						<input style="width: 100%;" name="minClanLevel" id="ex2" data-slider-id='ex2Slider' type="text" data-slider-min="1" data-slider-max="15" data-slider-step="1" data-slider-value="<?=isset($minClanLevel) ? $minClanLevel : 1;?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="minMembers">Minimum Clan Points:</label>
					<div class="col-sm-8">
						<input style="width: 100%;" name="minClanPoints" id="ex3" data-slider-id='ex3Slider' type="text" data-slider-min="1" data-slider-max="60000" data-slider-step="1" data-slider-value="<?=isset($minClanPoints) ? $minClanPoints : 1;?>"/>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 btn-actions">
					<button type="submit" class="btn btn-success">Search</button><br><br>
				</div>
			</div>
		</form>
		<?if(count($apiClans)==0 && (count($clans)>0 || count($players)>0)){?>
			<? error_log(count($clans)); ?>
			<? error_log(count($players)); ?>
			<div class="alert alert-info" role="alert">
				<strong>Oh no!</strong> We couldn't find anything in Clash of Clans.
			</div>
		<?}
	}if(count($apiClans)>0){?>
		<?if(count($apiClans)==1){?>
			<h4>Found <?=count($apiClans);?> clan matching in Clash of Clans.</h4>
		<?}elseif(count($apiClans)==50){?>
			<h4>Found 50+ clans matching in Clash of Clans. Showing top 50.</h4>
		<?}else{?>
			<h4>Found <?=count($apiClans);?> clans matching in Clash of Clans.</h4>
		<?}?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th></th>
						<th>Clan name</th>
						<th>Clan Points</th>
						<th>Clan Level</th>
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
							<td><?=$apiClan->clanPoints;?></td>
							<td><?=$apiClan->clanLevel;?></td>
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
			<h4>Found 50+ clans matching. Showing top 50.</h4>
		<?}else{?>
			<h4>Found <?=count($clans);?> clans matching.</h4>
		<?}?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th></th>
						<th>Clan name</th>
						<th>Clan Points</th>
						<th>Clan Level</th>
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
							<td><?=$clan->get('clanPoints');?></td>
							<td><?=$clan->get('clanLevel');?></td>
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
				Search Clash of Clans instead:&nbsp;
				<a type="button" class="btn btn-xs btn-success" href="/searchResults.php?query=<?=$query;?>&api=api">Search Clash of Clans</a>
			<?}else{?>
				or in Clash of Clans.
			<?}?>
		</div>
	<?}elseif(!isset($api)){?>
		<div class="alert alert-info" role="alert">
			Couldn't find what you're looking for? Search Clash of Clans instead:&nbsp;
			<a type="button" class="btn btn-xs btn-success" href="/searchResults.php?query=<?=$query;?>&api=api">Search Clash of Clans</a>
		</div>
	<?}?>
</div>
<script type="text/javascript">
function clickRow(href){
	window.document.location = href;
}
$('#ex1').slider({
	formatter: function(value) {
		return 'Between ' + value[0] + ' and ' + value[1];
	}
});
$('#ex2').slider({
	formatter: function(value) {
		return 'Level ' + value;
	}
});
$('#ex3').slider({
	formatter: function(value) {
		return value + ' Trophies';
	}
});
</script>
<?
require('footer.php');