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
		$thead = $this->thead();
		$tr = new Node('tr');
		foreach($data as $key => $col) {
			$properties = array();
			/*if($key == $this->getCols()-1) { // last column's cell
				$properties['colspan'] = $this->getCols() - count($data) + 1;
			}*/
			$td = new Node('td', $properties);
			$tr->push($td->content($col));
		}
		$thead->content($tr);
	}
	
	public function setBody($data) {
		$tbody = $this->tbody();
		$tr = new Node('tr');
		foreach($data as $key => $col) {
			$properties = array();
			/*if($key == $this->getCols()-1) { // last column's cell
				$properties['colspan'] = $this->getCols() - count($data) + 1;
			}*/
			$td = new Node('td', $properties);
			$tr->push($td->content($col));
		}
		$tbody->content($tr);
	}
	
	public function setData($data = array()) {
		if(isset($this->columns)) unset($this->columns);
		if(isset($this->rows)) unset($this->rows);
		if(is_string($data)) {
			$data = array($data);
		}
		if(is_array($data)) {
			foreach($data as $row) {
				$this->push_row($row);
			}
		}
	}
	
	public function push_row() {
		$args  = func_get_args();
		$row = $args[0];
		$new = false;
		$tbody = $this->tbody();
		$tr = Node::create('tr');
		if(!is_array($row)) $row = array($row);
		foreach($row as $key => $col) {
			$properties = array();
			if($key == $this->getCols()-1) { // last column's cell
				$properties['colspan'] = $this->getCols() - count($row) + 1;
			}
			$td = new Node('td', $properties);
			$td->content($col);
			$tr->push($td);
		}
		$tbody->push($tr);
		return $this;
	}
	
	public function thead() {
		$thead = $this->getChild('thead');
		if(is_null($thead)) {
			$thead = new Node('thead');
			$this->unshift($thead);
		}
		return $thead;
	}
	
	public function tbody() {
		$tbody = $this->getChild('tbody');
		if(is_null($tbody)) {
			$tbody = new Node('tbody');
			$this->push($tbody);
		}
		return $tbody;
	}
	
	public function tfoot() {
		$tfoot = $this->getChild('tfoot');
		if(is_null($tfoot)) {
			$tfoot = new Node('tfoot');
			$this->push($tfoot);
		}
		return $tfoot;
	}


	public function clear() {
		$this->thead()->content(array());
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
}

?>
