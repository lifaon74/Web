var OffsetRange, Parser;

fnc.require(['Tree', 'RegEx', 'AJAXRequest'], function() {
	
	OffsetRange = function(start, end) {
		var self = this;
		
		this.__construct = function(start, end) {
			self.start	= start;
			self.end	= end;
		}
		
		this.toString = function() {
			return "OffsetRange [" + self.start + ", " + self.end + "]";
		}
		
		this.getCorrespondingString = function(string) {
			return string.substring(self.start, self.end);
		}
		
		this.__construct(start, end);
	}
	
	Token = function(name, offsetRange) {
		var self = this;
		
		this.__construct = function(name, offsetRange) {
			self.name			= name;
			self.offsetRange	= offsetRange;
		}
		
		this.toString = function() {
			return "{ \"" + self.name + "\", " + self.offsetRange.toString() + " }";
		}
		
		this.__construct(name, offsetRange);
	}
	
	
	Parser = function() {
		var self = this;
		
		this.__construct = function() {
		}
		
		this.getRootTree = function(text) {
			var offsetRange = new OffsetRange(0, text.length);
			var tree = new Tree('root', offsetRange);
			tree.pushChild(new Tree("raw", offsetRange));
			return tree;
		}
	
		this.getTreeWithValueBetweenOffsetRange = function(root, value) {
			var correspondingTree = null;
			root.forEach(function(tree) {
				if(tree.name == "raw") {
					var treeOffsetRange = tree.value;
					
					if((value >= treeOffsetRange.start) && (value < treeOffsetRange.end)) {
						correspondingTree = tree;
						return false;
					}
				}
			});
			
			return correspondingTree;
		}
	
		this.replaceOffsetRangeInTree = function(root, offsetRanges, treeName) {
			
			for(var i = 0, size = offsetRanges.length; i < size; i++) {
				var offsetRange	= offsetRanges[i];
				var newTree		= new Tree(treeName, offsetRange);
				
				var startTree = self.getTreeWithValueBetweenOffsetRange(root, offsetRange.start);
				
				var treeOffsetRange = startTree.value;
				if((offsetRange.end >= treeOffsetRange.start) && (offsetRange.end <= treeOffsetRange.end)) { // the first element is too the last
					var endTree = startTree;
				} else {	
					var endTree = self.getTreeWithValueBetweenOffsetRange(root, offsetRange.end - 1);
				}
				
				if(startTree === endTree) {
					newTree.pushChild(new Tree("raw", offsetRange));
					self._splitTree(startTree, [
						self._offsetRangeToTree(new OffsetRange(treeOffsetRange.start, offsetRange.start)),
						newTree,
						self._offsetRangeToTree(new OffsetRange(offsetRange.end, treeOffsetRange.end))
					]);
				} else {
						// seek for a common parent
					var startTreePath		= startTree.getPath();
					var endTreePath			= endTree.getPath();
					var commonParentIndex	= self._getCommonParentIndex(startTreePath, endTreePath);
					var commonParent		= startTreePath[commonParentIndex];
					
					var startChild	= startTreePath[commonParentIndex + 1];
					var endChild	= endTreePath[commonParentIndex + 1];

					var treeOffsetRange = startTree.value;
					if(startTree.getParent() === commonParent) {
						var elements = self._splitTree(startTree, [
							self._offsetRangeToTree(new OffsetRange(treeOffsetRange.start, offsetRange.start)),
							self._offsetRangeToTree(new OffsetRange(offsetRange.start, treeOffsetRange.end))
						]);
						
						startChild = elements[elements.length - 1];
					} else {
						if(treeOffsetRange.start != offsetRange.start) {
							debugger;
							throw 'START OF TAG : Crossing offset => ' + offsetRange.toString() + ' - ' + treeOffsetRange.toString();
						}
					}
					
					
					var treeOffsetRange = endTree.value;
					if(endTree.getParent() === commonParent) {
						var elements = self._splitTree(endTree, [
							self._offsetRangeToTree(new OffsetRange(treeOffsetRange.start, offsetRange.end)),
							self._offsetRangeToTree(new OffsetRange(offsetRange.end, treeOffsetRange.end))
						]);
						
						endChild = elements[0];
					} else {
						if(treeOffsetRange.end != offsetRange.end) {
							throw ('END OF TAG : Crossing offset at offset ' + offsetRange.toString());
						}
					}
					
					startChild.insertBefore(newTree);
					
					newTree.pushChild(startChild);
					do {
						startChild = newTree.getNext();
						newTree.pushChild(startChild);
					} while(startChild != endChild);
				}
			}

			return root;
		}
		
			this._offsetRangeToTree = function(offsetRange) {
				if((offsetRange.end - offsetRange.start) > 0) {
					return new Tree("raw", offsetRange);
				} else {
					return null;
				}
			}
			
			this._splitTree = function(tree, subTrees) {
				var lastElement = tree;
				var elements = [];
				for(var i = 0, size = subTrees.length; i < size; i++) {
					var subTree = subTrees[i];
	
					if(subTree) {
						lastElement.insertAfter(subTree);
						lastElement = subTree;
						elements.push(subTree);
					}
				}
				
				tree.detach();
				
				return elements;
			}
	
			this._getCommonParentIndex = function(startTreePath, endTreePath) {
				for(var i = 0, size = Math.min(startTreePath.length,endTreePath.length); i < size; i++) {
					if(startTreePath[i] !== endTreePath[i]) {
						break;
					}
				}
				
				return i - 1;
			}
			
		
		this.displayTree = function(tree, text) {
			tree.forEach(function(tree) {
				if(tree.name == "raw") { 
					var offsetRange = tree.value;
					var string 	= offsetRange.getCorrespondingString(text);
					tree.name	= "text";
					tree.value	= string;
				}
			});
			
			console.log(tree.toString());
		}
	
		this.__construct();
	}
	
	
	TokenizedTree = function(tokenizedString, tree) {
		var self = this;
		
		this.__construct = function(tokenizedString, tree) {
			self.tokenizedString	= tokenizedString;
			self.tree				= tree;
		}
		
		
		this.findTreeWithValueBetweenOffsetRange = function(value) {
			var correspondingTree = null;
			self.tree.forEach(function(tree) {
				if(tree.name == "raw") {
					var treeOffsetRange = tree.value;
					
					if((value >= treeOffsetRange.start) && (value < treeOffsetRange.end)) {
						correspondingTree = tree;
						return false;
					}
				}
			});
			
			return correspondingTree;
		}
		
		
		this.findStartAndEndTree = function(offsetRange) {
			var startTree	= null;
			var endTree		= null;
			
			var value = offsetRange.start;
			
			self.tree.forEach(function(tree) {
				if(tree.name == "raw") {
					if((value >= tree.value.start) && (value < tree.value.end)) {
						if(startTree === null) {
							startTree = tree;
							value = offsetRange.end - 1;
							return tree;
						} else {
							endTree = tree;
							return false;
						}
					}
				}
			});
			
			return {
				"startTree"	: startTree,
				"endTree"	: endTree
			};
		}
		
		this.replaceOffsetRangeInTree = function(treeName, offsetRange) {
			var newTree		= new Tree(treeName, offsetRange);
			
			var _trees		= self.findStartAndEndTree(offsetRange);
			var startTree	= _trees.startTree;
			var endTree		= _trees.endTree;

			if(startTree === endTree) {
				var treeOffsetRange = startTree.value;
				newTree.pushChild(new Tree("raw", offsetRange));
				self._splitTree(startTree, [
					self._offsetRangeToTree(new OffsetRange(treeOffsetRange.start, offsetRange.start)),
					newTree,
					self._offsetRangeToTree(new OffsetRange(offsetRange.end, treeOffsetRange.end))
				]);
			} else {
					// seek for a common parent
				var startTreePath		= startTree.getPath();
				var endTreePath			= endTree.getPath();
				var commonParentIndex	= self._getCommonParentIndex(startTreePath, endTreePath);
				var commonParent		= startTreePath[commonParentIndex];
				
				var startChild	= startTreePath[commonParentIndex + 1];
				var endChild	= endTreePath[commonParentIndex + 1];

				var treeOffsetRange = startTree.value;
				if(startTree.getParent() === commonParent) {
					var elements = self._splitTree(startTree, [
						self._offsetRangeToTree(new OffsetRange(treeOffsetRange.start, offsetRange.start)),
						self._offsetRangeToTree(new OffsetRange(offsetRange.start, treeOffsetRange.end))
					]);
					
					startChild = elements[elements.length - 1];
				} else {
					if(treeOffsetRange.start != offsetRange.start) {
						debugger;
						throw 'START OF TAG : Crossing offset => ' + offsetRange.toString() + ' - ' + treeOffsetRange.toString();
					}
				}
				
				
				var treeOffsetRange = endTree.value;
				if(endTree.getParent() === commonParent) {
					var elements = self._splitTree(endTree, [
						self._offsetRangeToTree(new OffsetRange(treeOffsetRange.start, offsetRange.end)),
						self._offsetRangeToTree(new OffsetRange(offsetRange.end, treeOffsetRange.end))
					]);
					
					endChild = elements[0];
				} else {
					if(treeOffsetRange.end != offsetRange.end) {
						throw ('END OF TAG : Crossing offset at offset ' + offsetRange.toString());
					}
				}
				
				startChild.insertBefore(newTree);
				
				newTree.pushChild(startChild);
				do {
					startChild = newTree.getNext();
					newTree.pushChild(startChild);
				} while(startChild != endChild);
			}
			
		}
		
			this._offsetRangeToTree = function(offsetRange) {
				if((offsetRange.end - offsetRange.start) > 0) {
					return new Tree("raw", offsetRange);
				} else {
					return null;
				}
			}
			
			this._splitTree = function(tree, subTrees) {
				var lastElement = tree;
				var elements = [];
				for(var i = 0, size = subTrees.length; i < size; i++) {
					var subTree = subTrees[i];
	
					if(subTree) {
						lastElement.insertAfter(subTree);
						lastElement = subTree;
						elements.push(subTree);
					}
				}
				
				tree.detach();
				
				return elements;
			}
	
			this._getCommonParentIndex = function(startTreePath, endTreePath) {
				for(var i = 0, size = Math.min(startTreePath.length,endTreePath.length); i < size; i++) {
					if(startTreePath[i] !== endTreePath[i]) {
						break;
					}
				}
				
				return i - 1;
			}
		
		
		this.replaceOffsetRangesInTree = function(treeName, offsetRanges) {
			for(var i = 0, size = offsetRanges.length; i < size; i++) {
				self.replaceOffsetRangeInTree(treeName, offsetRanges[i]);
			}
		}
		
		
		this.display = function() {
			self.tree.forEach(function(tree) {
				if(tree.name == "raw") { 
					var offsetRange = tree.value;
					var string 	= offsetRange.getCorrespondingString(self.tokenizedString.string);
					tree.name	= "text";
					tree.value	= string;
				}
			});
			
			console.log(self.tree.toString());
		}
		
		this.__construct(tokenizedString, tree);
	}
	

	TokenizedString = function(string) {
		var self = this;
		
		this.__construct = function(string) {
			self.string			= string;
			self.coppedString	= null;
			self.tokens			= new ArrayList();
		}
		
		
	/**
		Public
	**/
	
		this.addToken = function(name, offsetRange) {
			self.coppedString	= null;
			var newToken		= new ArrayListElement(new Token(name, offsetRange));
			
			var tokenInsertBefore = null;
			self.tokens.forEach(function(token) {
				if(offsetRange.end <= token.value.offsetRange.start) {
					tokenInsertBefore = token;
					return false;
				}
			});
			
			if(tokenInsertBefore === null) {
				var previousToken = self.tokens.last;
				self.tokens.push(newToken);
			} else {
				var previousToken = tokenInsertBefore.previous;
				tokenInsertBefore.insertBefore(newToken);
			}
			
				// check if tockens are crossing
			if(previousToken !== null) {
				if(offsetRange.start < previousToken.value.offsetRange.end) {
					console.error("Crossing tokens ! Trying to insert token " + newToken.value.toString() + " but it cross " + previousToken.value.toString());
					newToken.detach();
				}
			}
		}
		
		this.addTokens = function(name, offsetRanges) {
			for(var i = 0, size = offsetRanges.length; i < size; i++) {
				self.addToken(name, offsetRanges[i]);
			}
		}
		
		
			// should be removed
		this.addTokensCallback = function(name, callback, cropString) {
			if(typeof cropString != "boolean") { var cropString = true; }
			
			if(cropString) {
				self.addTokens(name, self.tokenizeCutString(callback));
			} else {
				self.addTokens(name, callback(self.string));
			}
		}
		
			// remove existing tokens form the string, parse string, convert found offsets to match with the original string
		this.tokenizeCutString = function(callback) {
			return self._getOffsetRangesForCutString(callback(self._cut(), self.string));
		}
		
			this._cut = function() {
				if(self.coppedString === null) {
					self.coppedString = "";
					var lastIndex = 0;
					
					self.tokens.forEach(function(token) {
						self.coppedString += self.string.substring(lastIndex, token.value.offsetRange.start);
						lastIndex = token.value.offsetRange.end;
					});
							
					self.coppedString += self.string.substring(lastIndex, self.string.length);
				}
				
				return self.coppedString;
			}
			
			this._getOffsetRangesForCutString = function(offsetRanges) {
				for(var i = 0, size = offsetRanges.length; i < size; i++) {
					var offsetRange = offsetRanges[i];
					self.tokens.forEach(function(token) {
						var length = token.value.offsetRange.end - token.value.offsetRange.start;
						
						if(offsetRange.start >= token.value.offsetRange.start) { // add all the offsetRanges found before current offsetRange
							offsetRange.start += length;
							offsetRange.end += length;
						} else { // current offsetRange.start is under all the offsetRanges
							if(offsetRange.end > token.value.offsetRange.start) { // is there any offsetRanges inside current offsetRange?
								offsetRange.end += length;
							} else {
								return false;
							}
						}
					});
				}
				
				return offsetRanges;
			}
		
		
		this.getTokenizedTree = function() {
			var tree = new Tree('root', new OffsetRange(0, self.string.length));
			
			var lastIndex = 0;
			self.tokens.forEach(function(token) {
				var token = token.value;
				self._addTokenToTree(tree, "raw", lastIndex, token.offsetRange.start);
				self._addTokenToTree(tree, token.name, token.offsetRange.start, token.offsetRange.end);
				lastIndex = token.offsetRange.end;
			});
			
			self._addTokenToTree(tree, "raw", lastIndex, self.string.length);
			
			return new TokenizedTree(self, tree);
		}
		
			this._addTokenToTree = function(tree, tokenName, startOffset, endOffset) {
				if(endOffset - startOffset > 0) {
					tree.pushChild(new Tree(tokenName, new OffsetRange(startOffset, endOffset)));
				}
			}
		

		
	/**
		Private
	**/
			
		
	
		this.__construct(string);
	}

	
	
	function convertRecursiveMatchAsOffsetRange_old(matches) {
		offsetRanges = {
			"numberOfTags"	: matches.length,
			"startOfTag"	: [],
			"endOfTag"		: [],
			"fullTag"		: []
		};
		
		for(var i = 0, size = matches.length; i < size; i++) {
			var match = matches[i];
			offsetRanges.startOfTag.push(	new OffsetRange(match.startOfTag.offsetRange[0], match.startOfTag.offsetRange[1]));
			offsetRanges.endOfTag.push(		new OffsetRange(match.endOfTag.offsetRange[0], match.endOfTag.offsetRange[1]));
			offsetRanges.fullTag.push(		new OffsetRange(match.startOfTag.offsetRange[0], match.endOfTag.offsetRange[1]));
		}
		
		return offsetRanges;
	}

	
	function convertRecursiveMatchAsOffsetRange(matches) {
		offsetRanges = [];
		
		for(var i = 0, size = matches.length; i < size; i++) {
			var match = matches[i];
			offsetRanges[size * 0 + i] = new OffsetRange(match.startOfTag.offsetRange[0],	match.startOfTag.offsetRange[1]);
			offsetRanges[size * 1 + i] = new OffsetRange(match.endOfTag.offsetRange[0],	match.endOfTag.offsetRange[1]);
			offsetRanges[size * 2 + i] = new OffsetRange(match.startOfTag.offsetRange[0],	match.endOfTag.offsetRange[1]);
		}
		
		
		return offsetRanges;
	}
	
	function parseComments(string) {
		var offsetRanges = [];
		
		var regex = RegEx.new(RegEx.quote("<!--") + RegEx.anything + "*?" + RegEx.quote("-->"), 'g');
		var matches = RegEx.matchAll(regex, string);
		
		for(var i = 0, size = matches.length; i < size; i++) {
			var match = matches[i];
			offsetRanges.push(new OffsetRange(match.index, match.index + match.matchString.length));
		}
		
		return offsetRanges;
	}
	
	function parseStrings(string) {
		
		var state = "in_raw";
		var strings = [];
		var stringsIndex = 0;
		
		for(var i = 0; i < string.length; i++) {
			var _char = string[i];
			
			switch(state) {
				case "in_raw":
					switch(_char) {
						case "\"":
							state = "in_double_quote_string";
							strings[stringsIndex] = new OffsetRange(i, i);
						break;
					}
				break;
				case "in_double_quote_string":
					switch(_char) {
						case "\\":
							i++;
						break;
						case "\"":
							state = "in_raw";
							strings[stringsIndex].end = i + 1;
							stringsIndex++;
						break;
					}
				break;
			}
			
			
		}
		
		return strings;
	}

	function parseTag(string) {
		var matches = RegEx.recursiveMatch('<([\\w]+)( [^>]*)?>', '</$1>', string);
		var offsetRanges = convertRecursiveMatchAsOffsetRange(matches);
		
		for(var i = 0, size = offsetRanges.length / 3; i < size; i++) {
			var str = offsetRanges[i].getCorrespondingString(string);
			
			var match = RegEx.match(/^(<)(\w)( ?)(.*)>$/g, str);
			var tagName = match.variables[0];
			//offsetRanges[i * 4] = new OffsetRange(match.index + match.variables[0].length, match.index + match.variables[0].length + match.variables[1].length); //tagName
			
			//var match = RegEx.match(/^<(\w) ?(.*)>$/g, str);
			
			console.log(match);
		}
			
			
		return offsetRanges;
	}
	
	
	function detectColision(matches) {
		for(var i = 0, size = matches.length; i < size; i++) {
			var match_1 = matches[i];
			for(var j = i + 1; j < size; j++) {
				var match_2 = matches[j];
				if(
					match_2.start	>= match_1.start	&&
					match_2.start	< match_1.end		&&
					match_2.end >= match_1.end
				) {
					console.error("detect collision : " + match_1.toString() + " - " + match_2.toString());
				}
			}
		}
	}
	
	
	function cutText(text, matches) {
		var newText = "";
		var lastIndex = 0;
		for(var i = 0, size = matches.length; i < size; i++) {
			var match = matches[i];
			newText += text.substring(lastIndex, match.start);
			lastIndex = match.end;
		}
		
		newText += text.substring(lastIndex, text.length);
		
		console.log(newText);
	}
	
	/*a = new Tree("A", "a");
	b = new Tree("B", "b");
	a.pushChild(b);
	
	a.forEach(function(child) {
		console.log(child.name);
		return false;
	});*/
	
	console.clear()

		//$3(i)a(b)$1(ok)c(d)$2(8)
	var reg = new SuperRegEx("(a)$5(b)", "g"); // => a(ok)
	debugger;
	
	return;
	
	//var url = "../UNIGE/Master/docs/documentation.xml";
	var url = "test.xml";
	
	new AJAXRequest({
		"url"	: url,
		"responseType"	: "text",
		"oncomplete"		: function(string) {

			var t1 = new Date().getTime();
			var tokenizedString = new TokenizedString(string);
			
			tokenizedString.addTokens("comments", tokenizedString.tokenizeCutString(parseComments));
			//console.log(tokenizedString.tokens.toArray().toString());
			
			tokenizedString.addTokens("string", tokenizedString.tokenizeCutString(parseStrings));
			//console.log(tokenizedString.tokens.toArray().toString());
			
			var tokenizedTree = tokenizedString.getTokenizedTree();
			//tokenizedTree.display();
	
			var offsetRanges = tokenizedString.tokenizeCutString(parseTag);
			console.log(offsetRanges);
			
			/*for(var i = 0, size = offsetRanges.length / 3; i < size; i++) {
				var str = offsetRanges[i].getCorrespondingString(string);
				
				var match = RegEx.match(/^<(\w) ?(.*)>$/g, str);
				var tagName = match.variables[0];
				
				//var match = RegEx.match(/^<(\w) ?(.*)>$/g, str);
				
				console.log(tagName, match.variables[1]);
			}*/
		
			/*for(var i = 0, size = offsetRanges.length; i < size; i += 3) {
				tokenizedTree.replaceOffsetRangeInTree("startOfTag", offsetRanges[i]);
				tokenizedTree.replaceOffsetRangeInTree("endOfTag", offsetRanges[i + 1]);
				tokenizedTree.replaceOffsetRangeInTree("tag", offsetRanges[i + 2]);
			}*/
			
			/*tokenizedTree.tree.forEach(function(tree) {
				if(tree.name == "startOfTag") {
					var offsetRange = tree.value;
					
					var str = offsetRange.getCorrespondingString(string);
					var match = RegEx.match(/^<(\w) ?(.*)>$/g, str);
					var tagName = match.variables[0];
					
					console.log(tagName, str);
				}
			});*/
			
			var t2 = new Date().getTime();
			
			//tokenizedTree.display();
			console.log("elapsed time : " , t2 - t1);
			
			return; 
		}		
	});
	
	/*var iframe = document.getElementById('iframe');
	var xml =  iframe.contentDocument.documentElement.outerHTML*/

});