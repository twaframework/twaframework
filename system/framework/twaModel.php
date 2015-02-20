<?php
/**
 * The base twaModel object that can be extended to create typical data models.
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class twaModel {

/**
 * The error string
 *
 * @var string
 */

public $error;
/**
 * Function to handle error
 *
 * @param string $error contains the error string
 * @access public
 */
public function onError($error) {
	$this->error = $error;
}
/**
 * Starting point for twaModel
 *
 * @param string $id contains the identifier for this field in the database
 * @access public
 */
public function __construct($id = null) {
	
	if($id) {
		$this->fields[$this->meta['id']] = $id;
		$this->Load();
	}
}
/**
 * Loads the data into the model from the database
 *
 * 
 * @access public
 */
public function Load() {
	global $framework;
	$database = $framework->getDB();
	$str_select = "SELECT * FROM ".$this->meta['tablename']." WHERE ".$this->meta['id']."= ".$database->dbquote($this->fields[$this->meta['id']])."";
	
	if($results = $database->runQuery($str_select.";"))
	{
		$result_array = $results[0];
		foreach ($result_array as $key=>$value)
		{
			$this->fields[$key] = $value;
		}
		
	}


}
/**
 * Inserts a row into the database for the model or updates it.
 *
 * @param Array $data contains the values for all the fields
 * @access public
 */
public function Save($data) {
	global $framework;
	global $app;
	
	$database = $framework->getDB();
	$user = $framework->getUser();
	$debugger = $framework->load('twaDebugger');
	
	$str_insert = "INSERT INTO ".$this->meta['tablename']." SET "; 
	
	if($this->fields) {
		$comma = "";
		foreach($this->fields as $field=>$value) {
			
			if(isset($data[$field])) {
				$str_insert .=	$comma." `".$field."` = ".$database->dbquote($data[$field])." ";	
				$comma = ",";
			}
			
		}
	}
	
	$str_insert .= " , created_on = UTC_TIMESTAMP(), last_updated_on = UTC_TIMESTAMP() ON DUPLICATE KEY UPDATE ";
	
	if($this->fields) {
		$comma = "";
		foreach($this->fields as $field=>$value) {
			
			if(isset($data[$field]) && $field != $this->meta['id']) {
				$str_insert .=	$comma." `".$field."` = ".$database->dbquote($data[$field])." ";	
				$comma = ",";
			}
			
		}
	}
	$str_insert .= ", last_updated_on = UTC_TIMESTAMP()";
	$debugger->log($str_insert);
	$r = $database->runQuery($str_insert.";");
	
	if($r !== FALSE) {
		if(isset($data[$this->meta['id']])){
			$this->fields[$this->meta['id']] = $data[$this->meta['id']];
		} else {
			$this->fields[$this->meta['id']] = $database->last_insert_id;	
		}
		$this->Load();	
		return $this->fields[$this->meta['id']];
	}
	return false;
	
}

/**
 * DEPRECATED
 * Inserts a row into the database for the model.
 *
 * @param Array $data contains the values for all the fields
 * @access public
 */
public function Create($data) {
	
	global $framework;
	global $app;
	
	$database = $framework->getDB();
	$debugger = $framework->load('twaDebugger');
	$debugger->log("Warning! Use of deprecated function Create()");
	
	$str_insert = "INSERT INTO ".$this->meta['tablename']." SET "; 
	
	if($this->fields) {
		$comma = "";
		foreach($this->fields as $field=>$value) {
			
			if(isset($data[$field])) {
				$str_insert .=	$comma." `".$field."` = ".$database->dbquote($data[$field])." ";	
				$comma = ",";
			}
			
		}
	}
	
	$str_insert .= " , created_on = UTC_TIMESTAMP(), last_updated_on = UTC_TIMESTAMP()";
	$debugger->log($str_insert);
	$r = $database->runQuery($str_insert.";");
	if($r !== FALSE)
	{
		$this->fields[$this->meta['id']] = $database->last_insert_id;
		$this->Load();	
		return $this->fields[$this->meta['id']];
	}
	return false;
	
}

/**
 * DEPRECATED
 * Update a row in the database for the given identifier.
 *
 * @param Array $data contains the values for all the fields
 * @access public
 */
public function Update($data) {
    global $framework;
    global $app;
    $debugger = $framework->load('twaDebugger');
	$debugger->log("Warning! Use of deprecated function Update()");
    
    $database = $framework->getDB();
    $sql = "UPDATE ".$this->meta['tablename']." SET ";
    
    $comma = "";
	
	if($this->fields) {
		$comma = "";
		foreach($this->fields as $field=>$value) {
			
			if(isset($data[$field])) {
				
				$sql .= $comma." `".$field."` = ".$database->dbquote($data[$field])." ";	
			
				$comma = ",";
			}
			
		}
	}
    $sql .= " , last_updated_on = UTC_TIMESTAMP() WHERE ".$this->meta['id']."=".$database->dbquote($this->fields[$this->meta['id']])."";
    $r = $database->runQuery($sql.";");
    if($r !== FALSE)
	{
		$this->Load();	
		return true;
	}
	return false;   
    
}
/**
 * Delete a row in the database for the given identifier.
 *
 *
 * @access public
 */
