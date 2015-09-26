<?php
/**
 * webservices.php, twaFramework
 * 
 * This file is called by ajax requests from the site or external web-service requests.
 * @author	Akshay Kolte <akshay@twaframework.com>
 * @version 8.0
 * @package twaFramework
 */
?>
<?php

define('_TWACHK', 1);
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__FILE__));

session_start(); // Start Session
date_default_timezone_set('UTC');

//Error Reporting
error_reporting(E_ALL);
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

set_error_handler("handleError",E_ALL);

$axn= $_POST['axn'];
$code = $_POST['code'];

if(!$axn || $axn == ""){
	$axn = $_POST['endpoint'];
}

if(!$code || $code == ""){
	$code = $_POST['method'];
}

$framework->load('twaDebugger')->dump($_POST);

if(file_exists($framework->systempath.DS.'webservices'.DS.$axn.".php")) {
	try {
        //Load the web-services file as defined in axn.
        require_once $framework->systempath.DS.'webservices'.DS.$axn.".php";

        $path = str_replace('/','_',$axn);

        $class = 'twaWebServices_'.$path;

        $o = new $class(); //Get the class defined by axn.

        $o->$code();//Run the function as defined by code.
    } catch(Exception $e){
        handleException($e);
        die('{"returnCode":1,"error":"Web Service Failed"}');
    }
} else {
	die('{"returnCode":1,"error":"Web Service Not Found"}');
}


?>