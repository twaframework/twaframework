<?php
/**
 * The default controller.
 * This is an instance of the default controller. Add methods to this class that you want to access on your pages.  Mehtods in this class can be called in any page as follows:
 * <pre>$this-><method_name>();</pre>
 * @category controller 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;


class twaController_default extends twaController { 
	
	public function __construct(){
		parent::__construct();
		
		global $framework;
		$this->data['user'] = $framework->getUser();
		if($this->data['user']->isLoggedIn()){
			
		}
	}
}



?>