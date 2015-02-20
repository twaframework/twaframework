<?php
/**
 * download.php, twaFramework
 * 
 * Use this file to force download of a file.
 * @author	Akshay Kolte <akshay@twaframework.com>
 * @version 8.0
 * @package twaFramework
 */
?>

<?php
define('_TWACHK', 1);
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(__FILE__));
require_once BASE_PATH.'/system/framework/framework.php';
require_once(BASE_PATH.'/system/config/functions.php');
spl_autoload_register('loadClasses'); //Ask the autoload register to use loadClasses as the function to autoload classes.

$framework = new twaFramework();

$path = $_GET['file'];
$file = new twaFile($path);
if($file && $file->validExtension()){
	
	header('Content-disposition: attachment; filename='.$path);
	header('Content-type: '.$file->type);
	readfile($path);
}

?>