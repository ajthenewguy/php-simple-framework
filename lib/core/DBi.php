<?php
class DBi {
	
	protected $statement;
	
	protected static $mysqli;
	protected static $result;
	protected static $insert_id;
	protected static $num_rows;
	
	
	public function __construct($statement) {
		$this->statement = $statement;
	}
	
	public static function connect($host = NULL, $username = NULL, $passwd = NULL, $dbname = NULL) {
		if(!isset(static::$mysqli)) {
			if(NULL === $host) {
				$host = DB_HOST;//DB_SERVER;
			}
			
			if(NULL === $username) {
				$username = DB_USER;//DB_SERVER_USERNAME;
			}
			
			if(NULL === $passwd) {
				$passwd = DB_PASS;//DB_SERVER_PASSWORD;
			}
			
			if(NULL === $dbname) {
				$dbname = DB_NAME;//DB_DATABASE;
			}
			
			static::$mysqli = new mysqli($host, $username, $passwd, $dbname);
			
			if(static::$mysqli->connect_errno) {
				throw new Exception("Failed to connect to MySQL: (" . static::$mysqli->connect_errno . ") " . static::$mysqli->connect_error);
			}
		}
		
		return static::$mysqli;
	}
	
	
	public static function query($statement) {
		static::$result = false;
		static::connect();
		
		if(false !== (static::$result = static::$mysqli->query($statement))) {
			if(static::$mysqli->insert_id > 0) {
				static::$insert_id = static::$mysqli->insert_id;
				return static::$insert_id;
			}
			if(is_object( static::$result ) && static::$result->num_rows > 0) {
				static::$num_rows = static::$result->num_rows;
				return static::$num_rows;
			}
		} else {
			Note::error('SQL query failure: "'.static::error().'" on query '.$statement);
			return false;
		}
		
		if(!is_object(static::$result)) {
			#Note::error('SQL query failure: "'.static::error().'" on query '.$statement);
			#return false;
		}
		
		return static::$result;
	}
	
	
	public static function count($table = NULL, $column = '*', $where = false, $distinct = false) {
		if(NULL === $table) {
			return static::$num_rows;
		} else {
			$result = self::one(
				"SELECT 
					COUNT(".($distinct ? "DISTINCT ":'').$column.") 
					FROM `".$table."`".static::parse_where( $where )
			);
			return (false === $result ? 0 : (int) $result);
		}
	}
	
	private static function parse_where($where) {
		if(false !== $where) {
			if(is_array( $where )) {
				return SQL::array_where( $where, true, 'AND' );
			} else {
				return ' WHERE '.$where;
			}
		}
		return '';
	}
	
	
	public static function one($statement, $offset = 0) {
		$value = false;
		
		if(false !== self::query($statement)) {
			$row = static::$result->fetch_row();
			$value = $row[$offset];
		}
		
		return $value;
	}
	
	
	public static function row($statement, $result_type = 'assoc') {
		$row = false;
		
		if(false !== self::query($statement)) {
			$row = static::$result->fetch_array(self::get_result_type($result_type));
		}
		
		return $row;
	}
	
	
	public static function all($statement, $result_type = 'assoc') {
		$rows = array();
		
		if(self::query($statement)) {
			while($row = static::$result->fetch_array(static::get_result_type($result_type))) {
				$rows[] = $row;
			}
		}
		
		if(!empty($rows)) {
			return $rows;
		}
		
		return false;
	}
	
	
	public static function escape($string) {
		$mysqli = self::connect();
		return $mysqli->real_escape_string($string);
	}
	
	
	public static function close() {
		if(isset(static::$result)) {
			static::$result->free();
			unset(static::$result);
		}
		if(isset(static::$mysqli)) {
			static::$mysqli->close();
			unset(static::$mysqli);
		}
	}
	
	public static function get_result_type($result_type = 'assoc') {
		switch($result_type) {
			case 0:
			case 'array':
			case 'assoc':
				$type = MYSQLI_ASSOC;
			break;
			case 1:
			case 'num':
			case 'enum':
				$type = MYSQLI_NUM;
			break;
			default:
				$type = MYSQLI_BOTH;
			break;
		}
		
		return $type;
	}
	
	
	public static function error() {
		return (!empty( static::$mysqli->error ) ? static::$mysqli->error : static::$mysqli->connect_error);
	}
	
	
	public function __destruct() {
		self::close();
	}
}
?>