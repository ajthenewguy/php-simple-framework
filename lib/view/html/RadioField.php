<?php

/**
 * Description of RadioField
 *
 * @author Allen
 */
class RadioField extends FormField {
	
	protected $options = array();
	protected $selected;
	
	public function __construct($name, array $options = array(), array $properties = array(), $required = false) {
		parent::__construct($name, 'radio', $properties, $required);
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
						$option->property( 'checked', 'checked' );
					} else {
						$option->remove_property( 'checked' );
					}
				}
			}
		}
		
		return $this;
	}
	
	/**
	 *	eg. $Radio = new Radio_Field('color');
	 *	$Radio->option('red', 'Cherry Red');
	 *	$Radio->option('blue', 'Blue Ice');
	 *	$Radio->selected('blue');
	 *	$SelectedOption = $Radio->selected();
	 *	echo $SelectedOption->value; // 'blue'
	 *	echo $SelectedOption->content(); // 'Blue Ice'
	 */
	public function option($value, $display = NULL, $selected = NULL) {
		
		// SEARCH FOR EXISTING OPTION
		if(isset($this->options) && !empty($this->options)) {
			foreach($this->options as $label => $Radio) {
				if($Radio->property( 'value' ) == $value) {
					if($selected) {
						$Radio->property( 'checked', 'checked' );
					}
					return $Radio;
				}
			}
		}
		
		// OPTION NOT CURRENTLY SET
		if(NULL === $display) {
			$display = $value;
		}
		$Radio = new Node( 'input', array('type' => 'radio', 'name' => $this->name, 'value' => $value, 'label' => $display) );
		
		if($selected) {
			$Radio->property( 'checked', 'checked' );
		}
		$this->options[] = $Radio;

		return $Radio;
		
	}
	
	private function value_exists($value) {
		foreach($this->options as $option) {
			if($option->value == $value) {
				return $option;
			}
		}
	}
	
	public function __toString() {
		$out = '';
		if(!empty($this->options)) {
			foreach($this->options as $Radio) {
				$out .= $Radio.' '.$Radio->label;
			}
		}
		return parent::__toString();
	}
	
	public function render($include_label = true, $label_properties = array()) {
		$out = '';
		if(!empty($this->options)) {
			foreach($this->options as $Radio) {
				$out .= '<label style="margin-right:10px;">'.$Radio.' '.$Radio->label.'</label>';
			}
		}
		return $out;
	}
}