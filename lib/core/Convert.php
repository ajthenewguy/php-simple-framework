<?php

/**
 * Description of Convert
 *
 * @author Allen
 */
class Convert {
	
	/**
	 * Convert supplied integer value (bytes assumed) to human readable.
	 * 
	 * @param int $bytes
	 * @return string
	 */
	public static function bytes2nice($size, $precision = 2) {
		$base = log($size) / log(1024);
		$suffixes = array('', 'k', 'M', 'G', 'T');
		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}
}

?>
