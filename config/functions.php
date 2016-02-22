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
			if(isset($parameter)){
				$procedure .= "'" . $db->escape_string($parameter) . "',";
			}else{
				$procedure .= "NULL,";
			}
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
	$tag = strtoupper($tag);
	$tag = str_replace('O', '0', $tag);
	return $tag;
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

function sortPlayersByTrophies($players, $order='desc'){
	for ($i=1; $i < count($players); $i++) { 
		$j=$i;
		while ($j>0 && $players[$j-1]->get('trophies') < $players[$j]->get('trophies')){
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
			return $rank2 == 'LE';
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

function validPassword($password){
	return strlen($password)>8;
}

function generateRandomPassword(){
	$lowerCase = 'abcdefghijklmnopqrstuvwxyz';
	$upperCase = strtoupper($lowerCase);
	$nums = '1234567890';
	$types = array($lowerCase, $upperCase, $nums);
	$password = "";
	for ($i=0; $i < 10; $i++) { 
		$type = rand(0,count($types)-1);
		$chars = str_split($types[$type]);
		$char = rand(0,count($chars)-1);
		$char = $chars[$char];
		$password.=$char;
	}
	return $password;
}

function userHasAccessToUpdateLoot($player){
	global $loggedInUser;
	global $loggedInUserPlayer;
	$accessType = $player->get('accessType');
	if($accessType=='AN'){
		$userHasAccessToUpdateLoot = true;
	}else{
		if(isset($loggedInUser)){
			if(isset($loggedInUserPlayer) && $loggedInUserPlayer->get('id') == $player->get('id')){
				$userHasAccessToUpdateLoot = true;
			}else{
				$userHasAccessToUpdateLoot = false;
				$allowedUsers = $player->getAllowedUsers();
				foreach ($allowedUsers as $user) {
					if($loggedInUser->get('id') == $user->get('id')){
						$userHasAccessToUpdateLoot = true;
						break;
					}
				}
			}
		}else{
			$userHasAccessToUpdateLoot = false;
		}
	}
	return $userHasAccessToUpdateLoot;
}

function convertType($code){
	$clanType = array(
		'open' => 'AN',
		'inviteOnly' => 'IN',
		'closed' => 'CL');
	return $clanType[$code];
}

function convertFrequency($code){
	$warFrequency = array(
		'unknown' => 'NS',
		'always' => 'AL',
		'never' => 'NE',
		'moreThanOncePerWeek' => 'TW',
		'oncePerWeek' => 'OW',
		'lessThanOncePerWeek' => 'RA');
	return $warFrequency[$code];
}

function convertRank($code){
	$ranks = array(
		'member' => 'ME',
		'admin' => 'EL',
		'coLeader' => 'CO',
		'leader' => 'LE');
	return $ranks[$code];
}

function refreshClanInfo($clanId){
	try{
		$clan = new clan($clanId);
		$clan->load();
		$api = new clanApi();
		$clanInfo = $api->getClanInformation($clan->get('tag'));
	}catch(Exception $e){
		error_log($e->getMessage());
		return -1;
	}
	$clan->set('name', $clanInfo->name);
	$clan->set('clanType', convertType($clanInfo->type));
	$clan->set('description', $clanInfo->description);
	$clan->set('warFrequency', convertFrequency($clanInfo->warFrequency));
	$clan->set('minimumTrophies', $clanInfo->requiredTrophies);
	$clan->set('members', $clanInfo->members);
	$clan->set('clanPoints', $clanInfo->clanPoints);
	$clan->set('clanLevel', $clanInfo->clanLevel);
	$clan->set('warWins', $clanInfo->warWins);
	$clan->set('badgeUrl', $clanInfo->badgeUrls->small);
	$clan->set('location', $clanInfo->location->name);
	$members = $clan->getCurrentMembers();
	foreach ($clanInfo->memberList as $apiMember) {
		$count = 0;
		foreach ($members as $key => $temp) {
			if($apiMember->name == $temp->get('name')){
				$count++;
				$member = $temp;
				unset($members[$key]);
			}
		}
		if($count==1){
			$clan->updatePlayerRank($member->get('id'), convertRank($apiMember->role));
			$member->set('level', $apiMember->expLevel);
			$member->set('trophies', $apiMember->trophies);
			$member->set('donations', $apiMember->donations);
			$member->set('received', $apiMember->donationsReceived);
			$member->set('leagueUrl', $apiMember->league->iconUrls->small);
		}elseif ($count==0) {
			//TODO: The player needs to be added to the clan
			//		Need the player tag though, which is not provided yet
		}
	}
	foreach ($members as $member) {
		$member->leaveClan();
	}
	return 0;
}	