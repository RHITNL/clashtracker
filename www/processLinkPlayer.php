<?
require('init.php');
require('session.php');

if(!isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

if($_POST['cancel']){
	header('Location: /accountSettings.php?tab=player');
	exit;
}

if($_POST['name']){
	$name = $_POST['name'];	
}

$playerTag = $_POST['playerTag'];
if(strlen($playerTag)==0){
	$_SESSION['curError'] = 'Player tag cannot be blank.';
	header('Location: /accountSettings.php?tab=player');
	exit;
}

if(isset($name)){
	try{
		$player = new player($playerTag);
	}catch(Exception $e){
		$player = new player();
		$player->create($name, $playerTag);
	}
}

try{
	$loggedInUser->linkWithPlayer($playerTag);
}catch(noResultFoundException $e){
	header('Location: /linkPlayer.php?playerTag=' . $playerTag);
	exit;
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /accountSettings.php?tab=player');
	exit;
}

$_SESSION['curMessage'] = 'Player successfully linked to your account.';
header('Location: /accountSettings.php?tab=player');
exit;