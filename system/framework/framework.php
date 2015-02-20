<?php
/**
 * The main object for twaFramework.  This class is initialized with a global variable $framework declared in the index.php
 * @category system
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;
defined('BASE_PATH') or die;


class twaFramework {

/**
 * The application
 *
 * @var twaApp
 */
public static $app;
/**
 * An array containing database objects
 *
 * @var Array
 */
public $databases=array();
/**
 * An array containing loaded objects
 *
 * @var Array
 */
public $objects = array();
/**
 * The user object
 *
 * @var twaUser
 */
public static $user;
/**
 * An object that contains the global settings
 *
 * @var Array
 */
public static $settings;
/**
 * The path of the root folder.
 *
 * @var Array
 */
public  $basepath; //path to root directory
/**
 * The path of the systems folder.
 *
 * @var string
 */
public  $systempath=null; // System Folder Path
/**
 * The path of the web_content folder.
 *
 * @var string
 */
public  $contentpath=null; // System Folder Path


/**
 * Create an instance, optionally setting a starting point
 *
 * @access public
 */
public function __construct() {

	$this->basepath = BASE_PATH;
	$this->systempath = BASE_PATH.DS."system";
	$this->contentpath = BASE_PATH.DS."web_content";
}

/**
 * Create an instance of any object or load one that has been already initialzed.
 *
 * @param string $object the object name for which you want to load an instance
 *
 * @return mixed an instance of the object
 *
 * @access public
 */
public function load($object) {
	if (!isset($this->objects[$object])) {
			$this->objects[$object] = new $object();
	}
	return $this->objects[$object];
}


/**
 * Create an instance of the database or return a one that has already been initialized.
 *
 * @param string $db the database name
 *
 * @return twaDataObjects an instance of the database
 *
 * @access public
 */
public function getDB($db = 'default') {
	
	if (!isset($this->databases[$db]) || $this->databases[$db] == null) {
			$this->databases[$db] = new twaDataObjects($db);
	}
	return $this->databases[$db];
}

/**
 * Get an instance of the application.
 *
 * @param string $type the application type
 *
 * @return twaApp an instance of the app
 *
 * @access public
 * @see twaFramework::$app
 */
public function getApp($type="app") {
	
	if (!self::$app) {
		self::$app = new twaApp($type);	
	}
	return self::$app;	
}

/**
 * Get an instance of the user object.
 *
 * @param string $id the user id
 *
 * @return twaUser an instance of the user object
 *
 * @access public
 */
public function getUser($id = null) {	
	
	if($id == null) {	
		if (!self::$user) {	
				self::$user = new twaUser();
		}
		return self::$user;			
	} else {
		return new twaUser($id);
	}
}

/**
 * Get the global settings
 *
 * @return Array an array with the global settings
 *
 * @access public
 */
public function globalsettings() {
	try {
		if(!self::$settings) {
			if(file_exists($this->systempath.DS.'config'.DS.'global_settings.json')) {
				$json = file_get_contents($this->systempath.DS.'config'.DS.'global_settings.json');
				self::$settings = json_decode($json);
				return self::$settings; 
			} 
		}
		return self::$settings;
	} catch(Exception $e) {
		return false;
	}
}

}
?>
