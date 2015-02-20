<?php
/**
 * The OAuth2 authentication class used to authenticating users when they access web-services. Extends the twaDataObjects class
 * @category system
 * @see twaDataObjects 
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;


class twaOAuth2 extends twaDataObjects {

/**
 * Storage object of the OAuth2 Library
 *
 * @var PDO
 */
public $storage;
/**
 * Server object of the OAuth2 Library
 *
 * @var Server
 */
public $server;
/**
 * Connect to the database for OAuth2
 *
 * 
 * @access public
 */
private function connectDB(){
	global $app;
	if(!$app->LoadModelPackage('pkg_oauth2')) {
		die('{"returnCode":1,"error":"OAUTH2 Package Is Not Installed"}');
	}
	switch($this->_driver) {
		case 'mysql':
		case 'pgsql':
			$this->storage = new OAuth2\Storage\Pdo(array('dsn' => $this->_driver.":"."host=".$this->_servername.";dbname=".$this->_dbname , 'username' => $this->_user, 'password' => $this->_password));
			
		break;
		case 'sqlite':
		case 'sqlite2':
			$this->storage = new OAuth2\Storage\Pdo(array('dsn' => $this->_driver.":".$this->_path , 'username' => $this->_user, 'password' => $this->_password));
			
		break;
		case 'sqlsrv':
			$this->storage = new OAuth2\Storage\Pdo(array('dsn' =>$this->_driver.":"."Server=".$this->_servername.";Database=".$this->_dbname , 'username' => $this->_user, 'password' => $this->_password));
		break;
		case 'odbc':
			$this->storage = new OAuth2\Storage\Pdo(array('dsn' =>$this->_driver.":"."Driver".$this->_odbc_driver.";Server=".$this->_servername.";Database=".$this->_dbname.";Uid=".$this->_user.";Pwd=".$this->_password, 'username' => $this->_user, 'password' => $this->_password));
		break;	
	}
}
/**
 * Initialize the OAuth2 server
 *
 * 
 * @access public
 */

public function initServer() {
	global $framework;
	
	if(!$this->conf->isDBConfigured) {
		return FALSE;
	}
	if(!$this->storage) {
		$this->connectDB();
	}
	
	$this->server = new OAuth2\Server($this->storage);
	// Add the "Client Credentials" grant type (it is the simplest of the grant types)
	$server->addGrantType(new OAuth2\GrantType\ClientCredentials($this->storage));
	
	// Add the "Authorization Code" grant type (this is where the oauth magic happens)
	$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->storage));
}



}

?>