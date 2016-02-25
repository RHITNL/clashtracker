<?
require('init.php');
require('session.php');

if(!isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

try{
	$loggedInUser->unlinkFromClan();
	$loggedInUserClan->resetAccess();
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /accountSettings.php');
	exit;
}

$_SESSION['curMessage'] = 'Clan successfully unlinked from your account.';
header('Location: /accountSettings.php?tab=clan');
exit;