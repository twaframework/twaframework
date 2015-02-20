<?php
/**
 * The base twaDependency object that can be extended to create typical dependencies between objects.
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class twaDependency {

/**
 * The error string
 *
 * @var string
 */

public $error;

/**
 * The models array
 *
 * @var array
 */
public $models = array();

public $composite_key;

/**
 * The tablename string
 *
 * @var string
 */

public $tablename;
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
 * Starting point for twaDependency
 *
 * @param Array $ms contains the parent and child models
 * @param String $t contains the tablename in which this dependency is defined.
 * @access public
 */
public function __construct($ms,$t,$p = "") {
	$this->models = $ms; // Models Array
	$this->tablename = $t; //Table Name
	$this->composite_key = $p; //Composite Key of the Table Name
}

/**
 * The method for getting objects using this dependency
 *
 * @param Array $data contains parent_id, filters, sort and limit variables.
 * @access public
 */
public function get($data){
	global $framework;
    global $app;
	
	$parent = new $this->models['parent']($data['parent_id']);
	$child = new $this->models['child']();
	
	$child_meta_id = $child->meta['id'];
	
	$database = $framework->getDB();
	
	$sql = "SELECT ".$child_meta_id." FROM ".$this->tablename." WHERE ".$parent->meta['id']."=".$database->dbquote($parent->fields[$parent->meta['id']])." ";
	
	if(isset($data['filters']) && gettype($data['filters']) == 'array') {
		$comma = " AND ";
		foreach($data['filters'] as $filter) {
			$sql .= $comma." ".$filter." ";
		}
	}
	
	if(isset($data['sort']) && gettype($data['sort']) == 'array') {
		$comma = " ORDER BY ";
		foreach($data['sort'] as $sort) {
			$sql .= $comma." ".$sort." ";
			$comma = ",";
		}
	}
	
	if(isset($data['limit'])) {
		$sql .= " LIMIT ".$data['limit']." ";
	}	
	
	if($result = $database->runQuery($sql.";"))
	{
		
		$result_array = array();
		foreach($result as $object) {
			
			$result_array[$object->$child_meta_id] = new $this->models['child']($object->$child_meta_id);
			
		}
		return $result_array;
	}
	return false;
}

public function add($data){
	global $framework;
    global $app;
    $db = $framework->getDB();
    
    $parent = new $this->models['parent']($data['parent_id']);
	$child = new $this->models['child']();
	$child_meta_id = $child->meta['id'];
	
	$sql = "INSERT INTO ".$this->tablename." SET ";
	
	if($this->composite_key !== ""){
		$sql .= $this->composite_key." = ".$db->dbquote($data[$this->composite_key]).",";
	} 
	
	$sql .= $parent->meta['id']." = ".$db->dbquote($data['parent_id']);
	
	if($data){
		$comma = ",";
		foreach($data as $field=>$value){
			if($this->composite_key !== "" && $field !== $this->composite_key && $field != 'parent_id') {
				$sql .= $comma." ".$field." = ".$db->dbquote($value);
			}
		} 
	}
	$sql .= $comma." created_on = now(), last_updated_on = now() ON DUPLICATE KEY UPDATE last_updated_on = now()";
	
	$result = $db->runQuery($sql.";");
	if(FALSE !== $result){
		return true;
	}
	return false;
}

public function remove($data){
	global $framework;
    global $app;
    $db = $framework->getDB();
    $parent = new $this->models['parent']($data['parent_id']);
	$child = new $this->models['child']();
	$child_meta_id = $child->meta['id'];
	
	$sql = "DELETE FROM ".$this->tablename." WHERE ";
	
	if($this->composite_key !== ""){
		$sql .= $this->composite_key." = ".$db->dbquote($data[$this->composite_key])."";
	} else{
		$sql .= $parent->meta['id']." = ".$db->dbquote($data['parent_id'])." AND ".$child_meta_id." = ".$db->dbquote($data[$child_meta_id]);	
	}
	
	$result = $db->runQuery($sql.";");
	
	if(FALSE !== $result){
		return true;
	}
	
	return false;
}



}