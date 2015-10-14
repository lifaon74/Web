var canvas;

function drawLine(p0_x, p0_y, p1_x, p1_y, color) {
	canvas.ctx.strokeStyle = color;
	canvas.ctx.beginPath();
	canvas.ctx.moveTo(p0_x, p0_y);
	canvas.ctx.lineTo(p1_x, p1_y);
	canvas.ctx.stroke();
}

function drawPoint(p0_x, p0_y, radius, color) {
	canvas.ctx.fillStyle = color;
	canvas.ctx.beginPath();
	canvas.ctx.arc(p0_x, p0_y, radius, 0, 2 * Math.PI);
	canvas.ctx.fill();
}

function drawSegment(p0_x, p0_y, p1_x, p1_y) {
	drawLine(p0_x, p0_y, p1_x, p1_y, "#FF0000");
	drawPoint(p0_x, p0_y, 2, "#0000FF");
	drawPoint(p1_x, p1_y, 2, "#0000FF");
}

fnc.require(['CanvasImage'], function() {
	
	

	canvas = new Canvas(640, 480);
	canvas.append(document.body);
	
	canvas.ctx.translate(canvas.width / 2 + 0.5, canvas.height / 2 + 0.5);
	
	canvas.ctx.scale(1, -1);
	
	
	
	fnc.load({
		type	: 'js',
		url		: 'layer.js',
		onload : function() {
			var scale = 5;
			
			for(var i = 0; i < arr.length; i += 4) {
				drawSegment(
					arr[i + 0] * scale, arr[i + 1] * scale,
					arr[i + 2] * scale, arr[i + 3] * scale
				);
			}
		}
	});
});