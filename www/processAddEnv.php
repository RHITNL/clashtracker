<?
require('init.php');
require('session.php');

if(!isset($loggedInUser) || !$loggedInUser->isAdmin()){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

$env = $_POST['env'];
$limit = $_POST['limit'];
$ip = $_POST['ip'];

if(strlen($env)>0){
	try{
		API::addENV($env, $limit, $ip);
	}catch(Exception $e){
		$_SESSION['curError'] = $e->getMessage();
	}
}

$_SESSION['curMessage'] = 'ENV Variable successfully added.';
header('Location: /dev.php');
exit;