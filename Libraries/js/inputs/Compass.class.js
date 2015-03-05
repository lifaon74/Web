var Compass;

fnc.require(['Class', 'HTML5', 'Inputs'], function() {
	
	var CompassConstructor = function() {
		var self = this;
		Class(self);
		self.extend(Input);
		
		this.init = function() {
			self.eventListener = null;
			
			
			self.alpha = null;
			self.beta = null;
			self.gamma = null;
		}
		
		this._startDeviceOrientation = function() {
			self.eventListener = HTML5.addEventListener(window, 'deviceorientation', function(event) {
				self.alpha = event.webkitCompassHeading || event.alpha || null;
				self.beta = event.beta || null;
				self.gamma = event.gamma || null;
				
				if(!self.connected) {
					self.trigger('_connect');
				}
			});
		}
		
		this._stopDeviceOrientation = function() {
			HTML5.removeEventListener(self.eventListener);
		}
		
		this.connect = function() {
			if(!self.connected) {
				setTimeout(function() {
					if(!self.connected) {
						self.trigger('_disconnect', [0]);
					}
				}, 2000);
				
				self._startDeviceOrientation();
			}
		}
		
		this.disconnect = function() {
			if(self.connected) {
				self._stopDeviceOrientation();
				self.trigger('_disconnect', [0]);
			}
		}
		
		
		this.init();
	}
	
	Compass = new CompassConstructor();
	fnc.libs['Inputs/Compass'] = Compass;
});