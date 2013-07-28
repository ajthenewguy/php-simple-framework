<?php

/**
 * Description of Site
 *
 * @author Allen
 */
class Site {
	
	private $Name;
	
	public function __construct($name) {
		$this->Name = $name;
	}
	
	public function Name() {
		return $this->Name;
	}
	
}

?>
