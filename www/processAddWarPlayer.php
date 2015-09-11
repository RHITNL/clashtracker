<?
require(__DIR__ . '/../config/functions.php');

function unsetAll(){
	unset($_SESSION['name']);
	unset($_SESSION['playerTag']);
}

$warId = $_POST['warId'];
try{
	$war = new war($warId);
	$warId = $war->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No war with id ' . $warId . ' found.';
	header('Location: /wars.php');
	exit;
}

$clanId = $_POST['clanId'];
if($war->isClanInWar($clanId)){
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
	$clanEnemy = new clan($war->getEnemy($clanId));
}else{
	$clanId = null;
}

if(!$war->isEditable()){
	$_SESSION['curError'] = 'This war is no longer editable.';
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
		exit;
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
		exit;
	}
}

$addClanId = $_POST['addClanId'];
if($war->isClanInWar($addClanId)){
	$addClan = new clan($addClanId);
	$clan1 = new clan($war->get('firstClanId'));
	$clan2 = new clan($war->get('secondClanId'));
}else{
	$_SESSION['curError'] = 'Clan not in selected war.';
	header('Location: /wars.php');
	exit;
}

if($_POST['cancel']){
	unsetAll();
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clan->get('id'));
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
	}
	exit;
}

$members = $_POST['members'];
if($members){
	foreach ($members as $memberId) {
		if(!$war->isPlayerInWar($memberId) && $addClan->isPlayerInClan($memberId)){
			$war->addPlayer($memberId);
		}
	}
	$_SESSION['curMessage'] = 'Existing members successfully added to war.<br>';
}

$playerName = $_POST['name'];
$playerTag = $_POST['playerTag'];

$_SESSION['name'] = $playerName;
$_SESSION['playerTag'] = $playerTag;

if(strlen($playerTag) != 0 || strlen($playerName) != 0){
	if(strlen($playerTag) == 0){
		$_SESSION['curError'] = 'Player Tag cannot be blank.';
		if(isset($clanId)){
			header('Location: /addWarPlayer.php?warId=' . $war->get('id') . '&addClanId=' . $addClan->get('id') . '&clanId=' . $clan->get('id'));
		}else{
			header('Location: /addWarPlayer.php?warId=' . $war->get('id') . '&addClanId=' . $addClan->get('id'));
		}
		exit;
	}

	try{
		$player = new player($playerTag);
	}catch(Exception $e){
		$player = new player();
		if(strlen($playerName) == 0){
			$_SESSION['curError'] = 'Player Name cannot be blank.';
			if(isset($clanId)){
				header('Location: /addWarPlayer.php?warId=' . $war->get('id') . '&addClanId=' . $addClan->get('id') . '&clanId=' . $clan->get('id'));
			}else{
				header('Location: /addWarPlayer.php?warId=' . $war->get('id') . '&addClanId=' . $addClan->get('id'));
			}
			exit;
		}
		$player->create($playerName, $playerTag);
	}

	$playerId = $player->get('id');
	if(!$war->isPlayerInWar($playerId)){
		if(!$addClan->isPlayerInClan($playerId)){
			$addClan->addPlayer($playerId);
		}
		$war->addPlayer($playerId);
		$_SESSION['curMessage'] .= 'New member successfully added to war.';
	}
}

unsetAll();
if(isset($clanId)){
	header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clan->get('id'));
}else{
	header('Location: /war.php?warId=' . $war->get('id'));
}
exit;