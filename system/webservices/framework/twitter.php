<?php
/**
 * The twitter webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/social".  
 * This web-service contains all the actions related to twitter social login
 * @category web-service 
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaWebServices_framework_twitter extends twaWebServices {

	public function __construct(){
		parent::__construct();
	}	
	
	public function login(){
		if (empty($_SESSION['twitter_access_token']) || empty($_SESSION['twitter_access_token']['oauth_token']) || empty($_SESSION['twitter_access_token']['oauth_token_secret'])) {
		    unset($_SESSION['twitter_access_token']);
		}
		$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
		
		/* Get temporary credentials. */
		$request_token = $connection->getRequestToken($this->router->getPost('callback_url'));
		
		/* Save temporary credentials to session. */
		$_SESSION['twitter_access_token']['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['twitter_access_token']['oauth_token_secret'] = $request_token['oauth_token_secret'];
		 
		/* If last connection failed don't display authorization link. */
		$url = $connection->getAuthorizeURL($_SESSION['twitter_access_token']['oauth_token']);
		
		$return['url'] = $url;
		$return['returnCode'] = 0;
		$json = json_encode($return);
		echo $json;
	}
	
	public function getdata(){
		$_SESSION['twitter_access_token']['oauth_token'] = $this->router->getPost('oauth_token');
		$_SESSION['twitter_access_token']['oauth_verifier'] = $this->router->getPost('oauth_verifier');
		$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $_SESSION['twitter_access_token']['oauth_token'], $_SESSION['twitter_access_token']['oauth_token_secret']);
		$access_token = $connection->getAccessToken($_SESSION['twitter_access_token']['oauth_verifier']);
		$content = $connection->get('account/verify_credentials');
		$friends = $connection->get('friends/ids', array('screen_name' => $content->name));
		
		$return = array();
		$return['returnCode'] = 0;
		$return['content'] = $content;
		$return['access_token'] = $access_token;
		$return['friends'] = $friends;
		echo json_encode($return);
	}
	
	
}