<?
require('init.php');
require('session.php');

if(!isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

if($_POST['cancel']){
	header('Location: /accountSettings.php?tab=clan');
	exit;
}

if($_POST['name']){
	$name = $_POST['name'];	
}

$clanTag = $_POST['clanTag'];
if(strlen($clanTag)==0){
	$_SESSION['curError'] = 'Clan tag cannot be blank.';
	header('Location: /accountSettings.php?tab=clan');
	exit;
}

if(isset($name)){
	try{
		$clan = new clan($clanTag);
	}catch(Exception $e){
		$clan = new clan();
		$clan->create($name, $clanTag);
	}
}

try{
	$loggedInUser->linkWithClan($clanTag);
}catch(noResultFoundException $e){
	header('Location: /linkClan.php?clanTag=' . $clanTag);
	exit;
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /accountSettings.php?tab=clan');
	exit;
}

$_SESSION['curMessage'] = 'Clan successfully linked to your account.';
header('Location: /accountSettings.php?tab=clan');
exit;