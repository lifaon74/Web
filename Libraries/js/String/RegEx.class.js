var RegEx, SuperRegEx;

fnc.require(['ArrayList'], function() {
	
	SuperRegEx = function(pattern, flags) {
		var self = this;
		
		this.__construct = function(pattern, flags) {
			var reg = /(^|[^\\])\$([\d]+)/g;
			
			var matches = RegEx.matchAll(reg, pattern);
			var newPattern = RegEx.replace(reg, "$1", pattern);
			
			var indexes = [];
			
			for(var i = 0, size = matches.length; i < size; i++) {
				var match = matches[i];
				
				var index = match.index - (i * 2) + match.variables[0].length;
				var subPattern = newPattern.substr(0, index);
				var numberOfPreviousParen = RegEx.matchAll(/\(/g, subPattern).length;
				
				indexes[numberOfPreviousParen] = parseInt(match.variables[1]);
			}
			
			
			var regex = new RegExp(newPattern, flags);
			var matches = RegEx.matchAll(regex, "0abc");
			
			for(var i = 0, size_i = matches.length; i < size_i; i++) {
				var match = matches[i];
				for(elt in indexes) {
					match["$" + indexes[elt]] = match.variables[elt]
				}
			}
			
			console.log(matches);
		}
		
		this.__construct(pattern, flags);
	}
	
	
	var RegExConstructor = function(name, value) {
		var self = this;
		
		this.__construct = function() {
			self.whiteChar	= '[ \\n\\r\\t]';
			self._whiteChar	= '[^ \\n\\r\\t]';

			self.word		= '[a-zA-Z]';
			self._word		= '[^a-zA-Z]';
			
			self.anything	= '(.|\\n|\\r)'; // .|\\n|\\r    -    \\w|\\W
		}
		
		this.new = function(pattern, flags) {
			return new RegExp(pattern, flags);
		}	
		
			// escape sensitive characters
		this.quote = function(string) {
			var escapeChar = ['\\', '.', '$', '[', ']', '(', ')', '{', '}', '^', '?', '!', '=', '*', '+', '-'];
			var exp = '';
			
			for(var i = 0; i < escapeChar.length; i++) {
				if(i > 0) { exp += '|'; }
				exp += '\\' + escapeChar[i];
			}
			
			var reg = new RegExp('(' + exp + ')', 'gi');
			
			return string.replace(reg, '\\$1');
		}

		this.match = function(regex, string) {
			var _result = regex.exec(string);
			
			if(_result) {
				var result = {
					"matchString"	: _result[0],
					"index"			: _result.index,
					"variables"		: []
				};
				
				for(var i = 1; i < _result.length; i++) {
					result.variables.push(_result[i]);
				}
			} else {
				var result = null;
			}
			
			return result;
		}
		
			// match all results which satisfy the pattern
		this.matchAll = function(regex, string) {
			var matches = [];
			while(result = self.match(regex, string)) {
				matches.push(result);
			}
			return matches;
		}

			// replace results which satisfy the pattern
		this.replace = function(regex, replacement, string) {
			if(replacement instanceof Function) {
				var copy = replacement;
				replacement = function() {
					var length = arguments.length;
					
					var result = {
						"matchString"	: arguments[0],
						"index"			: arguments[length - 2],
						"variables"		: []
					};
					
					for(var i = 1; i < length - 2; i++) {
						result.variables.push(arguments[i]);
					}
					
					return copy(result);
				};
			}
			return string.replace(regex, replacement);
		}
		
			// split the string depending of the pattern
		this.split = function(regex, string) {
			return string.split(regex);
		}
		
			// do a recursive match for tag containing others tags
		this.recursiveMatch = function(startPattern, endPattern, string) {
			var matches = [];
			var matchesForStartOfTag = RegEx.matchAll(RegEx.new(startPattern, 'g'), string);
			
			for(var i = 0, size = matchesForStartOfTag.length; i < size; i++) {
				var matchForStartOfTag = matchesForStartOfTag[i];
				var _endPattern = RegEx.replace(/\$([0-9]+)/g, function(matchesForEnd) {
					return matchForStartOfTag.variables[matchesForEnd.variables[0] - 1];
				}, endPattern);
				
				
				matches[i] = {
					"startOfTag"	: {
						"value"			: matchForStartOfTag.matchString,
						"offsetRange"	: [matchForStartOfTag.index, matchForStartOfTag.index + matchForStartOfTag.matchString.length],
						"variables"		: matchForStartOfTag.variables
					},
					
					"endOfTag"	: {
						"value"			: _endPattern,
						"offsetRange"	: null,
						"variables"		: []
					}
				};
			}
			
			for(var i = 0, size_i = matches.length; i < size_i; i++) {
				var match = matches[i];
				
				if(match.endOfTag.offsetRange === null) {
					var matchesForEndOfTag = RegEx.matchAll(RegEx.new(match.endOfTag.value, 'g'), string);
				
					for(var j = 0, size_j = matchesForEndOfTag.length; j < size_j; j++) {
						var matchForEndOfTag = matchesForEndOfTag[j];
						var endIndex = matchForEndOfTag.index;
						
						for(var k = size_i - 1; k >= 0; k--) {
							var _match = matches[k];
							if(_match.endOfTag.offsetRange === null) {
								if(_match.endOfTag.value == match.endOfTag.value) {
									if(endIndex > _match.startOfTag.offsetRange[0]) {
										_match.endOfTag.value		= matchForEndOfTag.matchString;
										_match.endOfTag.offsetRange	= [endIndex, endIndex + _match.endOfTag.value.length];
										_match.endOfTag.variables	= matchForEndOfTag.variables;
										break;
									}
								}
							}
						}
					}
				}
			}
			
			return matches;
		}

		this.__construct();
	}
	
	RegEx = new RegExConstructor();
	
	fnc.libs['RegEx'] = RegEx;
});
