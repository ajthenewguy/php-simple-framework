<?php
/**
 * Description of Page
 *
 * @author Allen
 */
class Page extends Document {
	
	public function __construct($input = array()) {
		if(!isset($input['type'])) {
			$input['type'] = 'html';
		}
		parent::__construct($input);
	}
	
	public function Title($value = false) {
		if(false !== $value) {
			$this->Title = $value;
			parent::Title($value);
		}
		return $this->Title;
	}
	
	public function Content($value = false) {
		if(false !== $value) {
			$this->Content = $value;
			parent::Content($value);
		}
		return $this->Content;
	}
}

?>
