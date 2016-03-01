<?
require('init.php');

function unsetAll(){
	unset($_SESSION['email']);
}

if($_POST['cancel']){
	header('Location: /login.php');
	exit;
}

$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

$_SESSION['email'] = $email;

if($password != $confirmPassword){
	$_SESSION['curError'] = 'Passwords do not match.';
	header('Location: /signup.php');
	exit;
}

try{
	try{
		$user = new user($email);
		$_SESSION['curError'] = 'Account already exists with email: ' . $email;
		header('Location: /signup.php');
		exit;
	}catch(Exception $e){
		$user = new user();
		$user->create($email, $password);
		notice('New User on Clash Tracker!', 'Someone created a new account on Clash Tracker. Their email address is ' . $email . ". Welcome them to Clash Tracker!\n\nCheers,\n\tClash Tracker Team (a.k.a. Solo Espero)");
	}
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /signup.php');
	exit;
}

$_SESSION['user_id'] = $user->get('id');
unsetAll();
header('Location: /home.php');
exit;