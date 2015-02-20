<?php
/**
 * The basic authentication class used to authenticating users when they access web-services. Extends the twaModel class
 * @category system
 * @see twaModel 
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;


class twaBasicAuth extends twaModel {

/**
 * Create an instance, optionally setting a starting point.
 *
 * @access public
 */
public function __construct() {
	$this->meta = array(
		"tablename" => "#__basic_auth",
		"id" => "id"
	);
	
	$this->fields = array(
		"id" => "",
		"api_key"  => "",
		"secret"  => "",
		"user_id"  => "",
		"approved"  => "",
		"blocked" => "",
		"created_on"  => "",
		"last_updated_on"  => "",
		"last_logged_in" => ""
	);
	
}

/**
 * Authenticate the user request
 *
 * @param Array $data contains the api_key, and secret that can be then used to authenticate a user.
 *
 * @return boolean true if user has access, false is user does not have access
 *
 * @access public
 */
public static function authenticate($data) {
	global $framework;
	$database = $framework->getDB();
	
	if(isset($data['api_key'])) {
		$return = $database->runQuery("SELECT * FROM #__basic_auth WHERE api_key=".$database->dbquote($data['api_key']).";");
	} else {
		return false;
	}
	
	if($return) {
		$hash = $return[0]->secret;
		
		if($return[0]->approved == '0') {
			return false;
		}
		
		if(isset($return[0]->blocked) && $return[0]->blocked == '1') {
			return FALSE;
		}
		
		if(password_verify($data['secret'], $hash)) {
			
			$authcode = md5($data['api_key']);
			$_SESSION['_twa_api_auth'] = $authcode;
			$_SESSION['_twa_api_id']= $return[0]->id;
			
			$this->Update(array(
				"last_logged_in" => date('Y-m-d g:i:s',strtotime('now'))
			));
			
			return true;
		
		}
		
	}
	return false;

}

/**
 * Gets the user object based on the authentication
 *
 * @return boolean false if there is an error
 *
 * @access public
 */
public static function getUser() {
	if(isset($_SESSION['_twa_api_id'])) {
		$this->fields['id'] = $_SESSION['_twa_api_id'];
		$this->Load();
		if($this->fields['user_id'] != '' && $this->fields['user_id'] != 0) {
			return new twaUser($this->fields['user_id']);
		}
	}
	return false;
}


}

?>