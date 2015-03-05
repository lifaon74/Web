var Camera;

fnc.require(['Class', 'HTML5', 'Inputs'], function() {

	
	Camera = function(camera, params) {
		var self = this;
		Class(self);
		self.extend(Input);
		
		
		this.init = function(camera, params) {
			if(typeof camera == 'undefined') {
				self.usedCamera = null;
			} else {
				self.usedCamera = camera;
			}
			
			if(typeof params == 'undefined') {
				self.wantedWidth = 1920;
				self.wantedHeight = 1080;
			} else {
				self.wantedWidth = params.width;
				self.wantedHeight = params.height;
			}
			
			
			self.container = document.body;
			
			self.width = 0;
			self.height = 0;
			
			self.started = false;
			
			self.stream = null;
			
			self.timer = null;
			self.recording = false;
			self.frames = [];
			self.frameRate = 10;
			
			self.videoElement = document.createElement('video');
			self.videoElement.autoplay = "autoplay";
			
			self.canvasElement = document.createElement('canvas');
			self.ctx = self.canvasElement.getContext('2d');
		}
		
		
		this._startStream = function(callback) {
			if(!self.started) {
				if(navigator.getUserMedia) {
					var params = {
						video: {
							mandatory: {
								minWidth: self.wantedWidth,
								minHeight: self.wantedHeight
							}
						}
					};
					
					if(self.usedCamera) {
						params.video.optional = [{ sourceId: self.usedCamera.id }];
					}
					
					navigator.getUserMedia(
						params,
						function(stream) { // success
							self.stream = stream;
							self.started = true;
							callback(true);
						},
						function() { // error
							callback(false);
						});
				} else {
					callback(false);
				}
			}
		}
		
		this._stopStream = function() {
			if(self.started) {
				self.started = false;
				self.stream.stop();
			}
		}
		
		this._onstreamEnded = function() {
			self.trigger('_disconnect', [1]);
		}
		
		this._waitForImageData = function(callback) {
			if(self.started) {
				if(self.videoElement.readyState == 4) {
					self.width = self.videoElement.videoWidth;
					self.height = self.videoElement.videoHeight;
					
					self.canvasElement.width = self.width;
					self.canvasElement.height = self.height;
					
					callback();	
				} else {
					setTimeout(function() { self._waitForImageData(callback); }, 100);
				}
			}
		}
		
		
		this.connect = function() {
			if(!self.connected) {
				self._startStream(function(success) {
					if(success) {
						if(typeof self.stream.addEventListener == 'undefined') {
							self.stream.onended = self._onstreamEnded;
						} else {
							self.stream.addEventListener('ended', self._onstreamEnded, false);
						}
						
						self.videoElement.src = window.URL.createObjectURL(self.stream);
						self._waitForImageData(function() {
							self.trigger('_connect');
						});
					} else {
						self.trigger('_disconnect', [0]);
					}
				});
			}
		}
		
		this.disconnect = function() {
			if(self.connected) {
				if(typeof self.stream.removeEventListener == 'undefined') {
					self.stream.onended = null;
				} else {
					self.stream.removeEventListener('ended', self._onstreamEnded, false);
				}
						
				self._stopStream();
				self.trigger('_disconnect', [2]);
			}
		}
		
		
		this.getImage = function(type, quality) {
			var image = null;
			
			if(self.connected) {
				self.ctx.clearRect(0, 0, self.width, self.height);
				self.ctx.drawImage(self.videoElement, 0, 0, self.width, self.height);
			
				if(typeof type == 'undefined') {
					image = self.ctx.getImageData(0, 0, self.width, self.height);
				} else if(['webp', 'png', 'jpeg'].indexOf(type) >= 0) {
				
					if(typeof quality == 'undefined') {
						quality = 1;
					}
					
					image = self.canvasElement.toDataURL('image/' + type, quality);
				}
			}
			
			return image;
		}
		
		this.append = function(container) {
			if(self.started) {
				var videoElement = document.createElement('video');
				videoElement.autoplay = "autoplay";
				videoElement.src = window.URL.createObjectURL(self.stream);
				container.appendChild(videoElement);
				return videoElement;
			}
		}
		
		
		this.startRecording = function(frameRate, quality) {
			if(self.connected && !self.recording) {
				if(typeof frameRate != 'undefined') {
					self.frameRate = frameRate;
				}
				
				if(typeof quality == 'undefined') {
					quality = 1;
				}
				
				self.frames = [];
				self.recording = true;
				
				self.timer = setInterval(function() {
					var t1 = new Date().getTime();
					self.frames.push(self.getImage('webp', quality));
					var t2 = new Date().getTime();
					//console.log(t2 - t1);
				}, 1000 / self.frameRate);
			}
		}
		
		this.stopRecording = function() {
			var video = null;
			
			if(self.connected && self.recording) {
				clearInterval(self.timer);
				var webmBlob = Whammy.fromImageArray(self.frames, self.frameRate);
				video = window.URL.createObjectURL(webmBlob);
				
				self.recording = false;
			}
			
			return video;
		}
	
	
		this.init(camera, params);
	}
	
	fnc.load({
		url: fnc.getScriptRootPath('Camera.class.js') + 'whammy.js',
		type: 'js',
		onload: function() {
			fnc.libs['Inputs/Camera'] = Camera;
		}
	});
});