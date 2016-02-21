<?
require('init.php');
require('session.php');

$name = $_POST['name'];
$playerId = $_POST['playerId'];
$clanId = $_POST['clanId'];

try{
	$player = new player($playerId);
	$playerId = $player->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No player with id ' . $playerId . ' found.';
	header('Location: /players.php');
	exit;
}

try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$clan = null;
}

if($_POST['cancel']){
	if(isset($clan)){
		header('Location: /player.php?playerId=' . $playerId . '&clanId=' . $clan->get('id'));
	}else{
		header('Location: /player.php?playerId=' . $playerId);
	}
	exit;
}

if(strlen($name)==0){
	$_SESSION['curError'] = 'Player Name cannot be blank.';
	if(isset($clan)){
		header('Location: /player.php?playerId=' . $playerId . '&clanId=' . $clan->get('id'));
	}else{
		header('Location: /player.php?playerId=' . $playerId);
	}
	exit;
}

try{
	$player->set('name', $name);
	$_SESSION['curMessage'] = 'Player name successfully changed.';
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
}
if(isset($clan)){
	header('Location: /player.php?playerId=' . $playerId . '&clanId=' . $clan->get('id'));
}else{
	header('Location: /player.php?playerId=' . $playerId);
}
exit;