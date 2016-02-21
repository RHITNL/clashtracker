<?
require(__DIR__ . '/../config/functions.php');
$clans = clan::getClans(1000000);
foreach ($clans as $clan) {
	$clan->set('tag', correctTag($clan->get('tag')));
}
$players = player::getPlayers(1000000);
foreach ($players as $player) {
	$player->set('tag', correctTag($player->get('tag')));
}