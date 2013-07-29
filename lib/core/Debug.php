<?php

/**
 * Description of Debug
 *
 * @author Allen
 */
class Debug {
	
	private static $time_start;
	
	public function __construct() {
		static::start_timer();
	}
	
	public static function start_timer() {
		return static::$time_start = microtime(true);
	}
	
	public static function get_time_elapse($decimals = 5) {
		if(!isset(static::$time_start)) static::start_timer();
		$float = microtime(true) - static::$time_start;
		return rtrim(number_format($float, $decimals), '0') . ' sec.';
	}
	
	public static function stop_timer() {
		$return = $static::time_start;
		unset(static::$time_start);
		return $return;
	}
	
	public static function get_memory_usage() {
		return memory_get_usage();
	}
	
	public static function usage($metadata = array('time', 'usage')) {
		$return = '';
		$header = array();
		$data = array();
		$Table = new Table();
		$Table->properties(array('border' => 1, 'width' => 200));
		foreach($metadata as $meta) {
			switch (strtolower($meta)) {
				case 'time':
				case 'secs':
				case 'elapse':
				case 'seconds':
					$header[] = 'Seconds';
					$data[] = static::get_time_elapse();
					break;
				case 'usage':
				case 'memory':
				case 'memory_usage':
					$header[] = 'Memory';
					$data[] = Convert::bytes2nice(static::get_memory_usage());
					break;
			}
		}
		$Table->setHeader($header);
		$Table->setBody($data);
		return $Table;
	}
}

?>
