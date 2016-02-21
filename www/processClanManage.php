<?
require('init.php');
require('session.php');

$action = $_GET['action'];
$clanId = $_GET['clanId'];
$playerId = $_GET['playerId'];

try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clans.php');
	exit;
}

try{
	$player = new player($playerId);
	$playerId = $player->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No player with id ' . $playerId . ' found.';
	header('Location: /clan.php?clanId=' . $clan->get('id'));
	exit;
}

switch ($action) {
	case 'promote':
		$clan->promotePlayer($player->get('id'));
		break;
	case 'demote':
		$clan->demotePlayer($player->get('id'));
		break;
	case 'kick':
		$clan->kickPlayer($player->get('id'));
		break;
	case 'leave':
		$player->leaveClan();
		break;
	case 'rejoin':
		$clan->addPlayer($player->get('id'));
		break;
	default:
		break;
}

header('Location: /clan.php?clanId=' . $clan->get('id'));
exit;