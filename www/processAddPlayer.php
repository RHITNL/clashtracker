<?
require('init.php');
require('session.php');

function unsetAll(){
	unset($_SESSION['name']);
	unset($_SESSION['playerTag']);
	unset($_SESSION['clanId']);
}

$name = $_POST['name'];
$playerTag = $_POST['playerTag'];
$clanId = $_POST['clanId'];
if(!isset($loggedInUserPlayer) && isset($loggedInUser)){
	$link = $_POST['link'];
}

try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
	if(!userHasAccessToUpdateClan($clan)){
		$_SESSION['curError'] = NO_ACCESS;
		header('Location: /clan.php?clanId=' . $clanId);
		exit;
	}
}catch(Exception $e){
	$clan = null;
}

if($_POST['cancel']){
	unsetAll();
	if(isset($clan)){
		header('Location: /clan.php?clanId=' . $clan->get('id'));
	}else{
		header('Location: /players.php');
	}
	exit;
}

$_SESSION['name'] = $name;
$_SESSION['playerTag'] = $playerTag;
$_SESSION['clanId'] = $clanId;

if(strlen($playerTag) == 0){
	$_SESSION['curError'] = 'Player Tag cannot be blank.';
	if(isset($clan)){
		header('Location: /addPlayer.php?clanId=' . $clan->get('id'));
	}else{
		header('Location: /addPlayer.php');
	}
	exit;
}

try{
	$player = new player($playerTag);
	$alreadyCreated = true;
}catch(Exception $e){
	if(strlen($name) == 0){
		$_SESSION['curError'] = 'Player Name cannot be blank.';
		if(isset($clan)){
			header('Location: /addPlayer.php?clanId=' . $clan->get('id'));
		}else{
			header('Location: /addPlayer.php');
		}
		exit;
	}
	$player = new player();
	$player->create($name, $playerTag);
}

if(isset($clan)){
	$playerClan = $player->getClan();
	if(isset($playerClan) && $playerClan->get('id') == $clan->get('id')){
		$_SESSION['curError'] = 'Player already in ' . htmlspecialchars($clan->get('name')) . '.';
	}else{
		$clan->addPlayer($player);
		$_SESSION['curMessage'] = 'Member successfully added to the clan.';
	}
	header('Location: /clan.php?clanId=' . $clan->get('id'));
}else{
	if($alreadyCreated){
		$_SESSION['curError'] = 'Player already created with player tag: ' . correctTag($playerTag);
	}else{
		$_SESSION['curMessage'] = 'Player successfully created.';
	}
	header('Location: /player.php?playerId=' . $player->get('id'));
}

if($link){
	$linkedUser = $player->getLinkedUser();
	if(isset($linkedUser)){
		$_SESSION['curError'] = 'Cannot link to player who is already linked to another account.';
	}else{
		$loggedInUser->linkWithPlayer($player->get('id'));
	}
}
unsetAll();
exit;