<?php
/**
 * The general webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/general".  This webservice contains general methods that do not fall under other categories.
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class twaWebServices_framework_general extends twaWebServices {
/**
 * Check if a remote file exists.
 * This service is useful when trying to find out if a file exists on a remote location.
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function checkRemoteFile() {
	global $framework;
	$router = $framework->load('twaRouter');
	if($router->checkRemoteFile($_POST['url'])) {
		echo '{"returnCode":0}';
	} else {
		echo '{"returnCode":1}';
	}
}
/**
 * Use this service to save global settings.
 * This service is used by admin.php to save global settings on a website.
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function savesettings() {
	$path = $this->framework->systempath.DS.'config'.DS.'global_settings.json';
	if(!file_put_contents($path, $this->router->getPost('settings'))) {
		$this->fail(110,"Unable to Save Settings");
	}
	echo '{"returnCode":0}';
}

}


?>
