<?php
/**
 * The main application object.  This class is initialized with a global variable $app declared in the index.php
 * The application instance is created in $framework->getApp();
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaApp {

/**
 * The application type
 *
 * @var string
 */
public $app_type=null;
/**
 * The name of the view
 *
 * @var string
 */
public $_viewid=null;
/**
 * The name of the controller
 *
 * @var string
 */
public $_controllerid=null;
public $language = null;
/**
 * The controller instance
 *
 * @var twaController
 */
public $controller = null;
/**
 * TRUE if html tag is open
 *
 * @var boolean
 */
protected $_isOpenHtml=null;

/**
 * The site url
 *
 * @var string
 */
public  $siteurl = null;
/**
 * The secure version of your url
 *
 * @var string
 */
public  $secureurl = null;
/**
 * An array that contains the metadata for the page.
 *
 * @var string
 */
public $meta = array();

/**
 * Create an instance, optionally setting a starting point.  Defines the view, the controller, siteurl and secureurl
 *
 * @param string $app_type the type of the app; defualts to "app"
 *
 * @access public
 */
public function __construct($app_type="app") {
	global $framework;

	$this->app_type = $app_type;
	$router = $framework->load('twaRouter');
	
	$this->_viewid=$router->getFromURL('view');
	$this->_controllerid=$router->getFromURL('controller');
	
	$controller_class = 'twaController_'.$this->_controllerid;
	$controllerpath=$framework->systempath.DS.'controllers'.DS.$this->_controllerid.DS.'controller.php';
	if(file_exists($controllerpath)) {
		include_once $controllerpath;
	} else {
		die('Could not find controller - '.$controllerpath);
	}
	
	$this->controller = new $controller_class();
	$this->siteurl = $router->getFromURL('siteurl');
	$this->secureurl = $router->getFromURL('secureurl');
	$this->language = $framework->load('twaLanguage');
}

/**
 * Renders the HTML for the page
 *
 *
 * @access public
 */
public function render() {
	$this->Initialize();
	$this->LoadHeader();
	$this->LoadBody();
	$this->CloseTag('html');
	
}

/**
 * Initializes the app. This is the first step of the render process.  Check if page has access.
 *
 *
 * @access public
 */
public function Initialize() {
global $framework;

$router = $framework->load('twaLanguage');
$config = $framework->load('twaConfig');

if($config->maintenanceMode) {
	$user = $framework->getUser();
	if(!$user->isLoggedIn() || !in_array($user->fields['user_id'], $config->admin_accounts)) {
		
		header('Location:'.$this->siteurl."maintenance.php") ;
	}
} else {
	
	if(!$this->hasAccess()) {
		$this->controller->unAuthorized();
	}
}


echo "<!DOCTYPE HTML>";
echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='".$this->language->config['code']."' lang='".$this->language->config['code']."' dir='".$this->language->config['dir']."' >";

$this->_isOpenHtml = 1;

}


/**
 * Loads the head section of the HTML. This is the second step of the render process. 
 *
 *
 * @access public
 */
public function LoadHeader() {
	global $framework;
	echo "<head>";
		$this->controller->head();
	echo "</head>";
}

/**
 * Loads the body section of the HTML. This is the third step of the render process. 
 *
 *
 * @access public
 */
public function LoadBody() {
	$this->controller->render();
}


/**
 * Check if the current user has access to the page requested. 
 *
 *
 * @return boolean true if user has access, false is user does not have access
 *
 * @access public
 */
public function hasAccess() {
	
	global $framework;
	$user = $framework->getUser();
	$router = $framework->load('twaRouter');
	$view = $router->getRoutes()->pages[$this->_viewid];
	if(!$view) {
		$view = $router->getRoutes()->pages['default'];
	}
	
	if(!isset($view['access']) || $view['access'] == AUTHORIZE_ALL) {
		return true;
	} else if ($view['access'] == AUTHORIZED_USERS_ONLY && $user->isLoggedIn()) {
		return true;
	} else if ($view['access'] == CUSTOM_ACCESS) {
		return $this->controller->hasAccess();
	}
	return false;
	
}

/**
 * Closes the HTML tag
 *
 *
 * @access public
 */
public function CloseTag($tag) {
	echo "</".$tag.">";
}

}