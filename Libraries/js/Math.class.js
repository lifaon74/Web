/**
*	Angle functions
*/
	// convert degrees to radians
Math.degToRad = function(degrees) {
	return degrees * Math.PI / 180;
}

	// convert radians to degrees
Math.radToDeg = function(radians) {
	return radians * 180 / Math.PI;
}

	// cos with degrees
Math.dcos = function(degrees) {
	return Math.cos(Math.degToRad(degrees));
}

	// sin with degrees
Math.dsin = function(degrees) {
	return Math.sin(Math.degToRad(degrees));
}

	// tan with degrees
Math.dtan = function(degrees) {
	return Math.tan(Math.degToRad(degrees));
}

	// acos with degrees
Math.dacos = function(factor) {
	return Math.radToDeg(Math.acos(factor));
}

	// asin with degrees
Math.dasin = function(factor) {
	return Math.radToDeg(Math.asin(factor));
}

	// atan with degrees
Math.datan = function(factor) {
	return Math.radToDeg(Math.atan(factor));
}

	// atan2 with degrees
Math.datan2 = function(x, y) {
	return Math.radToDeg(Math.atan2(x, y));
}


/**
*	Random functions
*/
	// generate an uniform random number between start and end
Math.rand = function(start, end) {
	return Math.floor(Math.random() * (end - start + 1) + start);
}

	// generate a Gaussian random number (with mean and variance)
Math.gRand = function(mean, variance) {
	return Math.nRand() * variance + mean;
}
	
	// generate a Normal random number
Math.nRand = function() {
	var x1, x2, rad, y1;
	do {
		x1 = 2 * Math.random() - 1;
		x2 = 2 * Math.random() - 1;
		rad = x1 * x1 + x2 * x2;
	} while(rad >= 1 || rad == 0);
	var c = Math.sqrt(-2 * Math.log(rad) / rad);
	return x1 * c;
}


/**
*	Mathematical functions
*	for example : used in animation
*/
	// linear function
Math.linearFunction = function(startPoint, endPoint, progression) {
	return (endPoint - startPoint) * progression + startPoint;
}

	// easeInQuad function
Math.easeInQuadFunction = function(startPoint, endPoint, progression) {
	return (endPoint - startPoint) * progression * progression + startPoint;
}

	// easeOutQuad function
Math.easeOutQuadFunction = function(startPoint, endPoint, progression) {
	return -(endPoint - startPoint) * progression * (progression - 2) + startPoint;
}

	//easeInOutQuad function
Math.easeInOutQuadFunction = function(startPoint, endPoint, progression) {
	progression = progression * 2;
	
	if(progression < 1) {
		return (endPoint - startPoint) / 2 * progression * progression + startPoint;
	} else {
		progression--;
		return -(endPoint - startPoint) / 2 * (progression * (progression - 2) - 1) + startPoint;
	}
}


/**
*	Others functions
*/

Math.convertSize = function(size) {
	if(size < 1) { return '0 octets'; }
	var string = '';
	var table = ['octets', 'Ko', 'Mo', 'Go', 'To', 'Po', 'Eo', 'Zo', 'Yo'];
	
	var index = Math.floor(Math.log(size) / Math.log(1024));
	if(index < 0) { index = 0; }
	
	var size = size / Math.pow(1024, index);
	size += '';
	
	for(var i = 0; i < Math.min(4, size.length); i++) {
		if(size[i] == '.') { size[i] = ','; }
		string += size[i];
	}
	
	string = string.replace(/\.0*$/g, '');
	string += ' ' + table[index];
	
	return string;
}

Math.convertDistance = function(distance) {
	if(distance < 1) { return '0 m'; }
	var string = '';
	var table = ['mm', 'm', 'km'];
	
	distance = distance * 1000;
	var index = Math.floor(Math.log(distance) / Math.log(1000));

	var distance = distance / Math.pow(1000, index);
	distance += '';
	
	for(var i = 0; i < Math.min(4, distance.length); i++) {
		if(distance[i] == '.') { distance[i] = ','; }
		string += distance[i];
	}
	
	string = string.replace(/\.0*$/g, '');
	string += '' + table[index];
	
	return string;
}

