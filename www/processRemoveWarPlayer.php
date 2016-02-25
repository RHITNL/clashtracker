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
if(!$war->isClanInWar($clanId)){
	$clanId = null;
}
$clanIdText = isset($clanId) ? '&clanId=' . $clanId : '';

if(!userHasAccessToUpdateClan($war->get('clan1'))){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /war.php?warId=' . $war->get('id') . $clanIdText);
	exit;
}

if(!$war->isEditable()){
	$_SESSION['curError'] = 'This war is no longer editable.';
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
		exit;
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
		exit;
	}
}

$playerId = $_GET['playerId'];
if($war->isPlayerInWar($playerId)){
	$war->removePlayer($playerId);
	$_SESSION['curMessage'] = 'Player successfully removed from war.';
}else{
	$_SESSION['curError'] = 'Player not in war.';
}

if(isset($clanId)){
	header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
}else{
	header('Location: /war.php?warId=' . $war->get('id'));
}
exit;