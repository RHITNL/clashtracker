<?
require(__DIR__ . '/../config/functions.php');
if($_POST['cancel']){
	unsetAll();
	header('Location: /clans.php');
	exit;
}

function unsetAll(){
	unset($_SESSION['name']);
	unset($_SESSION['clanTag']);
	unset($_SESSION['description']);
	unset($_SESSION['clanType']);
	unset($_SESSION['minimumTrophies']);
	unset($_SESSION['warFrequency']);
}

$name = $_POST['name'];
$clanTag = $_POST['clanTag'];
$description = $_POST['description'];
$clanType = $_POST['clanType'];
$minimumTrophies = $_POST['minimumTrophies'];
$warFrequency = $_POST['warFrequency'];

$_SESSION['name'] = $name;
$_SESSION['clanTag'] = $clanTag;
$_SESSION['description'] = $description;
$_SESSION['clanType'] = $clanType;
$_SESSION['minimumTrophies'] = $minimumTrophies;
$_SESSION['warFrequency'] = $warFrequency;

if(strlen($clanTag) == 0){
	$_SESSION['curError'] = 'Clan Tag cannot be blank.';
	header('Location: /addClan.php');
	exit;
}

try{
	$clan = new clan($clanTag);
	$_SESSION['curMessage'] = 'Clan already created with clan tag: ' . correctTag($clanTag);
}catch(Exception $e){
	if(strlen($name) == 0){
		$_SESSION['curError'] = 'Clan Name cannot be blank.';
		header('Location: /addClan.php');
		exit;
	}
	$clan = new clan();
	$clan->create($name, $clanTag, $description, $clanType, $minimumTrophies, $warFrequency);
	$_SESSION['curMessage'] = 'Clan created successfully.';
}

unsetAll();
header('Location: /clan.php?clanId=' . $clan->get('id'));
exit;