Math.uniqid = function(degree) {
	var chars = '0123456789abcdef';
		
	if(typeof degree != 'number' || degree <= 0) { var degree = 4; }
	
	if(typeof self.currentid == 'undefined') {
		self.currentid = 0;
	} else {
		self.currentid++;
		self.currentid = self.currentid % (chars.length * degree);
	}
	
	var uniqid = [];
	uniqid[0] = [];
	uniqid[1] = [];
	uniqid[2] = [];
	
		//random
	for(var i = 0; i < degree; i++) {
		uniqid[0].push(Math.rand(0, chars.length - 1));
	}
	
		// time
	var time = new Date().getTime();
	for(var i = 0; i < degree; i++) {
		if(i == 0) {
			var t = time;
		} else {
			var t = Math.floor(time / (i * chars.length));
		}
		t = t % chars.length;
		uniqid[1].push(t);
	}
	
		// id
	for(var i = 0; i < degree; i++) {
		if(i == 0) {
			var currentid = self.currentid;
		} else {
			var currentid = Math.floor(self.currentid / (i * chars.length));
		}
		currentid = currentid % chars.length;
		uniqid[2].push(currentid);
	}

	var uniqidString = '';
	for(var i = 0; i < degree; i++) {
		for(var j = 0; j < 3; j++) {
			uniqidString += chars[uniqid[j][i]];
		}
	}
	
	return uniqidString;
}

	// return the sign of a number
Math.sign = function(value) {
	if(value >= 0) {
		return 1;
	} else {
		return -1;
	}
}

	// round a decimal number with "decimals" decimals
Math.roundFloat = function(value, decimals) {
	var power = Math.pow(10, decimals);
	return Math.floor(value *power) /power;
}

	// get decimals of a number
Math.deci = function(value) {
	return value % 1;
}

	// set interval of a number
Math.interval = function(value, min, max) {
	if(value < min) { value = min; }
	if(value > max) { value = max; }
	return value;
}

	// compute Euclidean distance between two points
Math.getDistance = function(point1, point2) {
	if(point1.length != point2.length) {
		console.error('Points must have the same dimensions.')
		return null;
	}
	
	var sum = 0;
	
	for(var i = 0; i < point1.length; i++) {
		sum += Math.pow(point2[i] - point1[i], 2);
	}
	
	return Math.sqrt(sum);
}


Math.arrayMin = function(array) {
	var min = array[0];
	for(var i = 1; i < array.length; i++) {
		if(array[i] < min) {
			min = array[i];
		}
	}
	return min;
}

Math.arrayMax = function(array) {
	var max = array[0];
	for(var i = 1; i < array.length; i++) {
		if(array[i] > max) {
			max = array[i];
		}
	}
	return max;
}

Math.arraySum = function(array) {
	var sum = 0;
	for(var i = 0; i < array.length; i++) {
		sum += array[i];
	}
	return sum;
}

Math.arrayMean = function(array) {
	return Math.arraySum(array) / array.length;
}

Math.arrayWeightedMean = function(array) {
	var sum = 0;
	var wsum = 0;
	
	for(var i = 0; i < array.length; i++) {
		var w = i + 1;
		sum += array[i] * w;
		wsum += w;
	}
	
	return sum / wsum;
}

Math.arrayNormalize = function(array) {
	var results = [];
	
	var min = Math.arrayMin(array);
	var max = Math.arrayMax(array);
	var scope = max - min;
	
	for(var i = 0; i < array.length; i++) {
		if(scope == 0) {
			results[i] = 0.5;
		} else {
			results[i] = (array[i] - min) / scope;
		}
	}
	
	return results;
}


fnc.libs['Math'] = Math;