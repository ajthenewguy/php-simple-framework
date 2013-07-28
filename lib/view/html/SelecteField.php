<?php

/**
 * Description of SelecteField
 *
 * @author Allen
 */
class SelectField extends FormField {
	
	protected $options = array();
	protected $selected;
	
	public function __construct($name, array $options = array(), array $properties = array(), $required = false) {
		parent::__construct($name, 'select', $properties, $required);
		if(!empty( $options )) $this->set_options( $options );
	}
	
	public function set_options(array $options = array()) {
		if(!empty( $options )) {
			foreach($options as $value => $display) {
				$this->option( $value, $display );
			}
		}
		
		return $this;
	}
	
	public function selected($value = NULL) {
		if(isset($this->options) && !empty($this->options)) {
			foreach($this->options as $key => $option) {
				if(NULL === $value) {
					if($this->options[$key]->selected) {
						return $option;
					}
				} else {
					if($option->property('value') == $value) {
						$option->property( 'selected', 'selected' );
					} else {
						$option->remove_property( 'selected' );
					}
				}
			}
		}
		
		return $this;
	}
	
	/**
	 *	eg. $Select = new Select_Field('color');
	 *	$Select->option('red', 'Cherry Red');
	 *	$Select->option('blue', 'Blue Ice');
	 *	$Select->selected('blue');
	 *	$SelectedOption = $Select->selected();
	 *	echo $SelectedOption->value; // 'blue'
	 *	echo $SelectedOption->content(); // 'Blue Ice'
	 */
	public function option($value, $display = NULL, $selected = NULL) {
		
		// SEARCH FOR EXISTING OPTION
		if(isset($this->options) && !empty($this->options)) {
			foreach($this->options as $option) {
				if($option->property( 'value' ) == $value) {
					if($selected) {
						$option->property( 'selected', 'selected' );
					}
					if(NULL !== $display) {
						$option->content( $display );
					} else {
						return $option->content();
					}
					return $option;
				}
			}
		}
		
		// OPTION NOT CURRENTLY SET
		if(NULL !== $display) {
			$option = new Node( 'option', array('value' => $value) );
			$option->content( $display );
			if($selected) {
				$option->property( 'selected', 'selected' );
			}
			$this->options[] = $option;
			
			return $option;
		}
		
		return NULL;
	}
	
	private function option_exists($name) {
		foreach($this->options as $option) {
			if($option->name == $name) {
				return $option;
			}
		}
	}
	
	private function value_exists($value) {
		foreach($this->options as $option) {
			if($option->value == $value) {
				return $option;
			}
		}
	}
	
	public function __toString() {
		if(!empty($this->options)) {
			$this->content(implode("\r\n", $this->options));
		}
		return parent::__toString();
	}
	
	public function render($include_label = true, $label_properties = array()) {
		$out = '';
		if(!empty($this->options)) {
			$this->content(implode("\r\n", $this->options));
		}
		if($include_label) $out .= $this->get_label( $label_properties );
		$out .= parent::render(false);
		return $out;
	}
}