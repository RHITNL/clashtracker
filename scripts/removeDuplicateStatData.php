<?
require(__DIR__ . '/../config/functions.php');

function query($query){
	global $db;
	if($db->multi_query($query) === true){
		$results = $db->store_result();
		while ($db->more_results()){
			$db->next_result();
		}
		$queryResults = array();
		if($results->num_rows){
			while($resultObj = $results->fetch_object()){
				$queryResults[] = $resultObj;
			}
		}
		return $queryResults;
	}else{
		error_log(cpr($db->error));
		return false;
	}
}

$query = "SELECT * FROM player_stats ORDER BY date_recorded";
$results = query($query);
$prevValues = array();
$count = 0;
foreach ($results as $result) {
	if($result->stat_type == 'GO' || $result->stat_type == 'EL' || $result->stat_type == 'DE'){
		continue;
	}
	if(!isset($prevValues[$result->player_id])){
		$prevValues[$result->player_id] = array();
	}
	if(!isset($prevValues[$result->player_id][$result->stat_type])){
		$prevValues[$result->player_id][$result->stat_type] = $result->stat_amount;
	}else{
		if($prevValues[$result->player_id][$result->stat_type] == $result->stat_amount){
			$query = "DELETE FROM player_stats WHERE player_id = '$result->player_id' AND date_recorded = '$result->date_recorded' AND stat_type = '$result->stat_type' AND stat_amount = '$result->stat_amount';";
			if(query($query) !== FALSE){
				$count++;
			}
		}else{
			$prevValues[$result->player_id][$result->stat_type] = $result->stat_amount;
		}
	}
}
error_log("$count duplicate player_stats rows were removed.");

$query = "SELECT * FROM clan_stats ORDER BY date_recorded";
$results = query($query);
$prevValues = array();
$count = 0;
foreach ($results as $result) {
	if($result->stat_type == 'GO' || $result->stat_type == 'EL' || $result->stat_type == 'DE'){
		continue;
	}
	if(!isset($prevValues[$result->clan_id])){
		$prevValues[$result->clan_id] = array();
	}
	if(!isset($prevValues[$result->clan_id][$result->stat_type])){
		$prevValues[$result->clan_id][$result->stat_type] = $result->stat_amount;
	}else{
		if($prevValues[$result->clan_id][$result->stat_type] == $result->stat_amount){
			$query = "DELETE FROM clan_stats WHERE clan_id = '$result->clan_id' AND date_recorded = '$result->date_recorded' AND stat_type = '$result->stat_type' AND stat_amount = '$result->stat_amount';";
			if(query($query) !== FALSE){
				$count++;
			}
		}else{
			$prevValues[$result->clan_id][$result->stat_type] = $result->stat_amount;
		}
	}
}
error_log("$count duplicate clan_stats rows were removed.");