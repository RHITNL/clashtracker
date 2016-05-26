<?
class clanApi extends api{
	public function searchClans($name=null, $warFrequency=null, $minMembers=null, $maxMembers=null, $minClanLevel=null, $minClanPoints=null){
		if(is_null($name)&&is_null($warFrequency)&&is_null($minMembers)&&is_null($maxMembers)&&is_null($minClanLevel)){
			throw new illegalArgumentException('At least one search parameter cannot be blank to search in Clash of Clans API.');
		}
		if(!is_null($name) && strlen($name)<3){
			throw new illegalArgumentException('Clan name must be at least 3 characters long to search in Clash of Clans API.');	
		}
		if(!is_null($minMembers) && !is_null($maxMembers) && $maxMembers<$minMembers){
			throw new illegalArgumentException('Max. Members must be greater than or equal to Min. Members to search in Clash of Clans API.');
		}
		$query_data = array();
		if(!is_null($name)) $query_data['name'] = $name;
		if(!is_null($warFrequency)) $query_data['warFrequency'] = $warFrequency;
		if(!is_null($minMembers)) $query_data['minMembers'] = $minMembers;
		if(!is_null($maxMembers)) $query_data['maxMembers'] = $maxMembers;
		if(!is_null($minClanLevel)) $query_data['minClanLevel'] = $minClanLevel;
		if(!is_null($minClanPoints)) $query_data['minClanPoints'] = $minClanPoints;
		$query_data['limit'] = 50;
		$extension = 'clans?' . http_build_query($query_data);
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

	private function fake($tag){
		$clan = new clan($tag);
		$clanInfo = new StdClass();
		$clanInfo->name = $clan->get('name');
		$clanInfo->type = convertBackType($clan->get('clanType'));
		$clanInfo->description = $clan->get('description');
		$clanInfo->warFrequency = convertBackFrequency($clan->get('warFrequency'));
		$clanInfo->requiredTrophies = $clan->get('minimumTrophies');
		$clanInfo->members = $clan->get('members');
		$clanInfo->clanPoints = $clan->get('clanPoints');
		$clanInfo->clanLevel = $clan->get('clanLevel');
		$clanInfo->warWins = $clan->get('warWins');
		$clanInfo->badgeUrls = new StdClass();
		$clanInfo->badgeUrls->small = $clan->get('badgeUrl');
		$clanInfo->location = new StdClass();
		$clanInfo->location->name = $clan->get('location');
		$clanInfo->memberList = array();
		$members = $clan->getMembers();
		foreach ($members as $member) {
			$apiMember = new StdClass();
			$apiMember->tag = $member->get('tag');
			$apiMember->name = $member->get('name');
			if($apiMember->name == 'Barby Doll' || $apiMember->name == 'Johnny Boy'){
				continue;
			}
			$apiMember->role = convertBackRank($member->get('clanRank'));
			$apiMember->expLevel = $member->get('level');
			$apiMember->trophies = $member->get('trophies');
			$apiMember->donations = $member->get('donations');
			$apiMember->donationsReceived = $member->get('received');
			$apiMember->league = new StdClass();
			$apiMember->league->iconUrls = new StdClass();
			$apiMember->league->iconUrls->small = $member->get('leagueUrl');
			$clanInfo->memberList[] = $apiMember;
		}
		$fakeNewMember = new StdClass();
		$fakeNewMember->tag = '#9DG28SH';
		$fakeNewMember->name = 'Barby Doll';
		$fakeNewMember->role = 'coLeader';
		$fakeNewMember->expLevel = 93;
		$fakeNewMember->trophies = 2402;
		$fakeNewMember->donations = 4120;
		$fakeNewMember->donationsReceived = 3203;
		$fakeNewMember->league = new StdClass();
		$fakeNewMember->league->iconUrls = new StdClass();
		$fakeNewMember->league->iconUrls->small = 'https://api-assets.clashofclans.com/leagues/72/kSfTyNNVSvogX3dMvpFUTt72VW74w6vEsEFuuOV4osQ.png';
		$clanInfo->memberList[] = $fakeNewMember;
		$fakeNewMember = new StdClass();
		$fakeNewMember->tag = '#28S0GRRR';
		$fakeNewMember->name = 'Johnny Boy';
		$fakeNewMember->role = 'admin';
		$fakeNewMember->expLevel = 34;
		$fakeNewMember->trophies = 1230;
		$fakeNewMember->donations = 142;
		$fakeNewMember->donationsReceived = 412;
		$fakeNewMember->league = new StdClass();
		$fakeNewMember->league->iconUrls = new StdClass();
		$fakeNewMember->league->iconUrls->small = 'https://api-assets.clashofclans.com/leagues/72/nvrBLvCK10elRHmD1g9w5UU1flDRMhYAojMB2UbYfPs.png';
		$clanInfo->memberList[] = $fakeNewMember;
		return $clanInfo;
	}
}