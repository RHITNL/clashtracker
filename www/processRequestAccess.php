<?
require('init.php');
require('session.php');

$warId = $_POST['warId'];
try{
	$war = new war($warId);
	$warId = $war->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No war with id ' . $warId . ' found.';
	header('Location: /wars.php');
	exit;
}

$clanId = $_POST['clanId'];
if($war->isClanInWar($clanId)){
	$clan = new clan($clanId);
}
$clanIdText = (isset($clan)) ? '&clanId=' . $clan->get('id') : '';

$message = $_POST['message'];

if(userHasAccessToUpdateClan($war->get('clan2'))
		&& $war->isEditable()
		&& !userHasAccessToUpdateWar($war)
		&& isset($loggedInUser)
		&& !$war->userHasRequested($loggedInUser->get('id'))){
	$war->requestAccess($loggedInUser, $message);
	$_SESSION['curMessage'] = 'Successfully requested access to edit war. Please wait for their response.';
}else{
	$_SESSION['curError'] = NO_ACCESS;
}
header('Location: /war.php?warId=' . $war->get('id') . $clanIdText);
exit;