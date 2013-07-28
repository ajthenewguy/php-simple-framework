<?php
/**
 * Bootstrap
 */
include('./lib/init.php');

$UsageInit = Debug::usage();

Document::instance()->Title('Overwritten Title');

echo Document::instance();


//echo $Document;