<?php

/**
 * Description of Date_Field
 *
 * @author Allen
 */
class DateField extends FormField {
	
	public function __construct($name, $value = NULL, array $properties = array(), $required = false, $format = "Y-m-d H:i:s") {
		if(isset($properties['class']) && !empty($properties['class'])) {
			$properties['class'] .= ' datepicker';
		} else {
			$properties['class'] = 'datepicker';
		}
		parent::__construct($name, 'date', $properties, $required);
		$this->value = $value;
		$this->format = $format;
	}
	
	public function validate() {
		return (bool)strtotime($this->value);
	}
}