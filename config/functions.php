<?
require('config.php');
require('../vendor/autoload.php');

function rankFromCode($code){
	$ranks = array('1' => 'Leader',
		'2' => 'Co-leader',
		'3' => 'Elder',
		'4' => 'Member',
		'5' => 'Left',
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
		error_log($procedure);
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
	return strtotime('-180 hours');
}

function dayAgo(){
	return strtotime('-1 day');
}

function hourAgo(){
	return strtotime('-1 hour');
}

function monthAgo(){
	return strtotime('-1 month');
}

function yearAgo(){
	return strtotime('-1 year');
}

function sortPlayersByWarScore($players){
	$players = array_values($players);
	for ($i=1; $i < count($players); $i++) { 
		$j=$i;
		while ($j>0 && $players[$j-1]->getScore() < $players[$j]->getScore()){
			$temp = $players[$j];
			$players[$j] = $players[$j-1];
			$players[$j-1] = $temp;
			$j--;
		}
	}
	foreach ($players as $key => $player) {
		if($player->get('numberOfWars') == 0){
			unset($players[$key]);
		}
	}
	return $players;
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

function randomTag(){
	$tag = '#';
	$chars = str_split("026789CDEGHJLPQRSUVWY");
	$length = count($chars)-1;
	$maxLen = rand(8,10);
	for ($i=0; $i < $maxLen; $i++) { 
		$index = rand(0, $length);
		$tag .= $chars[$index];
	}
	return $tag;
}

function userHasAccessToUpdatePlayer($player){
	global $loggedInUser;
	global $loggedInUserPlayer;
	$accessType = $player->get('accessType');
	if($accessType=='AN'){
		return true;
	}else{
		if(isset($loggedInUser)){
			if(isset($loggedInUserPlayer) && $loggedInUserPlayer->get('id') == $player->get('id')){
				return true;
			}else{
				$allowedUsers = $player->getAllowedUsers();
				foreach ($allowedUsers as $user) {
					if($loggedInUser->get('id') == $user->get('id')){
						return true;
					}
				}
				return false;
			}
		}else{
			return false;
		}
	}
	return false;
}

function userHasAccessToUpdateClan($clan){
	global $loggedInUser;
	global $loggedInUserClan;
	$accessType = $clan->get('accessType');
	if($accessType=='AN'){
		return true;
	}else{
		if(isset($loggedInUser)){
			if(isset($loggedInUserClan) && $loggedInUserClan->get('id') == $clan->get('id')){
				return true;
			}else{
				$allowedUsers = $clan->getAllowedUsers();
				foreach ($allowedUsers as $user) {
					if($loggedInUser->get('id') == $user->get('id')){
						return true;
					}
				}
				return false;
			}
		}else{
			return false;
		}
	}
	return false;
}

function userHasAccessToUpdateWar($war){
	if(!userHasAccessToUpdateClan($war->get('clan1'))){
		global $loggedInUser;
		if(isset($loggedInUser)){
			$allowedUsers = $war->getAllowedUsers();
			foreach ($allowedUsers as $user) {
				if($loggedInUser->get('id') == $user->get('id')){
					return true;
				}
			}
		}
		return false;
	}else{
		return true;
	}
}

function convertType($code){
	$clanType = array(
		'open' => 'AN',
		'inviteOnly' => 'IN',
		'closed' => 'CL');
	return $clanType[$code];
}

function convertBackType($code){
	$clanType = array(
		'AN' => 'open',
		'IN' => 'inviteOnly',
		'CL' => 'closed');
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

function convertBackFrequency($code){
	$warFrequency = array(
		'NS' => 'unknown',
		'AL' => 'always',
		'NE' => 'never',
		'TW' => 'moreThanOncePerWeek',
		'OW' => 'oncePerWeek',
		'RA' => 'lessThanOncePerWeek');
	return $warFrequency[$code];
}

function convertRank($code){
	$ranks = array(
		'member' => '4',
		'admin' => '3',
		'coLeader' => '2',
		'leader' => '1');
	return $ranks[$code];
}

function convertBackRank($code){
	$ranks = array(
		'4' => 'member',
		'3' => 'admin',
		'2' => 'coLeader',
		'1' => 'leader');
	return $ranks[$code];
}

function convertLocation($location){
	if(isset($location)){
		return $location;
	}else{
		return 'Not Set';
	}
}

function refreshClanInfo($clan, $force=false){
	try{
		if(hourAgo() > strtotime($clan->get('dateModified')) || $force){
			$api = new clanApi();
			$clanInfo = $api->getClanInformation($clan->get('tag'));
		}else{
			$apiInfo = $clan->get('apiInfo');
			if(isset($apiInfo)){
				$clanInfo = json_decode($apiInfo);
				if(!isset($clanInfo)){
					return array();
				}
			}else{
				return array();
			}
		}
	}catch(apiException $e){
		error_log($e->getReasonMessage());
		return false;
	}catch(Exception $e){
		error_log($e->getMessage());
		return false;
	}
	$clan->updateFromApi($clanInfo);
	$members = $clan->getMembers();
	$possibleMemberMatches = array();
	$possibleApiMemberMatches = array();
	foreach ($clanInfo->memberList as $key => $apiMember) {
		$possibleApiMemberMatches[$key] = array();
		foreach ($members as $id => $member) {
			if(!isset($possibleMemberMatches[$id])){
				$possibleMemberMatches[$id] = array();
			}
			if($apiMember->name == $member->get('name')){
				if($apiMember->expLevel >= $member->get('level')){
					$possibleMemberMatches[$id][$key] = $apiMember;
					$possibleApiMemberMatches[$key][$id] = $member;
				}
			}
		}
	}
	$result = findMemberWithNMatches($possibleMemberMatches, 1);
	while($result !== false){
		$id = $result;
		$matches = $possibleMemberMatches[$result];
		$match = each($matches);
		$members[$id]->updateFromApi($match['value']);
		$otherMatches = $possibleApiMemberMatches[$match['key']];
		unset($possibleMemberMatches[$id]);
		unset($otherMatches[$id]);
		if(count($otherMatches)>0){
			foreach ($otherMatches as $id => $member) {
				unset($possibleMemberMatches[$id][$match['key']]);
			}
		}
		unset($possibleApiMemberMatches[$match['key']]);
		$result = findMemberWithNMatches($possibleMemberMatches, 1);
	}
	$result = findMemberWithNMatches($possibleMemberMatches, 0);
	while($result !== false){
		$id = $result;
		$members[$id]->leaveClan();
		unset($possibleMemberMatches[$id]);
		$result = findMemberWithNMatches($possibleMemberMatches, 0);
	}
	$result = findMemberWithNMatches($possibleMemberMatches, 1, 1);
	$duplicates = 0;
	while($result !== false){
		$id = $result;
		unset($possibleMemberMatches[$id]);
		$duplicates++;
		$result = findMemberWithNMatches($possibleMemberMatches, 1, 1);
	}
	$apiMembers = array();
	foreach ($possibleApiMemberMatches as $key => $value) {
		$apiMembers[] = $clanInfo->memberList[$key];
	}
	return array('members' => $apiMembers, 'duplicates' => $duplicates);
}

function cpr($var, $limit=2, $tab="", $depth=0){
	if(is_array($var)){
		if($depth>$limit){
			return "DEPTH LIMIT REACHED";
		}
		$val .= "Array\n" . $tab . "(\n";
		foreach ($var as $key => $value) {
			$val .= $tab . "\t[" . $key . '] => ' . cpr($value, $limit, $tab."\t\t", $depth+1) . "\n";
		}
		$val .= $tab . ")";
	}elseif(is_object($var)){
		if($depth>$limit){
			return "DEPTH LIMIT REACHED";
		}
		$class = get_class($var);
		global $classes;
		if(in_array($class, $classes)){
			$reflect = new ReflectionClass($var);
			$props = $reflect->getProperties();
			$varArray = array();
			foreach ($props as $prop) {
				$key = $prop->name;
				$value = null;
				try{
					$value = $var->get($key);
				}catch(Exception $e){
					//ignore
				}
				if(isset($value)){
					$varArray[$key] = $value;
				}
			}
		}else{
			$varArray = get_object_vars($var);
		}
		$val .= $class . " Object\n" . $tab . "(\n";
		foreach ($varArray as $key => $value) {
			$val .= $tab . "\t[" . $key . '] => ' . cpr($value, $limit, $tab."\t\t", $depth+1) . "\n";
		}
		$val .= $tab . ")";
	}else{
		if(is_null($var)){
			$val .= "NULL";
		}elseif($var === true){
			$val .= "TRUE";
		}elseif($var === false){
			$val .= "FALSE";
		}else{
			$val .= $var;
		}
	}
	return $val;
}

function findMemberWithNMatches($possibleMemberMatches, $n, $compare=0){
	foreach ($possibleMemberMatches as $id => $matches) {
		if(compare(count($matches), $n) === $compare){
			return $id;
		}
	}
	return false;
}

function compare($a, $b){
	if($a > $b){
		return 1;
	}
	if($a == $b){
		return 0;
	}
	if($a < $b){
		return -1;
	}
	return false;
}

function email($to, $subject, $message){
	try{
		$sendgrid_username = $_ENV['SENDGRID_USERNAME'];
		$sendgrid_password = $_ENV['SENDGRID_PASSWORD'];
		$sendgrid = new SendGrid($sendgrid_username, $sendgrid_password, array("turn_off_ssl_verification" => true));
		$email = new SendGrid\Email();
		$email->addTo($to)->
				setFrom('password@clashtracker.ca')->
				setSubject($subject)->
				setText($message)->
				addHeader('X-Sent-Using', 'SendGrid-API')->
				addHeader('X-Transport', 'web');
		$response = $sendgrid->send($email);
		$responseBody = $response->getBody();
		return isset($responseBody) && $responseBody['message'] == 'success';
	}catch(Exception $e){
		error_log($e->getMessage());
		return false;
	}
}

function correctDateFormat($date){
	if(is_numeric($date)){
		return date('Y-m-d H:i:s', $date);
	}else{
		return date('Y-m-d H:i:s', strtotime($date));
	}
}

function displayName($name){
	$name = htmlspecialchars($name);
	return str_replace(' ', '&nbsp;', $name);
}