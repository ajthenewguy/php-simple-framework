<?php
/**
 * Initialize application bootstrap
 */
define('BASE_PATH', dirname(__DIR__));
define('CACHE_PATH', BASE_PATH.DIRECTORY_SEPARATOR.'cache');
define('LIBRARY_PATH', __DIR__);
define('VENDOR_PATH', LIBRARY_PATH.DIRECTORY_SEPARATOR.'vendor');
define('CHECKSUM_FILE', 'checksum.md5');
define('CHECK_SUM', false);

date_default_timezone_set('America/Los_Angeles');

if(is_readable(realpath('../').DIRECTORY_SEPARATOR.'_config.php')) {
	include(realpath('../').DIRECTORY_SEPARATOR.'_config.php');
} elseif(is_readable(realpath('./').DIRECTORY_SEPARATOR.'_config.php')) {
	include(realpath('./').DIRECTORY_SEPARATOR.'_config.php');
}
if(!isset($database)) {
	$database = array(
		'driver' => 'mysql',
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'phpSimpleFramework'
	);
}
if(!empty($database)) {
	define('DB_HOST', $database['host']);
	define('DB_USER', $database['username']);
	define('DB_PASS', $database['password']);
	define('DB_NAME', $database['database']);
}

// Include manifest
include(BASE_PATH.'/lib/core/Manifest.php');
include(BASE_PATH.'/lib/function/filesystem.php');
include(BASE_PATH.'/lib/function/utility.php');
include(BASE_PATH.'/lib/function/init.php');
include(BASE_PATH.'/lib/function/http.php');
$FLUSH = isset($_GET['flush']);
$Manifest = new Manifest(BASE_PATH, $FLUSH);

function __autoload($className) {
	global $Manifest;
	$path = $Manifest->getClassPath($className);
	if(!is_null($path)) {
		require_once($path);
	} else {
		die('Class '.$className.' not found. <a href="?flush=1">Flush</a>');
	}
}

if($FLUSH) {
	generate_checksum_file();
}
if(CHECK_SUM) {
	if(!compare_checksum()) {
		die('<br>Don\'t forget to <a href="?flush=1">flush</a>');
	}
}




if($FLUSH) {
	refresh_without('flush');
}

Debug::start_timer();