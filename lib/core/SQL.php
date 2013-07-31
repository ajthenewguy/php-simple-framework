<?php
class SQL {
	
	protected $statement;
	protected $update_on_duplicate = false;
	protected $fields;
	protected $table;
	protected $where;
	protected $where_conjunction = 'AND';
	protected $match;
	protected $boolean_mode;
	protected $against;
	protected $group_by;
	protected $having;
	protected $having_conjunction = 'AND';
	protected $order_by;
	protected $limit;
	
	protected static $mysqli;
	protected static $result;
	protected static $num_rows;
	
	
	public function __construct($statement = NULL, $fields = NULL, $table = NULL) {
		$this->set_statement($statement);
		
		if(NULL !== $fields) {
			$this->fields = esc( $fields );
		}
		
		if(NULL !== $table) {
			$this->table = $table;
		}
	}
	
	public function set_statement($statement = NULL) {
		if(NULL === $statement) $statement = 'SELECT';
		
		$this->statement = strtoupper($statement);
	}
	
	/* FACTORY METHODS */
	
	public static function select($fields = '*') {
		return new static('SELECT', $fields);
	}
	
	public static function update($table, $fields = '*') {
		return new static('UPDATE', $fields, $table);
	}
	
	public static function insert($fields = NULL, $table = NULL) {
		if($fields !== NULL) {
			if(is_array($fields)) {
				$_fields = array();
				foreach($fields as $field => $value) {
					$_fields[] = $field . " = '" . $value . "'";
				}
				$fields = implode(', ', $_fields);
			}
		}
		return new static('INSERT', $fields, $table);
	}
	
	public static function delete() {
		return new static('DELETE');
	}
	
	/* -- INSTANCE METHODS -- */
	
	public function into($table_reference, $update_on_duplicate = false) {
		$this->table = $table_reference;
		if($update_on_duplicate) {
			$this->update_on_duplicate = true;
		}
		return $this;
	}
	
	public function set($fields) {
		if(is_array($fields)) {
			$_fields = array();
			foreach($fields as $field => $value) {
				$_fields[] = $field . " = '" . esc( $value ) . "'";
			}
			$fields = implode(', ', $_fields);
		}
		$this->fields = $fields;
		
		return $this;
	}
	
	public function fields($fields) {
		$this->fields = esc( $fields );
		return $this;
	}
	
	public function from($table_references) {
		$this->table = $table_references;
		return $this;
	}
	
	public function match($match_fields) {
		if(is_array($match_fields)) {
			$this->match = $match_fields;
		} else {
			if(false !== strpos($match_fields, ',')) {
				$this->match = explode(',', esc( $match_fields ));
			} else {
				$this->match = array(esc( $match_fields ));
			}
		}
		return $this;
	}
	
	public function against($clause, $boolean_mode = true) {
		$this->against = esc( $clause );
		$this->boolean_mode = $boolean_mode;
		return $this;
	}
	
	public function where($where_condition, $conjunction = NULL) {
		$this->where = $where_condition;
		if(NULL !== $conjunction) {
			$this->where_conjunction = $conjunction;
		}
		return $this;
	}
	
	public function group_by($expr) {
		$this->group_by = $expr;
		return $this;
	}
	
	public function having($where_condition, $conjunction = NULL) {
		$this->having = $where_condition;
		if(NULL !== $conjunction) {
			$this->having_conjunction = $conjunction;
		}
		return $this;
	}
	
	public function order_by($expr) {
		$this->order_by = $expr;
		return $this;
	}
	
	public function limit($row_count) {
		$this->limit = $row_count;
		return $this;
	}
	
	public function offset($offset) {
		$this->offset = $offset;
	}
	
	/*
	public function __call($name, $arguments) {
		$this->$name = $arguments;
		return $this;
	}
	*/
	
	public function build_set() {
		if(!isset($this->fields)) {
			throw new Exception('No fields/values specified.');
		}
		
		if(is_array($this->fields)) {
			$fields = implode(', ', $this->fields);
		} else {
			$fields = $this->fields;
		}
		
		return ' SET ' . $fields;
	}
	
