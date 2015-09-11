<?
require('config.php');

function rankFromCode($code){
	$ranks = array('LE' => 'Leader',
		'CO' => 'Co-leader',
		'EL' => 'Elder',
		'ME' => 'Member',
		'KI' => 'Kicked',
		'EX' => 'Left',
		null => '');
	return $ranks[$code];
}

function clanTypeFromCode($code){
	$clanType = array('AN' => 'Anyone can join',
		'IN' => 'Invite Only',
		'CL' => 'Closed');
	return $clanType[$code];
}

function lootTypeFromCode($code){
	$clanType = array('GO' => 'Gold',
		'EL' => 'Elixir',
		'DE' => 'Dark Elixir');
	return $clanType[$code];
}

function warFrequencyFromCode($code){
	$warFrequency = array('NS' => 'Not Set',
		'AL' => 'Always',
		'NE' => 'Never',
		'TW' => 'Twice a week',
		'OW' => 'Once a week',
		'RA' => 'Rarely');
	return $warFrequency[$code];
}

function buildProcedure(){
	if(func_num_args() > 0){
		global $db;
		$parameters = func_get_args();
		$procedureName = array_shift($parameters);
		$procedure = 'CALL ' . $procedureName . '(';
		foreach ($parameters as $parameter) {
			$procedure .= "'" . $db->escape_string($parameter) . "',";
		}
		$procedure = rtrim($procedure, ",") . ');';
		return $procedure;
	}else{
		throw new illegalOperationException('buildProcedure first argument must be the procedure name.');
	}
}

function correctTag($tag){
	if($tag[0] != '#'){
		$tag = '#' . $tag;
	}
	$tag = str_replace('0', 'O', $tag);
	return strtoupper($tag);
}

function weekAgo(){
	return strtotime('-1 week');
}

function dayAgo(){
	return strtotime('-1 day');
}

function monthAgo(){
	return strtotime('-1 month');
}

function yearAgo(){
	return strtotime('-1 year');
}

function sortPlayersByRank($players, $order='desc'){
	for ($i=1; $i < count($players); $i++) { 
		$j=$i;
		while ($j>0 && rankIsLower($players[$j-1]->get('rank'), $players[$j]->get('rank'))){
			$temp = $players[$j];
			$players[$j] = $players[$j-1];
			$players[$j-1] = $temp;
			$j--;
		}
	}
	if($order == 'desc'){
		return $players;
	}else{
		return array_reverse($players);
	}
}

function rankIsHigher($rank1, $rank2){
	switch ($rank1) {
		case 'LE':
			return $rank2 != 'LE';
			break;
		case 'CO':
			return $rank2 != 'LE' && $rank2 != 'CO';
			break;
		case 'EL':
			return $rank2 == 'ME';
			break;
		default:
			return false;
			break;
	}
}

function rankIsLower($rank1, $rank2){
	switch ($rank1) {
		case 'CO':
			return $rank == 'LE';
			break;
		case 'EL':
			return $rank2 != 'ME' && $rank2 != 'EL';
			break;
		case 'ME':
			return $rank2 != 'ME';
			break;
		default:
			return false;
			break;
	}
}