<?
require('init.php');
function stringContainsSubstring($string, $substring){
	return strpos($string, $substring) !== false;
}

unset($_SESSION['user_id']);
$referer = $_SERVER['HTTP_REFERER'];
if(stringContainsSubstring($referer, 'accountSettings') ||
	stringContainsSubstring($referer, 'dev') ||
	stringContainsSubstring($referer, 'addWarPlayer') ||
	stringContainsSubstring($referer, 'clanRequests') ||
	stringContainsSubstring($referer, 'editWarAttacks') ||
	stringContainsSubstring($referer, 'recordClanLoot') ||
	stringContainsSubstring($referer, 'requestClanAccess') ||
	stringContainsSubstring($referer, 'addWarAttack')){
	$location = '/home.php';
}else{
	$host = $_SERVER['HTTP_HOST'];
	$location = str_replace('http://'.$host, '', $referer);
}
header('Location: ' . $location);
exit;