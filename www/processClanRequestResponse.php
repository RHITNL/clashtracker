<?
require('init.php');
require('session.php');

$clanId = $_GET['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clans.php');
	exit;
}

$userId = $_GET['userId'];
try{
	$user = new User($userId);
	$userId = $user->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No user with id ' . $clanId . ' found.';
	header('Location: /clan.php?clanId=' . $clanId);
	exit;
}

if(!isset($loggedInUserClan) || $loggedInUserClan->get('id') != $clanId){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /clan.php?clanId=' . $clanId);
	exit;
}

$requests = $clan->getRequests();
foreach ($requests as $request) {
	if($user->get('id') == $request->user->get('id')){
		$requestExists = true;
	}
}

if(!$requestExists){
	$_SESSION['curError'] = 'User has not requested to access this clan.';
	header('Location: /clan.php?clanId=' . $clanId);
	exit;
}

$clan->deleteRequest($user);

$response = $_GET['response'];
if($response == 'accept'){
	$clan->grantUserAccess($user);
	$_SESSION['curMessage'] = 'Successfully granted access to ' . $user->get('email') . '.';
}elseif($response == 'decline'){
	$_SESSION['curMessage'] = 'Successfully declined access to ' . $user->get('email') . '.';
}
if(count($requests)>1){
	header('Location: /clanRequests.php?clanId=' . $clanId);
}else{
	header('Location: /clan.php?clanId=' . $clanId);
}
exit;