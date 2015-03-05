var Socket;

fnc.require(['Class', 'HTML5'], function() {

	Socket = function(host, port) {
		var self = this;
		ClassWithBinds(self);
		
		this.init = function(host, port) {
			self.connected = false;
			self.supportWebsocket = false;
			
			self.host = null;
			self.port = null;
			self.socket = null;
				
			if(!window.WebSocket) { 
				self.supportWebsocket = false;
				self.trigger('_error', [0]);
			} else {
				self.supportWebsocket = true;
			}
			
			self.bind('_onbind', function(eventName) {
				switch(eventName) {
					case 'connect':
						if(self.connected) {
							self.trigger('connect');
						}
					break;
					case 'disconnect':
						if(!self.connected) {
							self.trigger('disconnect');
						}
					break;
				}
			});
			
			self._initErrors();
			
			if(typeof host != 'undefined') {
				self.connect(host, port);
			}
		}
		
		this._initErrors = function() {
			self.errors = [];
			
			self.errors[0] = 'Votre navigateur ne supporte pas les webSocket!';
			self.errors[1] = 'Url incorrecte ou serveur déconnecté !';
			
			self.bind('_error', function(error) {
				self.trigger('error', [error, self.errors[error]]);
				console.error(self.errors[error]);
			});
		}
		
		
		this.connect = function(host, port) {
			if(self.supportWebsocket && !self.connected) {
				self.host = host;
				self.port = port;
				
				var host = 'ws://' + self.host + ':' + self.port;
				
				try	{
					self.socket = new WebSocket(host);
				} catch(exception) { 
					self.trigger('error', [1]);
					return false;
				}
			
				self.socket.onerror = function(error) {
					self.trigger('error', [error]);
				}
				
				self.socket.onopen = function() {
					self.connected = true;
					self.trigger('connect');
				}
				
				self.socket.onmessage = function(message){
					self.trigger('receive', [message.data]);
				}
				
				self.socket.onclose = function() {
					self.connected = false;
					self.trigger('disconnect');
				}

				return true;
			}
		}
		
		this.disconnect = function() {
			if(self.connected) {
				self.socket.close();
			}
		}
		
		this.send = function(data) {
			if(self.connected) {
				if(typeof data == 'object') {
					data = JSON.stringify(data);
				}
				
				self.socket.send(data); 
			}
		}
		
		
		this.init(host, port);
	}
	
	fnc.libs['Network/Websocket'] = Socket;
});