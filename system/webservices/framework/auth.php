<?php
/**
 * The auth webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/auth".  This web-service can be used for authentication related activities like login, logut, signup forgot password etc.
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;
class twaWebServices_framework_auth extends twaWebServices {
/**
 * Login
 * Use for logging a user in.
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error
 * @access public
 */
public function login() {
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$user = $framework->getUser();
	
	if(!$user->isLoggedIn()) {
		if(!$user->Login($router->getCleanPost())) {
			$this->fail(201,$user->error);
		}
	}
	
	$user->Load();
	echo '{"returnCode":0,"user":'.$user->getJSON().',"social":'.json_encode($user->social()).'}';
}

public function socialLogin(){
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$user = $framework->getUser();
	$password = twaUser::getSocialPassword($router->getCleanPost());
	$new = "false";
	if(!$password) {
		ob_start();
		$this->signup();
		$new = "true";
		$result = ob_get_clean();
		$return = json_decode($result);
		if($return->returnCode == 1){
			$this->fail($return->errorCode,$return->error);
		}
		$password = twaUser::getSocialPassword($router->getCleanPost());
	}
	
	$data = $router->getCleanPost();
	$data['password'] = $password;
	
	if(!$user->isLoggedIn()) {
		if(!$user->Login($data)) {
			$this->fail(201,$user->error);
		}
	}
	
	$user->Load();
	echo '{"returnCode":0,"user":'.$user->getJSON().',"social":'.json_encode($user->social()).',"new":'.$new.'}';
}


/**
 * Get User Information
 * Use for getting a user's details.
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful along with user's information, and 1 on error
 * @access public
 */
public function user() {
	global $framework;
	$router = $framework->load('twaRouter');
	$nUser = new twaUser($router->getPost('id'));
	echo '{"returnCode":0,"user":'.$nUser->getJSON().'}';	
}
/**
 * Log the user out
 * Use for logging a user out
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error
 * @access public
 */
public function logout() {
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$user = $framework->getUser();
	if($user->Logout()) {
		echo '{"returnCode":0}';
	}	
}
/**
 * For sending emails to users that have forgotten their password.
 * When a user indicates they have forgotten their password, ask them for their email then call this service with their "email" sent as a POST variable.  The service sends the user an email with a reset link.
 * An authcode is sent in the reset link that can be used to retrieve the user's email on the reset password page if required.  It is recommended that you ask the user for their email.
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error
 * @access public
 */
public function forgotpassword() {
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$user = $framework->getUser();
	$data = $router->getCleanPost();
	if(!$user->ifExists(array(
		"field" => "email",
		"value" => $router->getPost('email')
	))) {
		$this->fail(203,"Email ID does not exist");
	}
	if(!$user->loadWithEmail($router->getCleanPost())) {
		$this->fail(204,"Unable to retrieve your account");
	}
	$auth = uniqid();
	
	$user->Update(array(
		"authcode" => $auth
	));
	
	if(isset($_SERVER['HTTPS']))
	{
		$protocol = "https://";
	}
	else
	{
		$protocol = "http://";
	}
		
	$url = $protocol.$_SERVER['SERVER_NAME'].str_replace('webservices.php','',$_SERVER['REQUEST_URI']);
	$url = $url.$data['view']."/".$auth;
	
	$info = array();
	$info['to'] = $user->fields['email'];
	$info['type'] = "TEMPLATE";
	$info['template'] = "forgotpassword";
	$info['subject'] = "Reset Your Password";
	$info['name'] = $user->fields['name'];
	$info['link'] = $url;
	
	if(!$app->controller->email($info)) {
		$this->fail(105,"Unable to send email");
	}
	echo '{"returnCode":0}';	
}
/**
 * For reseting password.
 * When a user clicks on the reset link ask them for the new password then call this service with their new "password" as a POST variable. You must also send their "email" as a POST variable.
 * An authcode is sent in the reset link that can be used to retrieve the user's email on the reset password page if required.  It is recommended that you ask the user for their email.
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error
 * @access public
 */
