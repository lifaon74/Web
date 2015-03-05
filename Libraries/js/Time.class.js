var Time = function(millisecondsTimestamp) {
	var self = this;
	
	this.init = function(millisecondsTimestamp) {
		self.time = new Date(millisecondsTimestamp);
	}
	
	this.formatNumber = function(number, numberOfDigit) {
		var string = number + "";
		
		if(string.length == numberOfDigit) {
		} else if(string.length > numberOfDigit) {
			string = string.slice(string.length - numberOfDigit, string.length);
		} else {
			var str = "";
			for(var i = string.length; i < numberOfDigit; i++) {
				str += "0";
			}
			string = str + string;
		}
		
		return string;
	}
	
	this.getMonthDay = function() {
		return self.formatNumber(self.time.getDate(), 2);
	}
	
	this.getMonth = function() {
		return self.formatNumber(self.time.getMonth() + 1, 2);
	}
	
	this.getYear = function() {
		return self.formatNumber(self.time.getFullYear(), 4);
	}
	
	this.getHours = function() {
		return self.formatNumber(self.time.getHours(), 2);
	}
	
	this.getMinutes = function() {
		return self.formatNumber(self.time.getMinutes(), 2);
	}
	
	this.getSeconds = function() {
		return self.formatNumber(self.time.getSeconds(), 2);
	}
	
	this.init(millisecondsTimestamp);
}

fnc.libs['Time'] = Time;