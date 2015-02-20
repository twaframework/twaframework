<?php
/**
 * The model webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/model".  
 * This web-service contains all the actions related to the extensions of the standard twaModel class. Typical tasks like save, get, getlist and delete
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaWebServices_framework_model extends twaWebServices {
/**
 * Save Record in Database
 * This method checks if a record exists in the database.  If it does then the it updates the row with the new data or inserts a new row based on the data provided.
 * POST variables must contain pkg key if a model package needs to be loaded, a object key to identify which model you want to create an instance of and a data key that contains all the values
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.  The object's data that was saved is returned.
 * @access public
 */
public function save() {
	global $framework;
	$router = $framework->load('twaRouter');
	global $app;
	$post = $router->getCleanPost();
	$class = $post['object'];
	
	$object = new $class(null);
	
	if(!$object->Save($post['data'])) {
		$this->fail(111,"Unable to create");
	}
	
	echo '{"returnCode":0,"'.$post['object'].'":'.$object->getJSON().'}';	
}

/**
 * Get A Record from Database
 * This method returns the model information in JSON format .
 * POST variables must contain pkg key if a model package needs to be loaded, a object key to identify which model you want to create an instance of and the <id> key that contains the id for which the data should be retrieved
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error. The name of the object is the key which contains the JSON data for that object.
 * @access public
 */
public function get() {
	global $framework;
	$router = $framework->load('twaRouter');
	global $app;
	$app->LoadModelPackage($router->getPost('pkg'));
	$post = $router->getCleanPost();
	$class = $post['object'];
	
	$object = new $class(null);
	$object->fields[$object->meta['id']] = $post[$object->meta['id']];
	$object->Load();
	
	echo '{"returnCode":0,"'.$post['object'].'":'.$object->getJSON().'}';
}

/**
 * Get A List of Rows From DB
 * This method returns a list of rows for the matching criteria
 * POST variables must contain pkg key if a model package needs to be loaded, a object key to identify which model you want to create an instance of and the <id> key that has to be matched
 * The data key may also contain filters, or sort_order keys
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error. The list key returns an JSON array of the rows
 * @access public
 */
public function getlist() {
	global $framework;
	$router = $framework->load('twaRouter');
	global $app;
	
	$app->LoadModelPackage($router->getPost('pkg'));
	$post = $router->getCleanPost();
	$class = $post['object'];
	
	$object = new $class(null);
	$object->fields[$object->meta['id']] = $post[$object->meta['id']];
	$object->Load();
	
	$list = $object->getList($post['data']);
	if(!$list) {
		die('{"returnCode":0,"list":[]}');
	} else {
		$comma = "";
		echo '{"returnCode":0,"list":[';
		foreach($list as $item) {
			echo $comma.$item->getJSON();
			$comma = ",";
		}
		echo ']}';
	}
		
}

/**
 * Delete A Row From the DB
 * This method deletes a row for the matching criteria
 * POST variables must contain pkg key if a model package needs to be loaded, a object key to identify which model you want to create an instance of and the <id> key that has to be deleted
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function delete() {
	global $framework;
	$router = $framework->load('twaRouter');
	global $app;
	
	$post = $router->getCleanPost();
	$object = new $post['object']();
	$object->fields[$object->meta['id']] = $post[$object->meta['id']];
	$object->Load();
	$object->Delete();
	
	echo '{"returnCode":0}';

}
/**
 * Execute a method in the extension of the twaModel Class
 * This method executes a method on any model object
 * POST variables must contain pkg key if a model package needs to be loaded, a object key to identify which model you want to create an instance of and the function key that identifies which method should be run.
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function run() {
	global $framework;
	$router = $framework->load('twaRouter');
	global $app;
	
	$app->LoadModelPackage($router->getPost('pkg'));
	$post = $router->getCleanPost();
	$object = new $post['object']();
	$object->fields[$object->meta['id']] = $post[$object->meta['id']];
	$object->Load();
	$object->$post['function']($post['data']);
	
	echo '{"returnCode":0}';

}

}


?>