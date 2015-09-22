<?php
/**
 * index.php, twaFramework
 * 
 * This file is the index file for twaFramework.
 * @author	Akshay Kolte <akshay@twaframework.com>
 * @version 8.0
 * @package twaFramework
 */

while (ob_get_level() > 0) { ob_end_clean() ; } 
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
	ob_start("ob_gzhandler");	
} else {
	ob_start();
}  

define('_TWACHK', 1); //Define a check constant
define('DS', DIRECTORY_SEPARATOR); //Directory Separator
define('BASE_PATH', dirname(__FILE__)); //The path at which this file exists.

session_start(); //Start Session

//Set cache expiration
/*
header("Cache-Control: public, max-age=315360000");
header("Expires: ".gmdate("D, d M Y H:i:s T", strtotime("+5 years")));
*/

//Error Reporting
error_reporting(E_ALL);
date_default_timezone_set('UTC');

ini_set("display_errors", "1");

$framework = null;
$app = null;
$model_paths = array();
/*Initialize the Framework*/
require_once BASE_PATH.'/system/framework/framework.php';
require(BASE_PATH.'/system/config/version.php');

if(!isset($_SESSION['_twa_auth_token'])) {
    $_SESSION['_twa_auth_token'] = md5(uniqid());
}

//Declare global variables.
require_once(BASE_PATH.'/system/config/globals.php');
require_once(BASE_PATH.'/system/config/functions.php');

spl_autoload_register('loadClasses'); //Ask the autoload register to use loadClasses as the function to autoload classes.

$framework = new twaFramework();
$app = $framework->getApp('app');

$framework->load('twaDebugger')->flushall(); //Empty Session Variables On Reload
set_error_handler("handleError",E_ALL);
set_exception_handler("handleException");
//Render the Application
$app->render();

?>