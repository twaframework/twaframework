<?php 
/**
* Database configuration
* The database connection information.
* @category config
*
* @author Akshay Kolte <akshay.kolte@etlok.com>
*/
 
class twaDBConfig_default { 
 /**
 * The hostname of the database server.
 *
 * @var String
 */
 public $host=''; 
 /**
 * The driver of the database
 *
 * @var String
 */
 public $driver='mysql'; 
 /**
 * Database Name
 *
 * @var String
 */
 public $db='';
 /**
 * Database username
 *
 * @var String
 */ 
 public $user=''; 
 /**
 * Database Password
 *
 * @var String
 */
 public $password=''; 
 /**
 * Tablename Prefix
 *
 * @var String
 */
 public $dbprefix=''; 
  /**
 * TRUE if database is configured, FALSE if database is not configured
 *
 * @var Boolean
 */
 public $isDBConfigured=FALSE; 
 } 
 ?>