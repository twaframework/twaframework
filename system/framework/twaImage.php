<?php
/**
 * The file object for managing files.  
 *
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaImage extends twaFile {

/**
 * The array that contains filetypes
 *
 * @var Array
 */
public $filetype = array();
public $image = false;
/**
 * The array that contains extensions that can be downloaded
 *
 * @var Array
 */
public $downloadableExtentions = array('png','gif','jpg','jpeg','bmp');
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
		
		$this->image = new Imagick($filepath);
		$this->getImageParams($filepath);
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
		$this->columns = '';
		$this->rows = '';
		$this->ratio = '';
}

public function getImageParams($fullpath){
	if(!$this->image){
		$this->image = new Imagick($fullpath);
	}
	
	$this->width = $this->image->getImageWidth();
	$this->height = $this->image->getImageHeight();
	$this->ratio = ($this->width/$this->height);
}

public function thumbnail($data){
	$this->image->cropThumbnailImage($data['width'],$data['height']);
}

public function resize($data){
	$this->image->resizeImage($data['width'],$data['height'],imagick::FILTER_UNDEFINED,1);
}

public function watermark($data){
	
	if(file_exists($data['path'])){
		$watermark = new Imagick($framework->basepath.$data['path']);
		$this->image->compositeImage($watermark, imagick::COMPOSITE_OVER, 0, 0);
	}
}

public function tileable($data) {
	global $framework;
	
	$flip = clone $this->image;
	$flop = clone $this->image;
	$opposite = clone $this->image;
	$flip->flipImage();
	$flop->flopImage();
	$opposite->flopImage();
	$opposite->flipImage();
	
	$new = new Imagick();
	$new->newImage($this->width*2, $this->height*2, new ImagickPixel($data['base_color']));
	$new->setImageFormat($data['format']);
	$new->compositeImage($this->image, imagick::COMPOSITE_OVER, 0, 0);
	$new->compositeImage($flip, imagick::COMPOSITE_OVER, 0, $this->rows);
	$new->compositeImage($flop, imagick::COMPOSITE_OVER, $this->columns, 0);
	$new->compositeImage($opposite, imagick::COMPOSITE_OVER, $this->columns, $this->rows);
	$this->image = $new;
}

public function rotate($data){
	$this->image->rotateImage(new ImagickPixel('#00000000'), $data['angle']);
}

public function crop($data){
	$r = floatval($this->width/$data['image_width']);
	$x = $data['x']*$r;
	$y = $data['y']*$r;
	$width = $data['width']*$r;
	$height = $data['height']*$r;	
	//echo $r.":".$x.":".$y.":".$width.":".$height;
	$this->image->cropImage($width,$height,$x,$y);
}


public function save($data){
	global $framework;
	
	$this->image->setImageFormat($data['format']);
	$this->image->writeImage($framework->basepath.$data['path']);
}

public function release(){
	$this->image->clear();
	$this->image->destroy(); 
}

}