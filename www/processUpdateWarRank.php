<?
require('init.php');
require('session.php');

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
	$clan2 = new clan($war->getEnemy($clanId));
}else{
	$clanId = null;
	$clan1 = new clan($war->get('firstClanId'));
	$clan2 = new clan($war->get('secondClanId'));
}

$playerId = $_GET['playerId'];
if($war->isPlayerInWar($playerId)){
	$player = new player($playerId);
	$playerId = $player->get('id');
}else{
	$_SESSION['curError'] = 'Player not in war.';
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
	}
	exit;
}

$clan = $war->getPlayerWarClan($player->get('id'));
$rank = $war->getPlayerRank($player->get('id'));

$action = $_GET['action'];
if($action=='up'){
	$newRank = $rank-1;
}elseif($action=='down'){
	$newRank = $rank+1;
}else{
	$_SESSION['curError'] = 'Invalid action.';
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
	}
	exit;
}
$otherPlayer = $war->getPlayerByRank($newRank, $clan->get('id'));
$war->updatePlayerRank($player->get('id'), $newRank);
$war->updatePlayerRank($otherPlayer->get('id'), $rank);

$rank = $player->get('warRank', $clan->get('id'));
$newRank = $otherPlayer->get('warRank', $clan->get('id'));

$clan->updatePlayerWarRank($player->get('id'), $newRank);
$clan->updatePlayerWarRank($otherPlayer->get('id'), $rank);

$_SESSION['curMessage'] = 'War rank successfully changed.';
if(isset($clanId)){
	header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
}else{
	header('Location: /war.php?warId=' . $war->get('id'));
}
exit;