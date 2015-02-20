<?php
/**
 * The debugger webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/debugger".  
 * This web-service can be used to retreive/log debugger information.
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaWebServices_framework_debugger extends twaWebServices {

 /**
 * Check the value of a certain value
 * Checks the debugger watchlist to retrieve the value of a variable that was previously saved
 * 
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function check() {
	global $framework;
	$router = $framework->load('twaRouter');
	global $app;
	
	$debugger = $framework->load('twaDebugger');
	$val = $debugger->check($router->getCleanPost());
	
	echo '{"returnCode":0,"value":'.$val.'}';	
}
/**
 * Logs error into the log file if logging is enabled
 * Logs the error message send by POST variable "error" into the log file
 * 
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function log() {
	global $framework;
	$router = $framework->load('twaRouter');
	global $app;
	
	$debugger = $framework->load('twaDebugger');
	$val = $debugger->log(stripslashes($router->getPost('error')));
	
	echo '{"returnCode":0}';	
}
/**
 * Gets the watch list
 * Gets the watch list in the JSON format. 
 * 
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function getwatchlist() {
	$debugger = $this->framework->load('twaDebugger');
	echo '{"returnCode":0,"watchlist":{';
	if($debugger->watchlist) {
		
		$comma = "";
		foreach($debugger->watchlist as $field=>$value) {
			if (strpos($field, '_json') > 0 && $value != '') {
				echo $comma.'"'.$field.'":'.$value;
			} else {
				if (strpos($value, '"') > 0)
                    $value = htmlspecialchars($value);
				$value =  str_replace(array("\\","{","}"),array("&#92;","&#123;","&#125;"),$value);
				echo $comma.'"'.$field.'":"'.$value.'"';
			}
			$comma = ",";
		}
		
		$debugger->flush('watchlist');
	}
	echo '}}';
	
}
/**
 * Gets the error list
 * Gets the error list in the JSON format. 
 * 
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function geterrorlist() {
	$debugger = $this->framework->load('twaDebugger');
	echo '{"returnCode":0,"errorlist":[';
	if($debugger->errorlist) {
		
		$comma = "";
		foreach($debugger->errorlist as $value) {			
			if (strpos($value, '"') > 0)
                $value = htmlspecialchars($value);
			$value =  str_replace(array("\\","{","}"),array("&#92;","&#123;","&#125;"),$value);
			echo $comma.'"'.$value.'"';
			$comma = ",";
		}
		
		$debugger->flush('errorlist');
	}
	echo ']}';
}
/**
 * Gets the waypoints list
 * Gets the waypoints list in the JSON format. Waypoints are just watchlists frozen at a particular time.
 * 
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function getwaypoints() {
	$debugger = $this->framework->load('twaDebugger');
	echo '{"returnCode":0,"waypoints":{';
	if($debugger->waypoints) {
		
		$comma = "";
		foreach($debugger->waypoints as $time=>$list) {
			echo $comma.'"'.$time.'":{';
			if(strtolower(gettype($list)) == 'array' || strtolower(gettype($list)) == 'object') {
				$cma = "";
				foreach($list as $field=>$value) {
					if (strpos($field, '_json') > 0 && $value != '') {
						echo $cma.'"'.$field.'":'.$value;
					} else {
						if (strpos($value, '"') > 0)
		                    $value = htmlspecialchars($value);
						$value =  str_replace(array("\\","{","}"),array("&#92;","&#123;","&#125;"),$value);
						echo $cma.'"'.$field.'":"'.$value.'"';
					}
					$cma = ",";
				}
			}
			$comma = ",";
			echo '}';			
		}
		$debugger->flush('waypoints');
	}
	echo '}}';
}


}


?>