	public function build_fields($default = '*') {
		$fields = $default;
		
		if(isset($this->fields)) {
			if(is_array($this->fields)) {
				$fields = implode(', ', $this->fields);
			} else {
				$fields = $this->fields;
			}
		}
		
		return $fields;
	}
	
	
	public function build_where($prepend_where = true) {
		$where = NULL;
		
		if(isset($this->where) || (isset($this->match) && isset($this->against))) {
			$where = ($prepend_where ? ' WHERE ' : '');
			if(isset($this->match) && isset($this->against)) {
				$where .= trim( $this->build_match_against() );
			}
			if(is_array($this->where)) {
				$where .= static::array_where( $this->where, false, $this->where_conjunction );
			} else {
				$where .= $this->where;
			}
		}
		
		return $where;
	}
	
	public static function array_where($where_array, $prepend_where = true, $where_conjunction = 'AND') {
		$where = array();
		$where_statement = '';
		foreach($where_array as $field => $value) {
			$where[] = $field . " = '". $value . "'";
		}
		$where_statement = ($prepend_where ? ' WHERE ' : '') . implode(' '.$where_conjunction.' ', $where);
		return $where_statement;
	}
	
	
	public function build_match_against() {
		$match_against = ' MATCH ('.implode(',', $this->match).") AGAINST('".$this->against."'".($this->boolean_mode ? ' IN BOOLEAN MODE' : '').")";
		return $match_against;
	}
	
	
	public function build_group_by() {
		$group_by = NULL;
		
		if(isset($this->group_by)) {
			if(is_array($this->group_by)) {
				$group_by = ' GROUP BY ' . implode(', ', $this->group_by);
			} else {
				$group_by = ' GROUP BY ' . $this->group_by;
			}
		}
		
		return $group_by;
	}
	
	
	public function build_having() {
		$having = NULL;
		
		if(isset($this->having)) {
			if(is_array($this->having)) {
				$having = array();
				foreach($this->having as $field => $value) {
					$having[] = $field . " = '". $value . "'";
				}
				$having = ' HAVING ' . implode(' '.$this->having_conjunction.' ', $where);
			} else {
				$having = ' HAVING ' . $this->having;
			}
		}
		
		return $having;
	}
	
	
	private function build_order_by() {
		$order_by = NULL;
		
		if(isset($this->order_by)) {
			if(is_array($this->order_by)) {
				$order_by = array();
				foreach($this->order_by as $field => $direction) {
					$order_by[] = $field . ' ' . $direction;
				}
				$order_by = ' ORDER BY ' . implode(', ', $order_by);
			} else {
				$order_by = ' ORDER BY ' . $this->order_by;
			}
		}
		
		return $order_by;
	}
	
	
	public function query() {
		$query = '';
		
		if(!isset($this->table)) {
			throw new Exception('No table specified.');
		}
		
		switch($this->statement) {
			case 'SELECT':
			//$this->fields
				$query .= 
					'SELECT ' . $this->build_fields() .
					' FROM ' . $this->table .
					$this->build_where() .
					$this->build_group_by() .
					$this->build_having() .
					$this->build_order_by() .
					(isset($this->limit) ? " LIMIT " . $this->limit : NULL) .
					(isset($this->offset) ? " OFFSET " . $this->offset : NULL);
			break;
			case 'UPDATE':
				$query .= 
					'UPDATE ' . $this->table .
					$this->build_set() .
					$this->build_where() .
					(false === strpos($this->table, ',') ? 
						$this->build_order_by() .
						(isset($this->limit) ? " LIMIT " . $this->limit : NULL)
					: NULL);
			break;
			case 'INSERT':
				$query .= 
					'INSERT INTO ' . $this->table .
					$this->build_set();
			break;
			case 'DELETE':
				$query .= 
					'DELETE FROM ' . $this->table .
					$this->build_where() .
					$this->build_order_by() .
					(isset($this->limit) ? " LIMIT " . $this->limit : NULL);
			break;
		}
		
		return $query;
	}
	
	
	public function exe() {
		$statement = $this->query();
		
		if(false !== ($result = DBi::query( $statement ))) {
			if(!empty($statement) && $this->statement == 'SELECT') {
				if(DBi::count() == 1) {
					return DBi::row($statement);
				} else {
					return DBi::all($statement);
				}
			} else {
				return $result;
			}
		}
		
		return false;
	}
	
	public function __toString() {
		return $this->query();
	}
}
?>