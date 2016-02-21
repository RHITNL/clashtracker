<?
class user{
	private $id;
	private $email;
	private $password;
	private $dateCreated;
	private $dateModified;
	private $player;
	private $clan;

	private $acceptGet = array(
		'id' => 'id',
		'email' => 'email',
		'player' => 'player',
		'clan' => 'clan',
		'date_created' => 'dateCreated',
		'date_modified' => 'dateModified'
	);

	private $acceptSet = array(
		'email' => 'email'
	);

	public function create($email, $password, $playerId=null){
		global $db;
		if(!isset($this->id)){
			if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
				if(validPassword($password)){
					$password = password_hash($password, PASSWORD_DEFAULT);
					$procedure = buildProcedure('p_user_create', $email, $password);
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
					if(isset($playerId)){
						$this->linkWithPlayer($playerId);
					}
				}else{
					throw new illegalArgumentException('Password is not safe.');
				}
			}else{
				throw new illegalArgumentException('Email must be valid address.');
			}
		}else{
			throw new illegalFunctionCallException('ID set, cannot create.');
		}
	}

	public function __construct($id=null){
		if(isset($id)){
			if(is_numeric($id)){
				$this->id = $id;
				$this->load();
			}else{
				$this->email = $id;
				$this->loadByEmail();
			}
		}
	}

	public function load(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_user_load', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$this->id = $record->id;
					$this->email = $record->email;
					$this->password = $record->password;
					$playerId = $record->player_id;
					if(isset($playerId)){
						$this->player = new player($playerId);
					}else{
						$this->player = null;
					}
					$clanId = $record->clan_id;
					if(isset($clanId)){
						$this->clan = new clan($clanId);
					}else{
						$this->clan = null;
					}
					$this->dateCreated = $record->date_created;
					$this->dateModified = $record->date_modified;
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

	public function loadByEmail(){
		global $db;
		if(isset($this->email)){
			$procedure = buildProcedure('p_user_load_by_email', $this->email);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$this->id = $record->id;
					$this->email = $record->email;
					$this->password = $record->password;
					$playerId = $record->player_id;
					if(isset($playerId)){
						$this->player = new player($playerId);
					}else{
						$this->player = null;
					}
					$clanId = $record->clan_id;
					if(isset($clanId)){
						$this->clan = new clan($clanId);
					}else{
						$this->clan = null;
					}
					$this->dateCreated = $record->date_created;
					$this->dateModified = $record->date_modified;
				}else{
					throw new noResultFoundException('No player found with email ' . $this->email);
				}
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('Email not set for load.');
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
				if($prpty != 'email' || filter_var($value, FILTER_VALIDATE_EMAIL)){
					$procedure = buildProcedure('p_user_set', $this->id, array_search($prpty, $this->acceptSet), $value);
					if(($db->multi_query($procedure)) === TRUE){
						while ($db->more_results()){
							$db->next_result();
						}
						$this->$prpty = $value;
					}else{
						throw new illegalQueryException('The database encountered an error. ' . $db->error);
					}
				}else{
					throw new illegalArgumentException('Email must be valid address.');
				}
			}else{
				throw new illegalOperationException('Property is not in accept set.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set for set.');
		}
	}

	public function changePassword($newPassword){
		global $db;
		if(isset($this->id)){
			if(validPassword($newPassword)){
				$newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
				$procedure = buildProcedure('p_user_change_password', $this->id, $newPassword);
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
					$this->password = $newPassword;
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new illegalArgumentException('New password is not safe.');	
			}
		}else{
			throw new illegalFunctionCallException('ID not set for change password.');
		}
	}

	public function linkWithPlayer($playerId){
		global $db;
		if(isset($this->id)){
			$player = new player($playerId);
			$linkedUser = $player->getLinkedUser();
			if(!isset($linkedUser)){
				$procedure = buildProcedure('p_user_link_player', $this->id, $player->get('id'));
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
					$this->player = $player;
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new illegalArgumentException('Cannot link to player who is already linked to another account.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set for link.');
		}
	}

	public function unlinkFromPlayer(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_user_unlink_player', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
				$this->player = null;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for unlink.');
		}
	}

	public function linkWithClan($clanId){
		global $db;
		if(isset($this->id)){
			$clan = new clan($clanId);
			$linkedUser = $clan->getLinkedUser();
			if(!isset($linkedUser)){
				$procedure = buildProcedure('p_user_link_clan', $this->id, $clan->get('id'));
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
					$this->clan = $clan;
				}else{
					throw new illegalQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new illegalArgumentException('Cannot link to clan which is already linked to another account.');
			}
		}else{
			throw new illegalFunctionCallException('ID not set for link.');
		}
	}

	public function unlinkFromClan(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_user_unlink_clan', $this->id);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
				$this->clan = null;
			}else{
				throw new illegalQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new illegalFunctionCallException('ID not set for unlink.');
		}
	}

	public function login($password){
		if(isset($this->id)){
			return password_verify($password, $this->password);
		}else{
			throw new illegalFunctionCallException('ID not set for login.');
		}
	}
}