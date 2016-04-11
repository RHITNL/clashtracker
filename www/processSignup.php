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
	}
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /signup.php');
	exit;
}

$_SESSION['user_id'] = $user->get('id');
if(!DEVELOPMENT){
	email('alexinmann@gmail.com', 'New Clash Tracker User!', 'There is a new user using Clash Tracker! Their email is ' . $email . ". Welcome them to the site!\n\nClash on!", 'activity@clashtracker.ca');
}
unsetAll();
header('Location: /home.php');
exit;