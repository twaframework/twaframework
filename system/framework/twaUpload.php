<?php
/**
 * The uploader object for uploading files.
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;


 
class twaUpload {

/**
 * An array containing the filetypes
 *
 * @var array()
 */
public $filetype = array();
public $file = array();
public $extensions = array('png','gif','jpg','jpeg','bmp','doc','ppt','pdf','xls','xlsx','docx','pptx','ppsx','zip','gzip','gz');

/**
 * Starting point for twaUpload
 *
 * Initialize the filetypes.  Get upload_max_filesize and post_max_size from php.ini
 * @param String $file
 * 
 * @access public
 */
public function __construct($file = null) {
	if($file) {
		$this->file = $file;
	}
	
	$this->filetype['jpg'] = "image/jpeg";
	$this->filetype['png'] = "image/png";
	$this->filetype['jpeg'] = "image/pjpeg";
	$this->filetype['bmp'] = "image/bmp";
	$this->filetype['gif'] = "image/gif";
	$this->filetype['doc'] = "application/msword";
	$this->filetype['ppt'] = "application/vnd.ms-powerpoint";
	$this->filetype['pdf'] = "application/pdf";
	$this->filetype['xls'] = "application/vnd.ms-excel";
	$this->filetype['docx'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
	$this->filetype['pptx'] = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
	$this->filetype['ppsx'] = "application/vnd.openxmlformats-officedocument.presentationml.slideshow";
	$this->filetype['xlsx'] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
	$this->filetype['zip'] = "multipart/x-zip";
	$this->filetype['gzip'] = "multipart/x-gzip";
	
	


	$this->uploadMaxFilesize = $this->toBytes(ini_get('upload_max_filesize'));
	$this->postMaxSize = $this->toBytes(ini_get('post_max_size'));
}
/**
 * Validate the upload
 *
 * Check if the destination folder is accessible, check if the $_SERVER['CONTENT_TYPE'] contains "multipart", check if the file value is set, the size is not 0 and is not more then the allowed file size and finally that the extension is valid
 * 
 * @return boolean TRUE if file is valid, FALSE if the file does not meet the validation criteria
 * @access public
 */

public function validate() {
	global $framework;
	if ($this->isInaccessible($this->path)){
		$framework->load('twaDebugger')->log("Inaccessible");
        return false;
    }
    if(!isset($_SERVER['CONTENT_TYPE']) || strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') !== 0) {
    	$framework->load('twaDebugger')->log("Content Type");
    	return false;     
    }
    if (!isset($this->file)){
    	$framework->load('twaDebugger')->log("File Non Existant");
        return false;
    }
    if ($this->file['size'] == 0){
    	$framework->load('twaDebugger')->log("Size = 0");
        return false;
    }
    if ($this->file['size'] > $this->uploadMaxFilesize){
    	$framework->load('twaDebugger')->log("Too LARGE");
        return false;
    }
    if ($this->extensions && !in_array(strtolower($this->extension), array_map("strtolower", $this->extensions))){
    	$framework->load('twaDebugger')->log("Not right extension");	
		return false;
	}
	return true;
}
/**
 * Upload the file to the specified destination
 *
 * Validate the file and then move the uploaded file to the destination path.
 * @param String $path is the destination where the file should be copied.
 * @return twaFile return an instance of the twaFile object
 * @access public
 */
public function uploadTo($path) {
	global $framework;
	$ext = end(explode(".",$this->file['name']));
	$path = $path.uniqid().".".$ext;
	$framework->load('twaDebugger')->dump($path);
	$this->getFileParams($path);
	if(!$this->validate()) {
		
		return false;
	}
	
	if(move_uploaded_file($this->file['tmp_name'], $path)) {
		
	} else {
		
		return false;
	}
	
	return new twaFile($path);
}

/**
 * Get file parameters
 *
 * Get the file path, extension, filename, partname
 * @param String $fullpath is the path of the file.
 * @return boolean TRUE if successfull
 * @access protected
 */
protected function getFileParams($fullpath) {
	$breakfile = pathinfo($fullpath);
	if (isset($breakfile['dirname']) && $breakfile['dirname'] != '') $this->path = $breakfile['dirname'];
	if (isset($breakfile['extension']) && $breakfile['extension'] != '') $this->extension = $breakfile['extension'];
	if (isset($breakfile['basename']) && $breakfile['basename'] != '') $this->filename = $breakfile['basename'];
	if (isset($breakfile['filename']) && $breakfile['filename'] != '') $this->partname = $breakfile['filename'];
	return true;
}	

/**
 * Check if path is inaccessible
 *
 * Check if the path is writable and executable
 * @param String $directory is the path.
 * @return boolean TRUE if folder is inaccessible, FALSE is the accessible
 * @access protected
 */
protected function isInaccessible($directory) {
    $isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    $folderInaccessible = ($isWin) ? !is_writable($directory) : ( !is_writable($directory) && !is_executable($directory) );
    return $folderInaccessible;
}
/**
 * Get a size of a string in the form of Bytes
 *
 * Get the size in Bytes
 * @param String $str is the string.
 * @return int the value in bytes.
 * @access public
 */    
public static function toBytes($str) {
    $val = trim($str);
    $last = strtolower($str[strlen($str)-1]);
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}


}