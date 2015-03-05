var Input;
var Inputs;

fnc.require(['Class', 'HTML5'], function() {
	
	Input = function() {
		var self = this;
		ClassWithBinds(self);
		
		this.init = function() {
			self.connected = false;
			
			self.bind('_connect', function(eventName) {
				if(!self.connected) {
					self.connected = true;
					self.trigger('connect');
				}
			});
			
			self.bind('_disconnect', function(eventName) {
				if(self.connected) {
					self.connected = false;
					self.trigger('disconnect');
				}
			});
			
			self.bind('_onbind', function(eventName) {
				if(eventName == 'connect' && self.connected) {
					self.trigger('connect');
				}
				
				if(eventName == 'disconnect' && !self.connected) {
					self.trigger('disconnect');
				}
			});
		}
		
		this.init();
	}
	
	
	var InputsConstructor = function() {
		var self = this;
		ClassWithBinds(self);
		
		
		this.init = function() {
		}
		
		this.listInputs = function(callback) {
			self.inputs = {
				micophone: [],
				camera: []
			};
			
			if(typeof MediaStreamTrack == 'undefined' || typeof MediaStreamTrack.getSources == 'undefined'){
				callback(self.inputs);
			} else {
				MediaStreamTrack.getSources(function(sources) {	
					for(var i = 0; i < sources.length; i++) {
						var source = sources[i];
						switch(source.kind) {
							case 'audio':
								self.inputs.micophone.push({
									label: source.label || 'microphone ' + (self.inputs.micophone.length + 1),
									id: source.id
								});
							break;
							case 'video':
								self.inputs.camera.push({
									label: source.label || 'camera ' + (self.inputs.camera.length + 1),
									id: source.id
								});
							break;
							default:
								console.log('Some other kind of source: ', source);
						}
					}
					
					callback(self.inputs);
				});
			}
		}
		
		this.init();
	}
	
	Inputs = new InputsConstructor();
	fnc.libs['Inputs'] = Inputs;
});