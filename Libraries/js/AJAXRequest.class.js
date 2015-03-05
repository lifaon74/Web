var AJAXRequest;

/**
*	settings = {
* 		url : url of the request,
*		type : "post" | "get",
*		data : object,
*		responseType : "text" | "json" | "arraybuffer" | "blob" | "document",
*		asynchronous: bool,
*		external: bool
*	}
*
*	events :
*		"downloadprogress" : event
*		"error" : message, id
*		"uploadprogress" : event
*		"complete" : response
*		"abort"
**/

fnc.require(['Class', 'Converter'], function() {

	AJAXRequest = function(settings) {
		var self = this;
		ClassWithBinds(self);
		
		if(typeof settings == 'undefined') { var settings = {}; }
		
		this.init = function(settings) {
			self._initErrors();
			
			self.url			= null;
			self.type			= 'post';
			self.data			= {};
			self.responseType	= 'text';
			self.partialContent	= false;
			self.external		= false;
			self.asynchronous	= true;
			
			self.uploaded = 0;
			self.downloaded = 0;
			
			/**
			*	pending : 0
			*	uploading : 1
			*	downloading: 2
			*	completed : 3
			*	aborted : 4
			*	failed : 5
			*/
			self.status = 0;
			
			self.xhr = self._getXMLHttpRequest();
			
			self.supportHTML5 = typeof self.xhr.upload != 'undefined';
			self.lastOffsetRead = 0;
			
			if(typeof self.xhr.addEventListener == 'function') {
				self.xhr.addEventListener('readystatechange', self._onreadystatechange, false); 
				
				self.xhr.addEventListener('progress', function(event) {
					if(self.status >= 2) {
						self.downloaded = event.loaded;
						self.trigger('downloadprogress', [event]);
					}
				}, false); 
				
				self.xhr.addEventListener('error', function(event) {
					self.trigger('fail');
					self.trigger('_error', [3]);
				}, false); 

				if(self.supportHTML5) {
					self.xhr.upload.addEventListener('progress', function(event) {
						self.uploaded = event.loaded;
						self.trigger('uploadprogress', [event]);
					}, false);
					
					self.xhr.upload.addEventListener('error', function() {
						self.trigger('fail');
						self.trigger('_error', [3]);
					}, false);
				}
			} else {
				self.xhr.onreadystatechange = self._onreadystatechange;
			}
			
			self.setSettings(settings);
			self.start();
		}
		
		this._initErrors = function() {
			self.errors = [];
			self.errors[0] = 'Invalid url';
			self.errors[1] = 'Your browser doesn\'t support AJAX';
			self.errors[2] = function() { return 'Url not found (404 not found) : ' + self.url; };
			self.errors[3] = 'AJAX request failed';
			self.errors[4] = function() { return 'Can\'t parse data : ' + self.xhr.responseText; };
			self.errors[5] = 'Your browser doesn\'t support partial content';
			self.errors[6] = 'Your browser doesn\'t support responseType';
			
			self.bind('_error', function(id) {
				switch(typeof self.errors[id]) {
					case 'function':
						var message = self.errors[id]();
					break;
					case 'string':
						var message = self.errors[id];
					break;
					default:
						var message = 'Unknown error';
					break;
				}
				
				self.trigger('error', [message, id]);
			});
		}
		
		
		this.start = function() {
			if(self.status == 0) {
				var data = null;
				
				switch(self.responseType) {
					case 'arraybuffer':
					case 'blob':
					case 'document':
						if(self.partialContent) {
							self.xhr.overrideMimeType('text\/plain; charset=x-user-defined');
						} else {
							self.xhr.responseType = self.responseType;
						}
					break;
				}
					
				
				switch(self.type) {
					case 'get':
						if(self.external) { // TODO : finish external implementation
							var url = fnc.getScriptRootPath('fnc.js') + '../php/JSBridge.script.php';
							self.xhr.open("GET", url + '?ACTION=GET_PAGE&URL=' + self.url, self.asynchronous);
						} else {
							self.xhr.open("GET", self.url + '?' + self._convertDataAsString(self.data), self.asynchronous);
						}
					break;
					case 'post':
						self.xhr.open("POST", self.url, self.asynchronous);

						data = self._convertDataAsForm(self.data);
						if(!data) {
							self.xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
							data = self._convertDataAsString(self.data);
						}
					break;
				}
				
				self.xhr.send(data);
			}
		}
		
		this.abort = function() {
			if(self.status == 1 || self.status == 2) {
				self.xhr.abort();
				self.status = 4;
				self.trigger('abort');
			}
		}
		
		this.setSettings = function(settings) {
				// url
			if(typeof settings.url == 'string') {
				self.url = settings.url;
			} else {
				self.trigger('_error', [0]);
			}
			
				// type
			if(typeof settings.type == 'string') {
				switch(settings.type.toLowerCase()) {
					case 'post':
						self.type = 'post';
					break;
					case 'get':
						self.type = 'get';
					break;
				}
			}
		
				// data
			if(typeof settings.data == 'object') {
				self.data = settings.data;
			}
			
				// external
			if(typeof settings.external == 'boolean') {
				self.external = settings.external;
			}
			
				// asynchronous
			if(typeof settings.asynchronous == 'boolean') {
				self.asynchronous = settings.asynchronous;
			}
			
			
				// responseType
			if(typeof settings.responseType == 'string') {
				settings.responseType = settings.responseType.toLowerCase();
				
				var validResponseType = false;
				switch(settings.responseType) {
					case 'arraybuffer':
					case 'blob':
					case 'document':
						if(typeof self.xhr.responseType != 'string') {
							validResponseType = false;
						} else {
							validResponseType = true;
						}
					break;
					case 'text':
					case 'json':
						validResponseType = true;
					break;
					default:
						validResponseType = false;
				}
				
				if(validResponseType) {
					self.responseType = settings.responseType;
				} else {
					self.responseType = 'text';
						self.trigger('_error', [6]);
				}
			}
			
				// partialContent
			if(typeof settings.partialContent == 'boolean') {
				self.partialContent = settings.partialContent;
				
				if(self.partialContent && typeof self.xhr.overrideMimeType != 'function') {
					self.partialContent = false;
					self.trigger('_error', [5]);
				}
			}
		
				// on error
			if(typeof settings.onerror == 'function') {
				self.bind('error', settings.onerror);
			}
		
				// on uploadprogress
			if(typeof settings.onuploadprogress == 'function') {
				self.bind('uploadprogress', settings.onuploadprogress);
			}
			
				// on downloadprogress
			if(typeof settings.ondownloadprogress == 'function') {
				self.bind('downloadprogress', settings.ondownloadprogress);
			}
			
				// on onpartialcontent
			if(typeof settings.onpartialcontent == 'function') {
				self.bind('partialcontent', settings.onpartialcontent);
			}
		
				// on complete
			if(typeof settings.oncomplete == 'function') {
				self.bind('complete', settings.oncomplete);
			}
			
				// on fail
			if(typeof settings.onfail == 'function') {
				self.bind('fail', settings.onfail);
			}
			
				// on abort
			if(typeof settings.onabort == 'function') {
				self.bind('abort', settings.onabort);
			}
		
		}
		
		this.getResponseHeaders = function() {
			var headers			= [];
			var headersString	= self.xhr.getAllResponseHeaders();
			var splitedHeaders	= RegEx.split(/\r?\n/g, headersString);
			
			for(var i = 0; i < splitedHeaders.length; i++) {
				var match = RegEx.match(/^([^:]+):(.*)$/g, splitedHeaders[i]);
				if(match) {
					var headerName	= match.variables[0].trim();
					var headerValue	= match.variables[1].trim();
					headers[headerName] = headerValue;
				}
			}
			
			return headers;
		}
		
		this.onerror = function(message, id) { console.error(message); }
		this.onuploadprogress = function(loaded, total) {}
		this.ondownloadprogress = function(loaded, total) {}
		this.onpartialcontent = function(partialContent, offset) {}
		this.oncomplete = function(message) {}
		this.onfail = function() {}
		this.onabort = function() {}
		
		this._getXMLHttpRequest = function () {
			var xhr = null;
			 
			if(window.XMLHttpRequest || window.ActiveXObject) {
				if (window.ActiveXObject) {
					try {
						xhr = new ActiveXObject("Msxml2.XMLHTTP");
					} catch(e) {
						xhr = new ActiveXObject("Microsoft.XMLHTTP");
					}
				} else {
					xhr = new XMLHttpRequest(); 
				}
			} else {
				self.trigger('_error', [1]);
				return null;
			}
			 
			return xhr;
		}
		
		this._onreadystatechange = function() {
			switch(self.xhr.readyState) {
				case 1:	// upload start
					self.status = 1;
				break;
				case 2:	// download start
					self.status = 2;
				break;
				case 3:
					if(self.partialContent) {
						var lastOffsetRead = self.lastOffsetRead;
						self.lastOffsetRead = self.xhr.response.length;
						self.trigger('partialcontent', [self._convertResponseAsResponseTypeExpected(self.xhr.response, lastOffsetRead), lastOffsetRead]);
					}
				break;
				case 4: // finish
					switch(self.xhr.status) {
						case 0:
							// error
						break;
						case 200:
							if(!self.supportHTML5) {
								self.uploaded = self._uploaded;
								if(typeof self.xhr.getHeaders == 'function') {
									self.downloaded = self.xhr.getHeaders("Content-Length");
								} else {
									self.downloaded = self.xhr.getResponseHeader("Content-Length");
								}
							}
							
							if(self.partialContent) {
								//console.log(self.xhr.responseText.length);
								var response = self._convertResponseAsResponseTypeExpected(self.xhr.response, 0);
							} else {
								switch(self.responseType) {
									case 'text':
										var response = self.xhr.responseText;
									break;
									case 'json':
										try {
											var response = JSON.parse(self.xhr.responseText);
										} catch(e) {
											self.trigger('_error', [4]);
											return;
										}
									break;
									case 'arraybuffer':
										var response = self.xhr.response;
									break;
									case 'blob':
									case 'document':
										var response = self.xhr.response;
									break;
								}
							}
							self.status = 3;
							self.trigger('complete', [response]);
						break;
						case 404:
						default:
							self.trigger('_error', [2]);
						break;
					}
				break;
			}
		}
		
		this._convertResponseAsResponseTypeExpected = function(responseText, lastOffsetRead) {
			switch(self.responseType) {
				case 'text':
					// TODO : manage partialContent with this types
					var response = responseText;
				break;
				case 'json':
					// TODO : manage partialContent with this types
					var response = null;
				break;
				case 'arraybuffer':
					var array = new Uint8Array(responseText.length - lastOffsetRead);
			
					for(var i = lastOffsetRead; i < responseText.length; i++) {
						var value = responseText.charCodeAt(i);
						array[i - lastOffsetRead] = value;
					}
					
					return array;
				break;
				case 'blob':
				case 'document':
					// TODO : manage partialContent with this types
					var response = responseText;
				break;
			}
			
			return response;
		}
		
		this._convertDataAsString = function(data) {
			var dataString = '';
			var i = 0;
			
			for(elt in data) {
				if(typeof data[elt] == 'number' || typeof data[elt] == 'string') {
					if(i > 0) { dataString += '&'; }
					dataString += encodeURIComponent(elt) + '=' + encodeURIComponent(data[elt]);
					i++;
				}
			}
			
			if(!self.supportHTML5) {
				self._uploaded = dataString.length;
			}
			
			return dataString;
		}
		
		this._convertDataAsForm = function(data) {
			if(typeof FormData == 'undefined') { return false; }
			
			var form = new FormData();
			
			for(elt in data) {
				switch(typeof data[elt]) {
					case 'object':
						var object = data[elt];
						
						if(object !== null) {
							Class(object);
							var className = object.className().toLowerCase();
							
							switch(className) {
								case 'object':
									form.append(elt, JSON.stringify(data[elt]));
								break;
								default:
									form.append(elt, data[elt]);
							}
						}
					break;
					case 'function':
					break;
					default:
						form.append(elt, data[elt]);
				}
			}
			
			return form;
		}
		
		
		this.init(settings);
	}
	
	fnc.libs['AJAXRequest'] = AJAXRequest;
	
});