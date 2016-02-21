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
	$user = new user();
	$user->create($email, $password);
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /signup.php');
	exit;
}

$_SESSION['user_id'] = $user->get('id');
unsetAll();
header('Location: /home.php');
exit;