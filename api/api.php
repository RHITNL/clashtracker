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
		$result = json_decode(curl_exec($curl));
		curl_close($curl);
		if($result->reason){
			throw new apiException($result->reason . ' - ' .$result->message);
		}
		return $result;
	}
}