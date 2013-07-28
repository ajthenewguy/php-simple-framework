<?php

/**
 * Description of FormField
 *
 * @author Allen
 */
class FormField extends Node {
	
	public $name;
	public $format;
	public $field_group;
	
	protected $type;
	protected $required;
	protected $label;
	protected $error;
	protected $value;
	
	
	public function __construct($name, $type, array $properties = array(), $required = false) {
		$this->name = $name;
		$this->type = $type;
		$tag_name = '';
		
		switch($type) {
			case 'date':
				$properties['type'] = 'text';
			case 'checkbox':
			case 'hidden':
			case 'radio':
			case 'submit':
			case 'text':
			case 'file':
				$tag_name = 'input';
			break;
			case 'select':
			case 'textarea':
				$tag_name = $type;
			break;
		}
		
		parent::__construct($tag_name, $properties, true);
		
		$this->required = $required;
		
		if(isset($_POST[$name])) {
			$this->value( $_POST[$name] );
		}
	}
	
	public function label($string, $error = false) {
		$this->label = $string;
		return $this;
	}
	
	public function value($input) {
		$this->property( 'value', stripslashes( $input ) );
		return $this;
	}
	
	public function field_group($name = NULL) {
		if(NULL !== $name) {
			$this->field_group = $name;
			return $this;
		}
		return $this->field_group;
	}


	public function get_label($properties = array()) {
		$label = '';
			if(isset( $this->label ) && !empty( $this->label )) {
			$id = $this->property( 'id' );
			if(NULL !== $id) {
				$properties['for'] = $id;
			}
			$label = new Node( 'label', $properties );
			$label->content( $this->label );
		}
		return $label;
	}
	
	public function checked($is_checked = true) {
		if($is_checked) {
			$this->property('checked', 'checked');
		}
	}
	
	
	public function error($error = true) {
		$this->error = $error;
		return $this;
	}
	
	public function format($format = NULL) {
		if(NULL === $format) {
			$value = $this->value;
			if(isset($this->format) && !empty($this->format)) {
				if($this->type == 'date') {
					$value = date($this->format, strtotime($value));
				} else {
					$value = sprintf($this->format, $value);
				}
			}
			return $value;
		} else {
			$this->format = $format;
		}
	}
	
	public function render($include_label = true, $label_properties = array()) {
		$this->value = $this->format();
		$out = '';
		if($include_label && $this->type !== 'hidden') $out .= $this->get_label( $label_properties ); 
		$out .= parent::render();
		return $out;
	}
	
	public function __toString() {
		return static::render();
	}
}