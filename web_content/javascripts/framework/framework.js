Function.prototype.inheritsFrom = function( parentClassOrObject ){ 
	if ( parentClassOrObject.constructor == Function ) 
	{ 
		//Normal Inheritance 
		this.prototype = new parentClassOrObject;
		this.prototype.constructor = this;
		this.prototype.parentclass = parentClassOrObject.prototype;
	} 
	else 
	{ 
		//Pure Virtual Inheritance 
		this.prototype = parentClassOrObject;
		this.prototype.constructor = this;
		this.prototype.parentclass = parentClassOrObject;
	} 
	return this;
} 

var jsScripts = [];
var today = new Date();

function twaObject() {
	var me = this;
	this.errorMessage = "";
}

twaObject.prototype.onJSONParseError = function(html,onError){
	var me = this;
	me.debug("HTML",html);
	if(typeof onError == 'function') {
		onError(100,"JSON Parse Error",html);
	} else {
		me.onError(100,"JSON Parse Error",html);
	}
};

twaObject.prototype.debug = function(text,object) { 
	console.debug(text,object);
};

twaObject.prototype.onAJAXError = function(xhr,datastring,onError){
	var me = this;
	if (xhr.status === 0) {
		this.errorMessage = "Network Error";
	} else if (xhr.status == 404) {
	    this.errorMessage = "404: Page Not Found";
	} else if (xhr.status == 500) {
	   this.errorMessage = "500: Internal Server Error";
	} else if (exception === 'parsererror') {
	    this.errorMessage = "JSON Parse Error";
	} else if (exception === 'timeout') {
	    this.errorMessage = "Request Has Timed Out";
	} else if (exception === 'abort') {
	    this.errorMessage = "Ajax Request Aborted";
	} else {
	    this.errorMessage = "Uncaught Exception:" + xhr.responseText;
	}
	
	if(xhr.status != 404) {
		if(typeof onError == 'function') {
			onError(101,this.errorMessage,xhr);
		} else {
			me.onError(101,this.errorMessage,xhr);
		}
	}
};

twaObject.prototype.onError = function(code,message,extra){
	var me = this;
	me.debug("All Error Codes Between 100 and 199 are server errors.  All Error Codes above 200 are User Errors. ", code);
	me.debug("Message ",message);
	
	if(typeof extra !== 'undefined'){
		me.debug("Extra ",extra);
	}
	$.event.trigger("frameworkError",{ code: code, message:message, extra:extra});
}

twaObject.prototype.onValidationError = function(fields){
	var me = this;
	$.each(fields, function(i,eField){
		
		eField.field.parent().find('.under-error').remove();
		eField.field.parent().append("<div class='under-error'><p class='message-content'>"+eField.error+"</p></div>");
	});
	
	if(fields.length > 1){
		message = "Oops! Looks like the form is incomplete.";
		me.onError(200, message,fields);
	}
	
}

twaObject.prototype.globalsettings = function(onSuccess){
	var me = this;
	if(typeof me.settings != 'undefined'){
		onSuccess(me.settings);
		return;
	}
	$.getJSON( $baseurl+"system/config/global_settings.json", function( data ) {
		me.debug("Global Settings",data);
		me.settings = data;	
		onSuccess(me.settings);
	});
}

twaObject.prototype.request = function(data, onSuccess, onError) {
	var me = this;
	var $datastring = {
		url: $baseurl+'webservices.php',
		"twa_token": $authtoken
	};		
	$.extend($datastring, data);
	me.debug("Web Request: "+$datastring.axn+'/'+$datastring.code,$datastring);
	$.ajax({
		type: 'POST',
		url: $datastring.url,
		data: $datastring,
		cache: false,
		success: function($html) {
			
			$html = $html.replace(/\n/g, '');
			if(typeof $twaDebugger !== 'undefined') {
				$twaDebugger.reload();
			}
			try {
				if(typeof $html == 'string') {
					$response = $.parseJSON($html);
				} else if(typeof $html == 'object') {
					$response = $html;
				} else {
					if(typeof $twaDebugger !== 'undefined') {
						$twaDebugger.log($html);
					}
					me.onJSONParseError($html,onError);
					
					return;
				}
				
			} catch(e) {
				if(typeof $twaDebugger !== 'undefined') {
					$twaDebugger.log($html);
				}
				me.onJSONParseError($html,onError);
				return;
			}
			me.debug("Web Response: "+$datastring.axn+'/'+$datastring.code,$response);	
			if($response.returnCode == 0) {
				if(typeof onSuccess === 'function') {
					onSuccess($response);
				}
				// Success
			} else {
				//Failure
				if(typeof $twaDebugger !== 'undefined') {
					$twaDebugger.log(JSON.stringify($response));
				}
				if(typeof onError == 'function') {
					onError($response);
				} else {
					me.onError($response.errorCode,$response.error);
				}
			}	
		
		},
		error: function(xhr, error) {
			if(typeof $twaDebugger !== 'undefined') {
				$twaDebugger.reload();
			}
			me.onAJAXError(xhr,$datastring,onError);
		}
	});
}

