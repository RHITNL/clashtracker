<?
require('init.php');
require('session.php');

$clanId = $_POST['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
	if($_POST['type'] == 'multiple' && !userHasAccessToUpdateClan($clan)){
		$_SESSION['curError'] = NO_ACCESS;
		header('Location: /clan.php?clanId=' . $clanId);
		exit;
	}
}catch(Exception $e){
	if($_POST['type'] == 'multiple'){
		$_SESSION['curError'] = 'No clan with id ' . $playerId . ' found.';
		header('Location: /clans.php');
		exit;
	}
	$clan = null;
}

if($_POST['cancel']){
	if($_POST['type'] == 'multiple'){
		header('Location: /clan.php?clanId=' . $clanId);
	}else{
		header('Location: /player.php?playerId=' . $_POST['playerId']);
	}
	exit;
}

if($_POST['type'] == 'single'){
	$playerId = $_POST['playerId'];
	try{
		$player = new player($playerId);
		$playerId = $player->get('id');
		if(!userHasAccessToUpdatePlayer($player)){
			$_SESSION['curError'] = NO_ACCESS;
			header('Location: /player.php?playerId=' . $playerId);
			exit;
		}
	}catch(Exception $e){
		$_SESSION['curError'] = 'No player with id ' . $playerId . ' found.';
		if(isset($clanId)){
			header('Location: /clan.php?clanId=' . $clanId);
		}else{
			header('Location: /players.php');
		}
		exit;
	}
	$loot = array();
	$loot[$playerId] = array();
	$loot[$playerId]['gold'] = $_POST['gold'];
	$loot[$playerId]['elixir'] = $_POST['elixir'];
	$loot[$playerId]['darkElixir'] = $_POST['darkElixir'];
}elseif($_POST['type'] == 'multiple'){
	$loot = array();
	$members = $clan->getMembers();
	foreach ($members as $member) {
		if(userHasAccessToUpdatePlayer($member)){
			$memberId = $member->get('id');
			$loot[$memberId] = array();
			$loot[$memberId]['gold'] = $_POST['gold' . $memberId];
			$loot[$memberId]['elixir'] = $_POST['elixir' . $memberId];
			$loot[$memberId]['darkElixir'] = $_POST['darkElixir' . $memberId];
		}
	}
}

$errors = false;
$updates = false;
foreach ($loot as $playerId => $playerLoot) {
	try{
		$player = new player($playerId);
		$playerId = $player->get('id');
	}catch(Exception $e){
		$_SESSION['curError'] .= 'No player with id ' . $playerId . ' found.<br>';
		continue;
	}
	if(strlen($playerLoot['gold']) != 0){
		try{
			$player->recordGold($playerLoot['gold']);
			$updates = true;
		}catch(LootAmountException $e){
			$errors = true;
			$_SESSION['curError'] .= htmlspecialchars($player->get('name')) . ' has already stolen ' . $e->getMinimumLoot() . ' gold. New value must be greater than this.<br>';
		}
	}
	if(strlen($playerLoot['elixir']) != 0){
		try{
			$player->recordElixir($playerLoot['elixir']);
			$updates = true;
		}catch(LootAmountException $e){
			$errors = true;
			$_SESSION['curError'] .= htmlspecialchars($player->get('name')) . ' has already stolen ' . $e->getMinimumLoot() . ' elixir. New value must be greater than this.<br>';
		}
	}
	if(strlen($playerLoot['darkElixir']) != 0){
		try{
			$player->recordDarkElixir($playerLoot['darkElixir']);
			$updates = true;
		}catch(LootAmountException $e){
			$errors = true;
			$_SESSION['curError'] .= htmlspecialchars($player->get('name')) . ' has already stolen ' . $e->getMinimumLoot() . ' dark elixir. New value must be greater than this.<br>';
		}
	}
}

if($_POST['type'] == 'single'){
	if(!$errors && $updates){
		$_SESSION['curMessage'] = 'Successfully updated loot records.';
	}
	if(isset($clanId)){
		header('Location: /player.php?playerId=' . $playerId . '&clanId=' . $clanId);
	}else{
		header('Location: /player.php?playerId=' . $playerId);
	}
}elseif($_POST['type'] == 'multiple'){
	if($errors){
		header('Location: /recordClanLoot.php?clanId=' . $clanId);
	}else{
		if($updates){
			$_SESSION['curMessage'] = 'Successfully updated loot records.';
		}
		header('Location: /clan.php?clanId=' . $clanId);
	}
}
exit;