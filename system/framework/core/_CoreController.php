<?php
/**
 * The base controller class the can be extended to write your own controller.
 * @category system 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;


class _CoreController {

/**
 * An array containing all the variables and values that need to be declared as Javascript variables
 *
 * @var Array
 */
public $variables = array();
/**
 * An array containing all the variables and values that need to be declared as Javascript variables
 *
 * @var Array
 */
public $data = array();
/**
 * Create an instance, optionally setting a starting point.
 *
 * @access public
 */
public function __construct() {
	
}
/**
 * Check if user has access at the controller level.
 *
 * @see twaApp::hasAccess()
 *
 * @return boolean TRUE if user has access, FALSE is user does not have access
 *
 * @access public
 */
public function hasAccess() {
	return true;
}

public function init(){
	global $framework;
	global $app;

	if(method_exists($this, 'default')){
		$this->default();
	}

	if(method_exists($this, $app->_viewid)){
		$vw = $app->_viewid;
		$this->$vw();
	}
}


/**
 * Load the head section of the page
 *
 * @see twaApp::LoadHeader()
 *
 * @access public
 */
public function head() {
	global $framework;
	global $app;
	
	$router = $framework->load('twaRouter');
	$debugger = $framework->load('twaDebugger');
	
	if($this->data){
		foreach($this->data as $field=>$value){
			$$field = $value;
		}
	}
	
	$base_url = $app->siteurl;
	$content_url = $app->siteurl.'web_content'.DS;

	$header_array = array();
	
	$header_array[] = $framework->systempath.DS.'config'.DS.'htmlhead_begin.php';

    if(isset($router->getRoutes()->pages[$app->_viewid])){
        $header_array[] = $framework->contentpath.DS.'layouts'.DS.$router->getRoutes()->pages[$app->_viewid]['layout'].DS.'htmlhead.php';
    } else {
        $header_array[] = $framework->contentpath.DS.'layouts'.DS.$router->getRoutes()->pages['default']['layout'].DS.'htmlhead.php';
    }

	$header_array[] = $framework->contentpath.DS.'pages'.DS.$app->_viewid.DS.'htmlhead.php';
	$header_array[] = $framework->systempath.DS.'config'.DS.'htmlhead_end.php';
	
	
	foreach ($header_array as $file)
	{
		
		if(file_exists($file)) {
			require $file;
		}
	}
		
}

/**
 * Render the body of the page.
 *
 * @see twaApp::LoadBody()
 *
 * @access public
 */
public function render() {
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$debugger = $framework->load('twaDebugger');
	
	if($this->data){
		foreach($this->data as $field=>$value){
			$$field = $value;
		}
	}
	
	$base_url = $app->siteurl;
	$content_url = $app->siteurl.'web_content'.DS;

	
	$controllerpath=$framework->contentpath.DS.'layouts'.DS.$router->getRoutes()->pages[$app->_viewid]['layout'].DS.'layout.php';
    $defaultpath=$framework->contentpath.DS.'layouts'.DS.$router->getRoutes()->pages['default']['layout'].DS.'layout.php';
	
	if(file_exists($controllerpath)) { 
		require $controllerpath;
		
		if(isset($_SESSION['_twa_user_token']) && isset($_SESSION['_twa_user_id']) && $framework->globalsettings()->logsettings->enabledebugger == 1) {
			echo '<link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/twadebug.css" />';
			echo '<script src="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/twadebug.js"></script>';
		}
	}
	else if(file_exists($defaultpath)) {
		require $defaultpath;
		if(isset($_SESSION['_twa_user_token']) && isset($_SESSION['_twa_user_id']) && $framework->globalsettings()->logsettings->enabledebugger == 1) {
			echo '<link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/twadebug.css" />';
			echo '<script src="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/twadebug.js"></script>';
		}
	} else {
		$framework->load('twaDebugger')->log('File not found: '.$controllerpath);
	}
}
/**
 * Handle the situation if user is un-authorized to view page.
 *
 * @see twaApp::LoadBody()
 *
 * @access public
 */
public function unAuthorized() {
	
	global $app;
	header('Location:'.$app->siteurl.'#'.urlencode($app->siteurl.$_SERVER['REQUEST_URI'])) ;
}

public function page_metadata(){
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');

	if(isset($router->getRoutes()->pages[$app->_viewid])){
		echo "<meta name='keywords' content='".$router->getRoutes()->pages[$app->_viewid]['keywords']."'>";
		echo "<meta name='description' content='".$router->getRoutes()->pages[$app->_viewid]['description']."'>";
		echo "<title>".$router->getRoutes()->pages[$app->_viewid]['title']."</title>";
	} else {
		echo "<meta name='keywords' content='".$router->getRoutes()->pages['default']['keywords']."'>";
		echo "<meta name='description' content='".$router->getRoutes()->pages['default']['description']."'>";
		echo "<title>".$router->getRoutes()->pages['default']['title']."</title>";
	}
}

/**
 * Add a component to the page
 *
 * @param string $module the name of the module to be added.
 *
 * @access public
 */
