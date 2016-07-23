<?
class User{
	private $id;
	private $email;
	private $password;
	private $dateCreated;
	private $dateModified;
	private $player;
	private $playerId;
	private $clan;
	private $clanId;
	private $lastLogin;
	private $admin;

	private $acceptGet = array(
		'id' => 'id',
		'email' => 'email',
		'date_created' => 'dateCreated',
		'date_modified' => 'dateModified',
		'admin' => 'admin',
		'last_login' => 'lastLogin'
	);

	private $acceptSet = array(
		'admin' => 'admin',
		'email' => 'email'
	);

	public function create($email, $password, $playerId=null){
		global $db;
		if(!isset($this->id)){
			if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
				if(validPassword($password)){
					$password = password_hash($password, PASSWORD_DEFAULT);
					$procedure = buildProcedure('p_user_create', $email, $password, date('Y-m-d H:i:s', time()));
					if(($db->multi_query($procedure)) === TRUE){
						$result = $db->store_result()->fetch_object();
						while ($db->more_results()){
							$db->next_result();
						}
						$this->id = $result->id;
						$this->load();
					}else{
						throw new SQLQueryException('The database encountered an error. ' . $db->error);
					}
					if(isset($playerId)){
						$this->linkWithPlayer($playerId);
					}
				}else{
					throw new ArgumentException('Password is not safe.');
				}
			}else{
				throw new ArgumentException('Email must be valid address.');
			}
		}else{
			throw new FunctionCallException('ID set, cannot create.');
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
					$this->playerId = $record->player_id;
					$this->player = null;
					$this->clanId = $record->clan_id;
					$this->clan = null;
					$this->dateCreated = $record->date_created;
					$this->dateModified = $record->date_modified;
					$this->lastLogin = $record->last_login;
					$this->admin = $record->admin == 1;
				}else{
					throw new NoResultFoundException('No player found with id ' . $this->id);
				}
			}else{
				throw new SQLQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new FunctionCallException('ID not set for load.');
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
					$this->playerId = $record->player_id;
					$this->player = null;
					$this->clanId = $record->clan_id;
					$this->clan = null;
					$this->dateCreated = $record->date_created;
					$this->dateModified = $record->date_modified;
					$this->lastLogin = $record->last_login;
					$this->admin = $record->admin == 1;
				}else{
					throw new NoResultFoundException('No player found with email ' . $this->email);
				}
			}else{
				throw new SQLQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new FunctionCallException('Email not set for load.');
		}
	}

	public function loadByObj($userObj){
		$this->id = $userObj->id;
		$this->email = $userObj->email;
		$this->password = $userObj->password;
		$this->playerId = $userObj->player_id;
		$this->player = null;
		$this->clanId = $userObj->clan_id;
		$this->clan = null;
		$this->dateCreated = $userObj->date_created;
		$this->dateModified = $userObj->date_modified;
		$this->lastLogin = $userObj->last_login;
		$this->admin = $userObj->admin == 1;
	}

	public function get($prpty){
		if(isset($this->id)){
			if(in_array($prpty, $this->acceptGet)){
				return $this->$prpty;
			}elseif($prpty == 'player'){
				if(isset($this->player)){
					return $this->player;
				}elseif(isset($this->playerId)){
					return new player($this->playerId);
				}else{
					return null;
				}
			}elseif($prpty == 'clan'){
				if(isset($this->clan)){
					return $this->clan;
				}elseif(isset($this->clanId)){
					return new clan($this->clanId);
				}else{
					return null;
				}
			}else{
				throw new OperationException('Property is not in accept get.');
			}
		}else{
			throw new FunctionCallException('ID not set for get.');
		}
	}

	public function set($prpty, $value){
		global $db;
		if(isset($this->id)){
			if(in_array($prpty, $this->acceptSet)){
				if($prpty != 'email' || filter_var($value, FILTER_VALIDATE_EMAIL)){
					$procedure = buildProcedure('p_user_set', $this->id, array_search($prpty, $this->acceptSet), $value, date('Y-m-d H:i:s', time()));
					if(($db->multi_query($procedure)) === TRUE){
						while ($db->more_results()){
							$db->next_result();
						}
						$this->$prpty = $value;
					}else{
						throw new SQLQueryException('The database encountered an error. ' . $db->error);
					}
				}else{
					throw new ArgumentException('Email must be valid address.');
				}
			}else{
				throw new OperationException('Property is not in accept set.');
			}
		}else{
			throw new FunctionCallException('ID not set for set.');
		}
	}

	public function changePassword($newPassword){
		global $db;
		if(isset($this->id)){
			if(validPassword($newPassword)){
				$newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
				$procedure = buildProcedure('p_user_change_password', $this->id, $newPassword, date('Y-m-d H:i:s', time()));
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
					$this->password = $newPassword;
				}else{
					throw new SQLQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new ArgumentException('New password is not safe.');	
			}
		}else{
			throw new FunctionCallException('ID not set for change password.');
		}
	}

	public function linkWithPlayer($playerId){
		global $db;
		if(isset($this->id)){
			$player = new player($playerId);
			$linkedUser = $player->getLinkedUser();
			if(!isset($linkedUser)){
				$procedure = buildProcedure('p_user_link_player', $this->id, $player->get('id'), date('Y-m-d H:i:s', time()));
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
					$this->player = $player;
				}else{
					throw new SQLQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new ArgumentException('Cannot link to player who is already linked to another account.');
			}
		}else{
			throw new FunctionCallException('ID not set for link.');
		}
	}

	public function unlinkFromPlayer(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_user_unlink_player', $this->id, date('Y-m-d H:i:s', time()));
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
				$this->player = null;
			}else{
				throw new SQLQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new FunctionCallException('ID not set for unlink.');
		}
	}

	public function linkWithClan($clanId){
		global $db;
		if(isset($this->id)){
			$clan = new clan($clanId);
			$linkedUser = $clan->getLinkedUser();
			if(!isset($linkedUser)){
				$procedure = buildProcedure('p_user_link_clan', $this->id, $clan->get('id'), date('Y-m-d H:i:s', time()));
				if(($db->multi_query($procedure)) === TRUE){
					while ($db->more_results()){
						$db->next_result();
					}
					$this->clan = $clan;
				}else{
					throw new SQLQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new ArgumentException('Cannot link to clan which is already linked to another account.');
			}
		}else{
			throw new FunctionCallException('ID not set for link.');
		}
	}

	public function unlinkFromClan(){
		global $db;
		if(isset($this->id)){
			$procedure = buildProcedure('p_user_unlink_clan', $this->id, date('Y-m-d H:i:s', time()));
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
				$this->clan = null;
			}else{
				throw new SQLQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new FunctionCallException('ID not set for unlink.');
		}
	}

	public function login($password){
		if(isset($this->id)) {
			if (password_verify($password, $this->password)){
					global $db;
				$procedure = buildProcedure('p_user_login', $this->id, date('Y-m-d H:i:s', time()));
				if (($db->multi_query($procedure)) === TRUE) {
					while ($db->more_results()) {
						$db->next_result();
					}
					return true;
				} else {
					throw new SQLQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				return false;
			}
		}else{
			throw new FunctionCallException('ID not set for login.');
		}
	}

	public function isAdmin(){
		return $this->get('admin');
	}

	public static function getAdmin(){
		$procedure = buildProcedure('p_user_get_admin');
		if (($db->multi_query($procedure)) === TRUE) {
			while ($db->more_results()) {
				$db->next_result();
			}
			if ($result->num_rows){
				$userObj = $result->fetch_object();
				return new User($userObj);
			}else{
				return null;
			}
		} else {
			throw new SQLQueryException('The database encountered an error. ' . $db->error);
		}
	}
}