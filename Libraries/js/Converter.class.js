var Converter;

fnc.require(['Math'], function() {
	
	ConverterConstructor = function() {
		var self = this;
		
		this.init = function() {
		}
		
		this.convertSizeToString = function(size) {
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
		
		this.convertDistanceToString = function(distance) {
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

		this.convertStringToArray = function(string) {
			var array = new Uint8Array(string.length);
			
			for(var i = 0; i < string.length; i++) {
				array[i] = string.charCodeAt(i);
			}
			
			return array;
		}
		
		this.init();
	}
	
	Converter = new ConverterConstructor();
	fnc.libs['Converter'] = Converter;
});