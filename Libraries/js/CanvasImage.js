var Canvas = function(width, height) {
	var self = this;
	
	this.init = function(width, height) {
		self.canvas = document.createElement('canvas');
		self.ctx = self.canvas.getContext('2d');
		self.setSize(width, height);		
	}
	
	this.setSize = function(width, height) {
		self.width = width;
		self.height = height;
		
		self.canvas.width = self.width;
		self.canvas.height = self.height;
		
		return self;
	}
	
	this.clear = function() {
		self.ctx.clearRect(0, 0, self.width, self.height);
		return self;
	}
	
	this.putCanvasImage = function(canvasImage, x, y) {
		if(typeof x != 'number') { var x = 0; }
		if(typeof y != 'number') { var y = 0; }
		
		self.ctx.putImageData(canvasImage.imageData, x, y);
	}
	
	this.toDataUrl = function() {
		return self.canvas.toDataURL();
	}
	
	this.append = function(element) {
		self.canvas.style.border = '5px solid grey';
		self.canvas.style.background = 'black';
		element.appendChild(self.canvas);
	}
		
	this.init(width, height);
}


var CanvasImage = function(arg1, arg2) {
	var self = this;
	
	/**
		params :
			- Image
			- Imagedata
			- CanvasImage
			- Width, Height
	**/
	this.init = function(arg1, arg2) {
		self.width = 0;
		self.height = 0;
		self.canvas = new Canvas(1, 1);
		
		if(typeof arg1 == 'number' && typeof arg2 == 'number') { // Width, Height
			self.width = arg1;
			self.height = arg2;
			self.canvas.setSize(self.width, self.height);
			self.imageData = self.canvas.ctx.getImageData(0, 0, self.width, self.height);
		} else if(typeof arg1 == 'object') {
			if(typeof arg1.src != 'undefined') { // Image
				if(arg1.complete) {
					self.width = arg1.width;
					self.height = arg1.height;
					self.canvas.setSize(self.width, self.height);
					self.canvas.ctx.drawImage(arg1, 0, 0);
					self.imageData = self.canvas.ctx.getImageData(0, 0, self.width, self.height);
				} else {
					console.error('Image must be loaded');
				}
			} else if(typeof arg1.data == 'object') { // imageData
				self.width = arg1.width;
				self.height = arg1.height;
				self.imageData = arg1;
			} else if(typeof arg1.imageData == 'object') { // CanvasImage
				self.width = arg1.width;
				self.height = arg1.height;
				self.canvas.setSize(self.width, self.height);
				self.canvas.ctx.putImageData(arg1.imageData, 0, 0);
				self.imageData = self.canvas.ctx.getImageData(0, 0, self.width, self.height);
			} else {
				console.error('First argument is not a image');
			}
		}
	}
	
	
	this.copy = function() {
		self._clearCanvas();
		self.canvas.ctx.putImageData(self.imageData, 0, 0);
		var newCanvasImage = new CanvasImage(self.canvas.ctx.getImageData(0, 0, self.width, self.height));
		return newCanvasImage;
	}
	
	this.cut = function(x, y, width, height) {
		var width = Math.min(self.width - x, width + x, self.width, width);
		var height = Math.min(self.height - y, height + y, self.height, height);
		
		var x = Math.max(0, x);
		var y = Math.max(0, y);
		
		self.width = width;
		self.height = height;
			
		self._clearCanvas();
		self.canvas.ctx.putImageData(self.imageData, -x, -y);
		self.imageData = self.canvas.ctx.getImageData(0, 0, self.width, self.height);
		
		return self;
	}
	
	this.resize = function(width, height) {
		var tempCanvas = new Canvas(self.width, self.height);
		tempCanvas.ctx.putImageData(self.imageData, 0, 0);
		
		self.canvas.setSize(width, height);
		self.canvas.clear();
		
		self.canvas.ctx.save();
		self.canvas.ctx.scale(width / self.width, height / self.height);
		self.canvas.ctx.drawImage(tempCanvas.canvas, 0, 0);
		self.canvas.ctx.restore();
		
		self.width = width;
		self.height = height;
		self.imageData = self.canvas.ctx.getImageData(0, 0, width, height);
		
		return self;
	}
	
	
	this.merge = function(canvasImage, x, y) {
		if(typeof x != 'number') { var x = 0; }
		if(typeof y != 'number') { var y = 0; }
		
			// 60px/ms pour le matrix et 350px/ms pour le draw
		var width = Math.min(self.width, x + canvasImage.width) - Math.max(0, x);
		var height = Math.min(self.height, y + canvasImage.height) - Math.max(0, y);
		var pixelsByMilliSecondsWidthMatrixMerge = width * height / 60;
		
		var width = Math.min(self.width - x, canvasImage.width + x, self.width, canvasImage.width) - Math.max(0, x);
		var height = Math.min(self.height - y, canvasImage.height + y, self.height, canvasImage.height) - Math.max(0, y);
		var pixelsByMilliSecondsWidthDrawMerge = Math.max(width, self.width) * Math.max(height, self.height) / 350;
		
		if(pixelsByMilliSecondsWidthMatrixMerge < pixelsByMilliSecondsWidthDrawMerge) {
			return self.matrix_merge(canvasImage, x, y);
		} else {
			return self.draw_merge(canvasImage, x, y);
		}
	}
	
		this.draw_merge = function(canvasImage, x, y) {
			if(typeof x != 'number') { var x = 0; }
			if(typeof y != 'number') { var y = 0; }

			var width = Math.min(self.width - x, canvasImage.width + x, self.width, canvasImage.width);
			var height = Math.min(self.height - y, canvasImage.height + y, self.height, canvasImage.height);
			
			canvasImage.canvas.setSize(width, height);
			canvasImage.canvas.clear();

			canvasImage.canvas.ctx.putImageData(canvasImage.imageData,  Math.min(0, x),  Math.min(0, y));
			
			self._clearCanvas();
			self.canvas.ctx.putImageData(self.imageData, 0, 0);
			self.canvas.ctx.drawImage(canvasImage.canvas.canvas,  Math.max(0, x),  Math.max(0, y));
			
			self.imageData = self.canvas.ctx.getImageData(0, 0, self.width, self.height);
			
			return self;
		}
	
		this.matrix_merge = function(canvasImage, x, y) {
			var newCanvasImage = self;

			if(typeof x != 'number') { var x = 0; }
			if(typeof y != 'number') { var y = 0; }
			
			var xStart = Math.max(0, x);
			var yStart = Math.max(0, y);
			
			var width = Math.min(self.width, x + canvasImage.width);
			var height = Math.min(self.height, y + canvasImage.height);
			
			for(var x1 = xStart; x1 < width; x1++) {
				for(var y1 = yStart; y1 < height; y1++) {
				
					var x2 = x1 - x;
					var y2 = y1 - y;
					
					var i = (x2 + (y2 * canvasImage.width)) * 4;
					var j = (x1 + (y1 * self.width)) * 4;
					
					var red1 = canvasImage.imageData.data[i];
					var green1 = canvasImage.imageData.data[i + 1];
					var blue1 = canvasImage.imageData.data[i + 2];
					var alpha1 = canvasImage.imageData.data[i + 3] / 255;
					
					var red2 = self.imageData.data[j];
					var green2 = self.imageData.data[j + 1];
					var blue2 = self.imageData.data[j + 2];
					var alpha2 = self.imageData.data[j + 3] / 255;
					
					var alpha3 = alpha1 + alpha2 * (1 - alpha1);
					
					newCanvasImage.imageData.data[j + 0] = (red1 * alpha1 + red2 * alpha2 * (1 - alpha1)) / alpha3;
					newCanvasImage.imageData.data[j + 1] = (green1 * alpha1 + green2 * alpha2 * (1 - alpha1)) / alpha3;
					newCanvasImage.imageData.data[j + 2] = (blue1 * alpha1 + blue2 * alpha2 * (1 - alpha1)) / alpha3;
					newCanvasImage.imageData.data[j + 3] = alpha3 * 255;
				}
			}
			
			return newCanvasImage;
		}
	
	
	this._clearCanvas = function() {
		self.canvas.setSize(self.width, self.height);
		self.canvas.clear();
	}
	
	this.foreachPixel = function(callback) {
		for(var x = 0; x < self.width; x++) {
			for(var y = 0; y < self.height; y++) {
				
					var i = (x + (y * canvasImage.width)) * 4;
					var j = (x + (y * self.width)) * 4;
					
					var colors = callback(
						canvasImage.imageData.data[i + 0],
						canvasImage.imageData.data[i + 1],
						canvasImage.imageData.data[i + 2],
						canvasImage.imageData.data[i + 3]
					);
					
					canvasImage.imageData.data[i + 0] = colors[0];
					canvasImage.imageData.data[i + 1] = colors[1];
					canvasImage.imageData.data[i + 2] = colors[2];
					canvasImage.imageData.data[i + 3] = colors[3];
			}
		}
	}
	
	this.init(arg1, arg2);
	
}

fnc.libs['CanvasImage'] = CanvasImage;

/*window.onload = function() {
	var image = new Image();
	image.onload = function() {
		var canvas = new Canvas(32, 32);
		canvas.append(document.body);
		
		var img = new CanvasImage(this);
		var img2 = img.copy();
		//img.cut(16, 16, 16, 16);
		//img.resize(16, 16);
		//img.merge(img2.resize(16, 16));
		canvas.putCanvasImage(img);
		
	}
	
	image.src = 'directory_01.png';
}*/