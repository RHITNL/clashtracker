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
	$clan = new clan($clanId);
}
$clanIdText = (isset($clan)) ? '&clanId=' . $clan->get('id') : '';

$userId = $_GET['userId'];
try{
	$user = new user($userId);
	$userId = $user->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No user with id ' . $warId . ' found.';
	header('Location: /war.php?warId=' . $war->get('id') . $clanIdText);
	exit;
}

if(!userHasAccessToUpdateClan($war->get('clan1'))){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /war.php?warId=' . $war->get('id') . $clanIdText);
	exit;
}

$war->revokeUserAccess($user->get('id'));
$_SESSION['curMessage'] = 'Successfully revoked access for ' . $user->get('email') . '.';
header('Location: /war.php?warId=' . $war->get('id') . $clanIdText);
exit;