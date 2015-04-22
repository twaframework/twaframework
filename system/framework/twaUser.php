<?php
/**
 * The base twaUser object defining users. Extends twaModel
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class twaUser extends twaModel {

public function onError($error) {
	$this->error = $error;
}
/**
 * Starting point for twaUser.
 *
 * Get information for the user id specified.  If no user id is specified check the session variable and load the saved user id.
 * @param int $id User ID to be loaded 
 * @access public
 */
public function __construct($id=null) {
	$this->meta = array(
		"tablename" => "#__users",
		"id" => "user_id"
	);
	
	$this->fields = array(
		"user_id" => "",
		"name"  => "",
		"username"  => "",
		"password"  => "",
		"authcode" => "",
		"email"  => "",
		"firstname"  => "",
		"lastname"  => "",
		"approved"  => "",
		"blocked" => "",
		"cookie" => "",
		"created_on"  => "",
		"last_updated_on"  => "",
		"last_logged_in" => ""
	);

    $this->protected_fields = array("password","authcode","cookie");
	
	if(!$id)
	{
		if(isset($_SESSION['twaUserID'])){
			
			$this->fields[$this->meta['id']]=$_SESSION['twaUserID'];
			$this->Load();	
		}
		else {
			$this->fields[$this->meta['id']]=0;
			$this->fields['username']='guest';
		}
	}
	else {
		
		$this->fields[$this->meta['id']]=$id;
		$this->Load();	
	}
	
}
/**
 * Load the user id using their email address
 *
 * Load information about the user from the email address.
 * @param Array $data Array containing the email address.
 * @access public
 */

