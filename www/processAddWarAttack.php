<?
require(__DIR__ . '/../config/functions.php');

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
	$clan1 = new clan($clanId);
	$clanId = $clan1->get('id');
	$clan2 = new clan($war->getEnemy($clanId));
}else{
	$clanId = null;
	$clan1 = new clan($war->get('firstClanId'));
	$clan2 = new clan($war->get('secondClanId'));
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

if($_POST['cancel']){
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
	}
	exit;
}

$attackerId = $_POST['playerId'];
try{
	$attacker = new player($attackerId);
	$attackerId = $attacker->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No attacker with id ' . $playerId . ' found.';
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
	}
	exit;
}

if(!$war->isPlayerInWar($attacker->get('id'))){
	$_SESSION['curError'] = $attacker->get('name') . ' not in war.';
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
	}
	exit;
}

$defenderId = $_POST['defenderId'];
try{
	$defender = new player($defenderId);
	$defenderId = $defender->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No defender selected.';
	if(isset($clanId)){
		header('Location: /addWarAttack.php?warId=' . $war->get('id') . '&playerId=' . $attacker->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /addWarAttack.php?warId=' . $war->get('id') . '&playerId=' . $attacker->get('id'));
	}
	exit;
}

if(!$war->isPlayerInWar($defender->get('id'))){
	$_SESSION['curError'] = $defender->get('name') . ' not in war.';
	if(isset($clanId)){
		header('Location: /addWarAttack.php?warId=' . $war->get('id') . '&playerId=' . $attacker->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /addWarAttack.php?warId=' . $war->get('id') . '&playerId=' . $attacker->get('id'));
	}
	exit;
}

$attackerClan = $war->getPlayerWarClan($attacker->get('id'));
$defenderClan = $war->getPlayerWarClan($defender->get('id'));

if($attackerClan->get('id') == $defenderClan->get('id')){
	$_SESSION['curError'] = 'Attacker and defender cannot be from the same clan.';
	if(isset($clanId)){
		header('Location: /addWarAttack.php?warId=' . $war->get('id') . '&playerId=' . $attacker->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /addWarAttack.php?warId=' . $war->get('id') . '&playerId=' . $attacker->get('id'));
	}
	exit;
}

$attackerAttacks = $war->getPlayerAttacks($attacker->get('id'));
if(count($attackerAttacks) >= 2){
	$_SESSION['curError'] = $attacker->get('name') . ' has already used both attacks.';
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
	}
	exit;
}

if(isset($attackerAttacks[0]) && $attackerAttacks[0]['defenderId'] == $defender->get('id')){
	$_SESSION['curError'] = $attacker->get('name') . ' has already attacked ' . $defender->get('name') . '.';
	if(isset($clanId)){
		header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /war.php?warId=' . $war->get('id'));
	}
	exit;
}

$stars = $_POST['stars'];

if($stars > 3 || $stars < 0 || $stars == ''){
	$_SESSION['curError'] = 'Stars must be between 0 and 3.';
	if(isset($clanId)){
		header('Location: /addWarAttack.php?warId=' . $war->get('id') . '&playerId=' . $attacker->get('id') . '&clanId=' . $clanId);
	}else{
		header('Location: /addWarAttack.php?warId=' . $war->get('id') . '&playerId=' . $attacker->get('id'));
	}
	exit;
}

$war->addAttack($attacker->get('id'), $defender->get('id'), $stars);
$_SESSION['curMessage'] = 'New attack added successfully.';
if(isset($clanId)){
	header('Location: /war.php?warId=' . $war->get('id') . '&clanId=' . $clanId);
}else{
	header('Location: /war.php?warId=' . $war->get('id'));
}
exit;