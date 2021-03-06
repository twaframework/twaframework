<?php
defined('_TWACHK') or die;
/**
 * The base database access class for accessing the database.
 * @category system 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
 class _CoreData {

/**
 * A database connection object
 *
 * @var Object
 */
public static $connection=null;

/**
 * A database configuration object
 *
 * @var Object
 */
public $conf;

/**
 * A string containing the servername
 *
 * @var String
 */
public $_servername=null;

/**
 * A string containing the driver
 *
 * @var String
 */
public $_driver=null;

/**
 * A string containing the user id
 *
 * @var String
 */
protected $_user=null;

/**
 * A string containing the password
 *
 * @var String
 */
protected $_password=null;

/**
 * A string containing the database name
 *
 * @var String
 */
protected $_dbname=null;

/**
 * A string containing the table name prefix
 *
 * @var String
 */
protected $_prefix=null;

/**
 * Create an instance, optionally setting a starting point.
 *
 * @access public
 */
public function __construct($db = 'default') {
		global $framework;
		$cname = "twaDBConfig_".$db;
		$this->conf = new $cname();
		if(!$this->conf->isDBConfigured) {
			return;
		}
		
		$this->_driver		= $this->conf->driver;
		$this->_prefix		= $this->conf->dbprefix;
		
		switch($this->_driver) {
			case 'mysql':
			case 'pgsql':
				$this->_servername  = $this->conf->host;
				$this->_user		= $this->conf->user;
				$this->_password	= $this->conf->password;
				$this->_dbname 	    = $this->conf->db;
				
			break;
			case 'sqlite':
			case 'sqlite2':
				$this->_path	    = $this->conf->path;
				$this->_user		= $this->conf->user;
				$this->_password	= $this->conf->password;
				
			break;
			case 'sqlsrv':
				$this->_servername  = $this->conf->host;
				$this->_user		= $this->conf->user;
				$this->_password	= $this->conf->password;
				$this->_dbname 	    = $this->conf->db;
				
			break;
			case 'odbc':
				$this->_servername  = $this->conf->host;
				$this->_odbc_driver  = $this->conf->odbc_driver;
				$this->_user		= $this->conf->user;
				$this->_password	= $this->conf->password;
				$this->_dbname 	    = $this->conf->db;
			break;	
		}
}
/**
 * Connect to the database using connection information
 *
 * @access private
 */
private function connectDB() {
	
	switch($this->_driver) {
		case 'mysql':
		case 'pgsql':
			$dsn = $this->_driver.":"."host=".$this->_servername.";dbname=".$this->_dbname;
			self::$connection=new PDO($dsn,$this->_user,$this->_password);
		break;
		case 'sqlite':
		case 'sqlite2':
			self::$connection=new PDO($this->_driver.":".$this->_path,$this->_user,$this->_password);
		break;
		case 'sqlsrv':
			self::$connection=new PDO($this->_driver.":"."Server=".$this->_servername.";Database=".$this->_dbname,$this->_user,$this->_password);
		break;
		case 'odbc':
			self::$connection=new PDO($this->_driver.":"."Driver".$this->_odbc_driver.";Server=".$this->_servername.";Database=".$this->_dbname.";Uid=".$this->_user.";Pwd=".$this->_password);
		break;	
	}
	
}



/************************************************************/
/*Always write queries using DBQuote to Prevent SQL injection
/***********************************************************/

/**
 * Prevents SQL injections by adding quotes around the string based on its type
 *
 * @param string  The input variable
 * @return String The string with the quotes added.
 * 
 * @access public
 */
public function dbquote($var) {
	if(!self::$connection) {
		$this->connectDB();
	}
	return self::$connection->quote($var);

}

/**
 * Run a query on the database
 *
 * @param string  In the input query string
 * @param Array  An array of values.
 * @return Boolean TRUE if the query was successful. FALSE if it failed
 * 
 * @access public
 */
public function runQuery($query,$values = null) {
	global $framework;
	
	if(!$this->conf->isDBConfigured) {
		return FALSE;
	}
	
	if(!self::$connection) {
		$this->connectDB();
	}
	$query = stripslashes(str_replace('#__',$this->_prefix,$query));
	$array=array();
	$statement = self::$connection->prepare($query);
	
	if($values) {
		$this->last_num_rows = 0;
		foreach($values as $v){
			$r = $statement->execute($v);
			if(!$r) {
				$statement->closeCursor();
				return false;
			}
			$array[] = $statement->fetchAll(PDO::FETCH_CLASS);
			$this->last_num_rows += $statement->rowCount();
			
			
			$statement->closeCursor();
		}
	} else {
		$r = $statement->execute();
		
		if(!$r) {
			$statement->closeCursor();
			return false;
		}
		$array = $statement->fetchAll(PDO::FETCH_CLASS);
		$this->last_num_rows=$statement->rowCount();
		$this->last_insert_id = self::$connection->lastInsertId();
		
		$statement->closeCursor();
	}
	
	return $array;
	
}

/**
 * Executes a query on the database
 *
 * @param string  In the input query string
 * @return Boolean TRUE if the query was successful. FALSE if it failed
 * 
 * @access public
 */
public function execute($query){
	global $framework;
	
	if(!$this->conf->isDBConfigured) {
		return FALSE;
	}
	
	$query = stripslashes(str_replace('#__',$this->_prefix,$query));
	$array=array();
	if(!self::$connection) {
		$this->connectDB();
	}
	self::$connection->exec($query);
	
	return true;
}



/**
 * Executes a SQL file on the database
 *
 * @param string  In the input filename of the SQL file
 * @return Boolean TRUE if the query was successful. FALSE if it failed
 * 
 * @access public
 */
public function runSQLFile($filename)
{
	global $framework;
	if(!self::$connection) {
		$this->connectDB();
	}
	if(file_exists($filename)) {
		$sql = file_get_contents($filename);
		$sql = stripslashes(str_replace('#__',$this->_prefix,$sql));
		self::$connection->beginTransaction();
		if(self::$connection->exec($sql) !== FALSE) {
			self::$connection->commit();
			return true;
		}
	} else {
		$framework->load('twaDebugger')->log('File No Found: '.$filename);
	}
	
	return false;
}


}