<?
require(__DIR__ . '/../config/functions.php');
$fileName = $argv[1];
$file = file_get_contents($fileName);
$lines = explode("\n", $file);
foreach ($lines as $line) {
	$tokens = explode(" ", $line);
	$ip = $tokens[0];
	$key = $tokens[1];
	try{
		$apiKey = new apiKey($ip);
		$apiKey->delete();
	}catch(Exception $e){
		//do nothing
	}
	$apiKey = new apiKey();
	$apiKey->create($ip, $key);
}