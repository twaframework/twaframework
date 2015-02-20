<?php
/**
 * The base twaDependency object that can be extended to create typical dependencies between objects.
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class twaQuery {

/**
 * The tablename string
 *
 * @var string
 */

public $tablename;
public $db;
/**
 * The type string
 *
 * @var string
 */
public $type = "";
/**
 * The filters array
 *
 * @var array
 */
public $filters = array();
/**
 * The sort string
 *
 * @var array
 */
public $sort = array();
/**
 * The limit string
 *
 * @var string
 */
public $limit = "";
/**
 * The values array
 *
 * @var array
 */
public $values = array();

/**
 * Starting point for twaDependency
 *
 * @param Array $ms contains the parent and child models
 * @param String $t contains the tablename in which this dependency is defined.
 * @access public
 */
public function __construct($db = "default") {
	$this->sql = "";
	global $framework;
	$this->db = $framework->getDB($db);
}

public function select($table,$fields = array()){
	$this->type = "select";
	$this->tablename = $table;
	if($fields){
		$this->fields = $fields;
	} else {
		$this->fields = array();
	}
	return $this;
}

public function insert($table,$values = array()){
	$this->type = "insert";
	$this->tablename = $table;
	if($values){
		$this->values = $values;
	}
	return $this;
}

public function update($table,$values = array()){
	$this->type = "insert";
	$this->tablename = $table;
	if($values){
		$this->values = $values;
	}
	return $this;
}

public function where($filters = array()){
	$this->filters = $filters;
	return $this;
}

public function sort($sort = array()){
	$this->sort = $sort;
	return $this;
}

public function limit($limit = array()){
	$this->limit = $limit;
	return $this;
}

public function execute(){
	if($this->type == 'select') {
		$sql = "SELECT ";
		if($fields){
			$commma ="";
			foreach($fields as $field){
				$commma = ",";
				$sql .= $comma.$field;
			}
		} else {
			$sql .= "*";
		}
		$sql .= " FROM ".$this->tablename;
		if($this->filters) {
			$sql .= " WHERE ";
			$comma = " AND ";
			foreach($this->filters as $filter) {
				$this->sql .= $comma." ".$filter." ";
			}
		}
		if($this->sort) {
			$comma = " ORDER BY ";
			foreach($data['sort'] as $sort) {
				$sql .= $comma." ".$sort." ";
				$comma = ",";
			}
		}
		
		if($this->limit != "") {
			$sql .= " LIMIT ".$this->limit." ";
		}
	} else if($type == "insert"){
		$sql = "INSERT INTO ";
		$sql .= " ".$this->tablename." SET ";
		if($this->values) {
			$comma = "";
			foreach($this->values as $field=>$value) {
					$sql .=	$comma." `".$field."` = ".$database->dbquote($value)." ";	
					$comma = ",";
			}
		}
	} else if($type == "UPDATE"){
		$sql = "UPDATE ";
		$sql .= " ".$this->tablename." SET ";
		if($this->values) {
			$comma = "";
			foreach($this->values as $field=>$value) {
					$sql .=	$comma." `".$field."` = ".$database->dbquote($value)." ";	
					$comma = ",";
			}
		}
		if($this->filters) {
			$sql .= " WHERE ";
			$comma = " AND ";
			foreach($this->filters as $filter) {
				$this->sql .= $comma." ".$filter." ";
			}
		}
	}
	
	global $framework;
    global $app;
	
	$r = $this->db->runQuery($sql);
	return $r;
}

}
?>