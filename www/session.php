<?
if(isset($_SESSION['user_id'])){
	$userId = $_SESSION['user_id'];
	try{
		$loggedInUser = new user($userId);
		$loggedInUserPlayer = $loggedInUser->get("player");
		$loggedInUserClan = $loggedInUser->get("clan");
	}catch(Exception $e){
		$loggedInUser = null;
		$_SESSION['curError'] = 'No user with id ' . $userId . ' found.';
		header('Location: /login.php');
		exit;
	}
}