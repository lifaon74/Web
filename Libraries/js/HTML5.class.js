var HTML5Constructor = function() {
	var self = this;
	
	this.init = function() {
		self.prefixes = ['webkit', 'moz', 'ms', 'o'];
		self.eventListenerList = [];
		
		self.prefixeSpecialsClasses(window, 'requestAnimationFrame');
		self.prefixeSpecialsClasses(window, 'cancelAnimationFrame');

		self.prefixeSpecialsClasses(window, 'requestFileSystem');
		self.prefixeSpecialsClasses(window, 'requestFileSystemSync');
		self.prefixeSpecialsClasses(navigator, 'persistentStorage');
		self.prefixeSpecialsClasses(navigator, 'temporaryStorage');
		self.prefixeSpecialsClasses(navigator, 'getUserMedia');
		self.prefixeSpecialsClasses(navigator, 'vibrate');
		self.prefixeSpecialsClasses(navigator, 'battery');
		self.prefixeSpecialsClasses(window, 'URL');
		
		self.prefixeSpecialsClasses(screen, 'lockOrientation');

		self.prefixeSpecialsClasses(navigator, 'pointer');
		self.prefixeSpecialsClasses(document, 'exitPointerLock');

		self.prefixeSpecialsClasses(document, 'cancelFullScreen');
		
		self.prefixeSpecialsClasses(window, 'WebSocket');
	}

	this.addEventListener = function(element, event, callback) {
		var compatibleEvent = self._searchCompatibleEvent(element, event);
		
		if(compatibleEvent) {
			element.addEventListener(compatibleEvent, callback, false);
			
			var eventListener = {
				element: element,
				event: event,
				compatibleEvent: compatibleEvent,
				callback: callback
				
			};
			
			self.eventListenerList.push(eventListener);
			
			return eventListener;
		} else {
			return null;
		}
	}
	
	this.removeEventListener = function(eventListener) {
		if(eventListener) {
			eventListener.element.removeEventListener(eventListener.compatibleEvent, eventListener.callback, false);
			return true;
		} else {
			return false;
		}
	}
	
		this._searchCompatibleEvent = function(element, event) {
			var compatibleEvent = null;
				
			if(typeof element['on' + event] == 'undefined') {
				for(var i = 0; i < self.prefixes.length; i++) {
					var eventPrefixedName = self.prefixes[i] + event;
					if(typeof element['on' + eventPrefixedName] != 'undefined') {
						compatibleEvent = eventPrefixedName;
						break;
					}
				}
			} else {
				compatibleEvent = event;
			}
			
			return compatibleEvent;
		}
	
	
	this.prefixeSpecialsClasses = function(object, attributeName, replaceObject) {
		if(typeof replaceObject == 'undefined') { replaceObject = true; }
		var element = null;
		
		if(typeof object[attributeName] != 'undefined') { return object[attributeName];	}
		
		for(var i = 0; i < self.prefixes.length; i++) {
			var attributePrefixedName = self.prefixes[i] + attributeName.charAt(0).toUpperCase() + attributeName.substring(1);
			if(typeof object[attributePrefixedName] != 'undefined') {
				element = object[attributePrefixedName];
				break;
			}
		}
		
		if(replaceObject) {	object[attributeName] = element; }
		
		return element;
	}

		// TODO
	this.applyCss3Style = function(element) {
		
	}
	
	
	this.init();
}

HTML5 = new HTML5Constructor();

fnc.libs['HTML5'] = HTML5;