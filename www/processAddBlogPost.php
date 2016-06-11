<?
require('init.php');
require('session.php');

if(!isset($loggedInUser) || $loggedInUser->get('email') != 'alexinmann@gmail.com'){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

$name = $_POST['name'];
$content = $_POST['content'];

if(strlen($name)>100){
	$_SESSION['curError'] = 'Name too long (<50 characters)';
	header('Location: /dev.php');
	exit;	
}

if(strlen($name)>50000){
	$_SESSION['curError'] = 'Content too long (<50000 characters)';
	header('Location: /dev.php');
	exit;	
}

try{
	$post = new BlogPost();
	$post->create($name, $content);
	$_SESSION['curMessage'] = 'Post successfully created.';
}catch(Exception $e){
	$_SESSION['curError'] = $e;
}
header('Location: /dev.php');
exit;