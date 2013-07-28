<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function compare_checksum($regenerate = false) {
	if($regenerate) generate_checksum_file();
	$live = get_live_checksum();
	$cached = get_cached_checksum();
	if($live !== $cached) {
		echo $live.'<br>';
		echo $cached.'<br>';
	}
	return $live == $cached;
}

function get_live_checksum() {
	$ignoreDir = array(
		'\\.git', '\\cache', '\\nbproject',
		 '/.git',  '/cache',  '/nbproject'
	);
	return Checksum::getDirectoryChecksum(BASE_PATH, $ignoreDir);
}

function get_cached_checksum() {
	$path = CACHE_PATH.DIRECTORY_SEPARATOR.CHECKSUM_FILE;
	if(!file_exists($path)) {
		generate_checksum_file();
	}
	return file_get_contents($path);
}

function generate_checksum_file($data = null) {
	$path = CACHE_PATH.DIRECTORY_SEPARATOR.CHECKSUM_FILE;
	$ignoreDir = array(
		'\\.git', '\\cache', '\\nbproject',
		 '/.git',  '/cache',  '/nbproject'
	);
	if(file_exists($path)) unlink($path);
	if(empty($data)) {
		$data = Checksum::getDirectoryChecksum(BASE_PATH, $ignoreDir);
	}
	return file_put_contents($path, $data);
}