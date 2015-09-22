<?php

/**
 * Created by PhpStorm.
 * User: akshay
 * Date: 9/21/15
 * Time: 11:28 AM
 */
class twaController extends _CoreController {
    public function __construct(){
        parent::__construct();

        global $framework;
        $this->data['user'] = $framework->getUser();
        if($this->data['user']->isLoggedIn()){

        }
    }
}