<?php 
/**
 * Configure the routes and security for pages & webservices 
 * Use the controllers, pages and service arrays to control access and routes.
 * @category config
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

define ('AUTHORIZE_ALL',0);
define ('AUTHORIZED_USERS_ONLY',1);
define ('LOCAL_ONLY',2);
define ('SECURITY_STANDARD_BASIC','BASIC');
define ('SECURITY_STANDARD_OAUTH','OAUTH');

class twaRoutes { 

public $controllers = array();
public $pages = array();
public $data = array();
public $service = array();
public $security = SECURITY_STANDARD_BASIC;

public function __construct() {
	
	$this->controllers['default'] = 'default';
	
	$this->service['default'] = array(
									"access"=>LOCAL_ONLY
								  );
								  
	$this->pages['default'] = array(
									"name"=>"home",
									"access"=>AUTHORIZE_ALL,
									"layout"=>"default",
									"title"=>"Park-O-Next | Home",
									"keywords"=>"",
									"description"=>""
								  );

}

}
  ?>