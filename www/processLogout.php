<?
require('init.php');
unset($_SESSION['user_id']);
$referer = $_SERVER['HTTP_REFERER'];
$host = $_SERVER['HTTP_HOST'];
$location = str_replace('http://'.$host, '', $referer);
header('Location: ' . $location);
exit;