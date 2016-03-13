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
$names = $_POST['names'];

$count=0;
foreach ($playerTags as $key => $playerTag) {
	if(strlen($playerTag)>0){
		try{
			$player = new player($playerTag);
			if($player->get('name') != $names[$key]){
				$player->set('name', $names[$key]);
			}
		}catch(Exception $e){
			$player = new player();
			$player->create($names[$key], $playerTag);
		}
		$playerClan = $player->getMyClan();
		if(isset($playerClan) && $playerClan->get('id') == $clan->get('id')){
			$_SESSION['curError'] .= htmlspecialchars($player->get('name')) . ' already in ' . htmlspecialchars($clan->get('name')) . '.<br>';
		}else{
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