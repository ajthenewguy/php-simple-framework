<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function redirect($Location, $query = array()) {
	if($Location == 'this') $Location = $_SERVER['REQUEST_URI'];
	$Location = strtok($_SERVER['REQUEST_URI'],'?').(!empty($query) ? '?'.http_build_query($query) : '');
	header("Location: ".$Location);
	die();
}

function redirect_without($Location, $query) {
	if($Location == 'this') $Location = $_SERVER['REQUEST_URI'];
	if(!is_array($query)) $query = array($query);
	foreach($_GET as $param => $val) {
		if(in_array($param, $query)) {
			unset($_GET[$param]);
		}
	}
	return redirect($Location, $_GET);
}

function refresh() {
	return redirect();
}

function refresh_without($query) {
	redirect_without($_SERVER['REQUEST_URI'], $query);
}