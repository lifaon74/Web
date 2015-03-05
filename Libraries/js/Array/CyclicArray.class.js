var _CyclicArray;
var CyclicArray;

fnc.require(['Class'], function() {
	_CyclicArray = function(length) {
		var self = this;
		
		this.init = function(length) {
			self.table = null;
			self.length = length;
			self.cursorPosition = 0;
		}
		
		this.put = function(value) {
			self.table[self.cursorPosition] = value;
			self.cursorPosition = (self.cursorPosition + 1) % self.length;
		}
		
		this.get = function(index) {
			if(typeof index == 'undefined') { var index = 0; }
			
			index = (self.cursorPosition + index) % self.length;
			if(index < 0) { index += self.length; }
			
			return self.table[self.cursorPosition];
		}
		
		this.init(length);
	}

	CyclicArray = function(length, initValue) {
		var self = this;
		Class(self);
		self.extend(_CyclicArray, [length]);
		
		this.init = function(initValue) {
			self.table = [];
			
			for(var i = 0; i < self.length; i++) {
				self.table[i] = initValue;
			}
		}
		
		this.init(initValue);
	}


	fnc.libs['CyclicArray'] = CyclicArray;
});