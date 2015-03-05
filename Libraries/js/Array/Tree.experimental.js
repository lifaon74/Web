var Tree;


	
fnc.require(['ArrayList', 'RegEx', 'String'], function() {
	
	Tree = function(name, value) {
		var self = this;
		
		this.__construct = function(name, value) {
			self.name				= name;
			self.value				= value;
			self._parent			= null;
			self._children			= new ArrayList();
			self._arrayListElement	= new ArrayListElement(self);
		}
		
		
			
		this.getParent = function() {
			return self._parent;
		}
		
		this.getPath = function() {
			var element = self;
			var parents = [];
			
			while(element !== null) {
				parents.unshift(element);
				element = element.getParent();
			}
			
			return parents;
		}
		
		this.getPrevious = function() {
			if(self.getParent() !== null) {
				if(self._arrayListElement.previous === null) {
					return null;
				} else {
					return self._arrayListElement.previous.value;
				}
			} else {
				return null;
			}
		}
		
		this.getNext = function() {
			if(self.getParent() !== null) {
				if(self._arrayListElement.next === null) {
					return null;
				} else {
					return self._arrayListElement.next.value;
				}
			} else {
				return null;
			}
		}
		
		
		this.unshiftChild = function(tree) {
			self._children.unshift(tree.attach(self));
		}
		
		this.pushChild = function(tree) {
			self._children.push(tree.attach(self));
		}
		
		
		this.insertAfter = function(tree) {
			if(self.getParent() !== null) {		
				self._arrayListElement.insertAfter(tree.attach(self._parent));
			}
		}
		
		this.insertBefore = function(tree) {
			if(self.getParent() !== null) {
				self._arrayListElement.insertBefore(tree.attach(self._parent));
			}
		}
		
		this.replaceBy = function(tree) {
			if(self.getParent() !== null) {
				self._arrayListElement.replaceBy(tree.attach(self._parent));
			}
		}
		
		
		this.attach = function(parent) {
			self.detach();
			self._parent = parent;
			return self._arrayListElement;
		}
		
		this.detach = function() {
			if(self.getParent() !== null) {
				self._arrayListElement.detach();
				self._parent = null;
			}
		}

		
		this.forEach = function(callback) {
			self.nextElement = null;
			
			self._children.forEach(function(child) {
				self.nextElement = child.value.forEach(callback);
				self.nextElement = self._formatCallbackResponseForArrayList(self.nextElement, child.value);
				return self.nextElement;
			});
			
			if(self.nextElement === false) {
				return false;
			} else {
				return callback(self);
			}
		}
		
			this._formatCallbackResponseForArrayList = function(returnedNextElement, currentElement) {		
				if(returnedNextElement) {
					var nextElement = returnedNextElement;
				} else if(returnedNextElement === false){
					return false;
				} else {
					var nextElement = currentElement.getNext();
				}

				if(nextElement !== null) {	
					return nextElement._arrayListElement;
				} else {
					return null;
				}
			}
		
		
		this.toString  = function() {
			
			var content = "";
			content += "value => " + RegEx.replace(/(\r?\n)/g, '', self.value.toString());
			if(self._children.size > 0) {
				content += "\n";
			}

			self._children.forEach(function(child) {
				content += "\n" + child.value.toString();
			});
			
			var string = "";
			string += "[" + self.name + "] {\n";
				string += content.tab();
			string += "\n}";
			
			return string;
		}
		
		this.__construct(name, value);
	}
	
	fnc.libs['Tree'] = Tree;
});
