<?php
/**
 * Description of Checksum
 *
 * @author Allen
 */
class Checksum {
	
	public static function getDirectoryChecksum($dir, $ignoreDir = array()) {
		$files = static::getFiles($dir, $ignoreDir);
		$value = '';
		foreach($files as $file) {
			$value .= filemtime($file).filesize($file);
		}
		return static::getSum($value);
	}
	
	public static function getSum($value) {
		return md5(serialize($value));
	}
	
	public static function getFiles($path, $ignoreDir = array()) {
		$return = array();
		
		/* // GET FULL PATH
		$ignoreDir = array_map(function ($var) use($path) {
			return $path . DIRECTORY_SEPARATOR . $var;
		}, $ignoreDir);
		
		print_r($ignoreDir);
		*/
		
		$ite = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
		foreach( new RecursiveIteratorIterator($ite) as $filename => $object) {
			if(in_array($filename, $ignoreDir))
				continue;
			
			foreach($ignoreDir as $dir) {
				if(strpos($filename, $dir) !== false)
					continue(2);
			}
			
			$return[] = $filename;
		}
		
		return $return;
	}
}