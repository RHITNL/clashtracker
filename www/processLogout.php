<?
require('init.php');
unset($_SESSION['user_id']);
// $referer = $_SERVER['HTTP_REFERER'];
header('Location: /login.php');
exit;