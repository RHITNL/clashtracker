<?
require('init.php');
require('session.php');

$clanId = $_POST['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clans.php');
	exit;
}

if(!userHasAccessToUpdateClan($clan)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /clan.php?clanId=' . $clanId);
	exit;	
}

$playerTags = $_POST['playerTags'];
$name = $_POST['name'];
$role = $_POST['role'];
$expLevel = $_POST['expLevel'];
$trophies = $_POST['trophies'];
$donations = $_POST['donations'];
$donationsReceived = $_POST['donationsReceived'];
$leagueUrl = $_POST['leagueUrl'];

$count=0;
foreach ($playerTags as $key => $playerTag) {
	if(strlen($playerTag)>0){
		try{
			$player = new player($playerTag);
			if($player->get('name') != $name[$key]){
				$player->set('name', $name[$key]);
			}
		}catch(Exception $e){
			$player = new player();
			$player->create($name[$key], $playerTag);
		}
		$apiMember = new StdClass();
		$apiMember->name = $name[$key];
		$apiMember->role = $role[$key];
		$apiMember->expLevel = $expLevel[$key];
		$apiMember->trophies = $trophies[$key];
		$apiMember->donations = $donations[$key];
		$apiMember->donationsReceived = $donationsReceived[$key];
		$apiMember->league = new StdClass();
		$apiMember->league->iconUrls = new StdClass();
		$apiMember->league->iconUrls->small = $leagueUrl[$key];
		$player->updateFromApi($apiMember);
		$playerClan = $player->getClan();
		if(!isset($playerClan) || $playerClan->get('id') != $clan->get('id')){
			$count++;
			$clan->addPlayer($player->get('id'));
		}
	}
}
if($count>1){
	$_SESSION['curMessage'] = 'Players successfully added to the clan.';
}elseif($count>0){
	$_SESSION['curMessage'] = 'Player successfully added to the clan.';
}
header('Location: /clan.php?clanId=' . $clanId);
exit;