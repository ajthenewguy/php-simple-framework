<?php

/**
 * Description of Manifest
 *
 * @author Allen
 */
class Manifest {
	
	public $base;
	
	public $file;
	
	protected $manifest;


	public function __construct($base, $build = false) {
		$this->base = $base;
		$this->file = $this->base.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'manifest.json';
		$this->manifest = $this->load($build);
	}
	
	/**
	 * 
	 * @param type $className
	 * @return type
	 */
	public function getClassPath($className) {
		if(in_array($className, array_keys($this->manifest))) {
			return $this->manifest[$className];
		}
		return null;
	}
	
	/**
	 * 
	 * @return type
	 */
	public function load($build = false) {
		if(isset($this->file)) {
			if($build || !is_readable($this->file)) $this->generateFile();
			if(is_readable($this->file)) {
				$contents = file_get_contents($this->file);
				return json_decode($contents, true);
			} else {
				throw new Exception('Manifest cache file not readable.');
			}
		} else {
			throw new Exception('Manifest cache file location unknown.');
		}
	}
	
	/**
	 * 
	 * @return string|boolean
	 */
	public function generateFile() {
		$this->build();
		$contents = json_encode($this->manifest);
		return file_put_contents($this->file, $contents);
	}
	
	
	public function build() {
		$this->manifest = array();
		$Directory = new RecursiveDirectoryIterator($this->base);
		$Iterator = new RecursiveIteratorIterator($Directory);
		$Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

		foreach($Regex as $filepath) {
			$filepath = $filepath[0];
			$classes = static::getFileClasses($filepath);
			if(!empty($classes)) {
				foreach($classes as $class) {
					if(is_scalar($filepath)) {
						$this->manifest[$class] = $filepath;
					} else {
						throw new Exception('Non-scalar value cannot be used for array key.');
					}
				}
			}
		}
		
		return $this->manifest;
	}
	
	/**
	 * 
	 * @param type $filepath
	 * @param type $className
	 * @return type
	 */
	public static function hasClass($filepath, $className = null) {
		$classes = static::getFileClasses($filepath);
		if(!is_null($className)) return in_array($className, $classes);
		return !empty($classes);
	}
	
	/**
	 * 
	 * @param type $filepath
	 * @return type
	 */
	public static function getFileClasses($filepath) {
		$classes = array();
		$fp = fopen($filepath, 'r');
		$class = $buffer = '';
		$i = 0;
		while(!$class) {
			if(feof($fp)) break;
			$buffer .= fread($fp, 2048);
			if(preg_match('/class\s+(\w+)(.*)?\{/', $buffer, $matches)) {
				$classes[] = $matches[1];
				break;
			}
		}
		fclose($fp);
		return $classes;
	}
	
	public function registry() {
		return $this->manifest;
	}
}