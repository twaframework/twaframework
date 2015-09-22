<?php
/**
 * twaRouter class for routing requests
 * The twaRouter object used for routing requsts to the appropriate pages and collecting information from the URL and $_REQUEST data.
 *
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

 
class _CoreRouter {

	/**
	 * The array contains the parameters collected from the URL.
	 *
	 * @var Array
	 */
	public $param = array();

	/**
	 * The array contains the parameters collected from the URL.
	 *
	 * @var Array
	 */
	public $components = array();

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
	public function __construct() {
		if(!$this->parse()) {
			$this->validateRoute();
		} else {
			$this->setRoute();
		}
	}

	public function parse(){

		if(isset($_SERVER['HTTPS'])) {
			$this->param['protocol'] = "https://";
		} else {
			$this->param['protocol'] = "http://";
		}

		if($_SERVER['SERVER_PORT'] != '80') {
			$server_name = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$server_name = $_SERVER['SERVER_NAME'];
		}

		$uri = $_SERVER['REQUEST_URI'];
		$path = "";

		if(isset($_SERVER['ORIG_PATH_INFO'])) {
			$path = $_SERVER['ORIG_PATH_INFO'];
		}

		if((!$path || $path == "") && isset($_SERVER['PATH_INFO'])) {
			$path = $_SERVER['PATH_INFO'];
		}

		if((!$path || $path == "" )&&!$_GET){
			//No path information found
			$this->param['siteurl']= $this->param['protocol'].$server_name.str_replace("webservices.php","",$_SERVER['REQUEST_URI']);
			$this->param['secureurl'] = 'https://'.$server_name.str_replace("webservices.php","",$_SERVER['REQUEST_URI']);

			return false;
		}

		$uri_parts = explode('/',$uri);
		$this->components = explode('/',$path,4);

		$base = "";
		if($uri_parts) {
			foreach ($uri_parts as $part) {
				if($part == $this->components[1]) {
					break;
				}

				if($part != 'index.php' && $part != 'webservices.php'){
					$base .= $part."/";
				}
			}
		}

		$this->param['siteurl'] = $this->param['protocol'].$server_name.$base;
		$this->param['secureurl'] = 'https://'.$server_name.$base;
		return true;
	}

	public function setRoute() {
		if($this->components && isset($this->components[1])){
			$c = $this->components[1];
			if($this->isController($c)){
				$this->param['controller'] = $c;
				if(isset($this->components[2])){
					$v = $this->components[2];
					if($this->isView($v)){
						$this->param['view'] = $v;
					}
				}
			} elseif($this->isView($c)) {
				$this->param['view'] = $c;
			}
		}

		$this->validateRoute();
	}

	public function validateRoute(){
		global $framework;
		$conf = $framework->load('twaConfig');

		if(!isset($this->param['controller']) && !isset($this->param['view'])){
			$c = $this->getRoutes()->controllers['default'];
			$v = $this->getRoutes()->pages[$c]['name'];
			if(!$v) {
				$v = $this->getRoutes()->pages['default']['name'];
			}

			if($this->isController($c)) {
				$this->param['controller'] = $c;
				if ($this->isView($v)) {
					$this->param['view'] = $v;
				} else {
					handleError("404","Unable to find view",$v,"0");
				}
			} else {
				handleError("404","Unable to find view",$c,"0");
			}

		} else if (isset($this->param['controller'])) {
			$v = $this->getRoutes()->pages[$this->param['controller']]['name'];
			if(!$v) {
				$v = $this->getRoutes()->pages['default']['name'];
			}
			if ($this->isView($v)) {
				$this->param['view'] = $v;
			} else {
				handleError("404","Unable to find view",$v,"0");
			}
		} else if (isset($this->param['view'])) {
			$c = $this->getRoutes()->controllers[$this->param['view']];
			if(!$c) {
				$c = $this->getRoutes()->controllers['default'];
			}
			if($this->isController($c)) {
				$this->param['controller'] = $c;
			}
		}

		$this->getFields();

	}

	public function getFields(){
		$this->fields = array();
		if($this->components){
			foreach($this->components as $component){
				if($component != $this->param['controller'] && $component != $this->param['view']) {
					$f = explode("/",$component);
					$this->fields = array_merge($this->fields,$f);
				}
			}
		}
	}

	public function setWebServiceRoute() {
		$axn = $this->components[1] . "/" . $this->components[2];

		if ($this->isWebService($axn)) {
			$this->param['class'] = 'twaWebServices_' . $this->components[1] . "_" . $this->components[2];
			$code = $this->components[3];
			if ($this->isWebServiceFunction($code)) {
				$this->param['code'] = $code;
				return true;
			}
		}

		echo json_encode(array("returnCode" => 1, "error" => "No Web Service Found"));
		die();
	}

	public function isController($ctrl) {
		global $framework;
		$controller_path=$framework->systempath.DS.'controllers'.DS.$ctrl;
		if(file_exists($controller_path)){
			return true;
		}
		return false;
	}


	public function isView($view){
		global $framework;
		$view_path=$framework->contentpath.DS.'pages'.DS.$view;

		if(file_exists($view_path)) {
			return true;
		}

		return false;
	}

	public function isWebService($axn){
		global $framework;
		$axn_path=$framework->systempath.DS.'webservices'.DS.$axn.".php";
		if(file_exists($axn_path)) {
			return true;
		}
		return false;
	}

	public function isWebServiceFunction($code){
		if(method_exists($this->param['class'],$code)){
			return true;
		}
		return false;
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