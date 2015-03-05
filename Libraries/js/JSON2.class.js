var JSON2Constructor = function() {
	var self = this;
	
	this.encode = function(object) {
		self.objectList = [];
		self.path = ['root'];
		
		var result = self._encodeRecursive(object, 0);
		self.objectList = [];
		return result;
	}
	
	this._encodeRecursive = function(object, depth) {
		var result = "";
		var typeOfObject = typeof object;
		
		switch(typeOfObject) {
			case 'object':
			case 'array':
			case 'function':
				var objectAlreadyFind = false;
				for(var i = 0; i < self.objectList.length; i++) {
					if(object === self.objectList[i].object) {
						objectAlreadyFind = true;
						break;
					}
				}
				
				if(objectAlreadyFind) {
					result += "&" + "\"" + self.objectList[i].path + "\"";
				} else {
					self.objectList.push({
						'object': object,
						'path': self._getStringPath(depth)
					});
					
					switch(typeOfObject) {
						case 'object':
							result += "{";
							
							var i = 0;
							for(key in object) {
								if(i > 0) { result += ","; }
								self.path[depth + 1] = key;
								result += "\"" + self.escapeString(key) + "\":" + self._encodeRecursive(object[key], depth + 1);
								i++;
							}
							
							result += "}";
						break;
						
						case 'array':
							result += "[";
							
							for(var i = 0; i < object.length; i++) {
								if(i > 0) { result += ","; }
								self.path[depth + 1] = i;
								result += self._encodeRecursive(object[i], depth + 1); //self.array.concat(path, [i])
							}
							
							result += "]";
						break;
						
						case 'function':
							result += object + "";
						break;
					}
				}
			break;
			
			case 'string':
				result += "\"" + self.escapeString(object) + "\"";
			break;
			case 'number':
				result += object;
			break;
			case 'boolean':
				result += object ? "true" : "false";
			break;
			case 'null':
				result += "null";
			break;
			
			default:
				result += object;
		}
		
		return result;
	}
	
	
	this.decode = function(string) {
		self.string = string;
		self.i = 0;
		self.pointers = [];
		
		var result = self._decodeCharacter(self.string[self.i++]);
		if(result.type) {
		
				// decode pointers
			var object = result.element;
			
			for(var i = 0; i < self.pointers.length; i++) {
				var pointer = self.pointers[i];
				var from = object;
				
				for(var j = 1; j < pointer.from.length; j++) {
					from = from[pointer.from[j]];
				}
				
				pointer.to.object[pointer.to.key] = from;
				
			}
			
			self.string = "";
			self.pointers = [];
			
			return object;
		} else {
			console.error('impossible de décrypter la chaine json');
			return null;
		}
	}
	
	this._decodeRecursive = function(type) {
		switch(type) {
			case 'object':
				var object = {};
				var key = "";
				var step = 0;
			break;
			
			case 'array':
				var array = [];
				var step = 0;
			break;
				
			case 'function':
				var fnc = "x=function";
				var step = 0;
				var depth = 0;
			break;
			
			case 'string':
				var string = "";
			break;
			
			case 'number':
				var number = "" + self.string[self.i - 1];
				var step = 0;
			break;
			
			default:
				var result = "";
		}
					
		while(self.i < self.string.length) {
		
			var character = self.string[self.i++];
			
			switch(type) {		
				case 'object':
					switch(step) {
						case 0:
							switch(character) {
								case "\"":
									key = self._decodeRecursive('string');
									step = 1;
								break;
							}
						break;
						
						case 1:
							switch(character) {
								case ":":
									step = 2;
								break;
							}
						break;
						
						case 2:
							var result = self._decodeCharacter(character);
							if(result.type) {
								if(result.type == 'pointer') {
									self.pointers.push({
										'from': result.element.split('.'),
										'to': { 'object': object, 'key': key }
									});
					
									object[key] = null;
								} else {
									object[key] = result.element;
								}
								step = 3;
							}
						break;
						
						case 3:
							switch(character) {
								case ",":
									step = 0;
								break;
								case "}":
									return object;
								break;
							}
						break;
					}
				break;
				
				case 'array':
					switch(step) {
						case 0:
							var result = self._decodeCharacter(character);
							if(result.type) {
								array.push(result.element);
								step = 1;
							}
						break;
			
						case 1:
							switch(character) {
								case ",":
									step = 0;
								break;
								case "]":
									return array;
								break;
							}
						break;
					}
				break;
				
				case 'pointer':
					switch(character) {
						case "\"":
							var path = self._decodeRecursive('string');
							return path;
						break;
					}
				break;
				
				case 'function':
					fnc += character;
					switch(step) {
						case 0:
							switch(character) {
								case "(":
									step = 1;
								break;
							}
						break;
			
						case 1:
							switch(character) {
								case ")":
									step = 2;
								break;
							}
						break;
						
						case 2:
							switch(character) {
								case "{":
									depth++;
								break;
								case "}":
									depth--;
									if(depth == 0) {
										try {
											eval(fnc + ';');
										} catch(e) {
											console.error('erreur json v2');
											return null;
										}
										
										return x;
									}
								break;
							}
						break;
					}
				break;
				
				case 'string':
					switch(character) {
						case "\"":
							if(self.string[self.i - 1] == "\\") {
								string += character;
							} else {
								return string;
							}
						break;
						default:
							string += character;
					}
				break;
				
				case 'number':		
					switch(character) {
						case "0":
						case "1":
						case "2":
						case "3":
						case "4":
						case "5":
						case "6":
						case "7":
						case "8":
						case "9":
							switch(step) {
								case 0:
								case 1:
									number += character;
								break;
							}
						break;
						
						case ".":
							switch(step) {
								case 0:
									number += character;
									step = 1;
								break;
								default:
									self.i--;
									return parseFloat(number);
							}
						break;
						default:
							self.i--;
							return parseFloat(number);
					}
				break;
				
			}
		}
		
		return result;
	}
	
	this._decodeCharacter = function(character) {
		var result = {
			'type': null,
			'element': null
		};
		
		switch(character) {
			case "{":
				result.type = 'object';
				result.element = self._decodeRecursive('object');
			break;
			
			case "[":
				result.type = 'array';
				result.element = self._decodeRecursive('array');
			break;
			
			case "&":
				result.type = 'pointer';
				result.element = self._decodeRecursive('pointer');
			break;
			
			case "\"":
				result.type = 'string';
				result.element = self._decodeRecursive('string');
			break;
			
			case "-":
			case "0":
			case "1":
			case "2":
			case "3":
			case "4":
			case "5":
			case "6":
			case "7":
			case "8":
			case "9":
				result.type = 'number';
				result.element = self._decodeRecursive('number');
			break;
			
			case "n":
				if(	self.string[self.i + 0] == 'u' &&
					self.string[self.i + 1] == 'l' &&
					self.string[self.i + 2] == 'l') {
					self.i += 3;
					result.type = 'null';
					result.element = null;
				}				
			break;
			
			case "t":
				if(	self.string[self.i + 0] == 'r' &&
					self.string[self.i + 1] == 'u' &&
					self.string[self.i + 2] == 'e') {
					self.i += 3;
					result.type = 'boolean';
					result.element = true;
				}				
			break;
			
			case "f":
				if(	self.string[self.i + 0] == 'a' &&
					self.string[self.i + 1] == 'l' &&
					self.string[self.i + 2] == 's' &&
					self.string[self.i + 3] == 'e') {
					self.i += 4;
					result.type = 'boolean';
					result.element = false;
				} else if(	self.string[self.i + 0] == 'u' &&
							self.string[self.i + 1] == 'n' &&
							self.string[self.i + 2] == 'c' &&
							self.string[self.i + 3] == 't' &&
							self.string[self.i + 4] == 'i' &&
							self.string[self.i + 5] == 'o' &&
							self.string[self.i + 6] == 'n') {
					self.i += 7;
					result.type = 'function';
					result.element = self._decodeRecursive('function');
				}
			break;
		}
		
		return result;
	}

	
	this.escapeString = function(string) {
		var charactersToEscape = ["\v", "\t", "\n", "\r", "\f", "\"", "\/"];
		var escapedCharacters = ["\\v", "\\t", "\\n", "\\r", "\\f", "\\\"", "\\/"];
		
		var result = "";
		
		for(var i = 0; i < string.length; i++) {
			var _char = string[i];
			for(var j = 0; j < charactersToEscape.length; j++) {
				if(_char == charactersToEscape[j]) {
					console.log('ok');
					_char = escapedCharacters[j];
					break;
				}
			}
			
			result += _char;
		}
		
		return result;
	}
		
	this._getStringPath = function(depth) {
		var path = "";
		
		for(var i = 0; i <= depth; i++) {
			if(i > 0) { path += "."; }
			path += self.path[i];
			
		}
		
		return path;
	}

}

JSON2 = new JSON2Constructor();