<?
require('init.php');
require('session.php');

$oldPassword = $_POST['oldPassword'];
$newPassword = $_POST['newPassword'];
$confirmPassword = $_POST['confirmPassword'];

if(!$loggedInUser->login($oldPassword)){
	$_SESSION['curError'] = 'Incorrect current password.';
	header('Location: /accountSettings.php?tab=password');
	exit;
}

if($newPassword != $confirmPassword){
	$_SESSION['curError'] = 'Passwords do not match.';
	header('Location: /accountSettings.php?tab=password');
	exit;
}

try{
	$loggedInUser->changePassword($newPassword);
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /accountSettings.php?tab=password');
	exit;
}

$_SESSION['curMessage'] = 'Password changed successfully.';
header('Location: /accountSettings.php?tab=password');
exit;