<?
require(__DIR__ . '/../config/functions.php');

$players = player::getPlayers(null, 10000);
foreach ($players as $player) {
	$player->set('firstAttackTotalStars', 0);
	$player->set('firstAttackNewStars', 0);
	$player->set('secondAttackTotalStars', 0);
	$player->set('secondAttackNewStars', 0);
	$player->set('attacksUsed', 0);
	$player->set('starsOnDefence', 0);
	$player->set('numberOfDefences', 0);
	$player->set('numberOfWars', 0);
	$player->set('rankAttacked', 0);
	$player->set('rankDefended', 0);
}

$clans = clan::getClans(null, 10000);
foreach ($clans as $clan) {
	$wars = $clan->getWars();
	if(count($wars)>1){
		foreach ($wars as $war) {
			if($war != $wars[0]){
				$war->getAttacks();
				$players = $war->getPlayers($clan);
				foreach ($players as $player) {
					$attacks = $war->getPlayerAttacks($player);
					$player->set('attacksUsed', $player->get('attacksUsed') + count($attacks));
					$firstAttack = $attacks[0];
					if(isset($firstAttack)){
						$player->set('firstAttackTotalStars', $player->get('firstAttackTotalStars') + $firstAttack['totalStars']);
						$player->set('firstAttackNewStars', $player->get('firstAttackNewStars') + $firstAttack['newStars']);
						$diff = $firstAttack['attackerRank'] - $firstAttack['defenderRank'];
						$player->set('rankAttacked', $player->get('rankAttacked') + $diff);
					}
					$secondAttack = $attacks[1];
					if(isset($secondAttack)){
						$player->set('secondAttackTotalStars', $player->get('secondAttackTotalStars') + $secondAttack['totalStars']);
						$player->set('secondAttackNewStars', $player->get('secondAttackNewStars') + $secondAttack['newStars']);
						$diff = $secondAttack['attackerRank'] - $secondAttack['defenderRank'];
						$player->set('rankAttacked', $player->get('rankAttacked') + $diff);
					}
					$defences = $war->getPlayerDefences($player->get('id'));
					$stars = 0;
					foreach ($defences as $defence) {
						$stars += $defence['newStars'];
						$diff = $defence['defenderRank'] - $defence['attackerRank'];
						$player->set('rankDefended', $player->get('rankDefended') + $diff);
					}
					$player->set('numberOfDefences', $player->get('numberOfDefences') + count($defences));
					$player->set('starsOnDefence', $player->get('starsOnDefence') + $stars);
					$player->set('numberOfWars', $player->get('numberOfWars') + 1);
				}
			}
		}
	}
}