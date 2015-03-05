var Map;

fnc.require(['Class', 'PHPBridge', 'RegEx', 'Math'], function() {
	
	Map = function(container) {
		var self = this;
		Class(self);
		
		
		this.init = function(container) {
			self.container = container;
			
			self.attributes = {};
			
			self.attributes.latitude = 0;
			self.attributes.longitude = 0;
			self.attributes.zoom = 1;
			self.attributes.type = 'road';
			self.attributes.displayUI = true;
			
			self.attributes.markerIcon = null;
			
			self.elements = [];
			
			
			self.map = new google.maps.Map(self.container, {
				zoom: self.attributes.zoom,
				center: {
					lat: self.attributes.latitude,
					lng: self.attributes.longitude
				},
				mapTypeId: google.maps.MapTypeId.ROADMAP,
			});
			
			/*self.timer = setInterval(function() {
				var $mapPart = $('.gm-style > div');
				if($mapPart.length > 1) {
					for(var i = 1; i < $mapPart.length; i++) {
						$mapPart.eq(i).hide();
					}
				}
			}, 1000);*/
			
		}
		
		
		this.getMapSizeInMetters = function() {
			var bounds = self.map.getBounds();
			var northEastLatLng = bounds.getNorthEast();
			var southWestLatLng = bounds.getSouthWest();
			
			var xMetters = self.getDistanceBetweenPoints(southWestLatLng.lat(), southWestLatLng.lng(), northEastLatLng.lat(), northEastLatLng.lng());
			
			var populationOptions = {
				strokeColor: '#FF0000',
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: '#FF0000',
				fillOpacity: 0.35,
				clickable: false,
				map: self.map,
				center: {
					lat: self.attributes.latitude,
					lng: self.attributes.longitude
				},
				radius: 3000000
			};
			// Add the circle for this city to the map.
			cityCircle = new google.maps.Circle(populationOptions);
	
			//console.log(xMetters);
		}
		
		
		
		this.centerTo = function(latitude, longitude) {
			self.attributes.latitude = latitude;
			self.attributes.longitude = longitude;
		
			self.map.panTo({
				lat: self.attributes.latitude,
				lng: self.attributes.longitude
			});
		}
		
		this.setZoom = function(zoom) {
			if(zoom > 21) {
				self.attributes.zoom = 21;
			} else {
				self.attributes.zoom = Math.floor(zoom);
			}
			
			self.map.setZoom(self.attributes.zoom);
		}
		
		this.goTo = function(latitude, longitude, radius) {
			var circle = new google.maps.Circle({
				center: {
					lat: latitude,
					lng: longitude
				},
				radius: radius
			});
			
			self.map.fitBounds(circle.getBounds());
			/*self.map.setZoom(self.map.getZoom() + 1);
			return self.map.getZoom();*/
		}
		
		
		/* road | satellite | hybrid | terrain */
		this.setType = function(type) {
			switch(type) {
				case 'road':
					self.attributes.type = type;
					type = google.maps.MapTypeId.ROADMAP;
				break;
				case 'satellite':
					self.attributes.type = type;
					type = google.maps.MapTypeId.SATELLITE;
				break;
				case 'hybrid':
					self.attributes.type = type;
					type = google.maps.MapTypeId.HYBRID;
				break;
				case 'terrain':
					self.attributes.type = type;
					type = google.maps.MapTypeId.TERRAIN;
				break;
				default:
					return;
			}
			
			self.map.setMapTypeId(type);
		}
		
		
		this.displayUI = function() {
			self.attributes.displayUI = true;
			self.map.setOptions({
				disableDefaultUI: false,
			});
		}
		
		this.hideUI = function() {
			self.attributes.displayUI = false;
			self.map.setOptions({
				disableDefaultUI: true,
			});
		}
		
		this.enableControls = function() {
			self.map.setOptions({
				scrollwheel: true,
				disableDoubleClickZoom: false,
				draggable: true
			});
		}
		
		this.disableControls = function() {
			self.map.setOptions({
				scrollwheel: false,
				disableDoubleClickZoom: true,
				draggable: false
			});
		}
		
		
		this.draw = function(elementOptions) {
			var element = null;
			
			switch(elementOptions.shape) {
				case 'marker':
					element = new google.maps.Marker({
						position: {
							lat: elementOptions.latitude,
							lng: elementOptions.longitude
						},
						cursor: elementOptions.cursor || 'default',
						icon: elementOptions.icon || null
					});
				break;
				
				case 'circle':
					element = new google.maps.Circle({
						center: {
							lat: elementOptions.latitude,
							lng: elementOptions.longitude
						},
						radius: elementOptions.radius,
						
						fillColor: elementOptions.fillColor || '#FF0000',
						fillOpacity: elementOptions.fillOpacity || 0.25,
						strokeColor: elementOptions.strokeColor || '#FF0000',
						strokeOpacity: elementOptions.strokeOpacity || 0.7,
						strokeWeight: elementOptions.strokeWeight || 1,
						
						clickable: elementOptions.clickable || false
					});
				break;
				
				case 'lines':
					//for(var i = 0; i < elementOptions.path
					element = new google.maps.Polyline({
						path: elementOptions.path,
						strokeColor: elementOptions.strokeColor || '#FF0000',
						strokeOpacity: elementOptions.strokeOpacity || 0.7,
						strokeWeight: elementOptions.strokeWeight || 2,
						
						clickable: elementOptions.clickable || false
					});
				break;
			}
			
			element.setMap(self.map);
			self.elements.push(element);
			
			return element;
		}
		
		this.clear = function(element) {
			for(var i = 0; i < self.elements.length; i++) {
				if(!element || self.elements[i] == element) {
					console.log(element);
					self.elements[i].setMap(null);
					self.elements.splice(i, 1);
				}
			}
		}
		
		
		this.init(container);
	}
	
	
	/*var key = 'AIzaSyADhFLDkjlbP5MJfaalX-8e079gVtTtRbs'
	var url = 'https://maps.googleapis.com/maps/api/js?key=' + key + '&sensor=false';*/
	
	var url = 'http://maps.google.com/maps/api/js?sensor=true';
	
	PHPBridge.getPage(url, function(response) {
		var pattern = RegEx.quote('getScript("') + '(.+)' + RegEx.quote('");');
		
		var matches = RegEx.match(pattern, response);
		var url = matches[0];
		
		window._mapCallback = function() {
			delete window._mapCallback;
			
			fnc.load({
				url: url,
				type: 'js',
				onload: function() {
					var callback = function() {
						if(typeof google.maps.Map == 'undefined') {
							setTimeout(callback, 100);
						} else {
							
							fnc.libs['Map'] = Map;
						}
					}
					
					callback();
				}
			});
		}
		
		var code = RegEx.replace(pattern, 'window._mapCallback();', response);
		eval(code);
	});
});