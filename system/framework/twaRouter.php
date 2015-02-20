<?php
/**
 * twaRouter class for routing requests
 * The twaRouter object used for routing requsts to the appropriate pages and collecting information from the URL and $_REQUEST data.
 *
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

 
class twaRouter {

/**
 * The array contains the parameters collected from the URL.
 *
 * @var Array
 */
public $param = array();
/**
 * An instance of the twaRoutes class.
 *
 * @var twaRoutes
 */
public $routes = null;
/**
 * An array containing the POST variables
 *
 * @var twaRoutes
 */
public $cleanpost = array();
/**
 * Starting point for twaRouter. Parse the URL to find the view, controller and other parameters in the URL
 *
 * 
 * 
 * @access public
 */
public function __construct()
{	

	global $framework;
	
	$uri = $_SERVER['REQUEST_URI'];
	$path = "";
	
	if(isset($_SERVER['ORIG_PATH_INFO'])) {
		$path = $_SERVER['ORIG_PATH_INFO'];	
	}
	
	if((!$path || $path=="") && isset($_SERVER['PATH_INFO'])) {
		$path = $_SERVER['PATH_INFO'];
	}
	if((!$path || $path=="" )&&!$_GET){	
		$this->getDefaultParams();
	}
	else
	{	
		if(!$path) {
			$path = $uri;
			$uri='';
		}
		$uri_parts = explode('/',$uri);
		$path_parts = explode('/',$path,4);
		
		$base = "";
		if($uri_parts)
		{
			foreach ($uri_parts as $part)
			{
				if($part == $path_parts[1])
				{
					break;
				}
				if($part != 'index.php' && $part != 'webservices.php'){
					$base .= $part."/";
				}	
			}
		}
		
		$this->param['base']=$base;
		
		if(isset($_SERVER['HTTPS']))
		{
			$this->param['protocol'] = "https://";
		}
		else
		{
			$this->param['protocol'] = "http://";
		}
		if($_SERVER['SERVER_PORT'] != '80') {
			$servername = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$servername = $_SERVER['SERVER_NAME'];
		}
		
		$this->param['siteurl'] = $this->param['protocol'].$servername.$this->param['base'];
		$this->param['secureurl'] = 'https://'.$servername.$this->param['base'];
		$this->param['fields'] = array();
		
		$ctrl = $path_parts[1];

		$controllerpath=$framework->systempath.DS.'controllers'.DS.$ctrl;
		
		if(file_exists($controllerpath)){
			$this->param['controller'] = $ctrl;		
			
			if($path_parts[2])
			{
				$view = $path_parts[2];
				$viewpath=$framework->contentpath.DS.'pages'.DS.$view;
				
				if(file_exists($viewpath)){
					$this->param['view'] = $view;
				} else {
					$this->getDefaultParams(false,true);
					
					$this->param['fields'][] = $view;
					/*
					$fieldcomponents = explode('-',$view,2);
					$this->param[$fieldcomponents[0]] = $fieldcomponents[1];	
					*/
				}
			}
			else
			{
				$this->getDefaultParams(false,true);	
			}
		
		} else {
			
			$view = $ctrl;
			
			$viewpath=$framework->contentpath.DS.'pages'.DS.$view;
			
			if(file_exists($viewpath)){
				$this->param['view'] = $view;
				$this->getDefaultParams(true,false);
				
				if(isset($path_parts[2]))
				{
					$field = $path_parts[2];
					/*
					$fieldcomponents = explode('-',$field,2);
					$this->param[$fieldcomponents[0]] = $fieldcomponents[1];	
					*/
					$this->param['fields'][] = $field;	
				}
			
			} else {
				
				$this->getDefaultParams(true,true);
				
				$field = $path_parts[1];
				/*
				$fieldcomponents = explode('-',$field,2);
				$this->param[$fieldcomponents[0]] = $fieldcomponents[1];
				*/
				$this->param['fields'][] = $field;	
				
				if($path_parts[2])
				{
					$field = $path_parts[2];
					/*
					$fieldcomponents = explode('-',$field,2);
					$this->param[$fieldcomponents[0]] = $fieldcomponents[1];	
					*/
					$this->param['fields'][] = $field;		
				}				
			}
		}
		
					
		if(isset($path_parts[3]))
		{
			$data = explode('/',$path_parts[3]);
			
			foreach ($data as $field)
			{
				/*
				$fieldcomponents = explode('-',$field,2);
				$this->param[$fieldcomponents[0]] = $fieldcomponents[1];
				*/
				$this->param['fields'][] = $field;	
			}
		}	
		
	}
	
	
	if(!isset($this->param['siteurl']) || $this->param['siteurl'] == '') {
		if(isset($_SERVER['HTTPS']))
		{
			$this->param['protocol'] = "https://";
		}
		else
		{
			$this->param['protocol'] = "http://";
		}
		
		if($_SERVER['SERVER_PORT'] != '80') {
			$servername = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$servername = $_SERVER['SERVER_NAME'];
		}
		
		$this->param['siteurl']= $this->param['protocol'].$servername.str_replace("webservices.php","",$_SERVER['REQUEST_URI']);
		
	}
	if(!isset($this->param['secureurl']) || $this->param['secureurl'] == '') {
		
		
		if($_SERVER['SERVER_PORT'] != '80') {
			$servername = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$servername = $_SERVER['SERVER_NAME'];
		}
		$this->param['secureurl'] = 'https://'.$servername.str_replace("webservices.php","",$_SERVER['REQUEST_URI']);
		
	}

}

