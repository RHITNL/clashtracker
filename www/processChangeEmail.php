<?
require('init.php');
require('session.php');

$newEmail = $_POST['newEmail'];
$password = $_POST['password'];

if(!$loggedInUser->login($password)){
	$_SESSION['curError'] = 'Incorrect password.';
	header('Location: /accountSettings.php?tab=general');
	exit;
}

if(strlen($newEmail)==0){
	$_SESSION['curError'] = 'New email cannot be blank.';
	header('Location: /accountSettings.php?tab=general');
	exit;
}

try{
	$user = new user($newEmail);
	$_SESSION['curError'] = 'New email is already in use.';
	header('Location: /accountSettings.php?tab=general');
	exit;
}catch(noResultFoundException $e){
	//ignore, this should happen
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /accountSettings.php?tab=general');
	exit;
}

try{
	$loggedInUser->set('email', $newEmail);
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /accountSettings.php?tab=general');
	exit;
}

$_SESSION['curMessage'] = 'Email changed successfully.';
header('Location: /accountSettings.php?tab=general');
exit;