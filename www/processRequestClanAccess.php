<?
require('init.php');
require('session.php');

$clanId = $_POST['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $warId . ' found.';
	header('Location: /wars.php');
	exit;
}

$message = $_POST['message'];

if($clan->canRequestAccess()){
	$clan->requestAccess($loggedInUser, $message);
	$_SESSION['curMessage'] = 'Successfully requested access to edit clan. Please wait for their response.';
}else{
	$_SESSION['curError'] = NO_ACCESS;
}
header('Location: /clan.php?clanId=' . $clanId);
exit;