<?php
/**
 * globals.php, twaFramework
 * 
 * Use this file to define global variables available throughout the app.
 * @author	Akshay Kolte <akshay@twaframework.com>
 * @version 8.0
 * @package twaFramework
 */


 $phpv = explode('.', PHP_VERSION);
 $phpi = $phpv[0] * 10000 + $phpv[1] * 100 + $phpv[2];
 
 if($phpi < 50307) {
	die("PHP Version Below 5.3.7 is not supported.");	 
 } else if($phpi < 50500) {
	 require_once('password.php');
 } 
 
 if(!isset($_SESSION['_org_host'])) {
	 $_url = $_SERVER['HTTP_HOST'];
	 $_params = explode('.', $_url);
	 if($_params[0] == "www") {
		 $_SESSION['_org_host'] = $_params[1];
	 } else {
		 $_SESSION['_org_host'] = $_params[0];
	 }
}

/* Model Paths To Load - Add the path of your model directory to load your classes. */

global $model_paths;
$model_paths = array(
	'system/framework/',
	'system/config/',
    'system/config/databases/'
);


/*********** TO USE TWITTER LOGIN *************/
define('TWITTER_CONSUMER_KEY', '');
define('TWITTER_CONSUMER_SECRET', '');
$model_paths[] ='system/models/twitter/';

?>
