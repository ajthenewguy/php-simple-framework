<?php

/**
 * Description of Checkbox_Field
 *
 * @author Allen
 */
class CheckboxField extends FormField {
	
	/*
	public $name;
	protected $type;
	protected $required;
	protected $label;
	protected $error;
	protected $value;
	
	protected $tag;
	protected $properties = array();
	protected $content;
	protected $block;
	*/
	protected $options = array();
	protected $selected;
	
	public function __construct($name, $value = NULL, array $properties = array(), $required = false) {
		parent::__construct($name, 'checkbox', $properties, $required);
		if(NULL == $value) $value = $name;
		$this->value = $value;
	}
	
	public function checked($is_checked = true) {
		if($is_checked) {
			$this->property('checked', 'checked');
		}
	}
	
	public function __toString() {
		$out = '';
		$out .= parent::render();
		return $out;
	}
	
	public function render($include_label = true, $label_properties = array()) {
		$out = '';
		$out .= parent::render(false);
		if($include_label) $out .= $this->get_label( $label_properties );
		return $out;
	}
}