<?
class ClanAPI extends API{
	public function searchClans($name=null, $warFrequency=null, $minMembers=null, $maxMembers=null, $minClanLevel=null, $minClanPoints=null){
		if(is_null($name)&&is_null($warFrequency)&&is_null($minMembers)&&is_null($maxMembers)&&is_null($minClanLevel)){
			throw new ArgumentException('At least one search parameter cannot be blank to search in Clash of Clans API.');
		}
		if(!is_null($name) && strlen($name)<3){
			throw new ArgumentException('Clan name must be at least 3 characters long to search in Clash of Clans API.');	
		}
		if(!is_null($minMembers) && !is_null($maxMembers) && $maxMembers<$minMembers){
			throw new ArgumentException('Max. Members must be greater than or equal to Min. Members to search in Clash of Clans API.');
		}
		$queryData = array();
		if(!is_null($name)) $queryData['name'] = $name;
		if(!is_null($warFrequency)) $queryData['warFrequency'] = $warFrequency;
		if(!is_null($minMembers)) $queryData['minMembers'] = $minMembers;
		if(!is_null($maxMembers)) $queryData['maxMembers'] = $maxMembers;
		if(!is_null($minClanLevel)) $queryData['minClanLevel'] = $minClanLevel;
		if(!is_null($minClanPoints)) $queryData['minClanPoints'] = $minClanPoints;
		$queryData['limit'] = 50;
		$extension = 'clans?' . http_build_query($queryData);
		return $this->request($extension)->items;
	}

	public function getClanInformation($tag){
		$extension = 'clans/' . urlencode($tag);
		return $this->request($extension);
	}

	public function listClanMembers($tag){
		$extension = 'clans/' . urlencode($tag) . '/members';
		return $this->request($extension);
	}

	public function getWarLog($tag){
		$extension = 'clans/' . urlencode($tag) . '/warlog';
		return $this->request($extension);
	}
}