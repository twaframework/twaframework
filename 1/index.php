<?php

define('_TWACHK', 1);
define('DS', DIRECTORY_SEPARATOR);
define('F_PATH', dirname(__FILE__));
define('BASE_PATH', F_PATH.'/..');
header('Access-Control-Allow-Origin: *');  
session_start(); // Start Session

//Error Reporting
error_reporting(E_ALL);
date_default_timezone_set('UTC');

ini_set("display_errors", "1");

$framework = null;
$app = null;
$analytics = null;

/*Initialize the Framework*/
require_once BASE_PATH.'/system/framework/framework.php';
require(BASE_PATH.'/system/config/version.php');
//Declare global variables.
require_once(BASE_PATH.'/system/config/globals.php');
require_once(BASE_PATH.'/system/config/functions.php');

spl_autoload_register('loadClasses');//Ask the autoload register to use loadClasses as the function to autoload classes.

$framework = new twaFramework();
$app = $framework->getApp('app');
$analytics = new twaAnalytics();
$router = $framework->load('twaRouter');
$router->parseWebService();

set_error_handler("handleError",E_ALL);

$class = $router->getFromURL('class');
$object = new $class();
$code = $router->getFromURL('code');
$object->$code();

?>