public function parseWebService(){
	global $framework;
	
	$uri = $_SERVER['REQUEST_URI'];
	$path = "";
	
	if(isset($_SERVER['ORIG_PATH_INFO'])) {
		$path = $_SERVER['ORIG_PATH_INFO'];	
	}
	
	if((!$path || $path=="") && isset($_SERVER['PATH_INFO'])) {
		$path = $_SERVER['PATH_INFO'];
	}
	if((!$path || $path=="" )&&!$_GET){	
		echo json_encode(array("returnCode"=>1,"error"=>"No Web Service Found"));
		die();
	}
	else
	{	
		if(!$path) {
			$path = $uri;
			$uri='';
		}
		$uri_parts = explode('/',$uri);
		//echo $path;
		$path_parts = explode('/',$path,5);
		
		$base = "";
		if($uri_parts)
		{
			foreach ($uri_parts as $part)
			{
				if($part == $path_parts[1]) {
					break;
				}
				if($part != 'index.php') {
					$base .= $part."/";
				}	
			}
		}
		
		$this->param['base']=$base;
		
		if(isset($_SERVER['HTTPS']))
		{
			$this->param['protocol'] = "https://";
		}
		else
		{
			$this->param['protocol'] = "http://";
		}
		if($_SERVER['SERVER_PORT'] != '80') {
			$servername = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$servername = $_SERVER['SERVER_NAME'];
		}
		
		$this->param['siteurl'] = $this->param['protocol'].$servername.$this->param['base'];
		$this->param['secureurl'] = 'https://'.$servername.$this->param['base'];

		$axn = $path_parts[1]."/".$path_parts[2];
		$axnpath=$framework->systempath.DS.'webservices'.DS.$axn.".php";
		
		if(file_exists($axnpath)){
			$this->param['axn'] = $axn;		
			require_once($axnpath);
			if($path_parts[3]) {
				$class = 'twaWebServices_'.$path_parts[1]."_".$path_parts[2];
				$this->param['class'] = $class;
				$code = $path_parts[3];
				if(method_exists($class,$code)){
					$this->param['code'] = $code;
				} else {
					echo json_encode(array("returnCode"=>1,"error"=>"No Web Service Found"));
					die();
					//Error
				}
			} else {
				echo json_encode(array("returnCode"=>1,"error"=>"No Web Service Found"));
				die();
				//Error	
			}
		
		} else {
			echo json_encode(array("returnCode"=>1,"error"=>"No Web Service Found"));
			die();
			// Error
		}
		
	}
	
	
	if(!isset($this->param['siteurl']) || $this->param['siteurl'] == '') {
		if(isset($_SERVER['HTTPS']))
		{
			$this->param['protocol'] = "https://";
		}
		else
		{
			$this->param['protocol'] = "http://";
		}
		
		if($_SERVER['SERVER_PORT'] != '80') {
			$servername = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$servername = $_SERVER['SERVER_NAME'];
		}
		
		$this->param['siteurl']= $this->param['protocol'].$servername.$_SERVER['REQUEST_URI'];
		
	}
	if(!isset($this->param['secureurl']) || $this->param['secureurl'] == '') {
		
		
		if($_SERVER['SERVER_PORT'] != '80') {
			$servername = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$servername = $_SERVER['SERVER_NAME'];
		}
		$this->param['secureurl'] = 'https://'.$servername.$_SERVER['REQUEST_URI'];
		
	}

}


