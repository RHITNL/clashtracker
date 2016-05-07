<?
require('init.php');
require('session.php');

if(isset($loggedInUserClan)){
	header('Location: /clan.php?clanId=' . $loggedInUserClan->get('id'));
}elseif(isset($loggedInUserPlayer)){
	$clan = $loggedInUserPlayer->getClan();
	if(isset($clan)){
		header('Location: /clan.php?clanId=' . $clan->get('id'));
	}else{
		header('Location: /player.php?playerId=' . $loggedInUserPlayer->get('id'));
	}
}else{
	header('Location: /home.php');
}
exit;