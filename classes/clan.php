<?
class clan{
	private $id;
	private $name;
	private $tag;
	private $description;
	private $clanType;
	private $minimumTrophies;
	private $warFrequency;
	private $dateCreated;
	private $dateModified;
	private $members;
	private $clanLevel;
	private $clanPoints;
	private $warWins;
	private $badgeUrl;
	private $location;
	private $accessType;
	private $minRankAccess;
	private $apiInfo;

	private $acceptGet = array(
		'id' => 'id',
		'name' => 'name',
		'tag' => 'tag',
		'description' => 'description',
		'clan_type' => 'clanType',
		'minimum_trophies' => 'minimumTrophies',
		'war_frequency' => 'warFrequency',
		'date_created' => 'dateCreated',
		'date_modified' => 'dateModified',
		'access_type' => 'accessType',
		'min_rank_access' => 'minRankAccess',
		'members' => 'members',
		'clan_level' => 'clanLevel',
		'clan_points' => 'clanPoints',
		'war_wins' => 'warWins',
		'location' => 'location',
		'api_info' => 'apiInfo',
		'badge_url' => 'badgeUrl'
	);

	private $acceptSet = array(
		'name' => 'name',
		'description' => 'description',
		'clan_type' => 'clanType',
		'minimum_trophies' => 'minimumTrophies',
		'war_frequency' => 'warFrequency',
		'members' => 'members',
		'clan_level' => 'clanLevel',
		'clan_points' => 'clanPoints',
		'war_wins' => 'warWins',
		'location' => 'location',
		'access_type' => 'accessType',
		'min_rank_access' => 'minRankAccess',
		'api_info' => 'apiInfo',
		'badge_url' => 'badgeUrl'
	);

