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
		// $quotaguard_env = getenv("QUOTAGUARDSTATIC_URL");
		$quotaguard_env = 'http://quotaguard4826:ba0ab104caa7@us-east-1-static-hopper.quotaguard.com:9293';
		$quotaguard = parse_url($quotaguard_env);

		$proxyUrl = $quotaguard['host'].":".$quotaguard['port'];
		$proxyAuth = $quotaguard['user'].":".$quotaguard['pass'];

		$curl = curl_init();
		$url = 'https://api.clashofclans.com/v1/' . $extension;
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_PROXY, $proxyUrl);
		curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyAuth);
		$result = json_decode(curl_exec($curl));
		curl_close($curl);
		if($result->reason){
			throw new apiException($result->reason . ' - ' .$result->message);
		}
		return $result;
	}
}