public function resetpassword() {
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$user = $framework->getUser();
	
	if(!$user->ifExists(array(
		"field" => "email",
		"value" => $router->getPost('email')
	))) {
		$this->fail(203,"Email ID does not exist");
		
	}
	if(!$user->loadWithEmail($router->getCleanPost())) {
		$this->fail(204,"Unable to retrieve your account");
	}
	$auth = uniqid();
	
	if($user->fields['authcode'] == $router->getPost('authcode'))
	{
		if(!$user->Update(array(
			"authcode" => $auth,
			"password" => $router->getPost('password')
		))) {
			$this->fail(106,$user->error);
		}
		echo '{"returnCode":0}';
	} else {
		$this->fail(205,"Invalid Auth Code");
	}	
		
}
/**
 * For registering a new user.
 * Register a new user with this service.  Must send the name, email and password through POST variables.
 * Additionally, username, firstname, lastname, approved and group_id may be sent in POST variables.  If name is not sent, then firstname and lastname keys can be used to create their full name.
 * If username is not sent, the first part of their email before the @ sign will be used. 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error
 * @access public
 */
public function signup() {
	global $framework;
	$router = $framework->load('twaRouter');
	global $app;
	$user = $framework->getUser();
	$newuser = new twaUser();
	$newuser->Logout();
	$data = $router->getCleanPost();
	
	if(!isset($data['name']))
	{
		$data['name'] = $router->getPost('firstname')." ".$router->getPost('lastname');
	}

	
	if(isset($data['type']) && $data['type'] == "social"){
		$data['password'] = uniqid();
	}
			
	
	if(!isset($data['firstname']) && !isset($data['lastname'])) {
		$p = explode(' ',$data['name'],2);
		$data['firstname'] = $p[0];
		$data['lastname'] = $p[1];
	}
	
	if(!isset($data['username']))
	{
		$uname = explode('@',$data['email']);
		$data['username'] = $uname[0];
	}
	
	if($newuser->ifExists(array("field"=>"email","value"=>$data['email']))) {
		$this->fail(206,"User Already Exists");
	}
			
	if(!$data['user_id']= $newuser->Create($data)){
		$this->fail(107,$newuser->error);
	}
	
	if(isset($data['group_id'])) {
		$database = $framework->getDB();
		$database->insert(array(
			"table"=>"#__user_group",
			"data" => array(
				array(
					"field"=>"group_id",
					"value"=>intval($data['group_id'])
				),
				array(
					"field"=>"user_id",
					"value"=>intval($newuser->fields['user_id'])
				)
			)
		));
	}
	if(isset($data['type']) && $data['type'] == "social"){
		$newuser->saveSocialProfile($data);
	}
	
	
	$newuser->Login($data);
	
	echo '{"returnCode":0,"user":'.json_encode($newuser->fields).'}';	
}
/**
 * To check if an email exists.
 * Call this service to check if an email exists in the DB.
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.  The exists key can be 0 / 1. 1 - email exists. 0 - email does not exist.
 * @access public
 */
public function checkemail() {
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$user = $framework->getUser();	
	if($user->ifExists(array(
		"field" => "email",
		"value" => $router->getPost('email')
	))) {
		echo '{"returnCode":0,"exists":1}';
	} else {
		echo '{"returnCode":0,"exists":0}';
	}
}
/**
 * To check if a user is an admin.
 * You can use twaConfig file to identify which users are administrators.  This is useful when the website is in maintenance mode.
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.  The admin key can be 0 / 1. 1 - user is admin. 0 - user is not admin.
 * @access public
 */
public function isadmin() {
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$user = $framework->getUser();
	if($user->isLoggedIn() && in_array($user->fields['user_id'], $framework->load('twaConfig')->admin_accounts)) {
		echo '{"returnCode":0,"admin":1}';
	} else {
		echo '{"returnCode":0,"admin":0}';
	}
}


}


?>