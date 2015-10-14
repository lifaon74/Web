var RegEx, SuperRegEx, RecursiveRegExp;
var VisibilityString;

fnc.require(['IntervalArray'], function() {
	
	var VisibilityStringInterval = function(start, end, visibility) {
		this.start			= start;
		this.end			= end;
		this.visibility		= visibility;
	}
	
	VisibilityStringInterval.prototype.getString = function(visibilityString) {
		return visibilityString.string.slice(this.start, this.end);
	}
	
		
	VisibilityString = function(string) {
		this.string		= string;
		this.length		= string.length;
		this.visibility	= new Uint8Array(this.length);
		
		this.needUpdate			= true;
		this.visibilityInterval	= [];
		
		this.setVisibility(VisibilityString.visible);
		this.getVisibility();
	}
	
	VisibilityString.visible		= 0;
	VisibilityString.transparent	= 1;
	VisibilityString.invisible		= 2;
	
	
	VisibilityString.prototype.setVisibility = function(start, end, visibility) {
		for(var i = start; i < end; i++) {
			this.visibility[i] = visibility;
		}
		
		this.needUpdate	= true;
	}
	
	VisibilityString.prototype.getVisibility = function() {
		if(this.needUpdate) {
			var lastVisibilityInterval = new VisibilityStringInterval(0, 0, this.visibility[0]);
			this.visibilityInterval  = [lastVisibilityInterval];
			
			for(var i = 1; i < this.length; i++) {
				var _visibility = this.visibility[i];
				if(_visibility != lastVisibilityInterval.visibility) {
					lastVisibilityInterval.end = i;
					lastVisibilityInterval = new VisibilityStringInterval(i, 0, _visibility);
					this.visibilityInterval.push(lastVisibilityInterval);
				}
			}
			
			lastVisibilityInterval.end = this.length;
			this.needUpdate = false;
		}
		
		return  this.visibilityInterval;
	}
	
	
	VisibilityString.prototype.toString = function() {
		/*var string = "";
		for(var i = 0; i < this.length; i++) {
			if(i > 0) { string += ", "; }
			string += this.visibility[i];
		}
		return string;*/
	}
	
	
	
	
	var RegExpMatch = function(matchString, index) {
		this.matchString	= matchString;
		this.position		= {
			"start"	: index,
			"end"	: index + this.matchString.length
		};
		this.variables		= null;
	}
	
	
	RegExp.whiteChar	= '[ \\n\\r\\t]';
	RegExp._whiteChar	= '[^ \\n\\r\\t]';

	RegExp.word		= '[a-zA-Z]';
	RegExp._word		= '[^a-zA-Z]';
	
	RegExp.anything	= '[^]'; // .|\\n|\\r    -    \\w|\\W   -    [^]
	
	function _prepareRegExpToQuote() {
			// quote
		var escapeChar = ['\\', '.', '$', '[', ']', '(', ')', '{', '}', '^', '?', '!', '=', '*', '#', ':', '+', '-'];
		var quoteRegExp = '';
		
		for(var i = 0; i < escapeChar.length; i++) {
			if(i > 0) { quoteRegExp += '|'; }
			quoteRegExp += '\\' + escapeChar[i];
		}
		
		RegExp.quoteRegExp = new RegExp('(' + quoteRegExp + ')', 'gi');
	}
	
	_prepareRegExpToQuote();
	
	RegExp.quote = function(string) {
		return string.replace(this.quoteRegExp, '\\$1');
	}
	
	RegExp.prototype.deepInspection	= false;
	
	RegExp.prototype.match = function(string) {
		var result = this.exec(string);
		if(result) {
			var match		= new RegExpMatch(result[0], result.index);
			match.variables	= [];
			for(var i = 1; i < result.length; i++) {
				match.variables.push(result[i]);
			}
			
			if(this.deepInspection) { this.setStartIndex(result.index + 1); }
			
			return match;
		} else {
			return null;
		}	
	}
	
	RegExp.prototype._match = function(visibilityString) {
		return this.match(visibilityString.string);
	}
	
	RegExp.prototype.matchAll = function(string) {
		var matches = [];
		while(result = this.match(string)) {
			matches.push(result);
		}
		return matches;
	}
	
	RegExp.prototype.replace = function(string, replacement) {
		if(replacement instanceof Function) {
			var replacement_copy = replacement;
			replacement = function() {
				var length = arguments.length;
				
				var match		= new RegExpMatch(arguments[0], arguments[length - 2]);
				match.variables	= [];
				
				for(var i = 1; i < length - 2; i++) {
					match.variables.push(arguments[i]);
				}
				
				return replacement_copy(match);
			};
		}
			
		return string.replace(this, replacement);
	}
	
	RegExp.prototype.setStartIndex = function(index) {
		this.lastIndex = index;
	}
	
	
	
	var RecursiveRegExpMatch = function(start, end) {
		this.start	= start;
		this.end	= end;
	}
	
	RecursiveRegExp = function(startPattern, startFlags, endPattern, endFlags) {
		this.startPattern	= startPattern;
		this.startFlags		= startFlags;
		this.endFlags		= endFlags;
		
		this.startRegExp	= new RegExp(this.startPattern, this.startFlags);
		
		
		if(this.endPattern instanceof Function) {
			this.endPattern		= endPattern;
		} else {
			var endVarRegExp	= new RegExp("(^|[^\\\\])\\$([0-9]+)", "g");
			this.endPattern = function(matchForStartPattern) {
				return endVarRegExp.replace(endPattern, function(matchForEnd) {
					return matchForEnd.variables[0] + matchForStartPattern.variables[matchForEnd.variables[1] - 1];
				});
			}
		}
	}
	
	RecursiveRegExp.prototype.matchAll = function(string, greedy) {
		if(typeof greedy == "undefined") { var greedy = false; }
		
		var matches = [];
		var startMatchesOrderedByEndPattern = [];
		
		var matchForStartPattern;
		var endPattern;
		while(matchForStartPattern = this.startRegExp.match(string)) {
			endPattern = this.endPattern(matchForStartPattern);
			//console.log(endPattern);
			
			if(typeof startMatchesOrderedByEndPattern[endPattern] == "undefined") {
				startMatchesOrderedByEndPattern[endPattern] = [];
			}
			
			startMatchesOrderedByEndPattern[endPattern].push(matchForStartPattern);
		}
		
		//console.log(startMatchesOrderedByEndPattern);
		
		var matchesForStartPattern;
		var matchForStartPattern;
		var matchForEndPattern;
		var startIndex;
		var endIndex;
		var endRegExp;
		var size;
		
		for(endPattern in startMatchesOrderedByEndPattern) {
			matchesForStartPattern = startMatchesOrderedByEndPattern[endPattern];
			endRegExp = new RegExp(endPattern, this.endFlags);

			if(greedy) {
				var matchesForEndPattern = [];
				while(matchForEndPattern = endRegExp.match(string)) {
					matchesForEndPattern.push(matchForEndPattern);
				}
				
				
				size = Math.max(matchesForStartPattern.length, matchesForEndPattern.length);
				var j = -1;
				var k = matchesForEndPattern.length;
				
				for(var i = 0; i < size; i++) {
					j++;
					k--;
					
					if(typeof matchesForStartPattern[j] == "undefined") {
						matches.push(new RecursiveRegExpMatch(null, matchesForEndPattern[k]));
						continue;
					} else {
						matchForStartPattern = matchesForStartPattern[j];
					}
					
					if(typeof matchesForEndPattern[k] == "undefined") {
						matches.push(new RecursiveRegExpMatch(matchesForStartPattern[j], null));
						continue;
					} else {
						matchForEndPattern = matchesForEndPattern[k];
					}
					
					if(matchForStartPattern.position.end < matchForEndPattern.position.end) {
						matches.push(new RecursiveRegExpMatch(matchForStartPattern, matchForEndPattern));
					} else {
						matches.push(new RecursiveRegExpMatch(matchForStartPattern, null));
						matches.push(new RecursiveRegExpMatch(null, matchForEndPattern));
					}
					
					
				}
				
			} else {
				while(matchForEndPattern = endRegExp.match(string)) {
					endIndex = matchForEndPattern.position.start;
					for(var i = matchesForStartPattern.length - 1; i >= 0; i--) {
						matchForStartPattern = matchesForStartPattern[i];
						if(matchForStartPattern != null) {
							startIndex = matchForStartPattern.position.end
							if(startIndex < endIndex) {
								matches.push(new RecursiveRegExpMatch(matchForStartPattern, matchForEndPattern));
								matchesForStartPattern[i] = null;
								break;
							}
						}
					}
					
						// no matchForStartPattern found for this matchForEndPattern
					if(i < 0) {
						matches.push(new RecursiveRegExpMatch(null, matchForEndPattern));
					}
				}
				
					// no matchForEndPattern found for this matchForStartPattern
				for(var i = 0; i < matchesForStartPattern.length; i++) {
					matchForStartPattern = matchesForStartPattern[i];
					if(matchForStartPattern != null) {
						matches.push(new RecursiveRegExpMatch(matchForStartPattern, null));
					}
				}
			}
		}
		
		return matches;
	}
	
	
	
	
	/****
		OLD
	****/
	
	var SuperRegExVar = function(regexpNumber, name) {
		this.regexpNumber	= regexpNumber; // starting from 1 !
		this.name			= name;
	}
	
		// TODO : problem with variables in variables #a#b#{var1}c#{var0}
	SuperRegExp = function(pattern, flags) {
		var self = this;
		
			/*
				- search <.*>{variable} and return corresponding value into variable
					ex : ((a)|(b)) => <(a)|(b)>{var}
				- replace (~abc) by (?!abc)
			*/
		this.__construct = function(pattern, flags) {
			
			self.specialVariables	= [];
			self.originalPattern	= pattern;
			self.flags				= flags;
			
			
			var result;
			
				// replace (~abc) by (?!abc)
			pattern = pattern.replace(/\(~([^\(]+)\)/g, '(?!$1)');
			
			var getStandardVariablesRegExp	= /\((?!\?)/g; // include : (?:abc)  x(?=y), (?!abc)
			
				// search index of all variables
			var standardVariablesIndexes = [];
			while(result = getStandardVariablesRegExp.exec(pattern)) {
				standardVariablesIndexes.push(result.index);		
			}
			
			/*var increment	= 0;
			for(var i = 0; i < pattern.length; i++) {
				var _char = pattern[i];
				switch(_char) {
					case '<':
						increment++;
					break;
					case '>':
						increment--;
					break;
				}
			}*/
			
			/*var getSpecialVariablesRegExp	= /#([^#]+(?!\\))#\{([^\}]+)\}/g;
		
			
				// search #.*#{var} and compare index with variables indexes
			var i = 0;
			while(result = getSpecialVariablesRegExp.exec(pattern)) {
				while(i < standardVariablesIndexes.length) {
					var index = standardVariablesIndexes[i];
					i++;
					
					if(result.index < index) {
						self.specialVariables.push(new SuperRegExVar(
							i + self.specialVariables.length,
							result[2]
						));
						break;
					}
				}	
			}
			
			self.pattern	= pattern.replace(getSpecialVariablesRegExp, '($1)');
			self.regexp		= new RegExp(self.pattern, self.flags);*/
		}
		
		this.exec = function(string) {
			var result = self.regexp.exec(string);
			if(result) {
				result.specialVariables = [];
				for(var i = 0; i < self.specialVariables.length; i++) {
					var specialVariable = self.specialVariables[i];
					result.specialVariables[specialVariable.name] = result[specialVariable.regexpNumber];
				}
			}
			return result;
		}
		
		this.__construct(pattern, flags);
	}
	


	
	
	var RegExConstructor = function() {
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
