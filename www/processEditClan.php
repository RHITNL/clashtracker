<?
require(__DIR__ . '/../config/functions.php');

$clanId = $_POST['clanId'];
try{
	$clan = new clan($clanId);
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clans.php');
	exit;
}

if($_POST['cancel']){
	header('Location: /clan.php?clanId=' . $clan->get('id'));
	exit;
}

$name = $_POST['name'];
$description = $_POST['description'];
$clanType = $_POST['clanType'];
$minimumTrophies = $_POST['minimumTrophies'];
$warFrequency = $_POST['warFrequency'];

if(strlen($name) == 0){
	$_SESSION['curError'] = 'Clan Name cannot be blank.';
	header('Location: /editClan.php?clanId=' . $clan->get('id'));
	exit;
}

$edited = false;

if($clan->get('name') != $name){
	$clan->set('name', $name);
	$edited = true;
}

if($clan->get('description') != $description){
	$clan->set('description', $description);
	$edited = true;
}

if($clan->get('clanType') != $clanType){
	$clan->set('clanType', $clanType);
	$edited = true;
}

if($clan->get('minimumTrophies') != $minimumTrophies){
	$clan->set('minimumTrophies', $minimumTrophies);
	$edited = true;
}

if($clan->get('warFrequency') != $warFrequency){
	$clan->set('warFrequency', $warFrequency);
	$edited = true;
}

if($edited){
	$_SESSION['curMessage'] = 'Clan updated successfully.';
}

header('Location: /clan.php?clanId=' . $clan->get('id'));
exit;