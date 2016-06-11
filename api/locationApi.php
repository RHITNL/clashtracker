<?
class LocationAPI extends API{
	public function getLocations(){
		$extension = 'locations';
		return $this->request($extension);
	}

	public function getLocation($id){
		$extension = 'locations/' . urlencode($id);
		return $this->request($extension);
	}

	public function getLocationRankingClans($id){
		$extension = 'locations/' . urlencode($id) . '/rankings/clans';
		return $this->request($extension)->items;
	}

	public function getLocationRankingPlayers($id){
		$extension = 'locations/' . urlencode($id) . '/rankings/players';
		return $this->request($extension)->items;
	}
}