<?php
/**
 * The load webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/load".  
 * This web-service can be used to retrieve an HTML/PHP file located on the server and return its contents.
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaWebServices_framework_load extends twaWebServices {

 /**
 * Loads the HTML
 *  This web-service can be used to retrieve an HTML/PHP file located on the server and return its contents.
 * 
 * 
 * @return String contains the HTML of the page.
 * @access public
 */
public function load () {
    global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	require $framework->basepath.DS.$router->getPost('path');
}

 /**
 * Loads the HTML
 *  This web-service can be used to retrieve an HTML/PHP file located on the server and return its contents.
 * 
 * 
 * @return String contains the HTML of the page.
 * @access public
 */
public function component () {
    global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$app->controller->add($router->getPost('component'));	
}

}