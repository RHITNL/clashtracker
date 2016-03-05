<?
require('init.php');
require('session.php');

if(!isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

if(!isset($loggedInUserClan)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /accountSettings.php?tab=clan');
	exit;
}

$userId = $_GET['userId'];

try{
	$loggedInUserClan->revokeUserAccess($userId);
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /accountSettings.php?tab=clan');
	exit;
}

$_SESSION['curMessage'] = 'Access successfully revoked for user.';
header('Location: /accountSettings.php?tab=clan');
exit;