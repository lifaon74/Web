var HashTable = function() {
	var self = this;
	
	this.init = function() {
		self.table = [];
		self.entries = [];
		self.length = 0;
	}
	
	this.get = function(key) {
		return self.table[key];
	}
	
	this.put = function(key, value) {
		if(typeof self.table[key] == 'undefined') {
			self.length++;
			self.entries.push(key);
		}
		
		self.table[key] = value;
		
		return value;
	}
	
	this.unset = function(key) {
		if(typeof self.table[key] != 'undefined') {
			self.length--;
			
			for(var i = 0; i < self.entries.length; i++) {
				if(self.entries[i] == key) {
					self.entries.splice(i, 1);
					break;
				}
			}
			
			var value = self.table[key];
			
			delete self.table[key];
			
			return value;
		}
	}
	
	this.getIterator = function() {
		var iterator = {};
		iterator.index = -1;
		
		iterator.hasNext = function() {
			return (iterator.index + 1) < self.length;
		}
		
		iterator.next = function() {
			iterator.index++;
			return self.table[self.entries[iterator.index]];
		}
		
		iterator.entryKey = function() {
			return self.entries[iterator.index];
		}
		
		return iterator;
	}
	
	this.init();
}

fnc.libs['HashTable'] = HashTable;