<?php
/**
 * The base twaModel object that can be extended to create typical data models.
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class _CoreModel {


public $fields = array();
public $protected_fields = array();

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
		return true;
	}
	return false;
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

		/* Check if primary key is already defined and set that */

		if($this->fields[$this->meta['id']] != ""){
			if(isset($data[$this->meta['id']])) {
				if($data[$this->meta['id']] != $this->fields[$this->meta['id']]) {
					$this->onError("Mismatched Primary Key.");
				}
			} else {
				$data[$this->meta['id']] = $this->fields[$this->meta['id']];
			}
		}

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
	 * Inserts a row into the database for the model or updates it.
	 *
	 * @param Array $data contains the values for all the fields
	 * @access public
	 */
	public function safeSave($data) {

		$clean = array();
		if($data){
			foreach($data as $k=>$v){
				if(isset($this->fields[$k]) && !in_array($k,$this->protected_fields)){
					$clean[$k] = $v;
				}
			}
		}
		return $this->Save($clean);
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

    /**
     * Get values stored in this model in JSON format
     *
     *
     * @return string the JSON string for this model's fields
     * @access public
     */
    public function getJSON() {
        $fields = array();
        foreach($this->fields as $k=>$v) {
            if(!in_array($k,$this->protected_fields)){
                $fields[$k] = $v;
            }
        }
        return json_encode($fields);
    }

    public function getFields(){
        $fields = array();
        foreach($this->fields as $k=>$v) {
            if(!in_array($k,$this->protected_fields)){
                $fields[$k] = $v;
            }
        }
        return $fields;
    }

}