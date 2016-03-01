<?
require(__DIR__ . '/../config/functions.php');
$tag = $argv[1];
$goldRawData = file_get_contents("goldBackFillData.txt");
$goldRawData = explode("\n", $goldRawData);
$goldDates = explode("\t", $goldRawData[0]);
$gold = array();
foreach ($goldRawData as $i => $goldPlayerRow) {
	if($i<1) continue;//skipping row of dates
	$goldPlayerRow = explode("\t", $goldPlayerRow);
	$ids = player::getIdsForPlayersWithName($goldPlayerRow[0]);
	if(count($ids)==1){
		$id = $ids[0];
		$curr = array();
		foreach ($goldPlayerRow as $j => $goldValue) {
			if($j<4) continue;//skipping name, last week, average, and best week
			$goldValue = str_replace(',', '', $goldValue);
			$curr[] = intval($goldValue);
		}
		$gold[$id] = $curr;
	}else{
		$clan = new clan($tag);
		foreach ($ids as $id) {
			$player = new player($id);
			$playerClan = $player->getMyClan();
			if(isset($playerClan)){
				if($playerClan->get('id') == $clan->get('id')){
					$curr = array();
					foreach ($goldPlayerRow as $j => $goldValue) {
						if($j<4) continue;//skipping name, last week, average, and best week
						$goldValue = str_replace(',', '', $goldValue);
						$curr[] = intval($goldValue);
					}
					$gold[$id] = $curr;
				}
			}
		}
	}
}
$elixirRawData = file_get_contents("elixirBackFillData.txt");
$elixirRawData = explode("\n", $elixirRawData);
$elixirDates = explode("\t", $elixirRawData[0]);
$elixir = array();
foreach ($elixirRawData as $i => $elixirPlayerRow) {
	if($i<1) continue;//skipping row of dates
	$elixirPlayerRow = explode("\t", $elixirPlayerRow);
	$ids = player::getIdsForPlayersWithName($elixirPlayerRow[0]);
	if(count($ids)==1){
		$id = $ids[0];
		$curr = array();
		foreach ($elixirPlayerRow as $j => $elixirValue) {
			if($j<4) continue;//skipping name, last week, average, and best week
			$elixirValue = str_replace(',', '', $elixirValue);
			$curr[] = intval($elixirValue);
		}
		$elixir[$id] = $curr;
	}else{
		$clan = new clan($tag);
		foreach ($ids as $id) {
			$player = new player($id);
			$playerClan = $player->getMyClan();
			if(isset($playerClan)){
				if($playerClan->get('id') == $clan->get('id')){
					$curr = array();
					foreach ($elixirPlayerRow as $j => $elixirValue) {
						if($j<4) continue;//skipping name, last week, average, and best week
						$elixirValue = str_replace(',', '', $elixirValue);
						$curr[] = intval($elixirValue);
					}
					$elixir[$id] = $curr;
				}
			}
		}
	}
}
$darkElixirRawData = file_get_contents("darkElixirBackFillData.txt");
$darkElixirRawData = explode("\n", $darkElixirRawData);
$darkElixirDates = explode("\t", $darkElixirRawData[0]);
$darkElixir = array();
foreach ($darkElixirRawData as $i => $darkElixirPlayerRow) {
	if($i<1) continue;//skipping row of dates
	$darkElixirPlayerRow = explode("\t", $darkElixirPlayerRow);
	$ids = player::getIdsForPlayersWithName($darkElixirPlayerRow[0]);
	if(count($ids)==1){
		$id = $ids[0];
		$curr = array();
		foreach ($darkElixirPlayerRow as $j => $darkElixirValue) {
			if($j<4) continue;//skipping name, last week, average, and best week
			$darkElixirValue = str_replace(',', '', $darkElixirValue);
			$curr[] = intval($darkElixirValue);
		}
		$darkElixir[$id] = $curr;
	}else{
		$clan = new clan($tag);
		foreach ($ids as $id) {
			$player = new player($id);
			$playerClan = $player->getMyClan();
			if(isset($playerClan)){
				if($playerClan->get('id') == $clan->get('id')){
					$curr = array();
					foreach ($darkElixirPlayerRow as $j => $darkElixirValue) {
						if($j<4) continue;//skipping name, last week, average, and best week
						$darkElixirValue = str_replace(',', '', $darkElixirValue);
						$curr[] = intval($darkElixirValue);
					}
					$darkElixir[$id] = $curr;
				}
			}
		}
	}
}

$goldDates = array_reverse($goldDates);
foreach ($goldDates as $i => $date) {
	$goldDates[$i] = date('Y-m-d H:m:s', strtotime($date) . " 20:00:00");
}
foreach ($gold as $id => $loot) {
	$loot = array_reverse($loot);
	$diff = count($goldDates) - count($loot);
	$player = new player($id);
	$removed = false;
	foreach ($loot as $i => $value) {
		if($value != 0){
			if(!$removed){
				$player->removeAllGoldValues($goldDates[$i+$diff]);
				$removed = true;
			}
			$player->recordGold($value, $goldDates[$i+$diff]);		
		}
	}
}
$elixirDates = array_reverse($elixirDates);
foreach ($elixirDates as $i => $date) {
	$elixirDates[$i] = date('Y-m-d H:m:s', strtotime($date) . " 20:00:00");
}
foreach ($elixir as $id => $loot) {
	$loot = array_reverse($loot);
	$diff = count($elixirDates) - count($loot);
	$player = new player($id);
	$removed = false;
	foreach ($loot as $i => $value) {
		if($value != 0){
			if(!$removed){
				$player->removeAllElixirValues($elixirDates[$i+$diff]);
				$removed = true;
			}
			$player->recordElixir($value, $elixirDates[$i+$diff]);		
		}
	}
}
$darkElixirDates = array_reverse($darkElixirDates);
foreach ($darkElixirDates as $i => $date) {
	$darkElixirDates[$i] = date('Y-m-d H:m:s', strtotime($date) . " 20:00:00");
}
foreach ($darkElixir as $id => $loot) {
	$loot = array_reverse($loot);
	$diff = count($darkElixirDates) - count($loot);
	$player = new player($id);
	$removed = false;
	foreach ($loot as $i => $value) {
		if($value != 0){
			if(!$removed){
				$player->removeAllDarkElixirValues($darkElixirDates[$i+$diff]);
				$removed = true;
			}
			$player->recordDarkElixir($value, $darkElixirDates[$i+$diff]);		
		}
	}
}