<?php
/**
 * The file object for managing files.  
 *
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaFile {

/**
 * The array that contains filetypes
 *
 * @var Array
 */
public $filetype = array();
/**
 * The array that contains extensions that can be downloaded
 *
 * @var Array
 */
public $downloadableExtentions = array('png','gif','jpg','jpeg','bmp','doc','ppt','pdf','xls','xlsx','docx','pptx','ppsx','zip','gzip','gz');
/**
 * Starting point for initializing twaFile
 *
 * @param string $filepath contains path of the file.
 * 
 * @access public
 */
public function __construct($filepath=null) {	
	
	global $framework;
	$framework->load('twaDebugger')->log($filepath);	
	
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
	
	if ($filepath != null){
		return $this->setFile($filepath);	
	} 
	
}
/**
 * Set the file
 *
 * @param string $filepath contains path of the file.
 * @return boolean TRUE if file exists
 * @access public
 */
public function setFile($filepath) {
	$this->resetFile();
	if (file_exists($filepath)) {
		$this->getFileParams($filepath);
		return true;
	} 
	return false;
}
/**
 * Reset the file. Empties all the fields
 *
 * @access protected
 */
protected function resetFile(){
		$this->fullpath = '';
		$this->path = '';
		$this->size = 0;
		$this->file = null;
		$this->partname = '';
		$this->filename = '';
		$this->extension = '';
		$this->url = '';
}
/**
 * Get file parameters for provided file
 *
 * @param string $fullpath contains the file path
 * @access protected
 */
protected function getFileParams($fullpath) {
		global $framework;
		global $app;
		$breakfile = pathinfo($fullpath);
		$this->fullpath = $fullpath;
		if (isset($breakfile['dirname']) && $breakfile['dirname'] != '') $this->path = $breakfile['dirname'];
		if (isset($breakfile['extension']) && $breakfile['extension'] != '') $this->extension = $breakfile['extension'];
		if (isset($breakfile['basename']) && $breakfile['basename'] != '') $this->filename = $breakfile['basename'];
		if (isset($breakfile['filename']) && $breakfile['filename'] != '') $this->partname = $breakfile['filename'];
		$this->size = filesize($fullpath);
		$this->type = $this->getFileType();
		$this->url = str_replace($framework->basepath.DS, $app->siteurl, $this->fullpath);
		return true;
}	
/**
 * Check is the extension is allowed/valid
 *
 * @param string $extensions contains the list of extensions to compare from
 * @access public
 */
public function validExtension($extensions)	{
	if ($extensions && !in_array(strtolower($this->extension), array_map("strtolower", $extensions))){
		return false;
	}
	return true;
}
/**
 * Set a new extension to the file
 *
 * @param string $extension contains the new extension
 * @access public
 */
public function setNewExtension($ext) {
		$this->extension = $ext;
		$this->filename = $this->partname.'.'.$ext;
}
/**
 * Get the mime type of the file
 *
 * 
 * @access public
 */

public function getFileType() {
	if(isset($this->filetype[$this->extension])) {
		return $this->filetype[$this->extension];	
	}
	return false;
}
/**
 * Delete the file
 *
 * 
 * @access public
 */
public function delete() {
	if(file_exists($this->fullpath)) {
		unlink($this->fullpath);
	}
}


}
