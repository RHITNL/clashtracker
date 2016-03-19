<?
require(__DIR__ . '/../config/functions.php');

try{
	$clan = new clan(2);
	$clanId = $clan->get('id');
}catch(Exception $e){
	error_log($e->getMessage());
	exit;
}

$members = $clan->getPastAndCurrentMembers();
$lootReports = array();
foreach ($members as $member) {
	$types = array('GO', 'EL', 'DE');
	foreach ($types as $type) {
		$stats = $member->getStat($type);
		foreach ($stats as $stat) {
			$date = $stat['dateRecorded'];
			$weekAgoStat = getWeekAgoStat($stats, $stat);
			if($weekAgoStat !== false){
				$lootReport = getCorrectLootReport($lootReports, $date);
				if($lootReport === false){
					$lootReport = new lootReport();
					$lootReport->createWithoutGeneration($clan, $date);
					$lootReports[] = $lootReport;
				}
				$amount = $stat['statAmount'] - $weekAgoStat['statAmount'];
				try{
					$lootReport->recordPlayerResult($member, $type, $amount);
				}catch(Exception $e){
					//already recorded this players result for this loot report
				}
			}
		}
	}
}

function getWeekAgoStat($stats, $stat){
	$statDate = strtotime($stat['dateRecorded']);
	$weekAgo = $statDate - WEEK;
	foreach ($stats as $tempStat) {
		$tempStatDate = strtotime($tempStat['dateRecorded']);
		$dif = abs($tempStatDate - $weekAgo);
		if($dif < DAY){
			return $tempStat;
		}
	}
	return false;
}

function getCorrectLootReport($lootReports, $date){
	foreach ($lootReports as $lootReport) {
		$dateCreated = $lootReport->get('dateCreated');
		if(dateWithinDay($dateCreated, $date)){
			return $lootReport;
		}
	}
	return false;
}

function dateWithinDay($date1, $date2){
	$date1 = strtotime($date1);
	$date2 = strtotime($date2);
	return abs($date1 - $date2) < DAY;
}