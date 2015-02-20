function User(id, isLoggedIn) {
	this.isLoggedIn = isLoggedIn || false;
	this.fields = {};
	this.fields['user_id'] = id;
	this.social = {};
}

User.prototype.login = function(data,onSuccess,onError){
	var properties = {
		axn: "framework/auth",
		code: "login"
	};
	$.extend(properties,data);
	var me = this;
	$framework.request(properties,function(r){
		me.fields = r.user;
		me.social = r.social;
		$.event.trigger("loginComplete",r);
		if(typeof onSuccess == 'function'){
			onSuccess(r);
		}
		
	},function(code,x){
		if(typeof onError == 'function'){
			onError(code,x);
		}
	});	
}

User.prototype.logout = function(data,onSuccess,onError){
	var properties = {
		axn: "framework/auth",
		code: "logout"
	};
	$.extend(properties,data);
	var me = this;
	$framework.request(properties,function(r){
		me.fields = {};
		me.social = {};
		$.event.trigger("logoutComplete");
		if(typeof onSuccess == 'function'){
			onSuccess(r);
		}
	},function(code,x){
		if(typeof onError == 'function'){
			onError(code,x);
		}
	});	
}

User.prototype.signup = function(data,onSuccess,onError){
	var properties = {
		axn: "framework/auth",
		code: "signup"
	};
	$.extend(properties,data);
	var me = this;
	$framework.request(properties,function(r){
		me.fields = r.user;
		me.social = r.social;
		$.event.trigger("signupComplete",r);
		if(typeof onSuccess == 'function'){
			onSuccess(r);
		}
	},function(code,x){
		if(typeof onError == 'function'){
			onError(code,x);
		}
	});	
}

