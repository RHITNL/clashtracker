<?php
class api{
	private $headers;

	public function __construct(){
		$this->ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
	}

	protected function request($extension){
		$url = 'https://api.clashofclans.com/v1/' . $extension;
		$curl = curl_init();
		$creds = null;
		if(!DEVELOPMENT){
			$creds = $this->determineProxy();
			$proxyUrl = $creds['host'].":".$creds['port'];
			$proxyAuth = $creds['user'].":".$creds['pass'];
			curl_setopt($curl, CURLOPT_PROXY, $proxyUrl);
			curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyAuth);
		}
		$apiKey = new apiKey($this->ip);
		$this->headers = array('Accept: application/json', 'authorization: Bearer ' . $apiKey->get('apiKey'));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$rawResult = curl_exec($curl);
		try{
			$this->recordRequest($creds, $url, $rawResult, $this->ip, $apiKey->get('apiKey'));
		}catch(Exception $e){
			error_log($e->getMessage());
			//ignore, I still want the response returned even if there is a problem recording the request
		}
		$result = json_decode($rawResult);
		curl_close($curl);
		if($result->reason){
			throw new apiException($result->reason, $result->message);
		}
		if(isset($result)){
			return $result;
		}else{
			throw new apiException('Nothing was returned from Clash of Clans API.', 'No Result');
		}
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
					if($limit - $count > 0){
						$env = $proxyObj->env;
						$this->updateProxyCount($env, $count+1);
						$this->ip = $proxyObj->ip;
						return parse_url($env);
					}
				}
			}
			throw new apiException('noRequestsLeft', 'The request limits for all available proxies have been reached.');
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}
	}

	private function recordRequest($proxy, $url, $response, $ip, $auth){
		global $db;
		$date = date('Y-m-d H:i:s', time());
		$procedure = buildProcedure('p_proxy_request_create', $proxy, $url, $response, $ip, $auth, $date);
		if(($db->multi_query($procedure)) === TRUE){
			while ($db->more_results()){
				$db->next_result();
			}
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}
	}

	public function updateProxyCount($env, $count){
		global $db;
		$procedure = buildProcedure('p_proxy_request_count_update', $env, $count, date('F'));
		if(($db->multi_query($procedure)) === TRUE){
			while ($db->more_results()){
				$db->next_result();
			}
		}else{
			$message = "Hello Alex!\nAn error occurred when trying to update the count for $env. We tried to update the count to $count but the database gave the following error:\n" . $db->error;
			$message .= "\nSorry for the inconvenience.\nCheers,\nClash Tracker";
			email('alexinmann@gmail.com', 'Update Proxy Request Count Error', $message, 'error_logging@clashtracker.ca');
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}
	}

	public static function getProxyInformation(){
		global $db;
		$procedure = buildProcedure('p_proxy_request_get');
		if(($db->multi_query($procedure)) === TRUE){
			$results = $db->store_result();
			while ($db->more_results()){
				$db->next_result();
			}
			$currentMonth = date('F');
			$proxies = array();
			if ($results->num_rows) {
				while ($proxyObj = $results->fetch_object()) {
					$proxy = new stdClass();
					$proxy->env = $proxyObj->env;
					$proxy->month = $proxyObj->month;
					$proxy->limit = $proxyObj->monthly_limit;
					$proxy->ip = $proxyObj->ip;
					if($proxy->month != $currentMonth){
						$proxy->month = $currentMonth;
						$proxy->count = 0;
						$api = new api();
						$api->updateProxyCount($proxy->env, $proxy->count);
					}else{
						$proxy->count = $proxyObj->count;
					}
					$proxies[] = $proxy;
				}
			}
			return $proxies;
		}else{
			throw new illegalQueryException('The database encountered an error. ' . $db->error);
		}
	}
}