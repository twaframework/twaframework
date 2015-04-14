<?php
/**
 * The language object for loading the language and the metadata for each language. 
 * 
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaLanguage {
/**
 * The path where the language files are located
 *
 * @var string
 */
public $path = null;
/**
 * The current language
 *
 * @var string
 */
public $lang = null;

public $languages = array(
	"en_US" => array(
		"code" => "en-us",
		"dir" => "ltr"
	),
	"ar_SA"=>array(
		"code"=>"ar-sa",
		"dir"=>"rtl"
	)
);

public $config = array();

/**
 * Starting point for twaLanguage
 *
 * 
 * @access public
 */
public function __construct() {
	global $framework;
	$router = $framework->load('twaRouter');
	$view = $router->getFromURL('view');
	
	if(isset($_SESSION['lang'])) {
		$lang = $_SESSION['lang'];
	} else {
		$lang = $framework->load('twaConfig')->lang;
	}
	$this->lang = $lang;
	$_SESSION['lang'] = $lang;
	
	$this->path = $framework->contentpath.DS.'language'.DS.$this->lang;
	$this->config = $this->languages[$this->lang];
}

/**
 * Set the language
 *
 * @param String $lang set the language
 * @access public
 */
public function setLanguage($lang) {
	$this->lang = $lang;
	$_SESSION['lang'] = $lang;
}


}