/**
 * If the URL does not contain information about view or controller, get the default values
 * @param boolean $getController TRUE if you want to set the default controller. FALSE if you do not want to set the controller as default
 * @param boolead $getView TRUE if you want to set the view as default view.  FALSE if you do not want to set the view as default
 * 
 * @access public
 */
public function getDefaultParams($getController=true, $getView=true)
{
	global $framework;
	$conf = $framework->load('twaConfig');
	
	if($getController)
	{
		if(isset($this->param['view']) && isset($this->getRoutes()->controllers[$this->param['view']])) {
			$controller_id = $this->getRoutes()->controllers[$this->param['view']];
			if(!$controller_id) {
				$controller_id = $this->getRoutes()->controllers['default'];
			}	
			
		} else {
			$controller_id = $this->getRoutes()->controllers['default'];
		}
		
		$controllerpath=$framework->systempath.DS.'controllers'.DS.$controller_id;
		
		if(file_exists($controllerpath)) {
			$this->param['controller'] = $controller_id;
		} else {
			$this->param['controller'] = $controller_id;
			handleError("404","Unable to find controller",$controllerpath,"0");
		}
	}
	
	if($getView)
	{
		
		$view = $this->getRoutes()->pages[$this->param['controller']]['name'];
		
		if(!$view)
		{
			$view = $this->getRoutes()->pages['default']['name'];
		}
		$viewpath=$framework->contentpath.DS.'pages'.DS.$view;
		if(file_exists($viewpath)){
			$this->param['view'] = $view;

		} else {
			$this->param['view'] = $view;
			handleError("404","Unable to find view",$viewpath,"0");
		}	
		
	}
	
}
/**
 * Get the value for the provided key from the params array
 *
 * @param string $id is the key for which you want to get the value
 * 
 * @access public
 */
public function getFromURL($id)
{ 
	if(isset($this->param[$id])) {
		
		return $this->param[$id];
	}
	return false;
	
}
/**
 * Get the instance of the twaRoutes file
 *
 * 
 * 
 * @access public
 */
public function getRoutes()
{
	if (!$this->routes) {
		global $framework;
		$this->routes = new twaRoutes();
	}
return $this->routes;	

}
/**
 * Get the $_POST array
 *
 * 
 * 
 * @access public
 */
public function getCleanPost()
{
	global $framework;
	
	
	if(!$this->cleanpost){
		$this->cleanpost = $this->getCleanArray($_POST);	
	}
	return $this->cleanpost;
	
}

/**
 * Get array items from a variable
 *
 * @param Array  $array contains the array that needs to be cleaned
 * @return Array  returns the cleaned array
 * @access public
 */
public function getCleanArray($array) {
	global $framework;
	$database= $framework->getDB();
	$return = array();
	if($array) {						
		foreach($array as $key=>$term) {
			if(gettype($term) == 'array') {
				$return[$key] = $this->getCleanArray($term);
			} else {
				$return[$key] = $term;
			}
		}	
		return $return;
	}
	return false;
}

/**
 * Get array items from a variable
 *
 * @param string $id the key of the post variable that you want to get the value for
 * @return mixed value of the post variable.
 * @access public
 */
public function getPost($id)
{
	global $framework;
	if(!$this->cleanpost)
	{
		if(isset($_POST[$id])) {
			return $_POST[$id];
		}		
	}
	else
	{
		if(isset($this->cleanpost[$id])) {
			return $this->cleanpost[$id];
		}
	}
	return false;
}
/**
 * Get file
 *
 * @param string $fileinput the key of the file
 * @param string $var the key of the file variable for the file specified
 * @return mixed value of the file variable.
 * @access public
 */
public function getFile($fileinput, $var = null) {
	if (isset($_FILES) && isset($_FILES[$fileinput])){
		if($var && isset($_FILES[$fileinput][$var])) {
			return $_FILES[$fileinput][$var];
		} else {
			return $_FILES[$fileinput];
		}
		
	}
	else return false;
}
/**
 * Get file
 *
 * @param string $url the url to check
 * 
 * @return bool TRUE if the remote file exists, FALSE is the remote file does not exist.
 * @access public
 */
public function checkRemoteFile($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(curl_exec($ch)!==FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}

}





?>