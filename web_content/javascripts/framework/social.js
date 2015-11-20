var social_config = {};

/*
 Sample Configuration

 var social_config = {
 facebook: {
 enabled: true,
 appId: '238ry87345683658',
 scope: 'email, user_friends'
 },
 twitter: {
 enabled: true
 },
 linkedin: {
 api_key: '234y37858346856',
 enabled: true,

 },
 gplus: {
 enabled: true,
 client_id: '239874385683648756'
 }
 }

 */

/**
 To Call Login Functions - call the following functions on the click event of the buttons

 social.loginWithFB();
 social.loginWithTwitter();
 social.loginWithGPlus();
 social.loginWithLinkedIn();

 To get friends list:

 social.getFriendsList(network,onComplete);

 network - can be facebook, twitter, linkedin or gplus
 onComplete(obj) - a callback function that contains the object containing the friends list.

 Events Triggered:

 fbDataRetrieved			- when we retrieve data from facebook.  Can be used to save Profile or image
 twitterDataRetrieved	- when we retrieve data from Twitter.  Can be used to save Profile or image
 gPlusDataRetrieved		- when we retrieve data from Google Plus.  Can be used to save Profile or image
 linkedInDataRetrieved	- when we retrieve data from LinkedIn.  Can be used to save Profile or image
 loginComplete			- when user is loggedIn to the app.

 */


/****************************************************************************/
//			$_GET VARIABLES FROM THE URL									//
/****************************************************************************/

var _GET = {};

document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
	function decode(s) {
		return decodeURIComponent(s.split("+").join(" "));
	}

	_GET[decode(arguments[1])] = decode(arguments[2]);
});

/****************************************************************************/


/****************************************************************************/
//		Social Logins API For Facebook, Twitter, LinkedIn & GooglePlus		//
/****************************************************************************/

function SocialLogins(data) {
	this.properties = {
		"facebook":{
			enabled: false,
			status: 'not-connected'
		},
		"twitter":{
			enabled: false,
			status: 'not-connected'
		},
		"linkedin":{
			enabled: false,
			status: 'not-connected'
		},
		"gplus":{
			enabled: false,
			status: 'not-connected'
		},
		"appLogin":{

		}
	};

	$.extend(this.properties,data);

}

SocialLogins.prototype.init = function(){

	if(this.properties.facebook.enabled){
		this.initFB();
	}

	if(this.properties.twitter.enabled){
		this.initTwitter();
	}

	if(this.properties.linkedin.enabled){
		this.initLinkedIn();
	}

	if(this.properties.gplus.enabled){
		this.initGPlus();
	}

};


SocialLogins.prototype.initGPlus = function(){
	var me = this;
	if(typeof gapi == 'undefined'){
		this.loadGPlus();
		return;
	}
	me.properties.gplus['status'] = 'initialized';
};

SocialLogins.prototype.initFB = function(){
	var me = this;
	if(typeof FB == 'undefined'){
		this.loadFB();
		return;
	}
	me.properties.facebook['status'] = 'initialized';
};

SocialLogins.prototype.initLinkedIn = function(){
	var me = this;
	if(typeof IN == 'undefined'){
		this.loadLinkedIn();
		return;
	}
	me.properties.linkedin['status'] = 'initialized';
};

SocialLogins.prototype.initTwitter = function(){
	var me = this;
	if(typeof _GET['network'] !== 'undefined' && _GET['network'] == 'twitter'){
		me.completeTwitterLogin();
	} else {
		me.properties.twitter['status'] = 'initialized';
	}
};

SocialLogins.prototype.getFriendsList = function(network,onComplete){
	var me = this;
	switch(network){
		case 'facebook':
			FB.api('/me/friends', function(obj) {
				$.event.trigger("fbFriendsList", obj);
				if(typeof onComplete == 'function'){
					onComplete(obj);
				}
			});
			break;
		case 'gplus':
			gapi.client.load('plus','v1', function(){
				var request = gapi.client.plus.people.list({
					'userId': 'me',
					'collection': 'visible'
				});
				request.execute(function(obj) {
					$.event.trigger("gPlusFriendsList", obj);
					if(typeof onComplete == 'function'){
						onComplete(obj);
					}
				});
			});
			break;
		case 'linkedin':
			IN.API.Connections("me").result(function(obj){
				$.event.trigger("linkedInFriendsList", obj);
				if(typeof onComplete == 'function'){
					onComplete(obj);
				}
			});
			break;

		case 'twitter':
			if(typeof onComplete == 'function'){
				onComplete(me.properties.twitter.friends);
			}
			break
	}

};

