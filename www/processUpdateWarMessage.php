<?
require('init.php');
require('session.php');

$warId = $_POST['warId'];
try{
	$war = new War($warId);
	$warId = $war->get('id');
}catch(Exception $e){
	$error = 'No war with id ' . $warId . ' found.';
	echo json_encode(array('error' => $error));
	exit;
}

$clanId = $_POST['clanId'];
try{
	$clan = new Clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$error = 'No clan with id ' . $warId . ' found.';
	echo json_encode(array('error' => $error));
	exit;	
}
$userCanEditMessage = $war->isEditable() && userHasAccessToUpdateClan($clan);
if(!$userCanEditMessage){
	$error = NO_ACCESS;
	echo json_encode(array('error' => $error));
	exit;
}


$message = $_POST['message'];
if($message == $war->getMessage($clanId)){
	echo json_encode(array('message' => $message));
	exit;
}

try{
	$war->set('clanMessage', $message, $clanId);
	echo json_encode(array('message' => $message));
}catch(Exception $e){
	echo json_encode(array('error' => $e->getMessage()));
}
exit;