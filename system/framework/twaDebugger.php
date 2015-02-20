<?php
/**
 * The debugger object.  The debugger can be loaded as follows:
 * <pre>
 *  $framework->load('twaDebugger');
 * </pre>
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;


class twaDebugger {

/**
 * An array containing the all the variables for which you want to watch the value
 *
 * @var Array
 */
public $watchlist = array();
/**
 * An array containing the all the errors
 *
 * @var Array
 */
public $errorlist = array();
/**
 * An array containing the all the waypoints.  Freeze watchlists at different times to have waypoints.
 *
 * @var Array
 */
public $waypoints = array();
/**
 * Create an instance, optionally setting a starting point.
 *
 * @access public
 */
public function __construct() {
	global $framework;
	$logsettings = $framework->globalsettings()->logsettings;
	if(!isset($logsettings->enabledebugger) || $logsettings->enabledebugger != 1) {
		return;
	}
	
	if(isset($_SESSION['_twadebug'])) {
		$this->watchlist = $_SESSION['_twadebug'];
	}
	if(isset($_SESSION['_twawaypoint'])) {
		$this->waypoints = $_SESSION['_twawaypoint'];
	}
	if(isset($_SESSION['_twaerrorlist'])) {
		$this->errorlist = $_SESSION['_twaerrorlist'];
	}
}
/**
 * Delete all the saved data
 *
 * @access public
 */
public function flushall() {
	if(isset($_SESSION['_twadebug']))
		unset($_SESSION['_twadebug']);
	
	if(isset($_SESSION['_twawaypoint']))
		unset($_SESSION['_twawaypoint']);
	
	if(isset($_SESSION['_twaerrorlist']))
		unset($_SESSION['_twaerrorlist']);
		
	$this->watchlist = array();
	$this->errorlist = array();
	$this->waypoints = array();
}
/**
 * Delete the saved data for the speficied type
 *
 * @param string $what watchlist | errorlist | waypoints depending on which data you want to clear
 * @access public
 */
public function flush($what) {
	switch($what) {
		case 'watchlist':
			unset($_SESSION['_twadebug']);
			$this->watchlist = array();
		break;
		case 'errorlist':
			unset($_SESSION['_twaerrorlist']);
			$this->errorlist = array();

		break;
		case 'waypoints':
			unset($_SESSION['_twawaypoint']);
			$this->waypoints = array();	
		
		break;
	}	
}
/**
 * Logs the error
 *
 * @param string $errorstring the error string to log
 * @access public
 */
public function log($errorstring) {
	
	global $framework;
	
	if($globalsettings = $framework->globalsettings()) {
		$logsettings = $globalsettings->logsettings;
	}
	$today = date('Y-m-d',strtotime('now'));
	$time = date('g:i',strtotime('now'));
	
	if(isset($logsettings->enabledebugger) && $logsettings->enabledebugger == 1) {
		$estring = "".$today." ".$time." ".$errorstring;
		$this->errorlist[] = $estring;
		$_SESSION['_twaerrorlist'] = array();
		$_SESSION['_twaerrorlist'] = $this->errorlist;
	}
	
	if(!isset($logsettings->enablelogging) || $logsettings->enablelogging != 1) {
		return;
	} 
	$errorstring = $today." ".$time." ".$errorstring;
	$handler = fopen($framework->systempath.DS.'logs'.DS.'systemlog-'.$today.'.log','a');
	fwrite($handler,$errorstring.PHP_EOL);
	fclose($handler);	
}
/**
 * Adds a variable to the watch list
 *
 * @param string $name the name to store the value
 * @param mixed  $var the variable to be watched
 * @access public
 */
public function watch($name , $var) {
	global $framework;
	$logsettings = $framework->globalsettings()->logsettings;
	if(!isset($logsettings->enabledebugger) || $logsettings->enabledebugger != 1) {
		return;
	}
	$this->watchlist[$name] = $var;
	$_SESSION['_twadebug'] = array();
	$_SESSION['_twadebug'] = $this->watchlist;
}
/**
 * Check the value of a watchlist variable
 *
 * @param Array $data contains the name of the variable to retrieve
 * @access public
 */
public function check($data) {
	if(isset($_SESSION['_twadebug'])) {
		$this->watchlist = $_SESSION['_twadebug'];
		return $this->watchlist[$data['name']];
	}
	return false;
}
/**
 * Save the current watchlist as a waypoint
 *
 * 
 * @access public
 */
public function setWaypoint() {
	global $framework;
	$logsettings = $framework->globalsettings()->logsettings;
	if(!isset($logsettings->enabledebugger) || $logsettings->enabledebugger != 1) {
		return;
	}
	$time = strtotime('now');
	$this->waypoints[$time] = $this->watchlist;
	$_SESSION['_twawaypoint'] = array();
	$_SESSION['_twawaypoint'] = $this->waypoints;
}
/**
 * Perform a var_dump on the variable and write the results to the log
 *
 * @param mixed $var the variable which needs to be dumped.
 * @access public
 */
public function dump($var) {
	ob_start();
	var_dump($var);
	$result = ob_get_clean();
	$this->log($result);
}


}
?>