public function Delete() {
	global $framework;
    global $app;
    
    $database = $framework->getDB();
    $sql = "DELETE FROM ".$this->meta['tablename']." WHERE ".$this->meta['id']."=".$database->dbquote($this->fields[$this->meta['id']])."";
     if($database->runQuery($sql.";"))
	{	
		return true;
	}
	return false;   
}
/**
 * Check if a value exists in the database
 *
 * @param Array $data contains the field and the value to check for
 * @return bool TRUE if exists, FALSE if it does not
 * @access public
 */
public function ifExists($data) {
	global $framework;
    global $app;
    
    $database = $framework->getDB();
    $sql = "SELECT * FROM ".$this->meta['tablename']." WHERE ".$data['field']."=".$database->dbquote($data['value'])."";
    if($database->runQuery($sql.";"))
	{	
		return true;
	}
	return false; 
}

public function get($dep,$data = array()){
	if(isset($this->$dep)){
		$par = array(
			"parent_id"=>$this->fields[$this->meta['id']]
		);
		$data = array_merge($par,$data);
		return $this->$dep->get($data);
	}
}

public function add($dep,$data){
	
	if(isset($this->$dep)){
		$par = array(
			"parent_id"=>$this->fields[$this->meta['id']]
		);
		$data = array_merge($par,$data);
		return $this->$dep->add($data);
	}
	
	return false;
}

public function remove($dep,$data){
	if(isset($this->$dep)){
		$par = array(
			"parent_id"=>$this->fields[$this->meta['id']]
		);
		$data = array_merge($par,$data);
		return $this->$dep->remove($data);
	}
	return false;
}

/**
 * DEPRECATED
 * Get a list of rows from a table for a matching condition
 *
 * @param Array $data contains the fields, table name, filters and sort order
 * @return Array the array of rows that fit the matching condition
 * @access public
 */

public function getList($data) {
	global $framework;
    global $app;
	$database = $framework->getDB();
	$debugger = $framework->load('twaDebugger');
	$debugger->log("Warning! Use of deprecated function getList()");
	
	$sql = "SELECT ".$data['list_id']." FROM ".$data['table']." WHERE ".$this->meta['id']."=".$database->dbquote($this->fields[$this->meta['id']])." ";
	
	if(isset($data['filters']) && gettype($data['filters']) == 'array') {
		$comma = " AND ";
		foreach($data['filters'] as $filter) {
			$sql .= $comma." ".$filter['field']." ".$filter['is']." ".$database->dbquote($filter['value'])." ";
		}
	}
	
	if(isset($data['sort']) && gettype($data['sort']) == 'array') {
		$comma = " ORDER BY ";
		foreach($data['sort'] as $sort) {
			$sql .= $comma." ".$sort['field']." ".$sort['direction'];
			$comma = ",";
		}
	}
	
	if($result = $database->runQuery($sql.";"))
	{
		
		$result_array = array();
		foreach($result as $object) {
			
			$result_array[$object->$data['list_id']] = new $data['object']($object->$data['list_id']);
			
		}
		return $result_array;
	}
	return false;
	
}

/**
 * DEPRECATED
 * Get a list of rows from a table for a matching condition
 *
 * @param Array $data contains the fields, table name, filters and sort order
 * @return Array the array of rows that fit the matching condition
 * @access public
 */
public function getData($data) {
	global $framework;
    global $app;
	$database = $framework->getDB();
	$debugger = $framework->load('twaDebugger');
	$debugger->log("Warning! Use of deprecated function getData()");
	
	$sql = "SELECT ".$data['list_id']." FROM ".$data['table']." WHERE ".$this->meta['id']."=".$database->dbquote($this->fields[$this->meta['id']])." ";
	
	if(isset($data['filters']) && gettype($data['filters']) == 'array') {
		$comma = " AND ";
		foreach($data['filters'] as $filter) {
			$sql .= $comma." ".$filter['field']." ".$filter['is']." ".$database->dbquote($filter['value'])." ";
		}
	}
	
	if($result = $database->runQuery($sql.";"))
	{
		return $results;
	}
	return false;
	
}

/**
 * Get values stored in this model in JSON format
 *
 * 
 * @return string the JSON string for this model's fields
 * @access public
 */
public function getJSON() {
	return json_encode($this->fields);
}

}