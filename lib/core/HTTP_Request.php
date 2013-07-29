<?php

/**
 * Description of HTTP_Request
 *
 * @author Allen
 */
class HTTP_Request {
	
	public static function method() {
		return $_SERVER['REQUEST_METHOD'];
	}
	
	public static function uri() {
		return $_SERVER['REQUEST_URI'];
	}
}

?>