	public function create($tag, $name="", $description=null, $clanType='AN', $minimumTrophies=0, $warFrequency='NS'){
		global $db;
		if(!isset($this->id)){
			if(strlen($tag) > 0){
				$tag = correctTag($tag);
				$procedure = buildProcedure('p_clan_create', $name, $tag, $description, $clanType, $minimumTrophies, $warFrequency);
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
				throw new illegalArgumentException('Clan tag cannot be blank.');
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
			$procedure = buildProcedure('p_clan_load', $this->id);
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
					$this->description = $record->description;
					$this->clanType = $record->clan_type;
					$this->minimumTrophies = $record->minimum_trophies;
					$this->warFrequency = $record->war_frequency;
					$this->dateCreated = $record->date_created;
					$this->dateModified = $record->date_modified;
					$this->members = $record->members;
					$this->clanPoints = $record->clan_points;
					$this->clanLevel = $record->clan_level;
					$this->warWins = $record->war_wins;
					$this->badgeUrl = $record->badge_url;
					$this->location = $record->location;
					$this->accessType = $record->access_type;
					$this->minRankAccess = $record->min_rank_access;
					$this->apiInfo = $record->api_info;
				}else{
					throw new noResultFoundException('No clan found with id ' . $this->id);
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
			$procedure = buildProcedure('p_clan_load_by_tag', $tag);
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
					$this->description = $record->description;
					$this->clanType = $record->clan_type;
					$this->minimumTrophies = $record->minimum_trophies;
					$this->warFrequency = $record->war_frequency;
					$this->dateCreated = $record->date_created;
					$this->dateModified = $record->date_modified;
					$this->members = $record->members;
					$this->clanPoints = $record->clan_points;
					$this->clanLevel = $record->clan_level;
					$this->warWins = $record->war_wins;
					$this->badgeUrl = $record->badge_url;
					$this->location = $record->location;
					$this->accessType = $record->access_type;
					$this->minRankAccess = $record->min_rank_access;
					$this->apiInfo = $record->api_info;
				}else{
					throw new noResultFoundException('No clan found with tag ' . $tag);
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('Tag not set for load.');
		}
	}

	public function loadByObj($clanObj){
		$this->id = $clanObj->id;
		$this->name = $clanObj->name;
		$this->tag = $clanObj->tag;
		$this->description = $clanObj->description;
		$this->clanType = $clanObj->clan_type;
		$this->minimumTrophies = $clanObj->minimum_trophies;
		$this->warFrequency = $clanObj->war_frequency;
		$this->dateCreated = $clanObj->date_created;
		$this->dateModified = $clanObj->date_modified;
		$this->members = $clanObj->members;
		$this->clanPoints = $clanObj->clan_points;
		$this->clanLevel = $clanObj->clan_level;
		$this->warWins = $clanObj->war_wins;
		$this->badgeUrl = $clanObj->badge_url;
		$this->location = $clanObj->location;
		$this->accessType = $clanObj->access_type;
		$this->minRankAccess = $clanObj->min_rank_access;
		$this->apiInfo = $clanObj->api_info;
	}

	public function get($prpty){
		if(isset($this->id)){
			if(in_array($prpty, $this->acceptGet)){
				return $this->$prpty;
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
				$procedure = buildProcedure('p_clan_set', $this->id, array_search($prpty, $this->acceptSet), $value);
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

	public function updateFromApi($clanInfo){
		global $db;
		if(isset($this->id)){
			if($this->name == $clanInfo->name
				&& $this->clanType == convertType($clanInfo->type)
				&& $this->description == $clanInfo->description
				&& $this->warFrequency == convertFrequency($clanInfo->warFrequency)
				&& $this->minimumTrophies == $clanInfo->requiredTrophies
				&& $this->members == $clanInfo->members
				&& $this->clanPoints == $clanInfo->clanPoints
				&& $this->clanLevel == $clanInfo->clanLevel
				&& $this->warWins == $clanInfo->warWins
				&& $this->badgeUrl == $clanInfo->badgeUrls->small
				&& $this->location == convertLocation($clanInfo->location->name)
				&& $this->apiInfo == json_encode($clanInfo)){
				return; //no changes will be made
			}
			$date = date('Y-m-d H:m:s', time());
			$procedure = buildProcedure('p_clan_update_bulk', 
										$this->id,
										$clanInfo->name,
										convertType($clanInfo->type),
										$clanInfo->description,
										convertFrequency($clanInfo->warFrequency),
										$clanInfo->requiredTrophies,
										$clanInfo->members,
										$clanInfo->clanPoints,
										$clanInfo->clanLevel,
										$clanInfo->warWins,
										$clanInfo->badgeUrls->small,
										convertLocation($clanInfo->location->name),
										date('Y-m-d H:m:s', time()),
										json_encode($clanInfo),
										$date);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
				$this->name = $clanInfo->name;
				$this->clanType = convertType($clanInfo->type);
				$this->description = $clanInfo->description;
				$this->warFrequency = convertFrequency($clanInfo->warFrequency);
				$this->minimumTrophies = $clanInfo->requiredTrophies;
				$this->members = $clanInfo->members;
				$this->clanPoints = $clanInfo->clanPoints;
				$this->clanLevel = $clanInfo->clanLevel;
				$this->warWins = $clanInfo->warWins;
				$this->badgeUrl = $clanInfo->badgeUrls->small;
				$this->location = convertLocation($clanInfo->location->name);
				$this->apiInfo = json_encode($clanInfo);
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for update.');
		}
	}

	public function addPlayer($playerId, $rank='ME'){
		global $db;
		if(isset($this->id)){
			$player = new player($playerId);
			$playerClan = $player->getMyClan();
			if(isset($playerClan) && $playerClan->get('id') != $this->id){
				$player->leaveClan();
			}
			$isNewMember = true;
			$members = $this->getPastAndCurrentMembers();
			foreach ($members as $member) {
				if($member->get('id') == $playerId){
					$isNewMember = false;
					break;
				}
			}
			$procedure = buildProcedure('p_clan_add_player', $this->id, $playerId, $rank);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
				if($isNewMember){
					$this->pastAndCurrentMembers[] = $player;
					$this->currentMembers[] = $player;
					$warRank = $this->getHighestWarRank() + 1;
					$this->updatePlayerWarRank($playerId, $warRank);
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for add.');
		}
	}

	public function updatePlayerRank($playerId, $rank){
		$this->addPlayer($playerId, $rank);
	}

	public function getMyLeader(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_clan_get_leader', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$leader = null;
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$leader = new player($record->player_id);
				}
				return $leader;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function hasLeader(){
		$leader = $this->getMyLeader();
		return isset($leader);
	}

	public function getMembers($force=false){
		global $db;
		if(isset($this->id)){
			if(isset($this->currentMembers) && !$force){
				return $this->currentMembers;
			}
			$procedure = buildProcedure('p_clan_get_current_members', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$this->currentMembers = array();
				if ($results->num_rows) {
					while ($memberObj = $results->fetch_object()) {
						$member = new player();
						$member->loadByObj($memberObj, $this);
						$this->currentMembers[] = $member;
					}
				}
				return $this->currentMembers;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function getPastAndCurrentMembers(){
		global $db;
		if(isset($this->id)){
			if(isset($this->pastAndCurrentMembers)){
				return $this->pastAndCurrentMembers;
			}
			$procedure = buildProcedure('p_clan_get_past_and_current_members', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$this->pastAndCurrentMembers = array();
				if ($results->num_rows) {
					while ($memberObj = $results->fetch_object()) {
						$member = new player();
						$member->loadByObj($memberObj);
						$this->pastAndCurrentMembers[] = $member;
					}
				}
				return $this->pastAndCurrentMembers;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function kickPlayer($playerId){
		if(isset($this->id)){
			$player = new player($playerId);
			$playerClan = $player->getMyClan();
			if(isset($playerClan) && $playerClan->get('id') == $this->id){
				$this->updatePlayerRank($playerId, 'KI');
			}else{
				throw new illegalClanManagementException('Cannot kick player. Player not in this clan.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set for kick.');
		}
	}

	public function getMyWars(){
		global $db;
		if(isset($this->id)){
			if(isset($this->wars)){
				return $this->wars;
			}
			$procedure = buildProcedure('p_clan_get_wars', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$this->wars = array();
				if ($results->num_rows) {
					while ($warObj = $results->fetch_object()) {
						$war = new war();
						$war->loadByObj($warObj);
						$this->wars[] = $war;
					}
				}
				return $this->wars;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for wars.');
		}
	}

	public static function getClans($pageSize=50){
		global $db;
		$procedure = buildProcedure('p_get_clans', $pageSize);
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
	}

	public function isPlayerInClan($playerId){
		try{
			$player = new player($playerId);
			$playerId = $player->get('id');
		}catch(Exception $e){
			return false;
		}
		$clan = $player->getMyClan();
		if(isset($clan)){
			return $clan->get('id') == $this->id;
		}else{
			return false;
		}
	}

	public function updatePlayerWarRank($playerId, $warRank){
		if(isset($this->id)){
			global $db;
			$procedure = buildProcedure('p_clan_update_player_war_rank', $this->id, $playerId, $warRank, date('Y-m-d H:m:s', time()));
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for war ranks.');
		}
	}

	public function getHighestWarRank(){
		if(isset($this->id)){
			global $db;
			$procedure = buildProcedure('p_clan_get_highest_war_rank', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$warRank = 0;
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$warRank = $record->war_rank;
				}
				return $warRank;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for war ranks.');
		}
	}

	public function playerJoined($playerId){
		if(isset($this->id)){
			global $db;
			$procedure = buildProcedure('p_clan_player_joined', $this->id, $playerId);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$joined = null;
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$joined = $record->date_joined;
				}
				return $joined;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for war ranks.');
		}
	}

	public static function searchClans($query, $warFrequency=null, $minMembers=null, $maxMembers=null, $minClanLevel=null, $minClanPoints=null){
		global $db;
		$query = trim($query);
		$queries = explode(' ', $query);
		$query = str_replace(' ', '%', $query);
		array_unshift($queries, $query);
		$queries = array_unique($queries);
		$clans = array();
		$where = '';
		if(!is_null($warFrequency)) 	$where .= " and war_frequency = '" . $db->escape_string(convertFrequency($warFrequency)) . "'";
		if(!is_null($minMembers)) 		$where .= " and members >= '" . $db->escape_string($minMembers) . "'";
		if(!is_null($maxMembers)) 		$where .= " and members <= '" . $db->escape_string($maxMembers) . "'";
		if(!is_null($minClanLevel)) 	$where .= " and clan_level >= '" . $db->escape_string($minClanLevel) . "'";
		if(!is_null($minClanPoints)) 	$where .= " and clan_points >= '" . $db->escape_string($minClanPoints) . "'";
		foreach ($queries as $query) {
			if(strlen($query)>1 || count($queries)==1){
				$procedure = "select * from clan where (lower(name) like lower('%".$query."%') or lower(tag) like lower('%".$query."%'))" . $where . ' limit 50;';
				error_log($procedure);
				if(($db->multi_query($procedure)) === TRUE){
					$results = $db->store_result();
					while ($db->more_results()){
						$db->next_result();
					}
					if ($results->num_rows) {
						while ($clanObj = $results->fetch_object()) {
							$clan = new clan();
							$clan->loadByObj($clanObj);
							$clans[] = $clan;
						}
					}
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}
		}
		$foundIds = array();
		foreach ($clans as $i => $clan) {
			if(in_array($clan->get('id'), $foundIds)){
				unset($clans[$i]);
			}else{
				$foundIds[] = $clan->get('id');
			}
		}
		$clans = array_splice($clans, 0, 50);
		return $clans;
	}

	public function delete(){
		if(isset($this->id)){
			$wars = $this->getMyWars();
			$members = $this->getPastAndCurrentMembers();
			if(count($wars) + count($members)==0){
				global $db;
				$procedure = buildProcedure('p_clan_delete', $this->id);
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}
		}else{
			throw new illegalFunctionCallException('ID not set for delete.');
		}
	}

	public function revokeAllAccess(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_clan_disallow_all_users', $this->id);
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

	public function resetAccess(){
		$this->revokeAllAccess();
		$this->set('accessType', 'AN');
		$this->set('minRankAccess', null);
	}

	public function getAllowedUsers(){
		global $db;
		if(isset($this->id)){
			if($this->accessType == 'US'){
				$procedure = buildProcedure('p_clan_get_allowed_users', $this->id);
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
				$clanMembers = $this->getMembers();
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
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function grantUserAccess($user){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_clan_allow_user', $this->id, $user->get('id'));
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
			$procedure = buildProcedure('p_clan_disallow_user', $this->id, $user->get('id'));
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

	public function getLinkedUser(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_clan_get_linked_user', $this->id);
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

	public function getPlayersAvailableForLootReport($type, $sinceTime=null){
		global $db;
		if(isset($this->id)){
			if(!isset($sinceTime)){$sinceTime=weekAgo();}
			$date = date('Y-m-d H:m:s', $sinceTime);
			$procedure = buildProcedure('p_clan_players_for_loot_report', $this->id, $type, $date);
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
		}else{
			throw new illegalFunctionCallException('ID not set for getting players available for loot report.');
		}
	}

	public function getPlayersAvailableForGoldReport($sinceTime=null){
		return $this->getPlayersAvailableForLootReport('GO', $sinceTime);
	}

	public function getPlayersAvailableForElixirReport($sinceTime=null){
		return $this->getPlayersAvailableForLootReport('EL', $sinceTime);
	}

	public function getPlayersAvailableForDarkElixirReport($sinceTime=null){
		return $this->getPlayersAvailableForLootReport('DE', $sinceTime);
	}

	public function canGenerateLootReport($sinceTime=null){
		$lootReports = $this->getLootReports();
		if(count($lootReports)>0){
			$lootReport = $lootReports[0];
			$date = strtotime($lootReport->get('dateCreated'));
			if($date > dayAgo()){
				return false;
			}
		}
		$playersAvailableForLootReport = $this->getPlayersAvailableForGoldReport($sinceTime);
		$playersAvailableForLootReport = array_merge($playersAvailableForLootReport, $this->getPlayersAvailableForElixirReport($sinceTime));
		$playersAvailableForLootReport = array_merge($playersAvailableForLootReport, $this->getPlayersAvailableForDarkElixirReport($sinceTime));
		return count($playersAvailableForLootReport)>0;
	}

	public function generateLootReport($sinceTime=null){
		if(isset($this->id)){
			$lootReport = new lootReport();
			$lootReport->create($this, $sinceTime);
			return $lootReport;
		}else{
			throw new illegalFunctionCallException('ID not set for generating loot report.');
		}
	}

	public function getLootReports(){
		global $db;
		if(isset($this->id)){
			if(isset($this->lootReports)){
				return $this->lootReports;
			}
			$procedure = buildProcedure('p_clan_get_loot_reports', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$this->lootReports = array();
				if ($results->num_rows) {
					while ($lootReportObj = $results->fetch_object()) {
						$lootReport = new lootReport();
						$lootReport->loadByObj($lootReportObj);
						$this->lootReports[] = $lootReport;
					}
				}
				return $this->lootReports;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set to getloot Reports.');
		}
	}

	public function canRequestAccess(){
		global $loggedInUser;
		return $this->accessType == 'US' && isset($loggedInUser) && !userHasAccessToUpdateClan($this) && !$this->userHasRequested($loggedInUser);
	}

	public function requestAccess($user, $message=""){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_clan_edit_request_create', $this->id, $user->get('id'), $message);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for request.');
		}
	}

	public function deleteRequest($user){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_clan_edit_request_delete', $this->id, $user->get('id'));
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for delete request.');
		}
	}

	public function getRequests(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_clan_get_edit_requests', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$editRequests = array();
				if ($results->num_rows) {
					while ($editRequestObj = $results->fetch_object()) {
						$editRequest = new stdClass();
						$editRequest->clan = $this;
						$user = new user();
						$user->loadByObj($editRequestObj);
						$editRequest->user = $user;
						$editRequest->message = $editRequestObj->message;
						$editRequests[] = $editRequest;
					}
				}
				return $editRequests;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set to get requests.');
		}
	}

	public function userHasRequested($user){
		$requests = $this->getRequests();
		if(!isset($user)){
			return false;
		}
		$userId = $user->get('id');
		foreach ($requests as $request) {
			if($request->user->get('id') == $userId){
				return true;
			}
		}
		return false;
	}
}