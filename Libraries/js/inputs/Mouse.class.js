var Mouse;

fnc.require(['Class', 'HTML5', 'Outputs/Screen'], function() {

		//TODO : adapt with new HTML5 lib
	var MouseController = function() {
		var self = this;
		ClassWithBinds(self);
		
		this.init = function() {
			self.bindElement = document.body;
			
				// attributes
			self.x = 0;
			self.y = 0;
			self.oldX = 0;
			self.oldY = 0;
			self.wheel = 0;
			self.button = [];
			
				// control commands
			self._preventDefaultAction = false;
			self.pause = false;
		
		
				// buttonList
			self.buttonList = [];
			self.buttonList[0] = 'LEFT';
			self.buttonList[1] = 'CENTER';
			self.buttonList[2] = 'RIGHT';
			
			self.bindElement.addEventListener('mousedown', self._onmousedown, false);
			self.bindElement.addEventListener('mouseup', self._onmouseup, false);
			self.bindElement.addEventListener('mousemove', self._onmousemove, false);
			self.bindElement.addEventListener('mousewheel', self._onmousewheel, false);

			HTML5.addEventListener(document, ['pointerlockchange'], self._pointerLockChange);
		}
		
		
		this.preventDefaultAction = function(preventDefaultAction) {
			self._preventDefaultAction = preventDefaultAction;
		}
		
		
		this.isLocked = function() {
			if(navigator.pointer) {
				return navigator.pointer.isLocked;
			} else {
				var pointerLockElement = HTML5.prefixeSpecialsClasses(document, 'pointerLockElement', false);
											
				if(pointerLockElement) {
					return true;
				} else {
					return false;
				}
			}
		}
	
		this.lock = function() {
			if(Screen.isFullScreen()) {
				if(!self.isLocked()) {
					if(navigator.pointer) {	// chrome old version
						navigator.pointer.lock(self.bindElement, function() { // success
						}, function() { // error
						});
					} else {
						setHTML5Prefixes(self.bindElement, 'requestPointerLock');										
						self.bindElement.requestPointerLock();
					}
				}
			} else {
				console.warn('You need to be in fullscreen to enable mouse lock');
			}
		}
		
		this.unlock = function() {
			if(navigator.pointer) {
				if(self.isLocked()) {
					navigator.pointer.unlock();
				}
			} else {
				document.exitPointerLock();
			}
		}
	
		this._pointerLockChange = function() {
			self.trigger('pointerLockChange');
			
			if(self.isLocked()) {
				self.trigger('lock');
			} else {
				self.trigger('unlock');
			}
		}
	
	
		this._onmousedown = function(event) {
			if(self.pause) { return ; }
			
			self.button[event.button] = true;
			self.button[self.buttonList[event.button]] = true;
			
			self._onmousemove(event);
			
			self.trigger('down', [self]);
			
			if(self._preventDefaultAction) {
				event.preventDefault();
				return false;
			}
		}
			
		this._onmouseup = function(event) {	
			self.button[event.button] = false;
			self.button[self.buttonList[event.button]] = false;
			
			self._onmousemove(event);
			
			self.trigger('up', [self]);
			
			if(self._preventDefaultAction) {
				event.preventDefault();
				return false;
			}
		}
		
		this._onmousemove = function(event) {
			if(self.pause) { return ; }
			
			self.oldX = self.x;
			self.oldY = self.y;
			self.x = event.clientX + self.bindElement.scrollLeft;
			self.y = event.clientY + self.bindElement.scrollTop;
			
			self.trigger('move', [self]);
			
			if(self.isLocked()) {
				setHTML5Prefixes(event, 'movementX');
				var movementX = event.movementX;
				if(movementX) { self.movementX = movementX; }
				
				setHTML5Prefixes(event, 'movementY');
				var movementY = event.movementY;
				if(movementY) { self.movementY = movementY; }

			} else {
				self.movementX = self.x - self.oldX;
				self.movementY = self.y - self.oldY;
			}
		}
		
		this._onmousewheel = function(event) {
			if(self.pause) { return ; }
			
			var delta = 0;
						
			if(event.wheelDelta) {
				delta = event.wheelDelta / 120;
			} else if (event.detail) {
				delta = -event.detail / 3;
			}
			
			self.wheel = delta;
			
			self.trigger('wheel', [self]);
			
			if(self._preventDefaultAction) {
				event.preventDefault();
				return false;
			}
		}
		
		this.init();
	}
	
	
	Mouse = new MouseController();
	fnc.libs['Inputs/Mouse'] = Mouse;
	
	/*Mouse.bind('down', function() {
		console.log(Mouse.button);
	});*/
	
	/*Mouse.bind('wheel', function() {
		console.log(Mouse.wheel);
	});
	
	Mouse.bind('move', function() {
		console.log(Mouse.x);
	});*/
});