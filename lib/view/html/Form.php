<?php

/**
 * Description of Form
 *
 * @author Allen
 */
class Form extends Node {
	
	protected $name;
	protected $Fields = array();
	
	/**
	 * 
	 * @param type $name
	 * @param type $action
	 * @param type $method
	 * @param type $id
	 */
	public function __construct($name, $action = '', $method = 'post', $id = NULL) {
		$this->name = $name;
		if(NULL === $id) $id = $name;
		parent::__construct('form', array('id' => $id, 'name' => $name, 'action' => $action, 'method' => $method), true);
		$hidden_name_element = new Node('input', array('type' => 'hidden', 'name' => 'form', 'value' => $name), false);
		$this->content($hidden_name_element);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function submitted() {
		return (bool)(isset($_POST['form']) && $_POST['form'] == $this->name);
	}
	
	
	/**
	 * 
	 * @param type $name
	 * @param type $type
	 * @param type $properties
	 * @param type $required
	 * @param type $field_group
	 * @return type
	 */
	public function add($name, $type = 'text', $properties = array(), $required = false, $field_group = NULL) {
		$properties = array_merge($properties, array('name' => $name, 'type' => $type));
		
		if($type == 'file') {
			$this->property( 'enctype', 'multipart/form-data' );
		}
		
		switch($type) {
			case 'date':
				$this->Fields[$name] = new DateField($name, $type, $properties, $required);
			break;
			case 'select':
				if(isset($properties['values'])) {
					$options = $properties['values'];
					unset($properties['values']); // DO NOT PASS TO CONSTRUCTOR
				}
				$this->Fields[$name] = new SelectField($name, array(), $properties, $required);
				if(isset($options)) {
					$this->Fields[$name]->set_options($options);
				}
			break;
			case 'checkbox':
				if(isset($properties['values'])) {
					$options = $properties['values'];
					unset($properties['values']); // DO NOT PASS TO CONSTRUCTOR
				}
				$this->Fields[$name] = new CheckboxField($name, array(), $properties, $required);
				if(isset($options)) {
					$this->Fields[$name]->set_options($options);
				}
			break;
			case 'radio':
				if(isset($properties['values'])) {
					$options = $properties['values'];
					unset($properties['values']); // DO NOT PASS TO CONSTRUCTOR
				}
				$this->Fields[$name] = new RadioField($name, array(), $properties, $required);
				if(isset($options)) {
					$this->Fields[$name]->set_options($options);
				}
			break;
			default:
				$this->Fields[$name] = new FormField($name, $type, $properties, $required);
			break;
		}
		
		$this->content( $this->Fields[$name] );
		
		
		return $this->Fields[$name];
	}
	
	public function add_hidden($name, $value = '', $properties = array()) {
		$properties = array_merge($properties, array('name' => $name, 'type' => 'hidden', 'value' => $value));
		
		$this->Fields[$name] = new FormField($name, 'hidden', $properties);
		$this->content( $this->Fields[$name] );
		
		return $this->Fields[$name];
	}
	
	public function get($name) {
		return $this->Fields[$name];
	}
	
	public function values() {
		if($this->submitted()) {
			$values = array();
			
			if(!empty( $this->Fields )) {
				foreach($this->Fields as $FormField) {
					if(isset( $_POST[$FormField->name] )) {
						$values[$FormField->name] = $_POST[$FormField->name];
					}
				}
			}
			
			return $values;
		}
		
		return false;
	}
	
	public function sort_Fields($key = 'field_group') {
		$func = function($a, $b) {
			if(isset($a->field_group) && isset($b->field_group)) return strnatcmp($a->field_group, $b->field_group);
			return 0;
		};
		
		return usort( $this->Fields, $func );
	}
	
	public function &get_Fields() {
		return $this->Fields;
	}
	
	public function get_FileFields() {
		$Fields = array();
		
		foreach($this->Fields as $Field) {
			if($Field->type == 'file') $Fields[] = $Field;
		}
		
		return $Fields;
	}
	
	
	private function render_Fields() {
		$out = '';
		if(isset($this->Fields) && !empty($this->Fields)) {
			$Fields = array();
			foreach($this->Fields as $Field) {
				$Fields[] = $Field->render();
			}
			$out .= implode($Fields);
		}
		
		return $out;
	}
	
	
	public function render() {
		$this->content( $this->render_Fields() );
		$out = '<'.$this->tag.$this->render_properties().'>'.$this->get_content().'</'.$this->tag.'>'.($this->block ? "\n" : '');
		return $out;
	}
}