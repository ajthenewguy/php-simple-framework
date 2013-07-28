<?php
/**
 * Initialize application bootstrap
 */
define('BASE_PATH', dirname(dirname(__FILE__)));
$scandir = realpath('../');
if(is_readable(realpath('../').DIRECTORY_SEPARATOR.'_config.php')) {
	include(realpath('../').DIRECTORY_SEPARATOR.'_config.php');
} elseif(is_readable(realpath('./').DIRECTORY_SEPARATOR.'_config.php')) {
	include(realpath('./').DIRECTORY_SEPARATOR.'_config.php');
}
if(!isset($database)) {
	$database = array(
		'driver' => 'mysql'
	);
}

include(BASE_PATH.'/lib/core/Manifest.php');
$Manifest = new Manifest(BASE_PATH, (isset($_GET['flush'])));
include(BASE_PATH.'/lib/function/init.php');

Debug::start_timer();