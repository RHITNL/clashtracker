<?
require(__DIR__ . '/../config/functions.php');

function unsetAll(){
	unset($_SESSION['name']);
	unset($_SESSION['playerTag']);
	unset($_SESSION['clanId']);
	unset($_SESSION['clanRank']);
}

$name = $_POST['name'];
$playerTag = $_POST['playerTag'];
$clanId = $_POST['clanId'];
$clanRank = $_POST['clanRank'];

try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
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
$_SESSION['clanRank'] = $clanRank;

if(strlen($playerTag) == 0){
	$_SESSION['curError'] .= 'Player Tag cannot be blank.';
	if(isset($clan)){
		header('Location: /addPlayer.php?clanId=' . $clan->get('id'));
	}else{
		header('Location: /addPlayer.php');
	}
	exit;
}

if(isset($clan) && $clan->hasLeader() && $clanRank == 'LE'){
	$_SESSION['curError'] .= 'Clan can only have one leader.';
	header('Location: /addPlayer.php?clanId=' . $clan->get('id'));
	exit;
}

try{
	$player = new player($playerTag);
	$alreadyCreated = true;
}catch(Exception $e){
	if(strlen($name) == 0){
		$_SESSION['curError'] .= 'Player Name cannot be blank.';
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
	$playerClan = $player->getMyClan();
	if(isset($playerClan) && $playerClan->get('id') == $clan->get('id')){
		$_SESSION['curMessage'] = 'Player already in ' . $clan->get('name') . '.';
	}else{
		$clan->addPlayer($player->get('id'), $clanRank);
		$_SESSION['curMessage'] = 'Member successfully added to the clan.';
	}
	header('Location: /clan.php?clanId=' . $clan->get('id'));
}else{
	if($alreadyCreated){
		$_SESSION['curMessage'] = 'Player already created with player tag: ' . correctTag($playerTag);
	}else{
		$_SESSION['curMessage'] = 'Player successfully created.';
	}
	header('Location: /player.php?playerId=' . $player->get('id'));
}

unsetAll();
exit;