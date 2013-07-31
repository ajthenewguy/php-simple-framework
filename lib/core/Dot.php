<?php
/**
 * A PHP class to access a PHP array via dot notation
 * (Agavi http://www.agavi.org was the inspiration).
 *
 * This was hacked in to an existing codebase hence the
 * global config array variable.
 *
 * The global $config variable should be an associative array like:
 * $config = array(
 *      'database' => array(
 *          'host' => 'localhost',
 *          'user' => 'Treffynnon',
 *          'pass' => 'password',
 *          'db'   => 'database',
 *      ),
 * );
 *
 * But it can be as deeply nested as you like. An example of how
 * to access the values  for the example config array above is
 * given below.
 *
 * @example $host = Dot::get('database.host');
 * @author Simon Holywell
 */
class Dot {
	
	public static function get(array &$data, $path){
		$keys = explode('.', $path);
		foreach($keys as $k){
			if(isset($data[$k])) {
				$data =& $data[$k];
			} else {
				return NULL;
			}
		}
		return $data;
	}
	
	public static function set(array &$data, $path, $value){
		$keys = explode('.', $path);
		$last = array_pop($keys);
		foreach($keys as $k){
			if(isset($data[$k]) && is_array($data[$k])){
				$data =& $data[$k];
			} else {
				$data[$k] = array();
				$data =& $data[$k];
			}
		}
		$data[$last] = $value;
	}
	
	public static function dcount(array &$data, $path){
		$keys = explode('.', $path);
		$last = array_pop($keys);
		foreach($keys as $k){
			if(isset($data[$k]) && is_array($data[$k])){
				$data =& $data[$k];
			} else {
				return null;
			}
		}
		return isset($data[$last]) && is_array($data[$last]) ? count($data[$last]) : null;
	}
	
	public static function del(array &$data, $path){
		$keys = explode('.', $path);
		$last = array_pop($keys);
		foreach($keys as $k){
			if(isset($data[$k]) && is_array($data[$k])){
				$data =& $data[$k];
			} else {
				return;
			}
		}
		unset($data[$last]);
	}
}