public function add($module) {
	
	global $framework;
	global $app;
	$router = $framework->load('twaRouter');
	$debugger = $framework->load('twaDebugger');
	
	
	
	$base_url = $app->siteurl;
	$base_path = $framework->basepath.DS;
	$content_url = $app->siteurl.'web_content'.DS;
	$content_path = $framework->contentpath.DS;
	
	
	//If the position is content then load the main view content.
	if($module === 'content') {
		
		if($this->data){
			foreach($this->data as $field=>$value){
				$$field = $value;
			}
		}
		
		$viewfile=$framework->contentpath.DS.'pages'.DS.$app->_viewid.DS.$app->_viewid.'.php';
		if(!file_exists($viewfile)) {
			$viewfile=$framework->contentpath.DS.'pages'.DS.$app->_viewid.DS.$app->_viewid.'.haml';
			
			if(file_exists($viewfile)) {
				if($app->LoadModelPackage('pkg_haml')) {
					$haml = new HamlParser(array('style'=>'nested', 'ugly'=>false));
					$xhtml = $haml->parse($viewfile);
					echo $xhtml;
				} else {
					$framework->load('twaDebugger')->log('File not found: '.$viewfile);
				}
			}
			else{
				$framework->load('twaDebugger')->log('File not found: '.$viewfile);
			}		
		
		} else {
			require $viewfile;
		}
	}
	else {
		
		if(method_exists($this, $module)){
			$this->$module();
		}
		
		if($this->data){
			foreach($this->data as $field=>$value){
				$$field = $value;
			}
		}
		
		$modulefile=$framework->contentpath.DS.'components'.DS.$module.DS.$module.'.php';	
		
		if(!file_exists($modulefile)) {
			$modulefile=$framework->contentpath.DS.'components'.DS.$module.DS.$module.'.haml';
			
			if(file_exists($modulefile)) {
				if($app->LoadModelPackage('pkg_haml')) {
					$haml = new HamlParser(array('style'=>'nested', 'ugly'=>false));
					$xhtml = $haml->parse($modulefile);
					echo $xhtml;		
				} else {
					$framework->load('twaDebugger')->log('File not found: '.$modulefile);
				}
			}
			else{
				$framework->load('twaDebugger')->log('File not found: '.$modulefile);
			}
		
		} else {
			
			require $modulefile;	
		}	
	}
}

/**
 * Add a css link to a page.
 *
 * @param string $css the name of the css file to be added.
 * @param string $version the version number of the file. 
 *
 * @access public
 */
public function setStyle($css,$version = "") {

	global $framework;
	global $app;
	
	if($version == "") {
		$version = $framework->load('twaConfig')->version;
	}
	
    if(file_exists($framework->contentpath.DS.'styles/'.$css.'.css'))
    {
        echo "<link href='".$app->siteurl.'web_content/styles/'.$css.'.css?version='.$version."' rel='stylesheet' type='text/css' />";
        return;
    } else if(file_exists($framework->contentpath.DS.'styles/'.$css.'.scss')) {
        if($app->LoadModelPackage('pkg_haml')) {
			$scss = new SassParser(array('style'=>'nested','css_location'=>$framework->contentpath.DS.'styles/'));
			$css = $scss->toCss($framework->contentpath.DS.'styles/'.$css.'.scss');
			if(file_exists($framework->contentpath.DS.'styles/'.$css.'.css'))
			{
				echo "<link href='".$app->siteurl.'web_content/styles/'.$css.'.css?version='.$version."' rel='stylesheet' type='text/css' />";
				return;
			}
		}
		return;
    }
                  
    return;
}

/**
 * Add a js script tag to a page.
 *
 * @param string $js the name of the js file to be added.
 * @param string $version the version number of the file. 
 * @param boolean $now TRUE means the javascript is loaded immediately.  FALSE means the javascript is loaded after the page loading is complete. 
 *
 * @access public
 */
public function setScript($js,$version = "",$now = true) {
	global $framework;
	global $app;
	if($version == "") {
		$version = $framework->load('twaConfig')->version;
	}
	
	if(file_exists($framework->contentpath.DS.'javascripts/'.$js.'.js')){
    	if($now){
	    	echo "<script type='text/javascript' src='".$app->siteurl."web_content/javascripts/".$js.".js?version=".$version."'></script>";	
    	} else {
	    	echo "<script>";
	    	echo "jsScripts.push('".$app->siteurl.'web_content/javascripts/'.$js.".js'".")";	
	    	echo "</script>";
    	}
		
		return;
	}	
}

/**
 * Declare a variable in PHP that will be made available JS.
 *
 * @param string $variable the name of the variable to be added.
 * @param string $value the value to assign to the variable.  
 *
 * @access public
 */
public function setJSVariable($variable,$value,$isObject = false) {
	$this->variables[] = array("variable"=>$variable,"value"=>$value,"isObject"=>$isObject);
}
/**
 * Declare all variables in JS.  Also added all the data from the Router param array.
 *
 * @access public
 */
public function declareJSVariables() {
	global $framework;
	
	echo "<script>".PHP_EOL;
	echo "var router = {}; ".PHP_EOL;
	$router = $framework->load('twaRouter');
	echo "router = ".json_encode($router->param).PHP_EOL;
	
	foreach($this->variables as $var => $val) {
		
			echo "var ".$val['variable']."='".$val['value']."';".PHP_EOL;	
			if($val['isObject']){
				echo $val['variable']." = JSON.parse(".$val['variable'].")".PHP_EOL;
			}
	}
	echo "</script>";
}
/**
 * Send emails. 
 *
 * @param Array $data contains the parameters for sending the emails
 * @access public
 */
public function email($data) {
	global $framework;
	
	$email = new twaEmail();
	
	if(isset($data['type']) && $data['type'] == 'PRESET') {
		$email->CreateEmailFromPreset($data);	
		
	} else if (isset($data['type']) &&  $data['type'] == 'TEMPLATE') {
		
		$email->CreateEmailFromTemplate($data);
		
	} else {
		$email->CreateEmail($data);	
	}	
	
	if($email->SendEmail($data)) {
		
		return true;
	}
	
	return false;
	
}


}
?>