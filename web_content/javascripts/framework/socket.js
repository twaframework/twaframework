function SocketConnection($datastring) {
	this.data = {
		"server":"",
		"channel":"",
		"account":""
	};
	this.mguids = [];
	$.extend(true,this.data,$datastring);
	this.socket = null;
	//this.init();
	this.onConnect = function(){};
	var me = this;
	
	this.messageHandler = function(message) {};
	
	this.onMessage = function(message){
		console.debug(message);
		if($.inArray(message.id,me.mguids) !== -1) {
			return;
		} else {
			me.mguids.push(message.id);
		}
		
		if(typeof me[message.message.type] == 'function') {
			me[message.message.type](message);
		}
		me.messageHandler(message);
	};
}

SocketConnection.prototype.init = function() {
	var me = this;
	if(typeof io !== 'undefined') {
		
		io.connect(me.data.server,{'flash policy port':8000}).emit('new-channel', {
		    channel: me.data.channel,
		    sender: me.data.account
		});
		me.socket = io.connect(me.data.server + me.data.channel);
	}
}


SocketConnection.prototype.setup = function() {

	var me = this;
	me.socket.on('connect',function(){
		me.onConnect();
	});
	me.socket.on('message',function(message){
		me.onMessage(message);
	});

	me.socket.send = function (message) {
		me.socket.emit('message', {
	        sender: me.data.account,
	        data: {
	        	id: guid(),
		        sender: $user,
		        message: message
	        }
	    });
	};
	
		
}