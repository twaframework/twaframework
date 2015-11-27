<?php
/**
 * The base class for defining web-services. Extend this class to create web-services.
 *
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;


class _CoreService{

	/**
	 * An instance of the twaFramework object
	 *
	 * @var twaFramework
	 */
	public $framework;
	/**
	 * An instance of the twaApp object
	 *
	 * @var twaApp
	 */
	public $app;
	/**
	 * An instance of the twaRouter object
	 *
	 * @var twaRouter
	 */
	public $router;
	/**
	 * Starting point for twaWebservices
	 *
	 * Declare the global variables and authenticate the request
	 *
	 *
	 * @access public
	 */
	public function __construct() {
		global $framework;
		global $app;
		$this->framework = $framework;
		$this->app = $app;
		$this->router = $framework->load('twaRouter');
		$this->debugger = $framework->load('twaDebugger');
		$this->authenticate($this->router->getRoutes()->security);
	}
	/**
	 * Authenticate the service
	 *
	 * Get the axn and code from POST variables.  Check the twaRoutes to see the access level for these axn and code. If AUTHORIZE_ALL then return TRUE, if AUTHORIZED_USERS_ONLY check the type
	 * If the type is SECURITY_STANDARD_BASIC then check the basic authentication. If type is SECURITY_STANDARD_OAUTH then authenticate using OAUTH.
	 * @param String $type identifies if you want to authenticate using BASIC or OAUTH authentication.
	 * @return mixed return nothing if successful.  Return a JSON string with an error if it fails.
	 * @access private
	 */
	public function authenticate($type) {
		$axn = $this->router->getPost('axn');
		$code = $this->router->getPost('code');

		if($axn == "" || $code == ""){
			$axn = $this->router->param['axn'];
			$code = $this->router->param['code'];
		}

		if(isset($this->router->getRoutes()->service[$axn."/".$code])) {
			if($this->router->getRoutes()->service[$axn."/".$code]['access'] == AUTHORIZE_ALL) {
				return;
			} else if ($this->router->getRoutes()->service[$axn."/".$code]['access'] == AUTHORIZED_USERS_ONLY) {
				switch($type) {
					case SECURITY_STANDARD_OAUTH:
						$oauth = $this->framework->load('twaOAuth2');
						$oauth->initServer();
						if (!$oauth->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
							$oauth->server->getResponse()->send();
							die;
						}
						break;
					case SECURITY_STANDARD_BASIC:
						if(!twaBasicAuth::authenticate($router->getCleanPost())) {
							$this->fail("Unauthorized Access");
						}
						break;
				}

			} else {
				if(!isset($_SESSION['_twa_auth_token']) || $this->router->getPost('twa_token') != $_SESSION['_twa_auth_token']) {
					$this->fail("Unauthorized Access 1");
				}
			}
		} else if (isset($this->router->getRoutes()->service['default'])) {
			if($this->router->getRoutes()->service['default']['access'] == AUTHORIZE_ALL) {
				return;
			} else if ($this->router->getRoutes()->service['default']['access'] == AUTHORIZED_USERS_ONLY) {
				switch($type) {
					case SECURITY_STANDARD_OAUTH:
						$oauth = $this->framework->load('twaOAuth2');
						$oauth->initServer();
						if (!$oauth->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
							$oauth->server->getResponse()->send();
							die;
						}
						break;
					case SECURITY_STANDARD_BASIC:
						if(!twaBasicAuth::authenticate($router->getCleanPost())) {
							$this->fail("Unauthorized Access");
						}
						break;
				}
			} else {

				if(!isset($_SESSION['_twa_auth_token']) || $this->router->getPost('twa_token') != $_SESSION['_twa_auth_token']) {
					$this->fail("Unauthorized Access");
				}
			}
		}
	}
	/**
	 * Return a JSON when service fails
	 *
	 * Return a JSON with returnCode = 1 and the error message
	 * @param string $error is the message
	 * @return string return JSON string with returnCode = 1 and error = message.
	 * @access public
	 */
	public function fail($code, $error="") {
		echo json_encode(array("returnCode"=>1,"error"=>$error,"errorCode"=>$code));
		die();
	}

	/**
	 * Return a JSON when service succeeds
	 *
	 * Return a JSON with returnCode = 0
	 * @param array $object
	 * @return string return JSON string with returnCode = 1 and error = message.
	 * @access public
	 */
	public function success($object = array()) {
		$result = array("returnCode"=>0);
		$result = array_merge($result,$object);
		echo json_encode($result);
	}

	/**
	 * Get the token for OAuth2 authentication
	 *
	 * Get the token for OAuth2 authentication
	 *
	 * @return string return JSON string with with the token.
	 * @access public
	 */
	public function token() {
		$oauth = $this->framework->load('twaOAuth2');
		$oauth->initServer();
		$oauth->server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
	}

}