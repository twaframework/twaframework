<?php
/**
 * The git webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/git".  This webservice contains methods git deploying git repositories.
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class twaWebServices_framework_git extends twaWebServices {

public $config = array();

public function __construct(){
	parent::__construct();
	/**** CONFIG FOR GIT PULL ****/
	/* 
		$this->config = array(
			"enable_pull"=>false,
			"enable_commit"=>false,
			"enable_push"=>false,
			"user"=>"apache:apache",
			"branch"=>"master",
			"remote"=>"origin"
		);
	*/	
	
	$this->config = array(
		"enable_pull"=>false,
		"enable_commit"=>false,
		"enable_push"=>false
	);
	
}


/**
 * Do a git pull
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function pull() {
	$deploy = $this->framework->load('twaDeploy');
	if($this->config->enable_pull){
		$deploy->pull($this->config);
	}
	$this->success();
}

/**
 * Do a git commit
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function commit() {
	$deploy = $this->framework->load('twaDeploy');
	if($this->config->enable_commit){
		$deploy->commit($this->config);
	}
	$this->success();
}

/**
 * Do a git commit
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */
public function commit() {
	$deploy = $this->framework->load('twaDeploy');
	if($this->config->enable_push){
		$deploy->commit($this->config);
	}
	$this->success();
}



}


?>
