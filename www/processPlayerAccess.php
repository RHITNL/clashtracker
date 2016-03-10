<?
require('init.php');
require('session.php');

if(!isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

if(!isset($loggedInUserPlayer)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /accountSettings.php?tab=player');
	exit;
}

$accessType = $_POST['accessType'];
switch ($accessType){
	case 'AN':
		$loggedInUserPlayer->resetAccess();
		break;
	case 'CL':
		$loggedInUserPlayer->resetAccess();
		$loggedInUserPlayer->set('accessType', 'CL');
		$loggedInUserPlayer->set('minRankAccess', $_POST['minRank']);
		break;
	case 'US':
		$loggedInUserPlayer->set('accessType', 'US');
		$additionalEmails = $_POST['additionalEmails'];
		if(count($additionalEmails)>0){
			foreach ($additionalEmails as $email) {
				if(strlen($email)>0){
					if(filter_var($email, FILTER_VALIDATE_EMAIL)){
						try{
							$user = new user($email);
							$loggedInUserPlayer->grantUserAccess($user);
						}catch(Exception $e){
							$errors = true;
							$_SESSION['curError'] .= $email . " is not associated to any users.<br>";
						}
					}else{
						$errors = true;
						$_SESSION['curError'] .= $email . " is not a valid email address.<br>";
					}
				}
			}
		}
		break;
	default:
		$_SESSION['curError'] = NO_ACCESS;
		header('Location: /accountSettings.php?tab=player');
		exit;
}

if($errors){
	$_SESSION['curMessage'] = 'Successfully updated player settings, however there were some issues.';
}else{
	$_SESSION['curMessage'] = 'Successfully updated player settings.';
}
header('Location: /accountSettings.php?tab=player');
exit;