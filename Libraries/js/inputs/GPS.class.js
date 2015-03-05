var GPS;

fnc.require(['Class', 'HTML5', 'Inputs'], function() {

	var CoordinatePoint = function(position) {
		var self = this;
		
		this.init = function(position) {
			self.latitude = position.latitude;
			self.longitude = position.longitude;
			self.altitude = position.altitude || null;
			
			self.accuracy = position.accuracy || null;
			self.altitudeAccuracy = position.altitudeAccuracy || null;
			self.angle = position.heading || null;
			self.speed = position.speed || null;
			
			self.country = position.country || null;
			self.region = position.region || null;
			self.department = position.department || null;
			self.city = position.city || null;
			self.postalCode = position.postalCode || null;
			self.street = position.street || null;
			self.streetNumber = position.streetNumber || null;
		}
		
			// TODO
		this.getAddress = function() {
		}
	
	
		this.init(position);
	}
	
		
	var GPSConstructor = function() {
		var self = this;
		Class(self);
		self.extend(Input);
		
		this.init = function() {
			self.watchId = null;
			self.coordinatePoint = null;
		}
		
		
		this.connect = function() {
			if(!self.connected) {
				if(navigator.geolocation) {
					self.watchId = navigator.geolocation.watchPosition(
						function(position) { // success
							self.coordinatePoint = new CoordinatePoint(position.coords);
							if(!self.connected) {
								self.trigger('_connect');
							}
						},
						function() { // error
							switch(error) {
								case error.PERMISSION_DENIED:
									self.trigger('_disconnect', [1]);
								break;
								case error.POSITION_UNAVAILABLE:
									self.trigger('_disconnect', [2]);
								break;
								case error.TIMEOUT:
									self.trigger('_disconnect', [3]);
								break;
								default:
								break;
							}

						},
						{ enableHighAccuracy:true, timeout: 60 * 1000});
				} else {
					self.trigger('_disconnect', [0]);
				}
			}
		}
		
		this.disconnect = function() {
			if(self.connected) {
				navigator.geolocation.clearWatch(self.watchId);
				self.trigger('_disconnect', [2]);
			}
		}
		
		
		this.getPosition = function() {
			if(self.connected) {
				return self.coordinatePoint
			} else {
				return null;
			}
			/*if(navigator.geolocation) {
				self.started = true;
				navigator.geolocation.getCurrentPosition(
					fnc.closure(self._getPosition, [callback]), // success
					function() { // error
						alert('erreur');
						callback(null);
					},
					{ enableHighAccuracy: true});
			} else {
				callback(null);
			}*/
		}
			
			
		this.getDistanceBetweenPoints = function(lat1, lng1, lat2, lng2) {
			return 6378000 * Math.acos( Math.dcos(lat1) * Math.dcos(lat2) * Math.dcos(lng2 - lng1) + Math.dsin(lat1) * Math.dsin(lat2));
		}
		
		this.getAngleBetweenPoints = function(lat1, lng1, lat2, lng2) {
			var dlng = lng2 - lng1;
			var y = Math.dsin(dlng) * Math.dcos(lat2);
			var x = Math.dcos(lat1) * Math.dsin(lat2) - Math.dsin(lat1) * Math.dcos(lat2) * Math.dcos(dlng);
			
			var angle = Math.datan2(y, x);
			/*while(angle < 0) {
				angle += 360;
			}*/
			return angle % 360;
		}
		
		this.init();
	}
	
	GPS = new GPSConstructor();
	fnc.libs['Inputs/GPS'] = GPS;
});
