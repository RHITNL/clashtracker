<?
require('init.php');

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
if(isset($linkedPlayer)){
	header('Location: /player.php?playerId=' . $linkedPlayer->get('id'));
}else{
	header('Location: /home.php');
}
exit;