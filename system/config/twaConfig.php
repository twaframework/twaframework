<?php 
/**
 * Configuration 
 * The configuration document.
 * @category config
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
 
class twaConfig { 

/**
* Indicates the default language to load.
*
* @var String
*/
public $lang = 'en_US';
/**
* TRUE is maintenance mode is ON, FALSE if maintenance mode is OFF
*
* @var Boolean
*/
public $maintenanceMode = FALSE;
/**
* A list of accounts who are administrators
*
* @var Array
*/
public $admin_accounts = array();

}

?>