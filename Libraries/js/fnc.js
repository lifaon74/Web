var FncConstructor = function() {
	var self = this;
	
	this.init = function() {
		self.libs = [];
		self.libsPath = [];
		
		self.rootPath = self.getScriptRootPath('fnc.js');
		
		self.libsPath['Math'] = self.rootPath + 'Math.class.js';
		self.libsPath['Class'] = self.rootPath + 'Class.class.js';
		self.libsPath['HTML5'] = self.rootPath + 'HTML5.class.js';
		self.libsPath['Converter'] = self.rootPath + 'Converter.class.js';
		self.libsPath['HashTable'] = self.rootPath + 'Array/HashTable.class.js';
		self.libsPath['AJAXRequest'] = self.rootPath + 'AJAXRequest.class.js';
		self.libsPath['RegEx'] = self.rootPath + 'String/RegEx.class.js';
		self.libsPath['CyclicArray'] = self.rootPath + 'Array/CyclicArray.class.js';
		self.libsPath['Map'] = self.rootPath + 'Map.class.js';
		self.libsPath['PHPBridge'] = self.rootPath + 'PHPBridge.class.js';
		self.libsPath['CanvasImage'] = self.rootPath + 'CanvasImage.js';
		self.libsPath['Time'] = self.rootPath + 'Time.class.js';
		
		self.libsPath['ArrayList'] = self.rootPath + 'Array/ArrayList.experimental.js';
		self.libsPath['Tree'] = self.rootPath + 'Array/Tree.experimental.js';
		self.libsPath['ChainedNode'] = self.rootPath + 'Array/ChainedNode.experimental.js';	
		self.libsPath['IntervalArray'] = self.rootPath + 'Array/IntervalArray.experimental.js';
		self.libsPath['Cookies'] = self.rootPath + 'Cookies.experimental.js';
		
		self.libsPath['String'] = self.rootPath + 'String/String.experimental.js';
		
		self.libsPath['DOM'] = self.rootPath + 'DOM.experimental.js';
		self.libsPath['View'] = self.rootPath + 'View.experimental.js';
		
		self.libsPath['Network/Websocket'] = self.rootPath + 'network/Websocket.class.js';
		self.libsPath['jQuery'] = self.rootPath + 'external/jquery-2.1.1.min.js';
		
		
		self.libsPath['FileAPI'] = 'http://localhost/libs/fileTransfer - v4/fileTransfer/client/fileAPI.class.js';
		self.libsPath['FileTransfer'] = 'http://localhost/libs/fileTransfer - v4/fileTransfer/client/FileTransfer.class.js';
		
		self.libsPath['Inputs'] = self.rootPath + 'Inputs.class.js';
		self.libsPath['Inputs/Mouse'] = self.rootPath + 'inputs/Mouse.class.js';
		self.libsPath['Inputs/Keyboard'] = self.rootPath + 'inputs/Keyboard.class.js';
		self.libsPath['Inputs/Camera'] = self.rootPath + 'inputs/Camera.class.js';
		self.libsPath['Inputs/GPS'] = self.rootPath + 'inputs/GPS.class.js';
		self.libsPath['Inputs/Compass'] = self.rootPath + 'inputs/Compass.class.js';
		self.libsPath['Outputs/Screen'] = self.rootPath + 'outputs/Screen.class.js';
		
		self.libsPath['GUI/ProgressBar'] = self.rootPath + 'GUI/ProgressBar.class.js';
		self.libsPath['GUI/DraggableElement']  = self.rootPath + 'GUI/DraggableElement.class.js';
	}
	
	this.onDocumentReady = function(callback) {
		if(typeof jQuery != "undefined") {
			jQuery(callback);
		} else if(document.body) {
			callback();
		} else {
			window.addEventListener('load', callback, false);
		}
	}
	
	this.registerLib = function(libName, libPath) {
		self.libsPath[libName] = libPath;
	}
	
	this.libReady = function(libName, _class) {
		self.libs[libName] = _class;
	}
	
	this.require = function(libs, onloadFunction) {
		if(typeof onloadFunction != 'function') { console.error('Need a callback onload function with fnc.require'); }

		self.onDocumentReady(function() {
			var loaded = 0;
			var length = libs.length;
			
			if(length == 0) {
				onloadFunction();
			} else {
			
				var _onloadFunction = function(element, libName) {
					var timer = setInterval(function() {
						if(self.libs[libName] !== null) {
							//console.log(libName, loaded, length);
							clearInterval(timer);
							loaded++;
							if(loaded >= length) {
								onloadFunction();
							}
						}
					}, 10);
				};
					
				for(var i = 0; i < length; i++) {
					var libName = libs[i];
					
					if(typeof self.libsPath[libName] == 'undefined') {
						console.error('Lib "' + libName + '" doesn\'t exist.');
						continue;
					}
					
					if(typeof self.libs[libName] == 'undefined') {
						self.libs[libName] = null;
						self.load({
							type: 'js',
							url: self.libsPath[libName],
							onload: self.closure(_onloadFunction, [libName])
						});
					} else {
						//console.log(libs, libName, loaded, length);
						_onloadFunction(null, libName);
					}
				}
			}
		});
	}
	
	/**
	*	load a resource : css, js, image
	*	params = {
	*		'type': 'js'|'css'|'image',
	*		'url': 'path',
	*		'forceRefresh': false,
	*		'onload': function,
	*		'onerror': function
	*	}
	*/
	this.load = function(params) {
		params = self._formatParams(params);
		
		switch(params.type) {
			case 'js':
			case 'css':
				var refreshTag = "refreshKey";
				
				var head = document.querySelector('head');		
				
				switch(params.type) {
					case 'js':
						var tagName = 'script';
						var attributeName = 'src';
						
					break;
					case 'css':
						var tagName = 'link';
						var attributeName = 'href';
					break;
				}
				
				var DOMElements = document.querySelectorAll(tagName + '[' + attributeName + ']');
				var alreadyLoad = false;
				var oldDOMElement = null;
				for(var i = 0; i < DOMElements.length; i++) {
					var DOMElement = DOMElements[i];
					for(var j = 0; j < DOMElement.attributes.length; j++) {
						var attribute = DOMElement.attributes[j];
						if(attribute.name == attributeName) {
							var value = attribute.value;
							value = value.replace(new RegExp("&?" + refreshTag + "=[^&]*(&|$)", 'g'), '');
							value = value.replace(new RegExp("^\\?", 'g'), '');
							if(value == params.url) {
								params.onload(DOMElement);						
								alreadyLoad = true;
								oldDOMElement = DOMElement;
							}
						}
						if(alreadyLoad) { break; }
					}
				}
				
				if(params.forceRefresh || !alreadyLoad) {
					var element = document.createElement(tagName);
					switch(params.type) {
						case 'js':
							element.type = 'text/javascript';
						break;
						case 'css':
							element.rel = 'stylesheet';
							element.type = 'text/css';
						break;
					}
					
					element.onerror = function() {
						head.removeChild(element);
						params.onerror();
					}
					
					var onloadFunction = function() {
						if(!element._loaded) {
							if(alreadyLoad) {
								head.removeChild(oldDOMElement);
							}
							params.onload(element);	
							element._loaded = true;
						}
					}
					
					element.onreadystatechange = function () {
						if(this.readyState == 'complete') { onloadFunction(); }
					}
				
					element.onload = onloadFunction;
					element._loaded = false;
					
					if(params.url.match(/\?([^=&]+=[^=&]+&?)*/g)) {
						var url = params.url + "&" + refreshTag + "=" + Math.random();
					} else {
						var url = params.url;
					}
					
					element[attributeName] = url;
					head.appendChild(element);
				}
			break;
			case 'image':
				var image = new Image();
		
				image.onerror = params.onerror;
				image.onload = function() {
					params.onload(image);
				}
				image.src = params.url;
			break;
			case 'html':
				var xhr = getXMLHttpRequest();
				xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
						params.onload(xhr.responseText);
					}
				}
				xhr.open('POST', params.url, true);
				xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				
				var _params = "";
				if(typeof params.params != 'undefined') {
					for(elt in params.params) {
						if(i > 0) { _params += "&"; }
						_params += elt + "=" + params.params;
					}
				}
				
				xhr.send(_params);
			break;
		}
	}
	
		this._formatParams = function(params) {
			if(typeof params.onload == 'undefined') { params.onload = function() {}; }
			if(typeof params.onerror == 'undefined') { params.onerror = function() {}; }
			if(typeof params.forceRefresh == 'undefined') { params.forceRefresh = false; }
			
			return params;
		}
	
	this.multiLoad = function(paramsArray, oncomplete, onerror, i) {
		
		if(typeof oncomplete == 'undefined') { var oncomplete = function() {}; }
		if(typeof onerror == 'undefined') { var onerror = function() {}; }
		if(typeof i == 'undefined') { var i = 0; }
		
		if(i >= paramsArray.length) {
			oncomplete();
		} else {
			var params = self._formatParams(paramsArray[i]);
			
			var onloadCopy = params.onload;
			params.onload = function(entity) {
				onloadCopy(entity);
				self.multiLoad(paramsArray, oncomplete, onerror, i + 1);
			};
			
			var onerrorCopy = params.onerror;
			params.onerror = function() {
				onerrorCopy();
				onerror();
			};
			
			self.load(params);
		}
	}
	
	
	this.closure = function(callback, args) {
		if(typeof args != 'object' || typeof args.length == 'undefined') { args = [args]; }
		return (function() {
			var table = [];

			for(var i = 0; i < arguments.length; i++) {
				table.push(arguments[i]);
			}
			
			for(var i = 0; i < args.length; i++) {
				table.push(args[i]);
			}
			
			callback.apply(this, table);
		})
	}
	
	this.getNavigatorLanguage = function() {
		var language = "en";
		
		if(navigator.language) {
			language = navigator.language.toLowerCase().substring(0, 2);
		} else if (navigator.userLanguage) {
			language = navigator.userLanguage.toLowerCase().substring(0, 2);
		} else if(navigator.userAgent.indexOf("[") != -1) {
			var start = navigator.userAgent.indexOf("[");
			var end = navigator.userAgent.indexOf("]");
			language = navigator.userAgent.substring(start + 1, end).toLowerCase();
		}
		return language;
	}
		
	this.getAbsolutePosition = function(element) {
		var x = element.offsetLeft;
		var y = element.offsetTop;
		if(element.offsetParent) {
			var position = self.getAbsolutePosition(element.offsetParent);
			x += position[0];
			y += position[1];
		}
		
		return [x, y];
	}
	
	this.escapeHTMLString = function(HTMLString) {
		self.tagsToReplace = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;'
		};

		return HTMLString.replace(/[&<>]/g, function replaceTag(tag) {
			return self.tagsToReplace[tag] || tag;
		});
	}
	
		// TODO : complete
	this.getScriptRootPath = function(pathOfThisScript) {
		var scripts = document.querySelectorAll('script');	
		
		var quote = function(string) {
			var escapeChar = ['\\', '.', '$', '[', ']', '(', ')', '{', '}', '^', '?', '*', '+', '-'];
			var exp = '';
			
			for(var i = 0; i < escapeChar.length; i++) {
				if(i > 0) { exp += '|'; }
				 exp += '\\' + escapeChar[i];
			}
			
			var reg = new RegExp('(' + exp + ')', 'gi');
			
			return string.replace(reg, '\\$1');
		}

		for(var i = 0; i < scripts.length; i++) {
			var src = scripts[i].src;
			if(src.match(new RegExp('^(.*)' + quote(pathOfThisScript) + '$', 'g'))) {
				var result = RegExp.$1;
				return result;
			}
		}
		
		return null;
	}

	this.init();
}

