<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function __autoload($className) {
	global $Manifest;
	$path = $Manifest->getClassPath($className);
	if(is_null($path)) $Manifest->generateFile();
	if(!is_null($path)) {
		require_once($path);
	}
}