twaObject.prototype.load = function(data, onSuccess, onError) {
	var $datastring = {
		url: $baseurl+'webservices.php'
	};		
	$.extend($datastring, data);
	if(typeof $twaDebugger !== 'undefined') {
		$twaDebugger.log(JSON.stringify($datastring));
	}
	var me = this;
	$.ajax({
		type: 'POST',
		url: $datastring.url,
		data: $datastring,
		cache: false,
		success: function($html) {
			if(typeof onSuccess === 'function') {
				onSuccess($html);
			}
		},
		error: function(xhr, error) {
			me.onAJAXError(xhr,$datastring,onError);
		}
	});
}

twaObject.prototype.modal = function(data,onLoad){
	var me = this;
	
	var properties = {
		"axn":"framework/load",
		"code":"component"
	}
	
	$.extend(properties,data);
	
	me.load(properties,function($html){
		if($('#'+data.container).length > 0) {
			$('#'+data.container).remove();
		}
		$('body').append("<div id='"+data.container+"'></div>");
		$('#'+data.container).html($html);
		if(typeof onLoad === 'function'){
			onLoad();	
		}
	});
}

twaObject.prototype.loadLater = function(onLoad){
	$.each(jsScripts,function(i,script){
		var element = document.createElement("script");
	 	element.src = script;
	 	document.body.appendChild(element);
	});
	setTimeout(function(){
		if(typeof onLoad === 'function') {
			onLoad();
		}
	},200);
}

twaObject.prototype.screen = function(){
	var myWidth = 0, myHeight = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
		//Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	}
	return {
		height: myHeight,
		width: myWidth
	}
}