public function loadWithEmail($data) {
	global $framework;
	$aUser = $framework->getUser();
	$database = $framework->getDB();
	$str_select = "SELECT * FROM ".$this->meta['tablename']." WHERE email= ".$database->dbquote($data['email'])."";
	
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
 * Load the user id using their authorization code.
 *
 * Load information about the user from their authorization code.  Useful for approving users or validating emails
 * @param Array $data Array containing the authcode.
 * @return boolean TRUE on success, FALSE if fail
 * @access public
 */

public function loadWithAuthCode($data) {
	global $framework;
	$aUser = $framework->getUser();
	$database = $framework->getDB();
	$str_select = "SELECT * FROM ".$this->meta['tablename']." WHERE authcode=".$database->dbquote($data['authcode'])."";
	
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

public static function getSocialPassword($data){
	global $framework;
	$db = $framework->getDB();
	$sql = "SELECT password FROM #__user_social WHERE ";
	if(isset($data['fb_id'])){
		$sql .= " fb_id = ".$db->dbquote($data['fb_id'])." ";
	} else if (isset($data['gplus_id'])){
		$sql .= " gplus_id = ".$db->dbquote($data['gplus_id'])." ";
	} else if (isset($data['linkedin_id'])){
		$sql .= " linkedin_id = ".$db->dbquote($data['linkedin_id'])." ";
	} else if (isset($data['twitter_id'])) {
		$sql .= " twitter_id = ".$db->dbquote($data['twitter_id'])." ";
	} else {
		return false;
	}
	
	$result = $db->runQuery($sql.";");
	
	if($result){
		return $result[0]->password;
	}
	
	return false;
	
}

public function social() {
	global $framework;
	$db = $framework->getDB();
	$sql = "SELECT * FROM #__user_social WHERE user_id = ".$db->dbquote($this->fields['user_id']);
	$result = $db->runQuery($sql.";");
	
	if($result){
		return $result[0];
	}
	
	return array();
}

public function saveSocialProfile($data){
	global $framework;
	global $app;
	
	$data['user_id'] = $this->fields['user_id'];
	
	$database = $framework->getDB();
	$debugger = $framework->load('twaDebugger');
	
	$fields = array(
		"fb_id"=>"",
		"gplus_id"=>"",
		"twitter_id"=>"",
		"linkedin_id"=>"",
		"password"=>"",
		"user_id"=>""
	);
	
	$str_insert = "INSERT INTO #__user_social SET "; 
	
	if($fields) {
		$comma = "";
		foreach($fields as $field=>$value) {
			
			if(isset($data[$field])) {
				$str_insert .=	$comma." `".$field."` = ".$database->dbquote($data[$field])." ";	
				$comma = ",";
			}
			
		}
	}
	
	$str_insert .= ", created_on = UTC_TIMESTAMP(), last_updated_on = UTC_TIMESTAMP() ON DUPLICATE KEY UPDATE ";
	
	if($fields) {
		$comma = "";
		foreach($fields as $field=>$value) {
			
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
		
		return true;
	}
	return false;
}

/**
 * Create the user in the database
 *
 * Use the details of the user and create a record in the #__users table.
 * @param Array $data Array containing the name, email, username and password. Additionally array may contain firstname, lastname, approved
 * @return mixed The user id of the newly created user if successful, FALSE if fails
 * @access public
 */
public function Create($data) {
	global $framework;
	global $app;
	
	$database = $framework->getDB();
	
	if(isset($data['password'])) {
 		$options = array(
		    'cost' => 12,
		);
		$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, $options);
 	}

    $data['user_id'] = md5(uniqid());

	$sql = "SELECT * FROM #__blocked_user_list WHERE email=".$database->dbquote($data['email'])."";
	if($database->runQuery($sql)) {
		$this->onError("Account suspended. Contact Administrator.");
		return false;
	}
	
	$str_insert = "INSERT INTO ".$this->meta['tablename']." SET "; 
	
	if($this->fields) {
		$comma = "";
		foreach($this->fields as $field=>$value) {
			
			if(isset($data[$field])) {
				$str_insert .=	$comma." ".$field." = ".$database->dbquote($data[$field])." ";	
				$comma = ",";
			}
			
		}
	}
	
	$str_insert .= " , created_on = now(), last_updated_on = now()";
	$r = $database->runQuery($str_insert.";");

	if($r !== FALSE) {
		$this->fields[$this->meta['id']] = $data['user_id'];
		$this->Load();	
		return $this->fields[$this->meta['id']];
	}
	return false;
	
}
/**
 * Update the user information
 *
 * Update the user data in the #__users table.
 * @param Array $data Array containing the user details.
 * @return boolean TRUE on success, FALSE if fail
 * @access public
 */
public function Update($data)
{
    global $framework;
    global $app;

    if (isset($data['password'])) {
        $options = array(
            'cost' => 12,
        );
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, $options);
    }

    $database = $framework->getDB();
    $sql = "UPDATE " . $this->meta['tablename'] . " SET ";

    if ($this->fields) {
        $comma = "";
        foreach ($this->fields as $field => $value) {

            if (isset($data[$field])) {

                $sql .= $comma . " " . $field . " = " . $database->dbquote($data[$field]) . " ";

                $comma = ",";
            }

        }
    }
    $sql .= " , last_updated_on = now() WHERE " . $this->meta['id'] . "=" . $database->dbquote($this->fields[$this->meta['id']]) . "";
    $r = $database->runQuery($sql . ";");
    if ($r !== FALSE) {
        $this->Load();
        return true;
    }
    return false;

}

/**
 * Check if user is logged in
 *
 * Check the session variables to confirm if the user is  logged in
 * 
 * @return boolean TRUE if user id logged in, FALSE if user is not logged in
 * @access public
 */
public function isLoggedIn() {

	if(isset($_SESSION['twatoken'])&&isset($_SESSION['twaUserID']) && $_SESSION['twaUserID'] == $this->fields[$this->meta['id']] && md5($this->fields['email']) == $_SESSION['twatoken']) {
		return TRUE;	
	}
	else {
		return FALSE;
	}
	
}
/**
 * Log the user in
 *
 * Log the user in by verifying their credentials.  Credentials can be username or email and password.
 * @param Array $data contains the user's credentials.  username or email and password keys
 * @return boolean TRUE if is logged in successfully, FALSE if user is not logged in
 * @access public
 */
public function Login($data) {
	global $framework;
	$database = $framework->getDB();
	if(isset($data['email'])) {
		$return = $database->runQuery("SELECT * FROM #__users WHERE email=".$database->dbquote($data['email']).";");
	} else {
		return false;
	}
	
	if($return) {
		$hash = $return[0]->password;
		
		if($return[0]->approved == '0') {
			$framework->load('twaDebugger')->log('User is not approved.');
			$this->onError("User is not approved.");
			return FALSE;
		}

        $sql = "SELECT * FROM #__blocked_user_list WHERE email=".$database->dbquote($data['email'])."";
        if($database->runQuery($sql)) {
            $this->onError("Account suspended. Contact Administrator.");
            return false;
        }
	
		if(password_verify($data['password'], $hash)) {
			if(!$this->isLoggedIn()){

				$authcode = md5($data['email']);
				$_SESSION['twatoken'] = $authcode;
				$_SESSION['twaUserID']= $return[0]->user_id;
				$this->fields[$this->meta['id']] = $return[0]->user_id;
				
				$this->Update(array(
					"last_logged_in" => date('Y-m-d g:i:s',strtotime('now'))
				));

				return TRUE;
			} else {
				$framework->load('twaDebugger')->log('User '.$return[0]->user_id.' is already logged in.');
				$this->onError("User ".$return->user_id." is already logged in.");

				return TRUE;
			}
		}
		else {
			$framework->load('twaDebugger')->log('Incorrect Password for  user '.$return[0]->user_id);
			$this->onError("Incorrect Email / Password ");
			return FALSE;
		}
	}
	else {
		$framework->load('twaDebugger')->log('Unable to find user id');
		$this->onError("Unable to find user id");
		
		return FALSE;
	}
	
	
}
/**
 * Log the user out
 *
 * Log the user out by unsetting the session variable.
 *
 * @return boolean TRUE if is logged out successfully
 * @access public
 */
public function Logout() {
	unset($_SESSION['twatoken']);
	unset($_SESSION['twaUserID']);
	return TRUE;
}
/**
 * Check if user has access
 *
 * Check is the user has access to the accesslevel provided
 * @param String $accesslevels a comma separated list of accesslevel id.
 * @return boolean TRUE if user has access, FALSE if user does not have access
 * @access public
 */
public function hasAccess($accesslevels="") {
	global $framework;
	$database = $framework->getDB();

	if($this->fields[$this->meta['id']]===0 && !$accesslevels) {
		return TRUE;
	}
	else {
		
		$result=$database->runQuery("SELECT a.group_id from #__user_group a, #__group_access b WHERE a.user_id=".$database->dbquote($this->fields[$this->meta['id']])." AND a.group_id=b.group_id AND b.access_id IN(".$accesslevels.");");
		if($result) {
			return TRUE;
		}
		return FALSE;
	}
	return FALSE;
}



}
?>