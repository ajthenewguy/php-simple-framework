<?php

/**
 * Description of utility
 *
 * @author Allen
 */

/**
 * Get an item from an array using "dot" notation.
 *
 * <code>
 *		// Get the $array['user']['name'] value from the array
 *		$name = array_get($array, 'user.name');
 *
 *		// Return a default from if the specified item doesn't exist
 *		$name = array_get($array, 'user.name', 'Taylor');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function array_get($array, $key, $default = null) {
	if (is_null($key)) return $array;

	// To retrieve the array item using dot syntax, we'll iterate through
	// each segment in the key and look for that value. If it exists, we
	// will return it, otherwise we will set the depth of the array and
	// look for the next segment.
	foreach (explode('.', $key) as $segment)
	{
		if ( ! is_array($array) or ! array_key_exists($segment, $array))
		{
			return value($default);
		}

		$array = $array[$segment];
	}

	return $array;
}

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * <code>
 *		// Set the $array['user']['name'] value on the array
 *		array_set($array, 'user.name', 'Taylor');
 *
 *		// Set the $array['user']['name']['first'] value on the array
 *		array_set($array, 'user.name.first', 'Michael');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return void
 */
function array_set(&$array, $key, $value) {
	if (is_null($key)) return $array = $value;

	$keys = explode('.', $key);

	// This loop allows us to dig down into the array to a dynamic depth by
	// setting the array value for each level that we dig into. Once there
	// is one key left, we can fall out of the loop and set the value as
	// we should be at the proper depth.
	while (count($keys) > 1)
	{
		$key = array_shift($keys);

		// If the key doesn't exist at this depth, we will just create an
		// empty array to hold the next value, allowing us to create the
		// arrays to hold the final value.
		if ( ! isset($array[$key]) or ! is_array($array[$key]))
		{
			$array[$key] = array();
		}

		$array =& $array[$key];
	}

	$array[array_shift($keys)] = $value;
}

/**
 * Remove an array item from a given array using "dot" notation.
 *
 * <code>
 *		// Remove the $array['user']['name'] item from the array
 *		array_forget($array, 'user.name');
 *
 *		// Remove the $array['user']['name']['first'] item from the array
 *		array_forget($array, 'user.name.first');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @return void
 */
function array_forget(&$array, $key) {
	$keys = explode('.', $key);

	// This loop functions very similarly to the loop in the "set" method.
	// We will iterate over the keys, setting the array value to the new
	// depth at each iteration. Once there is only one key left, we will
	// be at the proper depth in the array.
	while (count($keys) > 1)
	{
		$key = array_shift($keys);

		// Since this method is supposed to remove a value from the array,
		// if a value higher up in the chain doesn't exist, there is no
		// need to keep digging into the array, since it is impossible
		// for the final value to even exist.
		if ( ! isset($array[$key]) or ! is_array($array[$key]))
		{
			return;
		}

		$array =& $array[$key];
	}

	unset($array[array_shift($keys)]);
}

/**
 * Determine if a given string begins with a given value.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function starts_with($haystack, $needle) {
	return strpos($haystack, $needle) === 0;
}

/**
 * Determine if a given string ends with a given value.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function ends_with($haystack, $needle) {
	return $needle == substr($haystack, strlen($haystack) - strlen($needle));
}

/**
 * Determine if a given string contains a given sub-string.
 *
 * @param  string        $haystack
 * @param  string|array  $needle
 * @return bool
 */
function str_contains($haystack, $needle) {
	foreach ((array) $needle as $n) {
		if (strpos($haystack, $n) !== false) return true;
	}

	return false;
}

/**
 * Determine if the given object has a toString method.
 *
 * @param  object  $value
 * @return bool
 */
function str_object($value) {
	return is_object($value) and method_exists($value, '__toString');
}

/**
 * string_to-title -> String To Title
 * 
 * @param string $string
 * @return string
 */
function strtotitle($string) {
	return ucwords( str_replace( array('-', '_'), ' ', $string ) );
}

/**
 * String to Var -> string_to_var
 * 
 * @param string $string
 * @param string $delim
 * @return string
 */
function strtovar($string, $delim = '_') {
	return strtolower( str_replace( ' ', $delim, $string) );
}

/**
 * Get a random string
 * 
 * @param int $length
 * @param string $pattern
 * @return string
 */
function strrand($length = 64, $pattern = "/[^a-zA-Z]/") {
	$string = '';
	while(strlen($string) < $length) {
		$string .= chr(rand(33, 126));
		$string = preg_replace($pattern, '', $string);
	}
	return $string;
}

/**
 * Return friendly version of a MySQL date string
 * 
 * @param type $sql_date
 * @return string
 */
function datetostr($sql_date) {
	if(empty( $sql_date ) || substr($sql_date, 0, 1) == '0') return NULL;
	$Due = new HumanRelativeDate();
	return $Due->getTextForSQLDate($sql_date);
}

/**
 * Escape values for database.
 * 
 * @param mixed $mixed
 * @param string $key_index
 * @return string
 * @throws Exception
 */
function esc(&$mixed) {
	switch(gettype($mixed)) {
		case 'boolean':
			return ((bool)$mixed ? '1' : '0');
		break;
		case 'array':
			return array_map('esc', $mixed);
		break;
		case 'resource':
			throw new Exception('Invalid datatype for esc().');
		break;
		case 'NULL':
			return 'NULL';
		break;
		default:
			return DBi::escape( (string)$mixed );
		break;
	}
}
