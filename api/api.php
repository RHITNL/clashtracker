<?
class api{
	private $key;
	private $host;
	private $headers;

	public function __construct(){
		$apiKey = new apiKey(IP);
		$this->key = $apiKey->get('apiKey');
		$this->headers = array('Accept: application/json', 'authorization: Bearer ' . $this->key);
	}

	protected function request($extension){
		$curl = curl_init();
		$url = 'https://api.clashofclans.com/v1/' . $extension;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if(!DEVELOPMENT){
			$creds = $this->determineProxy();
			$proxyUrl = $creds['host'].":".$creds['port'];
			$proxyAuth = $creds['user'].":".$creds['pass'];
			curl_setopt($curl, CURLOPT_PROXY, $proxyUrl);
			curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyAuth);
		}
		$result = json_decode(curl_exec($curl));
		curl_close($curl);
		if($result->reason){
			throw new apiException($result->reason, $result->message);
		}
		return $result;
	}

	private function determineProxy(){
		global $db;
		$procedure = buildProcedure('p_proxy_request_get');
		if(($db->multi_query($procedure)) === TRUE){
			$results = $db->store_result();
			while ($db->more_results()){
				$db->next_result();
			}
			$currentMonth = date('F');
			if ($results->num_rows) {
				while ($proxyObj = $results->fetch_object()) {
					$month = $proxyObj->month;
					if($month != $currentMonth){
						$count = 0;
					}else{
						$count = $proxyObj->count;
					}
					$limit = $proxyObj->monthly_limit;
					if($limit - $count >= 0){
						$env = $proxyObj->env;
						$this->updateProxyCount($env, $count+1);
						return parse_url($env);
					}
				}
			}
			throw new apiException('noRequestsLeft', 'The request limits for all available proxies have been reached.');
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}
	}

	private function updateProxyCount($env, $count){
		global $db;
		$procedure = buildProcedure('p_proxy_request_count_update', $env, $count, date('F'));
		if(($db->multi_query($procedure)) === TRUE){
			while ($db->more_results()){
				$db->next_result();
			}
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}	
	}
}