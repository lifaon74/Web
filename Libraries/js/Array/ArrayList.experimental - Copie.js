var ArrayListElement, ArrayList;

fnc.require([], function() {
	
	ArrayListElement = function(value) {
		var self = this;
		
		this.__construct = function(value) {
			self.value		= value;
			self.previous	= null;
			self.next		= null;
			self.arrayList	= null;
		}
		
		this.copy = function() {
			return new ArrayListElement(self.value);
		}
		
		
		this.insertAfter = function(element) {
			if(self.arrayList !== null) {
				element.detach();
				
				element.previous	= self;
				element.next		= self.next;
				
				if(self.next !== null) {
					self.next.previous = element;
				}
				
				self.next = element;

				if(self.arrayList !== null) {
					element.arrayList = self.arrayList;
					self.arrayList.size++;
					
					if(self.arrayList.last === self) {
						self.arrayList.last = element;
					}
				}
			}
			
			return self;
		}
		
		this.insertBefore = function(element) {
			if(self.arrayList !== null) {
				element.detach();
				
				element.previous	= self.previous;
				element.next		= self;
				
				if(self.previous !== null) {
					self.previous.next	= element;
				}
				
				self.previous 	= element;
				
				if(self.arrayList !== null) {
					element.arrayList = self.arrayList;
					self.arrayList.size++;
					
					if(self.arrayList.first === self) {
						self.arrayList.first = element;
					}
				}
			}
			
			return self;
		}
		
		this.replaceBy = function(element) {
			if(self.arrayList !== null) {
				self.insertAfter(element);
				self.detach();
			}
			
			return self;
		}
		
		this.detach = function() {
			if(self.arrayList !== null) {
				if(self.previous !== null) {
					self.previous.next = self.next;
				}
				
				if(self.next !== null) {
					self.next.previous = self.previous;
				}
				
				if(self.arrayList.first === self) {
					self.arrayList.first = self.next;
				}
				
				if(self.arrayList.last === self) {
					self.arrayList.last = self.previous;
				}
				
				self.arrayList.size--;
				self.arrayList = null;
				
				self.previous	= null;
				self.next		= null;
			}
			
			return self;
		}
		
		this.forEach = function(callback) {
			var nextElement = self;
			
			do {
				var returnedNextElement = callback(nextElement);
				
				if(returnedNextElement) {
					nextElement = returnedNextElement;
				} else if(returnedNextElement === false) {
					break;
				}else {
					nextElement = nextElement.next;
				}
				
			} while(nextElement !== null);
		}
		
		
		this.__construct(value);
	}


	ArrayList = function() {
		var self = this;
		
		this.__construct = function() {
			self.first	= null;
			self.last	= null;
			self.size	= 0;
		}
		
		this.push = function(element) {
			if(self.first === null) {
				element.arrayList	= self;
				self.first			= element;
				self.last			= element;
				self.size++;
			} else {
				self.last.insertAfter(element);
			}

			return self;
		}
		
		this.pop = function() {
			var element = self.last;
			element.detach();
			return element;
		}
		
		this.unshift = function(element) {
			if(self.first === null) {
				element.arrayList	= self;
				self.first			= element;
				self.last			= element;
				self.size++;
			} else {
				self.first.insertBefore(element);
			}

			return self;
		}
		
		this.shift = function() {
			var element = self.first;
			element.detach();
			return element;
		}

		this.get = function(index) {
			var directionTheNext = (index >= 0);
			
			if(directionTheNext) {
				var element = self.first;
			} else {
				index = Math.abs(index) - 1;
				var element = self.last;
			}
			
			for(var i = 0; i < index; i++) {
				if(element === null) {
					return null;
				} else {
					if(directionTheNext) {
						element = element.next;
					} else {
						element = element.previous;
					}
					
				}
			}
			
			return element;
		}
		
		
		this.forEach = function(callback, startByLast) {
			if(typeof startByLast != "boolean") { var startByLast = false; }
			
			if(startByLast) {
				var nextElement = self.last;
			} else {
				var nextElement = self.first;
			}
			
			if(nextElement !== null) {
				nextElement.forEach(callback);
			}
		}
		
		this.toArray = function() {
			var array = [];
			self.forEach(function(element) {
				array.push(element.value);
			});
			return array;
		}
		
		this.__construct();
	}

	fnc.libs['ArrayList'] = ArrayList;
	
	/*var arrayList = new ArrayList();

	for(var i = 0; i < 500000; i++) {
		arrayList.push(new ArrayListElement(i));
	}

	var _i = 0;
	t1 = new Date().getTime();
	arrayList.forEach(function(element) {
		_i += element.value;
	});
	t2 = new Date().getTime();
	console.log(t2 - t1);
	console.log(_i);*/
});