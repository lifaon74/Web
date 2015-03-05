var Screen;

fnc.require(['Class', 'HTML5'], function() {

	var ScreenController = function(controller) {
		var self = this;
		ClassWithBinds(self);
		
		this.init = function(controller) {
			self.bindElement = document.body;
			
			self.orientation = 0;
			
			
			HTML5.prefixeSpecialsClasses(self.bindElement, 'requestFullScreen');
			HTML5.addEventListener(self.bindElement, 'fullscreenchange', self._onFullScreenChange);
			HTML5.addEventListener(screen, 'orientationchange', self._onOrientationChange);
		}
		
		
		this.isFullScreen = function() {
			var isFullScreen = 	setHTML5Prefixes(document, 'isFullScreen', false) ||
								setHTML5Prefixes(document, 'fullScreen', false);
			
			if(!isFullScreen) { isFullScreen = false; }
			
			return isFullScreen;
		}
	
	
		this.enterFullScreen = function(dontWaitInput) {
			if(typeof dontWaitInput == 'undefined') { var dontWaitInput = false; }
			
			if(dontWaitInput) {
				self._enterFullScreen();
			} else {
				var events = ['mousedown', 'mouseup', 'keydown', 'keyup'];
			
				var callback = function() {
					for(var i = 0; i < events.length; i++) {
						self.bindElement.removeEventListener(events[i], callback, false);
					}
					
					self._enterFullScreen();
				}
				
				for(var i = 0; i < events.length; i++) {
					self.bindElement.addEventListener(events[i], callback, false);
				}
			}
		}
			
			this._enterFullScreen = function() {
				if(!self.bindElement.requestFullScreen) {
					alert('Your browser is too old');
					return;
				}
				
				if(!self.isFullScreen()) {
					self.bindElement.requestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
				}
			}
			
		this.exitFullScreen = function() {
			if(self.isFullScreen()) {
				document.cancelFullScreen();
			}
		}

		
			// TODO
		this.lockOrientation = function() {
			//alert(screen.lockOrientation(["landscape-primary", "landscape-secondary"]));
		}
		
		
		this._onFullScreenChange = function(event) {
			self.trigger('fullScreenChange');
			
			if(self.isFullScreen()) {
				self.trigger('enterFullScreen');
			} else {
				self.trigger('exitFullScreen');
			}
		}
	
		this._onOrientationChange = function() {
			var orientation = HTML5.prefixeSpecialsClasses(screen, 'orientation', false);
			
			switch(orientation) {
				case 'portrait-primary':
					self.orientation = 0;
				break;
				case 'portrait-secondary':
					self.orientation = 180;
				break;
				case 'landscape-primary':
					self.orientation = 90;
				break;
				case 'landscape-secondary':
					self.orientation = -90;
				break;
				default:
					self.orientation = 0;
			}
		}
		
		this.init();
	}
	
	Screen = new ScreenController();
	fnc.libs['Outputs/Screen'] = Screen;
	
	/*Screen.bind('fullScreenChange', function() {
		if(Screen.isFullScreen()) {
			Mouse.lock();
		}
	});
	
	Screen.enterFullScreen();*/
});