<?
require('init.php');
require('session.php');

if(!isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

if(!isset($loggedInUserPlayer)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /accountSettings.php?tab=player');
	exit;
}

try{
	$loggedInUserPlayer->revokeAllAccess();
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /accountSettings.php?tab=player');
	exit;
}

$_SESSION['curMessage'] = 'Access successfully revoked for all users.';
header('Location: /accountSettings.php?tab=player');
exit;