<?php
defined('_TWACHK') or die;

/*Class: TWAApp -the main application controller.*/
class twaAnalytics {

public $identifier;
public $ip;
public $postfields = array();

public function __construct() {
	if(isset($_SESSION['_twaanalyticsid'])) {
		$this->identifier = $_SESSION['_twaanalyticsid'];
	} else {
		$_SESSION['_twaanalyticsid'] = uniqid();
	}
	
	$this->ip = $_SERVER['REMOTE_ADDR'];
	
}

public function track($event,$params = null) {
	$time = strtotime('now');
	global $framework;
	global $app;
	$user = $framework->getUser();
	$account = $framework->load('twaConfig')->twa_API_KEY;	
	if($account == "") {
		return;
	}
	$this->postfields = array();
	$this->postfields['account'] = $account;
	$this->postfields['time'] = $time;
	$this->postfields['event'] = $event;
	$this->postfields['url'] = $app->siteurl;
	$this->postfields['ip'] = $_SERVER['REMOTE_ADDR'];
	$this->postfields['params']= "";
	if($params) {
		$this->postfields['params'] = urlencode(json_encode($params));
	}
	
	$this->send();
}

private function send() {
	$url = 'http://api.twaframework.com/analytics/data/track';
	
	$fields_string = "";
	foreach($this->postfields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');
	//open connection
	$ch = curl_init();
	
	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($this->postfields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//execute post
	$result = curl_exec($ch);
	
	//close connection
	curl_close($ch);
}


}
