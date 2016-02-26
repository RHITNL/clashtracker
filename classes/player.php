<?
class player{
	private $id;
	private $name;
	private $tag;
	private $dateCreated;
	private $dateModified;
	private $warsSinceLastParticipated;
	private $accessType;
	private $minRankAccess;
	private $level;
	private $trophies;
	private $donations;
	private $received;
	private $leagueUrl;

	private $acceptGet = array(
		'id' => 'id',
		'name' => 'name',
		'tag' => 'tag',
		'date_created' => 'dateCreated',
		'date_modified' => 'dateModified',
		'access_type' => 'accessType',
		'min_rank_access' => 'minRankAccess',
		'trophies' => 'trophies',
		'donations' => 'donations',
		'received' => 'received',
		'league_url' => 'leagueUrl',
		'level' => 'level'
	);

	private $acceptSet = array(
		'tag' => 'tag',
		'name' => 'name',
		'access_type' => 'accessType',
		'level' => 'level',
		'trophies' => 'trophies',
		'donations' => 'donations',
		'received' => 'received',
		'league_url' => 'leagueUrl',
		'min_rank_access' => 'minRankAccess'
	);

	public function create($name, $tag){
		global $db;
		if(!isset($this->id)){
			if((strlen($name) > 0) && (strlen($tag) > 0)){
				$tag = correctTag($tag);
				$procedure = buildProcedure('p_player_create', $name, $tag);
				if(($db->multi_query($procedure)) === TRUE){
					$result = $db->store_result()->fetch_object();
					while ($db->more_results()){
						$db->next_result();
					}
					$this->id = $result->id;
					$this->load();
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new illegalArgumentException('Niether name nor tag can be blank.');
			}
		}else{
			throw new illegalFunctionCallException('ID set, cannot create.');
		}
	}

	public function __construct($id=null){
		if($id!=null){
			if(is_numeric($id)){
				$this->id = $id;
				$this->load();
			}else{
				$this->tag = $id;
				$this->loadByTag();
			}
		}
	}

	public function load(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_player_load', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$this->id = $record->id;
					$this->name = $record->name;
					$this->tag = $record->tag;
					$this->dateCreated = $record->date_created;
					$this->dateModified = $record->date_modified;
					$this->accessType = $record->access_type;
					$this->minRankAccess = $record->min_rank_access;
					$this->level = $record->level;
					$this->trophies = $record->trophies;
					$this->donations = $record->donations;
					$this->received = $record->received;
					$this->leagueUrl = $record->league_url;
				}else{
					throw new noResultFoundException('No player found with id ' . $this->id);
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for load.');
		}
	}

	public function loadByTag($tag=null){
		global $db;
		if(isset($this->tag) || $tag != null){
			if($tag == null){
				$tag = $this->tag;
			}
			$tag = correctTag($tag);
			$procedure = buildProcedure('p_player_load_by_tag', $tag);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$this->id = $record->id;
					$this->name = $record->name;
					$this->tag = $record->tag;
					$this->dateCreated = $record->date_created;
					$this->dateModified = $record->date_modified;
					$this->accessType = $record->access_type;
					$this->minRankAccess = $record->min_rank_access;
					$this->level = $record->level;
					$this->trophies = $record->trophies;
					$this->donations = $record->donations;
					$this->received = $record->received;
					$this->leagueUrl = $record->league_url;
				}else{
					throw new noResultFoundException('No player found with tag ' . $tag);
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('Tag not set for load.');
		}
	}

	public function loadByObj($playerObj, $clan=null){
		$this->id = $playerObj->id;
		$this->name = $playerObj->name;
		$this->tag = $playerObj->tag;
		$this->dateCreated = $playerObj->date_created;
		$this->dateModified = $playerObj->date_modified;
		$this->accessType = $playerObj->access_type;
		$this->minRankAccess = $playerObj->min_rank_access;
		$this->level = $playerObj->level;
		$this->trophies = $playerObj->trophies;
		$this->donations = $playerObj->donations;
		$this->received = $playerObj->received;
		$this->leagueUrl = $playerObj->league_url;
		$this->clan = $clan;
		if(isset($clan)){
			$this->clanRank = $playerObj->rank;
		}
	}

