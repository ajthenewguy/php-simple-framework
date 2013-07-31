<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Get relative path to web root
 * 
 * @return string
 */
function root() {
	return str_repeat( '../', substr_count( $_SERVER['REQUEST_URI'], '/' ) - substr_count( BASE_PATH, '/' ) + 1 );
}

/**
 * Get full system path
 * 
 * @return string
 */
function froot() {
	return BASE_PATH;
}