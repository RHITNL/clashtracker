<?
class clanApi extends api{
	public function searchClans($name=null, $warFrequency=null, $minMembers=null, $maxMembers=null, $minClanLevel=null){
		if(is_null($name)&&is_null($warFrequency)&&is_null($minMembers)&&is_null($maxMembers)&&is_null($minClanLevel)){
			throw new illegalArgumentException('At least one search parameter cannot be blank.');
		}
		if(!is_null($name) && strlen($name)<0){
			throw new illegalArgumentException('Clan name must be at least 3 characters long.');	
		}
		$query_data = array();
		if(!is_null($name)) $query_data['name'] = $name;
		if(!is_null($warFrequency)) $query_data['warFrequency'] = $warFrequency;
		if(!is_null($minMembers)) $query_data['minMembers'] = $minMembers;
		if(!is_null($maxMembers)) $query_data['maxMembers'] = $maxMembers;
		if(!is_null($minClanLevel)) $query_data['minClanLevel'] = $minClanLevel;
		$extension = 'clans?' . http_build_query($query_data);
		return $this->request($extension);
	}

	public function getClanInformation($tag){
		$extension = 'clans/' . urlencode($tag);
		return $this->request($extension);
	}

	public function listClanMembers($tag){
		$extension = 'clans/' . urlencode($tag) . '/members';
		return $this->request($extension);
	}
}