twaObject.prototype.clean = function(val){
    val = val.replace(/[`\{\}\\\<\>]/gi,'');
    val = val.replace(/\n/g, "<br/>");
    val = val.replace(/'/gi,'&rsquo;');
    val = val.replace(/"/gi,'&rdquo;');
    return val;
}

twaObject.prototype.validate = function(form,properties,onSuccess, onError){
	var success = onSuccess || function(){};
	var errorFields = [];
	var me = this;
	var settings = {
		elementOnly: false,
		errorClass: 'input-error',
		errorMessageClass: 'under-error'
	}
	
	$.extend(settings, properties);
	
	form.find('.'+settings.errorClass).removeClass(settings.errorClass);
	form.find('.'+settings.errorMessageClass).hide().remove();
	
	
	
	form.find('.vrequired').each(function(){
		if(!settings.elementOnly || (settings.elementOnly && $(this).attr('id')== settings.element))
		{	
			if($(this).val() == ''&&$(this).is(':visible')) {
				$(this).addClass(settings.errorClass);
				errorFields.push({field: $(this), type: 'vrequired', error: 'This field is required'});
			}
			
		}
	});
	
	form.find('.srequired').each(function(){
		if(!settings.elementOnly || (settings.elementOnly && $(this).attr('id')== settings.element))
		{	
			
			if($(this).data('value') === '' && $(this).is(':visible')) {
				$(this).addClass(settings.errorClass);
				errorFields.push({field: $(this), type: 'srequired', error: 'This field is required'});
			}
		}
	});
	
	form.find('.vminlength').each(function(){
		if(!settings.elementOnly || (settings.elementOnly && $(this).attr('id')== settings.element))
		{	
			var thresh = parseInt($(this).data('value'));
			if($(this).val().length < thresh && $(this).val() != '') {
				$(this).addClass(settings.errorClass);
				errorFields.push({field: $(this), type: 'vminlength', error: 'Value cannot be less than '+thresh+' characters'});
			}
			
		}
	});
	
	form.find('.vmaxlength').each(function(){
		if(!settings.elementOnly || (settings.elementOnly && $(this).attr('id')== settings.element))
		{	
			var thresh = parseInt($(this).data('value'));
			if($(this).val().length > thresh) {
				$(this).addClass(settings.errorClass);
				errorFields.push({field: $(this), type: 'vminlength', error: 'Value cannot be more than '+thresh+' characters'});
			}
			
		}
	});
	
	form.find('.vcompare').each(function(){
		if(!settings.elementOnly||(settings.elementOnly&&$(this).attr('id')== settings.element))
		{	
			$value = $(this).data('value');
			
			if(!$value){
				$field = $(this).data('field');
				$value = $('#'+$field).val();
			}
			
			$check = $(this).data('check');
			
			if(!$check) {
				$check = 'equal';
			}
		
			switch($check) {
				case 'equal':
					
					if($(this).val() == $value) { $result= true} else { $result = false}
				break;
				case 'greaterthan':
					if($(this).val() > $value) { $result= true} else { $result = false}
				break;
				case 'lessthan':
					if($(this).val() < $value) { $result= true} else { $result = false}
				break;
				case 'greaterthanequal':
					if($(this).val() >= $value) { $result= true} else { $result = false}
				break;
				case 'lessthanequal':
					if($(this).val() <= $value) { $result= true} else { $result = false}
				break;
				case 'rangeinclusive':
					$minval = $(this).data('minval');
					$maxval = $(this).data('maxval');
					if($(this).val() >= $minval && $(this).val() <= $maxval ) { $result= true} else { $result = false}
				break;
			
			}
		
		if(!$result&&$(this).is(':visible')) {
			$(this).addClass(settings.errorClass);
			
			var msg = $(this).data('vcompare');
			if(typeof msg == 'undefined' || msg.length <= 0) {
				msg = "This field is invalid.";
			}
			errorFields.push({field: $(this), type: 'vcompare', error: msg});
			
		}
		
	}
  });
  form.find('.vcomparedate').each(function(){
	  if(!settings.elementOnly||(settings.elementOnly&&$(this).attr('id')== settings.element))
	  {	
		var $value = new Date($(this).data('value'));
		if(!$(this).data('value')){
			
			$field = $(this).data('field');
			var $value = new Date($('#'+$field).val());
		}
		
		$check = $(this).data('check');
		
		var $source = new Date($(this).val());
		
		$result=false;
		switch($check) {
			case 'equal':
				if($source == $value) { $result= true} else { $result = false}
			break;
			case 'greaterthan':
				if($source > $value) { $result= true} else { $result = false}
			break;
			case 'lessthan':
				if($source < $value) { $result= true} else { $result = false}
			break;
			case 'greaterthanequal':
				if($source >= $value) { $result= true} else { $result = false}
			break;
			case 'lessthanequal':
				if($source <= $value) { $result= true} else { $result = false}
			break;
			
		}
		if(!$result&&$(this).is(':visible')) {
			$(this).addClass(settings.errorClass);
			errorFields.push({field: $(this), type: 'vcomparedate', error: 'This field is invalid.'});
		}
		
	}
	});
	form.find('.vemail').each(function(){
		if(!settings.elementOnly||(settings.elementOnly&&$(this).attr('id')== settings.element))
		{	
			$emailpattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
			$emailAddress = $(this).val();
			if($emailpattern.test($emailAddress)===false && $emailAddress != '') {
				
				$(this).addClass(settings.errorClass);
				errorFields.push({field: $(this), type: 'vemail', error: 'Invalid email address'});
			}
		}
	});
		  
	form.find('.vnumeric').each(function(){
		if(!settings.elementOnly||(settings.elementOnly&&$(this).attr('id')== settings.element))
		{		
			$pattern = /^(0|[1-9][0-9]*)$/;
			$value = $(this).val();
			if($pattern.test($value)===false && $value != '') {
				$(this).addClass(settings.errorClass);
				errorFields.push({field: $(this), type: 'vnumeric', error: 'This field must be numeric'});
			}
		}
	});
	  	
	form.find('.vzip').each(function(){
	if(!settings.elementOnly||(settings.elementOnly&&$(this).attr('id')== settings.element))
	{	
		$pattern = /^(\d{5}|\d{5}\-\d{4})$/;
		$value = $(this).val();
		if($pattern.test($value)===false && $value != '') {
			
			$(this).addClass(settings.errorClass);
			errorFields.push({field: $(this), type: 'vzip', error: 'Invalid zip code'});
		}
	}
	});
	
	form.find('.vurl').each(function(){
	if(!settings.elementOnly||(settings.elementOnly&&$(this).attr('id')== settings.element))
	{	
		$pattern = /(https?:\/\/)?([\da-z\.\-]+)?\.?([\da-z\.\-]+)\.([a-z]{2,6})\/?([\/\w\.\-\?\&\=\%]+)?[^\s(\<br)?]/;
		$value = $(this).val();
		if($pattern.test($value)===false && $value != '') {
			
			$(this).addClass(settings.errorClass);
			errorFields.push({field: $(this), type: 'vurl', error: 'Invalid URL'});
		}
	}
	});
	
	if(errorFields.length > 0) {
		if(typeof onError == 'function') {
			onError(errorFields);
		} else {
			me.onValidationError(errorFields);
		}
	} else {
		success();
	}
	

}
Array.prototype.remove = function(v) { this.splice(this.indexOf(v) == -1 ? this.length : this.indexOf(v), 1); }

function S4() {   

   return (((1+Math.random())*0x10000)|0).toString(16).substring(1);   

}   

function guid() {   

   return (S4()+S4()+""+S4()+""+S4()+""+S4()+""+S4()+S4()+S4());   

}  

if (typeof Object.keys !== "function") {
    (function() {
        Object.keys = Object_keys;
        function Object_keys(obj) {
            var keys = [], name;
            for (name in obj) {
                if (obj.hasOwnProperty(name)) {
                    keys.push(name);
                }
            }
            return keys;
        }
    })();
}

var $framework = new twaObject();



