<?php

/**
 * Description of Singleton
 *
 * @author Allen
 */
class Singleton extends Object {
	
	protected static $instance;

	public function __construct() {
		parent::__construct(func_get_args());
	}
	
	public static function instance() {
		if(!isset(static::$instance)) static::$instance = static::create();
		return static::$instance;
	}
}