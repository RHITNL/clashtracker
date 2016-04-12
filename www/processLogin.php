<?
require('init.php');
require('session.php');
if(isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

function unsetAll(){
	unset($_SESSION['email']);
}

$email = $_POST['email'];
$password = $_POST['password'];

$_SESSION['email'] = $email;

try{
	$user = new user($email);
}catch(Exception $e){
	$_SESSION['curError'] = 'User account not found with specified email.';
	header('Location: /login.php');
	exit;
}

if($user->login($password)){
	$_SESSION['user_id'] = $user->get('id');
}else{
	$_SESSION['curError'] = 'Incorrect password.';
	header('Location: /login.php');
	exit;
}
unsetAll();
$linkedPlayer = $user->get("player");
$linkedClan = $user->get("clan");
if(isset($linkedClan)){
	header('Location: /clan.php?clanId=' . $linkedClan->get('id'));
}elseif(isset($linkedPlayer)){
	$clan = $linkedPlayer->getMyClan();
	if(isset($clan)){
		header('Location: /clan.php?clanId=' . $clan->get('id'));
	}else{
		header('Location: /player.php?playerId=' . $linkedPlayer->get('id'));
	}
}else{
	header('Location: /clans.php');
}
exit;