SocialLogins.prototype.loginWithTwitter = function(){
	var me = this;
	$framework.request({
		"axn":"framework/twitter",
		"code":"login",
		"callback_url":window.location.href.replace(/#/g,'')+'?network=twitter'
	},function(obj){
		window.open(obj.url);
	});
};

SocialLogins.prototype.completeTwitterLogin = function(){
	var me = this;
	$framework.request({
		"axn":"framework/twitter",
		"code":"getdata",
		"oauth_token":_GET['oauth_token'],
		"oauth_verifier":_GET['oauth_verifier']
	},function(obj){
		me.properties.twitter['status'] = 'logged-in';
		me.appLogin({
			"username":obj.content.screen_name,
			"twitter_id":obj.content.id,
			"name":obj.content.name,
			"email":obj.content.id+"@twitter.com"
		});
		$.event.trigger("twitterDataRetrieved", obj.content);
		me.properties.twitter.friends = obj.friends;
	});
};


SocialLogins.prototype.loginWithGPlus = function(){
	var me = this;
	gapi.auth.authorize({
		client_id: me.properites.gplus.client_id,
		scope: me.properites.gplus.scope,
		immediate: true,
		cookie_policy: 'single_host_origin'
	}, function(obj){
		if(obj && obj.status && obj.status.signed_in && obj.status.signed_in === true){
			gapi.client.load('oauth2', 'v2', function(){
				var gplusObj = gapi.client.oauth2.userinfo.get();
				gplusObj.execute(function(obj){
					me.properties.gplus['status'] = 'logged-in';
					me.appLogin({
						"email":obj.email,
						"gplus_id":obj.id,
						"firstname":obj.given_name,
						"lastname":obj.family_name
					});
					$.event.trigger("gPlusDataRetrieved", obj);
				});
			});
		}
	});
};

SocialLogins.prototype.loginWithFB = function(){
	var me = this;
	if(typeof unitiques !== 'undefined'){
		unitiques.modal("logging_with_fb");
	}

	FB.login(function(obj) {
		if (obj.authResponse) {
			FB.api('/me', {fields: "id, first_name, last_name, email, picture"}, function(obj) {
				me.properties.facebook['status'] = 'logged-in';

				me.appLogin({
					"email":obj.email,
					"fb_id":obj.id,
					"firstname":obj.first_name,
					"lastname":obj.last_name
				});
				$.event.trigger("fbDataRetrieved",obj);
			});

		}
	},{scope: me.properties.facebook.scope});
};

SocialLogins.prototype.loginWithLinkedIn = function(){
	var me = this;
	if(IN.User.isAuthorized() === true){
		var fields = ['id',
			'first-name',
			'last-name',
			'email-address',
			'picture-url'];
		IN.API.Profile("me").fields(fields).result(function(obj){
			me.properties.linkedin['status'] = 'logged-in';
			me.appLogin({
				"email":obj.emailAddress,
				"linkedin_id":obj.id,
				"firstname":obj.firstName,
				"lastname":obj.lastName
			});
			$.event.trigger("linkedInDataRetrieved",obj);
		});
	}
};

SocialLogins.prototype.getFBData = function(onComplete){
	var me = this;
	FB.login(function(obj) {
		if (obj.authResponse) {
			FB.api('/me', {fields: "id, first_name, last_name, email, picture"}, function(obj) {
				$.event.trigger("fbDataRetrieved",obj);
				if(typeof onComplete == 'function'){
					onComplete(obj);
				}
			});

		}
	},{scope: me.properties.facebook.scope});
};

SocialLogins.prototype.getLinkedInData = function(){
	var me = this;
	if(IN.User.isAuthorized() === true){
		var fields = ['id',
			'first-name',
			'last-name',
			'email-address',
			'picture-url'];
		IN.API.Profile("me").fields(fields).result(function(obj){
			$.event.trigger("linkedInDataRetrieved",obj);
		});
	}
};


SocialLogins.prototype.getGPlusData = function(){
	var me = this;
	gapi.auth.authorize({
		client_id: me.properites.gplus.client_id,
		scope: me.properites.gplus.scope,
		immediate: true,
		cookie_policy: 'single_host_origin'
	}, function(obj){
		if(obj && obj.status && obj.status.signed_in && obj.status.signed_in === true){
			gapi.client.load('oauth2', 'v2', function(){
				var gplusObj = gapi.client.oauth2.userinfo.get();
				gplusObj.execute(function(obj){
					$.event.trigger("gPlusDataRetrieved", obj);
				});
			});
		}
	});
};


SocialLogins.prototype.getTwitterData = function(){
	var me = this;
	$framework.request({
		"axn":"framework/twitter",
		"code":"getdata",
		"oauth_token":_GET['oauth_token'],
		"oauth_verifier":_GET['oauth_verifier']
	},function(obj){
		me.properties.twitter.friends = obj.friends;
		$.event.trigger("twitterDataRetrieved", obj.content);
	});
};


SocialLogins.prototype.appLogin = function(obj){

	var properties = {
		"axn":"framework/auth",
		"code":"socialLogin",
		"type":"social"
	};
	$.extend(properties,social_config.appLogin);
	$.extend(properties,obj);

	$framework.request(properties,function(r){
		var data = r;
		data.input = properties;

		$user.fields = r.user;
		$user.social = r.social;
		if(r.new == 1){
			$.event.trigger("signupComplete",data);
		} else {
			$.event.trigger("loginComplete",data);
		}
	},function(x){
		console.debug(x);
	});
};

SocialLogins.prototype.loadFB = function(){
	var me = this;
	window.fbAsyncInit = function() {
		FB.init({
			appId      : me.properties.facebook.appId,
			cookie     : true,  // enable cookies to allow the server to access the session
			xfbml      : true,  // parse social plugins on this page
			version    : 'v2.1' // use version 2.1
		});
		me.initFB();
	};

	var js, fjs = document.getElementsByTagName('script')[0];
	if (document.getElementById('facebook-jssdk')){
		return;
	}
	js = document.createElement('script'); js.id = 'facebook-jssdk';
	js.src = "//connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
};

SocialLogins.prototype.loadGPlus = function(){
	var htmlStr = "<script src='https://apis.google.com/js/client.js?onload=googlePlusLoaded'>";
	$("head").append(htmlStr);
	var me = this;
	me.initGPlus();
};

SocialLogins.prototype.loadLinkedIn = function(){
	var me = this;
	var htmlStr = "";
	htmlStr += "<script type='text/javascript' src='https://platform.linkedin.com/in.js'>";
	htmlStr += "onLoad: linkedinLoaded \n";
	htmlStr += "api_key: "+me.properties.linkedin.api_key+" \n";
	htmlStr += "authorize: true \n";
	htmlStr += "</script>";
	$("head").append(htmlStr);
};


var social = new SocialLogins(social_config);
social.init();


