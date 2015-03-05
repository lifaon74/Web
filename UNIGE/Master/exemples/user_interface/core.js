fnc.require(['jQuery', 'Class', 'Views', 'AJAXRequest', 'Time', 'CanvasImage'], function() {
	
	body = new DOMElement(document.body);

	mainView = Views.new('Main');
	body.append(mainView);

	/*canvas = new Canvas(32, 32);
	canvas.append(document.body);
	
	var img = new Image();
	img.src = 'ressources/icons/32x32/share_2.png';
	img.onload = function() {
		canvasImage = new CanvasImage(img);
		
		//canvasImage.resize(30, 30);
		
		canvasImage.foreachPixel(function(r, g, b, a) {
			r = g = b = 255;
			if(a > 180) { a = 255; }
			return [r, g, b, a];
		});
		
		
		
		//background: black;
		
		canvas.putCanvasImage(canvasImage, 1, 1);
		window.open(canvas.toDataUrl());
	}*/
});