<?

// Time values in seconds
define('MINUTE', 60);
define('HOUR', 3600);
define('DAY', 86400);
define('WEEK', 604800);
define('MONTH', 2629743);
define('YEAR', 31556926);

define('NO_ACCESS', 'You do not have access to that page.');

$ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
error_log(print_r($ip, true));
define('IP', $ip);

$dir = str_replace('/config', '', __DIR__);
define('APPROOT', $dir);

date_default_timezone_set('UTC');
session_start();

$classes = scandir(APPROOT . '/classes');
foreach ($classes as $class) {
	if(strpos($class, '.php') !== false){
		include(APPROOT . '/classes/' . $class);
	}
}
$exceptions = scandir(APPROOT . '/exceptions');
foreach ($exceptions as $exception) {
	if(strpos($exception, '.php') !== false){
		include(APPROOT . '/exceptions/' . $exception);
	}
}
$apiClasses = scandir(APPROOT . '/api');
foreach ($apiClasses as $apiClass) {
	if(strpos($apiClass, '.php') !== false){
		include(APPROOT . '/api/' . $apiClass);
	}
}

$development = $_SERVER['REMOTE_ADDR'] == '127.0.0.1';
define('DEVELOPMENT', $development);
if(DEVELOPMENT){
	//	Configuration for the MySQL Local Server
	define('DBHOST', 'localhost');
	define('DBNAME', 'clash');
	define('DBUSER', 'clash');
	define('DBPASS', 'cl@sh!');
}else{
	$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
	define('DBHOST', $url["host"]);
	define('DBNAME', substr($url["path"], 1));
	define('DBUSER', $url["user"]);
	define('DBPASS', $url["pass"]);
}

// Create connection
$db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);