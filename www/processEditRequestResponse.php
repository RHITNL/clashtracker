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
	$user = new User($userId);
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

$requests = $war->getRequests();
foreach ($requests as $request) {
	if($user->get('id') == $request->user->get('id')){
		$requestExists = true;
	}
}

if(!$requestExists){
	$_SESSION['curError'] = 'User has not requested to access this war.';
	header('Location: /war.php?warId=' . $war->get('id') . $clanIdText);
	exit;
}

$war->deleteRequest($user);

$response = $_GET['response'];
if($response == 'accept'){
	$war->grantUserAccess($user);
	$_SESSION['curMessage'] = 'Successfully granted access to ' . $user->get('email') . '.';
}elseif($response == 'decline'){
	$_SESSION['curMessage'] = 'Successfully declined access to ' . $user->get('email') . '.';
}
header('Location: /war.php?warId=' . $war->get('id') . $clanIdText);
exit;