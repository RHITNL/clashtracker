<?
require(__DIR__ . '/../config/functions.php');

function unsetAll(){
	unset($_SESSION['enemyClanName']);
	unset($_SESSION['enemyClanTag']);
	unset($_SESSION['size']);
	unset($_SESSION['clanId']);
}

$enemyClanName = $_POST['enemyClanName'];
$enemyClanTag = $_POST['enemyClanTag'];
$size = $_POST['size'];
$clanId = $_POST['clanId'];

$_SESSION['enemyClanName'] = $enemyClanName;
$_SESSION['enemyClanTag'] = $enemyClanTag;
$_SESSION['size'] = $size;
$_SESSION['clanId'] = $clanId;

try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	unsetAll();
	header('Location: /clans.php');
	exit;
}

if($_POST['cancel']){
	unsetAll();
	header('Location: /clan.php?clanId=' . $clan->get('id'));
	exit;
}

if(strlen($enemyClanTag) == 0){
	$_SESSION['curError'] = 'Enemy Clan Tag cannot be blank.';
	header('Location: /addWar.php?clanId=' . $clan->get('id'));
	exit;
}

try{
	$enemyClan = new clan($enemyClanTag);
}catch(Exception $e){
	$enemyClan = new clan();
	if(strlen($enemyClanName) == 0){
		$_SESSION['curError'] = 'Enemy Clan Name cannot be blank.';
		header('Location: /addWar.php?clanId=' . $clan->get('id'));
		exit;
	}
	$enemyClan->create($enemyClanName, $enemyClanTag);
}

$war = new war();
$war->create($clan->get('id'), $enemyClan->get('id'), $size);
$_SESSION['curMessage'] = 'Clan War created successully.';
unsetAll();
header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clan->get('id'));
exit;