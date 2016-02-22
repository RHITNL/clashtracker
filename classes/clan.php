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
		'members' => 'members',
		'clan_level' => 'clanLevel',
		'clan_points' => 'clanPoints',
		'war_wins' => 'warWins',
		'location' => 'location',
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

	public function addPlayer($playerId, $rank='ME'){
		global $db;
		if(isset($this->id)){
			$player = new player($playerId);
			$playerClan = $player->getMyClan();
			if(isset($playerClan) && $playerClan->get('id') != $this->id){
				$player->leaveClan();
			}
			$isNewMember = true;
			$members = $this->getMembers();
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

	public function updatePlayerRank($playerId, $rank){
		$this->addPlayer($playerId, $rank);
	}

	public function getCurrentMembers($rank='%'){
		$allMembers = $this->getMembers($rank);
		$members = array();
		foreach ($allMembers as $member) {
			$rank = $member->get('rank', $this->id);
			if($rank != 'KI' && $rank != 'EX'){
				$members[] = $member;
			}
		}
		return $members;
	}

	public function getPastMembers($rank='%'){
		$allMembers = $this->getMembers($rank);
		$members = array();
		foreach ($allMembers as $member) {
			$rank = $member->get('rank', $this->id);
			if($rank == 'KI' || $rank == 'EX'){
				$members[] = $member;
			}
		}
		return $members;
	}

	public function getMembers($rank='%'){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_clan_get_members', $this->id, $rank);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$members = array();
				if ($results->num_rows) {
					while ($memberObj = $results->fetch_object()) {
						$member = new player($memberObj->player_id);
						$members[] = $member;
					}
				}
				return $members;
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
			$procedure = buildProcedure('p_clan_get_wars', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$wars = array();
				if ($results->num_rows) {
					while ($warObj = $results->fetch_object()) {
						$war = new war($warObj->id);
						$wars[] = $war;
					}
				}
				return $wars;
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
					$clan = new clan($clanObj->id);
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
			$procedure = buildProcedure('p_clan_update_player_war_rank', $this->id, $playerId, $warRank);
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

	public static function searchClans($query){
		global $db;
		$query = trim($query);
		$queries = explode(' ', $query);
		$query = str_replace(' ', '%', $query);
		array_unshift($queries, $query);
		$queries = array_unique($queries);
		$clans = array();
		foreach ($queries as $query) {
			$procedure = buildProcedure('p_clan_search', '%'.$query.'%');
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if ($results->num_rows) {
					while ($clanObj = $results->fetch_object()) {
						$clan = new clan($clanObj->id);
						$clans[] = $clan;
					}
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
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
		return $clans;
	}

	public function delete(){
		if(isset($this->id)){
			$wars = $this->getMyWars();
			$members = $this->getMembers();
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
}