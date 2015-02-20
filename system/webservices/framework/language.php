<?php
/**
 * The language webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/language".  
 * This web-service can be used to set or change the language parameter.
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;


class twaWebServices_framework_language extends twaWebServices {
/**
 * Changes the Language
 * This service is called when you want to change the language in the session variable.
 * POST variables must specify the lang key which indicates the language.
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function change() {
	global $framework;
	$router = $framework->load('twaRouter');
	global $app;
	
	$lang = $framework->load('twaLanguage');
	$lang->setLanguage($router->getPost('lang'));	
	echo '{"returnCode":0}';	
}

}


?>