	public function get($prpty, $clanId=null){
		if(isset($this->id)){
			if(in_array($prpty, $this->acceptGet)){
				return $this->$prpty;
			}elseif($prpty == 'rank' || $prpty == 'clanRank'){
				return $this->getClanRank($clanId);
			}elseif($prpty == 'warRank'){
				return $this->getWarRank($clanId);
			}else{
				throw new illegalOperationException('Property is not in accept get.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function set($prpty, $value){
		global $db;
		if(isset($this->id)){
			if(in_array($prpty, $this->acceptSet)){
				$procedure = buildProcedure('p_player_set', $this->id, array_search($prpty, $this->acceptSet), $value);
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
					$this->$prpty = $value;
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new illegalOperationException('Property is not in accept set.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set for set.');
		}
	}

	public function updateFromApi($apiMember){
		global $db;
		if(isset($this->id)){
			if($this->clanRank == convertRank($apiMember->role)
				&& $this->level == $apiMember->expLevel
				&& $this->trophies == $apiMember->trophies
				&& $this->donations == $apiMember->donations
				&& $this->received == $apiMember->donationsReceived
				&& $this->leagueUrl == $apiMember->league->iconUrls->small){
				return; //no changes will be made
			}
			$procedure = buildProcedure('p_player_update_bulk',
										$this->id,
										convertRank($apiMember->role),
										$apiMember->expLevel,
										$apiMember->trophies,
										$apiMember->donations, 
										$apiMember->donationsReceived,
										$apiMember->league->iconUrls->small);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
				$this->clanRank = convertRank($apiMember->role);
				$this->level = $apiMember->expLevel;
				$this->trophies = $apiMember->trophies;
				$this->donations = $apiMember->donations;
				$this->received = $apiMember->donationsReceived;
				$this->leagueUrl = $apiMember->league->iconUrls->small;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for set.');
		}
	}

	private function recordLoot($type, $amount, $date='%'){
		global $db;
		if(isset($this->id)){
			$loot = $this->getLoot($type);
			if((count($loot) == 0 || $loot[0]['lootAmount'] <= $amount) && $amount >= 0){
				$procedure = buildProcedure('p_player_record_loot', $this->id, $type, $amount, $date);
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new illegalLootAmountException('New loot recording must be positive and more than previous recording. Player ID: ' . $this->id . ".", $loot[0]['lootAmount']);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for recording loot.');
		}
	}

	public function recordGold($amount, $date='%'){
		$this->recordLoot('GO', $amount, $date);
	}

	public function recordElixir($amount, $date='%'){
		$this->recordLoot('EL', $amount, $date);
	}

	public function recordDarkElixir($amount, $date='%'){
		$this->recordLoot('DE', $amount, $date);
	}

	private function getLoot($type, $earliestDate=0){
		global $db;
		if(isset($this->id)){
			if($earliestDate == 0 && isset($this->loot[$type])){
				return $this->loot[$type];
			}
			if(!isset($this->loot[$type])){
				$procedure = buildProcedure('p_player_get_loot', $this->id, $type);
				if(($db->multi_query($procedure)) === TRUE){
					$results = $db->store_result();
					while ($db->more_results()){
						$db->next_result();
					}
					$loot = array();
					if ($results->num_rows) {
						while ($lootObj = $results->fetch_object()) {
							$tempLoot = array();
							$tempLoot['playerId'] = $lootObj->player_id;
							$tempLoot['dateRecorded'] = $lootObj->date_recorded;
							$tempLoot['lootType'] = $lootObj->loot_type;
							$tempLoot['lootAmount'] = $lootObj->loot_amount;
							$loot[] = $tempLoot;
						}
					}
					$this->loot[$type] = $loot;
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				$loot = $this->loot[$type];
			}
			$length = 0;
			foreach ($loot as $tempLoot) {
				if(strtotime($tempLoot['dateRecorded']) < $earliestDate){
					break;
				}
				$length++;
			}
			return array_splice($loot, 0, $length);
		}else{
			throw new illegalFunctionCallException('ID not set for recording loot.');
		}
	}

	public function getGold($earliestDate=0){
		return $this->getLoot('GO', $earliestDate);
	}

	public function getElixir($earliestDate=0){
		return $this->getLoot('EL', $earliestDate);
	}

	public function getDarkElixir($earliestDate=0){
		return $this->getLoot('DE', $earliestDate);
	}

	/**
	 * Gets the average loot per $perTimePeriod since the $sinceTime
	 * @param $type string Type of loot (GO, EL, or DE)
	 * @param $sinceTime int unix timestamp of the time you want the average since. (e.g. If you want the average over the last week, pass in the unix timestamp of a week ago)
	 * @param $perTimePeriod int Time in seconds for the average. (e.g. If you want the average loot per week, pass in 604800 or the constant WEEK)
	 */
	private function getAverageLoot($type, $sinceTime=0, $perTimePeriod=WEEK){
		$allLoot = $this->getLoot($type);
		$loot = array();
		foreach ($allLoot as $curLoot) {
			$date = strtotime($curLoot['dateRecorded']);
			if($date > $sinceTime){
				$loot[] = $curLoot;
			}else{
				break;
			}
		}
		if(count($loot) > 1){
			$totalLoot = $loot[0]['lootAmount'] - $loot[count($loot)-1]['lootAmount'];
			$startDate = strtotime($loot[count($loot)-1]['dateRecorded']);
			$endDate = strtotime($loot[0]['dateRecorded']);
			$totalTime = $endDate - $startDate;
			$averagePerSec = $totalLoot / $totalTime;
			return $averagePerSec * $perTimePeriod;
		}else{
			return 0;
		}
	}

	public function getAverageGold($sinceTime=0, $perTimePeriod=WEEK){
		return $this->getAverageLoot('GO', $sinceTime, $perTimePeriod);
	}

	public function getAverageElixir($sinceTime=0, $perTimePeriod=WEEK){
		return $this->getAverageLoot('EL', $sinceTime, $perTimePeriod);
	}

	public function getAverageDarkElixir($sinceTime=0, $perTimePeriod=WEEK){
		return $this->getAverageLoot('DE', $sinceTime, $perTimePeriod);
	}

	public function getMyClan(){
		global $db;
		if(isset($this->id)){
			if(isset($this->clan)){
				return $this->clan;
			}
			$procedure = buildProcedure('p_player_get_clan', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$this->clan = null;
				if ($results->num_rows) {
					$clanObj = $results->fetch_object();
					$results->close();
					$this->clan = new clan();
					$this->clan->loadByObj($clanObj);
					$this->clanRank = $clanObj->rank;
				}
				return $this->clan;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function leaveClan(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_player_leave_clan', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for leave.');
		}
	}

	public function getClanRank($clanId=null){
		if(!isset($clanId)){
			if(isset($this->clanRank)){
				return $this->clanRank;
			}
			$clan = $this->getMyClan();
			if(isset($clan)){
				$clanId = $clan->get('id');
			}
		}
		if(isset($clanId)){
			global $db;
			$procedure = buildProcedure('p_player_get_rank', $this->id, $clanId);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$record = $results->fetch_object();
				$results->close();
				$rank = $record->rank;
				if(isset($clan)){
					$this->clanRank = $rank;
				}
				return $rank;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			return null;
		}
	}

	public function getWarRank($clanId=null){
		if($clanId==null){
			$clan = $this->getMyClan();
			if(isset($clan)){
				$clanId = $clan->get('id');
			}
		}
		if(isset($clanId)){
			global $db;
			$procedure = buildProcedure('p_player_get_war_rank', $this->id, $clanId);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$record = $results->fetch_object();
				$results->close();
				return $record->war_rank;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			return null;
		}
	}

	public function getMyClans(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_player_get_clans', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$clans = array();
				if ($results->num_rows) {
					while ($clanObj = $results->fetch_object()) {
						$clan = new clan();
						$clan->loadByObj($clanObj);
						$clans[] = $clan;
					}
				}
				return $clans;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public static function getPlayers($pageSize=50){
		global $db;
		$procedure = buildProcedure('p_get_players', $pageSize);
		if(($db->multi_query($procedure)) === TRUE){
			$results = $db->store_result();
			while ($db->more_results()){
				$db->next_result();
			}
			$players = array();
			if ($results->num_rows) {
				while ($playerObj = $results->fetch_object()) {
					$player = new player();
					$player->loadByObj($playerObj);
					$players[] = $player;
				}
			}
			return $players;
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}
	}

	public function getWars(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_player_get_wars', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$wars = array();
				if ($results->num_rows) {
					while ($record = $results->fetch_object()) {
						$war = new war();
						$war->loadByObj($record);
						$wars[] = $war;
					}
				}
				return $wars;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function getAttacks(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_player_get_attacks', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$warAttacks = array();
				if ($results->num_rows) {
					while ($warAttackObj = $results->fetch_object()) {
						$warAttack = array();
						$warAttack['warId'] = $warAttackObj->war_id;
						$war = new war($warAttack['warId']);
						$warAttack['attackerId'] = $warAttackObj->attacker_id;
						$warAttack['defenderId'] = $warAttackObj->defender_id;
						$warAttack['attackerClanId'] = $warAttackObj->attacker_clan_id;
						$warAttack['defenderClanId'] = $warAttackObj->defender_clan_id;
						$totalStars = $warAttackObj->stars;
						$warAttack['totalStars'] = $totalStars;
						$defenderDefences = $war->getPlayerDefences($warAttack['defenderId']);
						$starsAchievedSoFar = 0;
						foreach ($defenderDefences as $defence) {
							if($defence['attackerId'] != $warAttack['attackerId']){
								$starsAchievedSoFar += $defence['newStars'];
							}else{
								break;
							}
						}
						$newStars = max(0, $totalStars - $starsAchievedSoFar);
						$warAttack['newStars'] = $newStars;
						$warAttack['dateCreated'] = $warAttackObj->date_created;
						$warAttack['dateModified'] = $warAttackObj->date_modified;
						$warAttacks[] = $warAttack;
					}
				}
				return $warAttacks;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function getDefences(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_player_get_defences', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$defences = array();
				$starsAchievedSoFar = 0;
				if ($results->num_rows) {
					while ($defenceObj = $results->fetch_object()) {
						$defence = array();
						$defence['warId'] = $defenceObj->war_id;
						$defence['attackerId'] = $defenceObj->attacker_id;
						$defence['defenderId'] = $defenceObj->defender_id;
						$defence['attackerClanId'] = $defenceObj->attacker_clan_id;
						$defence['defenderClanId'] = $defenceObj->defender_clan_id;
						$totalStars = $defenceObj->stars;
						$defence['totalStars'] = $totalStars;
						$newStars = max(0, $totalStars - $starsAchievedSoFar);
						$starsAchievedSoFar += $newStars;
						$defence['newStars'] = $newStars;
						$defence['dateCreated'] = $defenceObj->date_created;
						$defence['dateModified'] = $defenceObj->date_modified;
						$defences[] = $defence;
					}
				}
				return $defences;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public static function searchPlayers($query){
		global $db;
		$query = trim($query);
		$queries = explode(' ', $query);
		$query = str_replace(' ', '%', $query);
		array_unshift($queries, $query);
		$queries = array_unique($queries);
		$players = array();
		foreach ($queries as $query) {
			$procedure = buildProcedure('p_player_search', '%'.$query.'%');
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if ($results->num_rows) {
					while ($playerObj = $results->fetch_object()) {
						$player = new player($playerObj->id);
						$players[] = $player;
					}
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}	
		}
		$foundIds = array();
		foreach ($players as $i => $player) {
			if(in_array($player->get('id'), $foundIds)){
				unset($players[$i]);
			}else{
				$foundIds[] = $player->get('id');
			}
		}
		return $players;
	}

	public function warsSinceLastParticipated(){
		if(isset($this->warsSinceLastParticipated)){
			return $this->warsSinceLastParticipated;
		}
		$wars = $this->getWars();
		if(count($wars)>0){
			$lastWarId = $wars[0]->get("id");
			$clanWars = $this->getMyClan()->getMyWars();
			if(count($clanWars)>0){
				$count = 0;
				foreach ($clanWars as $war) {
					if($war->get("id") != $lastWarId){
						$count++;
					}else{
						break;
					}
				}
				return $count;
			}else{
				return 0;
			}
		}else{
			return INF;
		}
	}

	public static function getIdsForPlayersWithName($name){
		global $db;
		$procedure = buildProcedure('p_get_players_with_name', $name);
		if(($db->multi_query($procedure)) === TRUE){
			$results = $db->store_result();
			while ($db->more_results()){
				$db->next_result();
			}
			$playerIds = array();
			if ($results->num_rows) {
				while ($playerObj = $results->fetch_object()) {
					$playerIds[] = $playerObj->id;
				}
			}
			return $playerIds;
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}		
	}

	public function removeAllLootValues($type, $date='%'){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_player_remove_loot', $this->id, $type, $date);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for recording loot.');
		}
	}

	public function removeAllGoldValues($date='%'){
		$this->removeAllLootValues('GO', $date);
	}

	public function removeAllElixirValues($date='%'){
		$this->removeAllLootValues('EL', $date);
	}

	public function removeAllDarkElixirValues($date='%'){
		$this->removeAllLootValues('DE', $date);
	}

	public function getLinkedUser(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_player_get_linked_user', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$result = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if ($result->num_rows){
					$userObj = $result->fetch_object();
					return new user($userObj->id);
				}else{
					return null;
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for getting linked player.');
		}
	}

	public function resetAccess(){
		$this->revokeAllAccess();
		$this->set('accessType', 'AN');
		$this->set('minRankAccess', null);
	}

	public function getAllowedUsers(){
		global $db;
		if(isset($this->id)){
			if($this->accessType == 'US'){
				$procedure = buildProcedure('p_player_get_allowed_users', $this->id);
				if(($db->multi_query($procedure)) === TRUE){
					$results = $db->store_result();
					while ($db->more_results()){
						$db->next_result();
					}
					$users = array();
					if ($results->num_rows) {
						while ($userObj = $results->fetch_object()) {
							$user = new user($userObj->user_id);
							$users[] = $user;
						}
					}
					return $users;
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}elseif($this->accessType == 'CL'){
				$clan = $this->getMyClan();
				if(isset($clan)){
					$clanMembers = $clan->getMembers();
					$users = array();
					foreach ($clanMembers as $member) {
						$clanRank = $member->getClanRank();
						if($clanRank == $this->minRankAccess || rankIsHigher($clanRank, $this->minRankAccess)){
							$user = $member->getLinkedUser();
							if(isset($user)){
								$users[] = $user;
							}
						}
					}
					return $users;
				}else{
					return array();
				}
			}else{
				return array();
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}		
	}

	public function grantUserAccess($userId){
		global $db;
		if(isset($this->id)){
			$user = new user($userId);
			$procedure = buildProcedure('p_player_allow_user', $this->id, $user->get('id'));
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for grant.');
		}	
	}

	public function revokeUserAccess($userId){
		global $db;
		if(isset($this->id)){
			$user = new user($userId);
			$procedure = buildProcedure('p_player_disallow_user', $this->id, $user->get('id'));
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for revoke.');
		}	
	}

	public function revokeAllAccess(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_player_disallow_all_users', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for revoke.');
		}	
	}
}