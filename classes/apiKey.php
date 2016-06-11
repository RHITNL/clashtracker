<?
class APIKey{
	private $ip;
	private $apiKey;

	private $acceptGet = array(
		'ip' => 'ip',
		'api_key' => 'apiKey'
	);

	public function create($ip, $key){
		global $db;
		if(!isset($this->ip)){
			if((strlen($ip)>0) && (strlen($key)>0)){
				$procedure = buildProcedure('p_api_key_create', $ip, $key);
				if(($db->multi_query($procedure)) === TRUE){
					$result = $db->store_result()->fetch_object();
					while ($db->more_results()){
						$db->next_result();
					}
					$this->ip = $result->ip;
					$this->apiKey = $result->api_key;
				}else{
					throw new SQLQueryException('The database encountered an error. ' . $db->error);
				}
			}else{
				throw new ArgumentException('Niether ip address nor api key can be blank.');
			}
		}else{
			throw new FunctionCallException('IP set, cannot create.');
		}
	}

	public function __construct($ip=null){
		if($ip!=null){
			$this->ip = $ip;
			$this->load();
		}
	}

	public function load(){
		global $db;
		if(isset($this->ip)){
			$procedure = buildProcedure('p_api_key_get', $this->ip);
			if(($db->multi_query($procedure)) === TRUE){
				$results = $db->store_result();
				while ($db->more_results()){
					$db->next_result();
				}
				if ($results->num_rows) {
					$record = $results->fetch_object();
					$results->close();
					$this->ip = $record->ip;
					$this->apiKey = $record->api_key;
				}else{
					throw new NoResultFoundException('No api key found with IP ' . $this->ip);
				}
			}else{
				throw new SQLQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new FunctionCallException('IP not set for load.');
		}
	}

	public function loadByObj($record){
		$this->ip = $record->ip;
		$this->apiKey = $record->api_key;
	}

	public function get($prpty){
		if(isset($this->ip)){
			if(in_array($prpty, $this->acceptGet)){
				return $this->$prpty;
			}else{
				throw new OperationException('Property is not in accept get.');
			}
		}else{
			throw new FunctionCallException('IP not set for get.');
		}
	}

	public function delete(){
		if(isset($this->ip)){
			global $db;
			$procedure = buildProcedure('p_api_delete', $this->ip);
			if(($db->multi_query($procedure)) === TRUE){
				while ($db->more_results()){
					$db->next_result();
				}
			}else{
				throw new SQLQueryException('The database encountered an error. ' . $db->error);
			}
		}else{
			throw new FunctionCallException('ID not set for delete.');
		}
	}

	public static function getKeys(){
		global $db;
		$procedure = buildProcedure('p_get_api_keys');
		if(($db->multi_query($procedure)) === TRUE){
			$results = $db->store_result();
			while ($db->more_results()){
				$db->next_result();
			}
			$apiKeys = array();
			if ($results->num_rows) {
				while ($apiKeyObj = $results->fetch_object()) {
					$apiKey = new APIKey();
					$apiKey->loadByObj($apiKeyObj);
					$apiKeys[] = $apiKey;
				}
			}
			return $apiKeys;
		}else{
			throw new SQLQueryException('The database encountered an error. ' . $db->error);
		}
	}
}