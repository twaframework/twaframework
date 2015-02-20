
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

class twaWebServices_framework_image extends twaWebServices {

	public function __construct(){
		parent::__construct();
	}	

	public function info(){
		$image = new twaImage($this->framework->basepath.$this->router->getPost('image_path'));
		$return['returnCode'] = 0;
		$return['width'] = $image->width;
		$return['height'] = $image->height;
		$return['ratio'] = $image->ratio;
		$json = json_encode($return);
		echo $json;
		
	}

	public function resize() {
		$image = new twaImage($this->framework->basepath.$this->router->getPost('image_path'));
		$image->resize($this->router->getCleanPost());	
		$image->save($this->router->getCleanPost());
		$image->release();
		$return['returnCode'] = 0;
		$json = json_encode($return);
		echo $json;
	}
	
	public function watermark() {
		$image = new twaImage($this->framework->basepath.$this->router->getPost('image_path'));
		$image->watermark($this->router->getCleanPost());
		$image->save($this->router->getCleanPost());
		$image->release();
		$return['returnCode'] = 0;
		$json = json_encode($return);
		echo $json;
	}
	
	public function tileable(){
		$image = new twaImage($this->framework->basepath.$this->router->getPost('image_path'));
		$image->tileable($this->router->getCleanPost());
		$image->save($this->router->getCleanPost());
		$image->release();
		$return['returnCode'] = 0;
		$json = json_encode($return);
		echo $json;
	}

	public function thumbnail() {
		$image = new twaImage($this->framework->basepath.$this->router->getPost('image_path'));
		$image->thumbnail($this->router->getCleanPost());	
		$image->save($this->router->getCleanPost());
		$image->release();
		$return['returnCode'] = 0;
		$json = json_encode($return);
		echo $json;
	}
	
	public function write(){
		$image = new twaImage($this->framework->basepath.$this->router->getPost('image_path'));
		$image->save($this->router->getCleanPost());
		$image->release();
		$return['returnCode'] = 0;
		$json = json_encode($return);
		echo $json;
	}
	
	public function rotate(){
		$image = new twaImage($this->framework->basepath.$this->router->getPost('image_path'));
		$image->rotate($this->router->getCleanPost());	
		$image->save($this->router->getCleanPost());
		$image->release();
		$return['returnCode'] = 0;
		$json = json_encode($return);
		echo $json;
	}
	
	public function crop(){
		$image = new twaImage($this->framework->basepath.$this->router->getPost('image_path'));
		$image->crop($this->router->getCleanPost());	
		$image->save($this->router->getCleanPost());
		$image->release();
		$return['returnCode'] = 0;
		$json = json_encode($return);
		echo $json;
	}

}