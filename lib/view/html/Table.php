<?php

/**
 * Description of Table
 *
 * @author Allen
 */
class Table extends Node {
	
	public $border = 0;
	
	public $width;
	
	private $header;
	
	public function __construct($data = array(), $first_row_header = false, $properties = array()) {
		parent::__construct('table', $properties, true);
		
		if(!empty($data)) {
			if($first_row_header) {
				$this->setHeader(array_shift($data));
			}
			$this->setData($data);
		}
	}
	
	public function setHeader($data) {
		$thead = new Node('thead');
		$tr = new Node('tr');
		foreach($data as $key => $col) {
			$properties = array();
			if($key == $this->getCols()-1) { // last column's cell
				$properties['colspan'] = $this->getCols() - count($data) + 1;
			}
			$td = new Node('td', $properties);
			$tr->content($td->content($col));
		}
		
		$this->content($thead->content($tr));
	}
	
	public function setData($data = array()) {
		if(isset($this->columns)) unset($this->columns);
		if(isset($this->rows)) unset($this->rows);
		if(is_string($data)) {
			$data = array($data);
		}
		if(is_array($data)) {
			$tbody = new Node('tbody');
			foreach($data as $row) {
				$tr = new Node('tr');
				if(!is_array($row)) $row = array($row);
				foreach($row as $key => $col) {
					$properties = array();
					if($key == $this->getCols()-1) { // last column's cell
						$properties['colspan'] = $this->getCols() - count($row) + 1;
					}
					$td = new Node('td', $properties);
					$tr->content($td->content($col));
				}
				$tbody->content($tr);
			}
			$this->content($tbody);
		}
	}
	
	public function getCols() {
		if(!isset($this->columns)) {
			if(!empty($this->data)) {
				for($row = 0; $row < count($this->data); $row++) {
					if(count($this->data) > $max_rows)
						$this->rows = count($this->data);
					for($col = 0; $col < count($this->data[$row]); $col++) {
						if(count($this->data[$row]) > $max_rows)
							$this->columns = count($this->data[$row]);
					}
				}
			}
		}
		return $this->columns;
	}
	
	public function getRows() {
		if(!isset($this->rows)) {
			if(!empty($this->data)) {
				for($row = 0; $row < count($this->data); $row++) {
					if(count($this->data) > $max_rows)
						$this->rows = count($this->data);
				}
			}
		}
		return $this->rows;
	}
	
	public function push($row) {
		$new = false;
		if(!$tbody = $this->tbody()) {
			$new = true;
			$tbody = new Node('tbody');
		}
		$tr = Node::create('tr');
		if(!is_array($row)) $row = array($row);
		foreach($row as $key => $col) {
			$properties = array();
			if($key == $this->getCols()-1) { // last column's cell
				$properties['colspan'] = $this->getCols() - count($row) + 1;
			}
			$td = new Node('td', $properties);
			$tr->content($td->content($col));
		}
		$tbody->content($tr);
		if($new) {
			$this->content($tbody);
		}
	}
	
	public function clear() {
		$this->data = array();
	}
	
	public function __toString() {
		return $this->render();
	}
}

?>
