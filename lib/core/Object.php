<?php

/**
 * Description of Object
 *
 * @author Allen
 */
class Object extends ArrayObject {
	
	/**
	 * Construct new Object
	 * 
	 * @param array|object $input
	 * @param bitmask $flags
	 * @param string $iterator_class
	 */
	public function __construct($input = array(), $flags = false, $iterator_class = 'ArrayIterator') {
		parent::__construct($input, ($flags ?: ArrayObject::ARRAY_AS_PROPS | ArrayObject::STD_PROP_LIST), $iterator_class);
	}
	
	/**
	 * Create instace of an object.
	 * - $Car = Object::create('Car', array('args'));
	 * - $Bus = Bus::create(array('args'));
	 * 
	 * @return instanceof Object
	 */
	public static function create() {
		$args = func_get_args();
		$class = get_called_class();
		if($class == 'Object') $class = array_shift($args);
		$reflector = new ReflectionClass($class);
		return $reflector->newInstanceArgs($args);
	}
}

?>
