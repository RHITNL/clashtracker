<?
require('init.php');
require('session.php');

$lootReportId = $_GET['lootReportId'];
try{
	$lootReport = new lootReport($lootReportId);
	$clan = $lootReport->get('clan');
}catch(Exception $e){
	$_SESSION['curError'] = 'No Loot Report with id ' . $lootReportId . ' found.';
	header('Location: /clans.php');
	exit;
}

if(!userHasAccessToUpdateClan($clan)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /clan.php?clanId=' . $clanId);
	exit;
}

try{
	$lootReport->delete();
	$_SESSION['curMessage'] = 'Loot Report successfully deleted.';
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
}
header('Location: /clan.php?clanId=' . $clan->get('id'));
exit;