var fnc = new FncConstructor();


if(typeof jQuery != 'undefined') {
	jQuery.fn.rotate = function(degrees, x, y) {
	
		//transform-origin
		$(this).css({
			'transform-origin' :'207px 240px',
			'transform' : 'rotate('+ degrees +'deg)'
		});
	};
}


/*fnc.require(['AJAXRequest'], function() {
	var url = '../../fileTransfer - v4/fileTransfer/server/transfer.php';
	var id = '538f6160f2021';
	
	var size = 1200000;
	var pContent = new Uint8Array(size);
	
	var connection = new AJAXRequest({
		url: url,
		type: 'post',
		responseType: 'arraybuffer',
		data: {
			type: 'download',
			action: 'getFileData',
			id: id,
			startOffset: 0,
			endOffset: size
		},
		partialContent : true,
		onpartialcontent: function(partialContent, offset) {
			partialContent = new Uint8Array(partialContent);
			for(var i = 0; i < partialContent.length; i++) {
				pContent[i + offset] = partialContent[i];
			}
		},
		oncomplete: function(response) {
			response = new Uint8Array(response);
			
			for(var i = 0; i < response.length; i++) {
				if(response[i] != pContent[i]) {
					console.log(i);
					break;
				}
			}
			
			console.log('ok');
		},
		onfail: function() {
			
		}
	});
});*/

/*fnc.require(['AJAXRequest'], function() {
	var req = new AJAXRequest({
		url: 'test.html',
		complete: function(html) {
			console.log(html);
		}
	});
});*/
