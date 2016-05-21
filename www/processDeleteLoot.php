<?
require('init.php');
require('session.php');

$playerId = $_POST['playerId'];
try{
    $player = new player($playerId);
    $playerId = $player->get('id');
}catch(Exception $e){
    $_SESSION['curError'] = 'No player with id ' . $playerId . ' found.';
    header('Location: /players.php');
    exit;
}

$clanId = $_POST['clanId'];
try{
    $clan = new clan($clanId);
    $clanId = $clan->get('id');
    $clanUrl = '&clanId=' . $clanId;
}catch(Exception $e){
    $clan = null;
    $clanUrl = '';
}

if(!userHasAccessToUpdatePlayer($player, false)){
    $playerClan = $player->getClan();
    if(!isset($playerClan) || !userHasAccessToUpdateClan($playerClan, false)){
        $_SESSION['curError'] = NO_ACCESS;
        header('Location: /player.php?playerId=' . $playerId . $clanUrl);
        exit;
    }
}

$types = ['GO', 'EL', 'DE'];
try{
    $messages = array();
    foreach ($types as $type){
        if(isset($_POST[$type])){
            $player->deleteLootRecord($type);
            $messages[] = 'Successfully deleted the previous ' . lootTypeFromCode($type) . ' record for ' . $player->get('name') . '.';
        }
    }
    $_SESSION['curMessage'] = implode('<br>', $messages);
}catch(Exception $e){
    $_SESSION['curError'] = $e->getMessage();
}
header('Location: /player.php?playerId=' . $playerId . $clanUrl);
exit;