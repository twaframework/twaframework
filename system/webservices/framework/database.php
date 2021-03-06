<?php
/**
 * The database webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/database".  
 * This web-service can be used to run a SQL file located on the server or select rows from the database directly.
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaWebServices_framework_database extends twaWebServices {
/**
 * Runs a SQL file on the server
 * Run a SQL file specified in the filename key.
 * POST variables must specify the filename and the db
 * NOTE:  The file is deleted after it has been executed.
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */

public function runSQL() {
	global $framework;
	$router = $framework->load('twaRouter');

	$database = $framework->getDB($router->getPost('db'));
	$result = $database->runSQLFile(BASE_PATH.DS.$router->getPost('filename'));
	
	if(!$result) {
		$this->fail(108,"Unable to run file");
		
	} else {
		unlink(BASE_PATH.DS.$router->getPost('filename'));
		echo '{"returnCode":0}';	
	}
}


/**
 * Tests to see if DB is working correctly
 * Tests to see if the DB is working/responding
 * POST variables must specify the db
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function test() {
	$cname = "twaDBConfig_".$this->router->getPost('db');
	$debugger = $this->framework->load('twaDebugger');
	$conf = new $cname();
	if($conf->isDBConfigured) {
		$db = $this->framework->getDB();
		$sql = "SHOW TABLES";
		if(!$db->runQuery($sql)) {
		
			$debugger->log("Unable to connect");
			$this->fail("Unable to connect");
			
		}
	} else {
		
		$debugger->log("DB Not Configured");
		$this->fail(207,"DB Not Configured");
	}
	
	echo '{"returnCode":0}';
}



}


?>