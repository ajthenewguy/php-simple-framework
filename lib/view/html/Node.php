<?php
/**
 * Class Node
 */
class Node extends Object {
	
	protected $tag;
	protected $properties = array();
	protected $content = array();
	protected $block;
	
	public function __construct($tag, array $properties = array(), $block = false) {
		$this->tag = $tag;
		$this->block = $block;
		
		if(!empty($properties)) {
			foreach($properties as $property => $value) {
				$this->properties[$property] = $value;
			}
		}
	}
	
	/**
	 * Create with variable arguments (content)
	 */
	public static function create() {
		$args  = func_get_args();
		$tag = array_shift($args);
		$instance = new static($tag);
		return $instance->content($args);
	}
	
	/**
	 * Bulk properties getter/setter
	 * 
	 * @param type $props
	 * @return type
	 */
	public function properties($props = array()) {
		if(empty($props)) {
			return $this->properties;
		} else {
			foreach($props as $name => $value) {
				$this->property($name, $value);
			}
		}
		return $this;
	}
	
	public function content() {
		$args  = func_get_args();
		if(empty($args)) {
			return $this->get_content();
		} else {
			if(count($args) == 1) {
				$content = $args[0];
				if(is_object($content)) {
					$this->content[] = clone $content;
				} else {
					$this->content[] = $content;
				}
			} else {
				for($i = 0; $i < count($args); $i++) {
					$content = $args[$i];
					if(is_object($content)) {
						$this->content[] = clone $content;
					} else {
						$this->content[] = $content;
					}
				}
			}
			
			return $this;
		}
	}
	
	
	public function get_content() {
		if(!isset( $this->content ) || empty( $this->content )) return '';
		$return = array();
		
		if(is_array($this->content)) {
			foreach($this->content as $content) {
				$content = $this->render_node($content);
				$return[] = $content;
			}
		} else {
			$return[] = $this->content;
		}
		
		/*
		print '<pre>';
		print_r($return);
		print '</pre>';
		*/
		if(!empty($return)) {
			if($this->block) {
				return implode("\n", $return);
			} else {
				return implode('', $return);
			}
		}
	}
	
	/**
	 * 
	 * @param mixed $node
	 * @return array
	 * @throws InvalidArgumentException
	 */
	private function render_node($node) {
		$return = array();
		switch(true) {
			case (is_string($node)):
				return $node;
				break;
			default:
			case ($node instanceof Node):
				return (string)$node;
				break;
			case (is_array($node)):
				foreach($node as $subnode) {
					$return[] = $this->render_node($subnode);
				}
				return implode(($this->block ? "\n" : ''), $return);
				break;
		}
		throw new InvalidArgumentException('Node render error.');
	}
	
	public function property($name, $value = NULL) {
		if(NULL === $value) {
			if(array_key_exists($name, $this->properties)) {
				$value = $this->properties[$name];
			}
			return $value;
		} else {
			$this->properties[$name] = $value;
			return $this;
		}
	}
	
	public function remove_property($name) {
		if(array_key_exists($name, $this->properties)) {
			unset($this->properties[$name]);
			return true;
		}
		return false;
	}
	
	public function tag($new = NULL) {
		$out = $this->tag;
		if(NULL !== $tag) {
			$this->tag = $new;
		}
		return $out;
	}
	
	public function empty_() {
		$this->content = '';
	}
	
	public function __toString() {
		$out = '';
		switch($this->tag) {
			case 'DOCTYPE':
			case '!DOCTYPE':
				$out = '<!DOCTYPE '.(!empty($this->content) ? $this->get_content() : 'html').'>'."\n";
			break;
			case 'area':
			case 'base':
			case 'br':
			case 'col':
			case 'command':
			case 'embed':
			case 'hr':
			case 'img':
			case 'input':
			case 'keygen':
			case 'link':
			case 'meta':
			case 'param':
			case 'source':
			case 'track':
			case 'wbr':
				$out = '<'.$this->tag.$this->render_properties().'>'."\n";
			break;
			case 'textarea':
				if(NULL !== ($value = $this->property( 'value' ))) {
					$this->content( $value );
					unset( $this->properties['value'] );
				}
			default:
				$out = '<'.$this->tag.$this->render_properties().'>'.$this->get_content().'</'.$this->tag.'>'.($this->block ? "\n" : '');
			break;
		}
		
		return $out;
	}
	
	public function render() {
		$out = '';
		switch($this->tag) {
			case 'DOCTYPE':
			case '!DOCTYPE':
				$out = '<!DOCTYPE '.(!empty($this->content) ? $this->get_content() : 'html').'>'."\n";
			break;
			case 'area':
			case 'base':
			case 'br':
			case 'col':
			case 'command':
			case 'embed':
			case 'hr':
			case 'img':
			case 'input':
			case 'keygen':
			case 'link':
			case 'meta':
			case 'param':
			case 'source':
			case 'track':
			case 'wbr':
				$out = (isset($this->properties['pre_text']) ? $this->properties['pre_text'].' ' : '').'<'.$this->tag.$this->render_properties().'>'.(isset($this->properties['post_text']) ? $this->properties['post_text'].' ' : '')."\n";
			break;
			case 'textarea':
				if(NULL !== ($value = $this->property( 'value' ))) {
					$this->content( $value );
					unset( $this->properties['value'] );
				}
			default:
				$out = '<'.$this->tag.$this->render_properties().'>'.$this->get_content().'</'.$this->tag.'>'.($this->block ? "\n" : '');
			break;
		}
		
		return $out;
	}
	
	protected function render_properties() {
		$out = '';
		if(!empty($this->properties)) {
			$property_strings = array();
			foreach($this->properties as $property => $value) {
				if(in_array($property, array('label', 'pre_text', 'post_text'))) continue;
				if(in_array($property, array('selected', 'checked', 'disabled'))) {
					$property_strings[] = $property;
				} else {
					$property_strings[] = $property.'="'.$value.'"';
				}
			}
			$out = ' '.implode(' ', $property_strings);
		}
		
		return $out;
	}
	
	private function Children($tag = null) {
		if(is_null($tag)) {
			return $this->content;
		} else {
			$found = array();
			foreach($this->content as $content) {
				if($content instanceof Node) {
					if($tag == $content->tag) {
						$found[] = $content;
					}
				}
			}
			if(count($found) == 1) {
				return $found[0];
			} else {
				return $found;
			}
		}
	}
	
	public function __get($name) {
		if(property_exists(get_class($this), $name)) {
			return $this->$name;
		} elseif(array_key_exists($name, $this->properties)) {
			return $this->properties[$name];
		}
		
		return NULL;
	}
	
	public function __call($name, $arguments) {
		if(!empty($arguments)) {
			$found = array();
			foreach($this->content as $content) {
				if($content instanceof Node && $content->tag == $name) {
					$found[] = $content;
				}
			}
			if(count($found) > 0) {
				if(is_numeric($arguments[0]) && isset($found[$arguments[0]])) {
					return $found[$arguments[0]];
				} else {
					return null;
				}
			} else {
				return null;
			}
			
			return $this;
		} else {
			$found = array();
			foreach($this->content as $content) {
				if($content instanceof Node && $content->tag == $name) {
					$found[] = $content;
				} elseif(is_array($content)) {
					foreach($content as $content_ele) {
						if($content_ele instanceof Node && $content_ele->tag == $name) {
							$found[] = $content_ele;
						}
					}
				}
			}
			if(count($found) > 0) {
				if(count($found) == 1) {
					return $found[0];
				} else {
					return $found;
				}
			} else {
				return null;
			}
		}
    }
}