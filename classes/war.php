<?
class war{
	private $id;
	private $firstClanId;
	private $clan1;
	private $secondClanId;
	private $clan2;
	private $size;
	private $dateCreated;
	private $dateModified;

	private $acceptGet = array(
		'id' => 'id',
		'first_clan_id' => 'firstClanId',
		'second_clan_id' => 'secondClanId',
		'size' => 'size',
		'date_created' => 'dateCreated',
		'date_modified' => 'dateModified'
	);

	private $acceptSet = array(
		'size' => 'size',
		'first_clan_stars' => 'firstClanStars',
		'second_clan_stars' => 'secondClanStars',
		'clan_stars' => 'clanStars'
	);

	public function create($clan1, $clan2, $size){
		global $db;
		if(!isset($this->id)){
			$clan1Id = $clan1->get('id');
			$clan2Id = $clan2->get('id');
			$procedure = buildProcedure('p_war_create', $clan1Id, $clan2Id, $size);
			if(($db->multi_query($procedure)) === TRUE){
				$result = $db->store_result()->fetch_object();
				while ($db->more_results()){
					$db->next_result();
				}
				$this->id = $result->id;
				$this->load();
				$this->updateClanWarStats($clan1);
				$this->updateClanWarStats($clan2);
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID set, cannot create.');
		}
	}

	public function __construct($id=null){
		$this->clanWarPlayers = array();
		$this->clanStars = array();
		$this->playerRanks = array();
		$this->playerDefences = array();
		if(isset($id)){
			$this->id = $id;
			$this->load();
		}
	}

	public function load(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_war_load', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$this->id = $record->id;
					$this->firstClanId = $record->first_clan_id;
					$this->secondClanId = $record->second_clan_id;
					$this->size = $record->size;
					$this->dateCreated = $record->date_created;
					$this->dateModified = $record->date_modified;
					$this->clanStars[$this->firstClanId] = $record->first_clan_stars;
					$this->clanStars[$this->secondClanId] = $record->second_clan_stars;
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

	public function loadByObj($warObj){
		$this->id = $warObj->id;
		$this->firstClanId = $warObj->first_clan_id;
		$this->secondClanId = $warObj->second_clan_id;
		$this->size = $warObj->size;
		$this->dateCreated = $warObj->date_created;
		$this->dateModified = $warObj->date_modified;
		$this->clanStars[$this->firstClanId] = $warObj->first_clan_stars;
		$this->clanStars[$this->secondClanId] = $warObj->second_clan_stars;
	}

	public function get($prpty){
		if(isset($this->id)){
			if(in_array($prpty, $this->acceptGet)){
				return $this->$prpty;
			}elseif($prpty == 'clan1'){
				if(!isset($this->clan1)){
					$this->clan1 = new clan($this->firstClanId);
					return $this->clan1;
				}else{
					return $this->clan1;
				}
			}elseif($prpty == 'clan2'){
				if(!isset($this->clan2)){
					$this->clan2 = new clan($this->secondClanId);
					return $this->clan2;
				}else{
					return $this->clan2;
				}
			}else{
				throw new illegalOperationException('Property is not in accept get.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function set($prpty, $value, $clanId=null){
		global $db;
		if(isset($this->id)){
			if(in_array($prpty, $this->acceptSet)){
				if($prpty == 'clanStars'){
					if($clanId == $this->firstClanId){
						$prpty = 'firstClanStars';
					}else{
						$prpty = 'secondClanStars';
					}
				}
				$procedure = buildProcedure('p_war_set', $this->id, array_search($prpty, $this->acceptSet), $value);
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
					if($prpty == 'clanStars'){
						$this->clanStars[$clanId] = $value;
					}else{
						$this->$prpty = $value;
					}
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

	public function isClanInWar($clanId){
		return ($clanId == $this->firstClanId || $clanId == $this->secondClanId);
	}

	public function addPlayer($playerId){
		if(isset($this->id)){
			$player = new player($playerId);
			$playerId = $player->get('id');
			$clan = $player->getMyClan();
			if(isset($clan) && $this->isClanInWar($clan->get('id'))){
				if(count($this->getMyWarPlayers($clan)) < $this->size){
					global $db;
					$procedure = buildProcedure('p_war_add_player', $this->id, $playerId, $clan->get('id'));
					if(($db->multi_query($procedure)) === TRUE){
						while ($db->more_results()){
							$db->next_result();
						}
						$this->warPlayers[] = $player;
						$this->clanWarPlayers[$clan->get('id')][] = $player;
						$this->updateRanks($clan);
					}else{
						throw new illegalQueryException('The database encountered an error. ' . $db->error);
					}
				}else{
					throw new illegalWarPlayerException('Cannot add player to war. Already ' . $this->size . ' members for this clan.');
				}
			}else{
				throw new illegalWarPlayerException('Cannot add player to war. Player not in either war clan.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set to add players.');
		}
	}

	public function removePlayer($playerId){
		if(isset($this->id)){
			$player = new player($playerId);
			$playerId = $player->get('id');
			if($this->isPlayerInWar($playerId)){
				global $db;
				$procedure = buildProcedure('p_war_remove_player', $this->id, $playerId);
				$playerClan = $this->getPlayerWarClan($playerId);
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
					$this->updateRanks($playerClan);
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new illegalWarPlayerException('Cannot remove player from war. Player not in war.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set to remove players.');
		}
	}

	public function getMyWarPlayers($clan=null){
		global $db;
		if(isset($this->id)){
			if(isset($clan)){
				$clanId = $clan->get('id');
				if(!$this->isClanInWar($clanId)){
					throw new illegalWarClanException('Clan not in war.');
				}
				if(isset($this->clanWarPlayers[$clanId])){
					return $this->clanWarPlayers[$clanId];
				}
			}else{
				$clanId = '%';
				if(isset($this->warPlayers)){
					return $this->warPlayers;
				}
			}
			$procedure = buildProcedure('p_war_get_players', $this->id, $clanId);
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
				if(isset($clan)){
					$this->clanWarPlayers[$clanId] = $players;
				}else{
					$this->warPlayers = $players;
				}
				return $players;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set to get war players.');
		}
	}

	public function isPlayerInWar($playerId){
		$warPlayers = $this->getMyWarPlayers();
		foreach ($warPlayers as $warPlayer) {
			if($warPlayer->get('id') == $playerId){
				return true;
			}
		}
		return false;
	}

	public function addAttack($attackerId, $defenderId, $stars){
		global $db;
		if(isset($this->id)){
			if($this->isPlayerInWar($attackerId) && $this->isPlayerInWar($defenderId)){
				$attacker = new player($attackerId);
				$attackerId = $attacker->get('id');
				$attackerClan = $this->getPlayerWarClan($attacker->get('id'));
				$defender = new player($defenderId);
				$defenderId = $defender->get('id');
				$defenderClan = $this->getPlayerWarClan($defender->get('id'));
				if($attackerClan->get('id') != $defenderClan->get('id')){
					if($stars >=0 && $stars <= 3){
						$procedure = buildProcedure('p_war_add_attack', $this->id, $attackerId, $defenderId, $attackerClan->get('id'), $defenderClan->get('id'), $stars);
						if(($db->multi_query($procedure)) === TRUE){
							while ($db->more_results()){
								$db->next_result();
							}
						}else{
							throw new illegalQueryException('The database encountered an error. ' . $db->error);
						}
					}else{
						throw new Exception('Invalid amount of stars for an attack. Must be between 0-3.');
					}
				}else{
					throw new illegalWarPlayerException('Attacker and defender cannot be from the same clan.');
				}
			}else{
				throw new illegalWarPlayerException('Attacker and/or defender not in war.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set to add war attacks.');
		}
	}

	public function updateAttack($attackerId, $defenderId, $stars){
		$this->addAttack($attackerId, $defenderId, $stars);
	}

	public function removeAttack($attacker, $defender){
		global $db;
		if(isset($this->id)){
			$attackerId = $attacker->get('id');
			$defenderId = $defender->get('id');
			$procedure = buildProcedure('p_war_remove_attack', $this->id, $attackerId, $defenderId);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set to remove war attacks.');
		}
	}

	public function getAttacks($clan=null){
		global $db;
		if(isset($this->id)){
			if(isset($clan)){
				$clanId = $clan->get('id');
				if(!$this->isClanInWar($clanId)){
					throw new illegalWarClanException('Clan not in war.');
				}
				if(isset($this->clanAttacks[$clanId])){
					return $this->clanAttacks[$clanId];
				}
			}else{
				if(isset($this->warAttacks)){
					return $this->warAttacks;
				}
			}
			$procedure = buildProcedure('p_war_get_attacks', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if(!isset($this->clanAttacks)){
					$this->clanAttacks = array();
				}
				$this->clanAttacks[$this->firstClanId] = array();
				$this->clanAttacks[$this->secondClanId] = array();
				if(!isset($this->playerAttacks)){
					$this->playerAttacks = array();
				}
				$this->warAttacks = array();
				if ($results->num_rows) {
					while ($warAttackObj = $results->fetch_object()) {
						$warAttack = array();
						$warAttack['warId'] = $warAttackObj->war_id;
						$warAttack['attackerId'] = $warAttackObj->attacker_id;
						$warAttack['defenderId'] = $warAttackObj->defender_id;
						$warAttack['attackerClanId'] = $warAttackObj->attacker_clan_id;
						$warAttack['defenderClanId'] = $warAttackObj->defender_clan_id;
						$totalStars = $warAttackObj->stars;
						$warAttack['totalStars'] = $totalStars;
						$defenderDefences = $this->getPlayerDefences($warAttack['defenderId']);
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
						$this->warAttacks[] = $warAttack;
						$this->playerAttacks[$warAttack['attackerId']][] = $warAttack;
						$this->clanAttacks[$warAttack['attackerClanId']][] = $warAttack;
					}
				}
				if(isset($clan)){
					return $this->clanAttacks[$clanId];
				}else{
					return $this->warAttacks;
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set to get war attacks.');
		}
	}

	public function getAttack($attackerId, $defenderId){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_war_get_attack', $this->id, $attackerId, $defenderId);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$warAttack = null;
				if ($results->num_rows) {
					$warAttackObj = $results->fetch_object();
					$warAttack = array();
					$warAttack['warId'] = $warAttackObj->war_id;
					$warAttack['attackerId'] = $warAttackObj->attacker_id;
					$warAttack['defenderId'] = $warAttackObj->defender_id;
					$warAttack['attackerClanId'] = $warAttackObj->attacker_clan_id;
					$warAttack['defenderClanId'] = $warAttackObj->defender_clan_id;
					$totalStars = $warAttackObj->stars;
					$warAttack['totalStars'] = $totalStars;
					$defenderDefences = $this->getPlayerDefences($warAttack['defenderId']);
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
				}
				return $warAttack;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set to remove war attacks.');
		}
	}

	public function getPlayerAttacks($player){
		global $db;
		if(isset($this->id)){
			$playerId = $player->get('id');
			if(!$this->isPlayerInWar($playerId)){
				throw new illegalWarPlayerException('Player not in war.');
			}
			if(isset($this->playerAttacks[$playerId])){
				return $this->playerAttacks[$playerId];
			}
			$procedure = buildProcedure('p_war_get_player_attacks', $this->id, $playerId);
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
						$warAttack['attackerId'] = $warAttackObj->attacker_id;
						$warAttack['defenderId'] = $warAttackObj->defender_id;
						$warAttack['attackerClanId'] = $warAttackObj->attacker_clan_id;
						$warAttack['defenderClanId'] = $warAttackObj->defender_clan_id;
						$totalStars = $warAttackObj->stars;
						$warAttack['totalStars'] = $totalStars;
						$defenderDefences = $this->getPlayerDefences($warAttack['defenderId']);
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
				$this->playerAttacks[$playerId] = $warAttacks;
				return $warAttacks;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set to get war attacks.');
		}
	}

	public function getPlayerDefences($playerId){
		global $db;
		if(isset($this->id)){
			if(!$this->isPlayerInWar($playerId)){
				throw new illegalWarPlayerException('Player not in war.');
			}
			if(isset($this->playerDefences[$playerId])){
				return $this->playerDefences[$playerId];
			}
			$procedure = buildProcedure('p_war_get_player_defences', $this->id, $playerId);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$warAttacks = array();
				$starsAchievedSoFar = 0;
				if ($results->num_rows) {
					while ($warAttackObj = $results->fetch_object()) {
						$warAttack = array();
						$warAttack['warId'] = $warAttackObj->war_id;
						$warAttack['attackerId'] = $warAttackObj->attacker_id;
						$warAttack['defenderId'] = $warAttackObj->defender_id;
						$warAttack['attackerClanId'] = $warAttackObj->attacker_clan_id;
						$warAttack['defenderClanId'] = $warAttackObj->defender_clan_id;
						$totalStars = $warAttackObj->stars;
						$warAttack['totalStars'] = $totalStars;
						$newStars = max(0, $totalStars - $starsAchievedSoFar);
						$starsAchievedSoFar += $newStars;
						$warAttack['newStars'] = $newStars;
						$warAttack['dateCreated'] = $warAttackObj->date_created;
						$warAttack['dateModified'] = $warAttackObj->date_modified;
						$warAttacks[] = $warAttack;
					}
				}
				$this->playerDefences[$playerId] = $warAttacks;
				return $warAttacks;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set to get war attacks.');
		}
	}

	public function getClanStars($clan, $force=false){
		if(!$force){
			return (int)$this->clanStars[$clan->get('id')];
		}
		$clanAttacks = $this->getAttacks($clan);
		$count = 0;
		foreach ($clanAttacks as $attack) {
			$count += $attack['newStars'];
		}
		if($count != $this->clanStars[$clan->get('id')]){
			$this->set('clanStars', $count, $clan->get('id'));
		}
		return $count;
	}

	public function getWinner(){
		$clan1Stars = $this->getClanStars($this->get('clan1'));
		$clan2Stars = $this->getClanStars($this->get('clan2'));
		if($clan1Stars > $clan2Stars){
			return $this->firstClanId;
		}else if($clan2Stars > $clan1Stars){
			return $this->secondClanId;
		}else{
			return null;
		}
	}

	public function getEnemy($clanId){
		if($this->isClanInWar($clanId)){
			if($clanId == $this->firstClanId){
				return $this->secondClanId;
			}else{
				return $this->firstClanId;
			}
		}else{
			throw new illegalWarClanException('Clan not in war.');
		}
	}

	public static function getWars($pageSize=50){
		global $db;
		$procedure = buildProcedure('p_get_wars', $pageSize);
		if(($db->multi_query($procedure)) === TRUE){
			$results = $db->store_result();
			while ($db->more_results()){
				$db->next_result();
			}
			$wars = array();
			if ($results->num_rows) {
				while ($warObj = $results->fetch_object()) {
					$war = new war();
					$war->loadByObj($warObj);
					$wars[] = $war;
				}
			}
			return $wars;
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}
	}

	public function getPlayerWarClan($playerId){
		if($this->isPlayerInWar($playerId)){
			global $db;
			$procedure = buildProcedure('p_war_get_player_war_clan', $this->id, $playerId);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$clan = null;
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$clan = new clan($record->clan_id);
				}
				return $clan;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalWarPlayerException('Player not in war.');
		}
	}

	public function updatePlayerRank($playerId, $rank){
		if(isset($this->id)){
			global $db;
			$procedure = buildProcedure('p_war_update_player_rank', $this->id, $playerId, $rank);
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

	public function getHighestRank(){
		if(isset($this->id)){
			global $db;
			$procedure = buildProcedure('p_war_get_highest_rank', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$rank = 0;
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$rank = $record->rank;
				}
				return $rank;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for war ranks.');
		}
	}

	public function updateRanks($clan=null){
		if(!$this->isClanInWar($clan->get('id'))){
			$this->updateRanks($this->get('clan1'));
			$this->updateRanks($this->get('clan2'));
			return;
		}
		$warPlayers = $this->getMyWarPlayers($clan);
		$clanId = $clan->get('id');
		for ($i=1; $i < count($warPlayers); $i++) { 
			$j=$i;
			while ($j>0 && ($warPlayers[$j-1]->get('warRank', $clanId) > $warPlayers[$j]->get('warRank', $clanId))){
				$temp = $warPlayers[$j];
				$warPlayers[$j] = $warPlayers[$j-1];
				$warPlayers[$j-1] = $temp;
				$j--;
			}
		}
		$count = 1;
		foreach ($warPlayers as $player) {
			$prevRank = $this->getPlayerRank($player->get('id'));
			if($prevRank!=$count){
				$this->updatePlayerRank($player->get('id'), $count);
			}
			$count++;
		}
	}

	public function getPlayerRank($playerId){
		if(isset($this->id)){
			if($this->isPlayerInWar($playerId)){
				if(isset($this->playerRanks[$playerId])){
					return $this->playerRanks[$playerId];
				}
				global $db;
				$procedure = buildProcedure('p_war_get_player_rank', $this->id, $playerId);
				if(($db->multi_query($procedure)) === TRUE){
					$results = $db->store_result();
					while ($db->more_results()){
						$db->next_result();
					}
					$rank = 0;
					if ($results->num_rows) {
						$record = $results->fetch_object();
						$results->close();
						$rank = $record->rank;
					}
					$this->playerRanks[$playerId] = $rank;
					return $rank;
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new illegalWarPlayerException('Player not in war.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set for war ranks.');
		}
	}

	public function getPlayerByRank($rank, $clanId){
		if(isset($this->id)){
			if($this->isClanInWar($clanId)){
				global $db;
				$procedure = buildProcedure('p_war_get_player_by_rank', $this->id, $clanId, $rank);
				if(($db->multi_query($procedure)) === TRUE){
					$results = $db->store_result();
					while ($db->more_results()){
						$db->next_result();
					}
					$player = null;
					if ($results->num_rows) {
						$record = $results->fetch_object();
						$results->close();
						$player = new player($record->player_id);
					}
					return $player;
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new illegalWarClanException('Clan not in war.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set for war ranks.');
		}
	}

	public function isEditable(){
		$clan1 = $this->get('clan1');
		$clan1Wars = $clan1->getMyWars();
		$isClan1CurrWar = $clan1Wars[0]->get('id') == $this->id;

		$clan2 = $this->get('clan2');
		$clan2Wars = $clan2->getMyWars();
		$isClan2CurrWar = $clan2Wars[0]->get('id') == $this->id;

		return $isClan1CurrWar && $isClan2CurrWar;
	}

	public function revokeAllAccess(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_war_disallow_all_users', $this->id);
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
	}

	public function getAllowedUsers(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_war_get_allowed_users', $this->id);
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
		}else{
			throw new illegalFunctionCallException('ID not set for get.');
		}
	}

	public function grantUserAccess($user){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_war_allow_user', $this->id, $user->get('id'));
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
			$procedure = buildProcedure('p_war_disallow_user', $this->id, $user->get('id'));
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

	public function requestAccess($user, $message=""){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_war_edit_request_create', $this->id, $user->get('id'), $message);
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
			$procedure = buildProcedure('p_war_edit_request_delete', $this->id, $user->get('id'));
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
			$procedure = buildProcedure('p_war_get_edit_requests', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				$editRequests = array();
				if ($results->num_rows) {
					while ($editRequestObj = $results->fetch_object()) {
						$editRequest = new stdClass();
						$editRequest->war = $this;
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

	public function userHasRequested($userId){
		$requests = $this->getRequests();
		foreach ($requests as $request) {
			if($request->user->get('id') == $userId){
				return true;
			}
		}
		return false;
	}

	private function updateClanWarStats($clan){
		$wars = $clan->getMyWars();
		if(count($wars)>1){
			$war = $wars[1];
			$war->getAttacks();
			$players = $war->getMyWarPlayers($clan);
			foreach ($players as $player) {
				$attacks = $war->getPlayerAttacks($player);
				$player->set('attacksUsed', $player->get('attacksUsed') + count($attacks));
				$firstAttack = $attacks[0];
				if(isset($firstAttack)){
					$player->set('firstAttackTotalStars', $player->get('firstAttackTotalStars') + $firstAttack['totalStars']);
					$player->set('firstAttackNewStars', $player->get('firstAttackNewStars') + $firstAttack['newStars']);
				}
				$secondAttack = $attacks[1];
				if(isset($secondAttack)){
					$player->set('secondAttackTotalStars', $player->get('secondAttackTotalStars') + $secondAttack['totalStars']);
					$player->set('secondAttackNewStars', $player->get('secondAttackNewStars') + $secondAttack['newStars']);
				}
				$defences = $war->getPlayerDefences($player->get('id'));
				$stars = 0;
				foreach ($defences as $defence) {
					$stars += $defence['newStars'];
				}
				$player->set('numberOfDefences', $player->get('numberOfDefences') + count($defences));
				$player->set('starsOnDefence', $player->get('starsOnDefence') + $stars);
				$player->set('numberOfWars', $player->get('numberOfWars') + 1);
			}
		}
	}
}