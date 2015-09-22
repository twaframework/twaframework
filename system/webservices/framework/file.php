
<?php
/**
 * The file webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/file".  
 * This web-service contains all thefile management actions line upload, download remote file, delete file etc.
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaWebServices_framework_file extends twaWebServices {
/**
 * Upload a File
 * This service is called when you want to upload a file
 * POST variables must specify the file parameter and the destination the file needs to be saved to
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error. The metadata of the file including path, extension and filename is returned.
 * @access public
 */

public function upload() {
	global $framework;
	$router = $framework->load('twaRouter');
	$files = $router->getPost('files');
	$uploaded = array();
	$framework->load('twaDebugger')->dump($files);
	if($files) {
		
		foreach($files as $file) {
			$f = $router->getFile($file['param']);
			$upload = new twaUpload($f);
			$u = $upload->uploadTo($framework->basepath.DS.$file['destination']);
			if($u !== FALSE) {	
				$u->original = $f['name'];
				$uploaded[] = $u;
			}
		}
	}	
	
	echo '{"returnCode":0,"files":[';
	if($uploaded) {
		$comma = "";
		foreach($uploaded as $file) {
			$url = $this->app->siteurl.$file->filename;
			echo $comma.'{"original":"'.$file->original.'","path":"'.$file->path.'","extension":"'.$file->extension.'","filename":"'.$file->filename.'","partname":"'.$file->partname.'","url":"'.$url.'","fileid":"'.$this->router->getPost('fileid').'"}';
			$comma = ",";
		}
	}
	echo ']}';
		
}
/**
 * Download a Remote File
 * This service is called when you want to download a file from a remote location.
 * POST variables must specify the url and the path on the local server you want to save the file to.
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */

public function download() {
	
	global $framework;
	$router = $framework->load('twaRouter');
	$url = $router->getPost('url');
	$path = $router->getPost('path');
	
	$fp = fopen($path, 'w');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    
    echo '{"returnCode":0}'; 
}
/**
 * Delete a File
 * This service is called when you want to delete a file.
 * POST variables must specify the path on the local server where the file is.
 * 
 * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
 * @access public
 */

public function delete() {
	global $framework;
	$router = $framework->load('twaRouter');

	$file = new twaFile($router->getPost('path'));
	$file->delete();
	
	echo '{"returnCode":0}';	
}



}


?>