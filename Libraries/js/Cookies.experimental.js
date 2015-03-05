var Cookies;

fnc.require(['RegEx', 'String', 'DOM'], function() {
	
	CookiesConstructor = function() {
		var self = this;
		
		this.init = function() {
			//self._initFontDetection();
		}
		
		this.set = function(key, value) {
			self._createCookie(key, value, 365 * 24 * 60 * 60 * 1000);
		}
		
		this.get = function(key) {
			return self._getCookie(key);
		}
		
		
		/**
			HTML Cookies
		**/
		
			this._createCookie = function(key, value, timeout, path) {
				if(typeof timeout == "undefined") { var timeout = -1; }
				if(typeof path == "undefined") { var path = "/"; }
				
				var date = new Date();
				date.setTime(date.getTime() + timeout);
				var expires = "; expires=" + date.toGMTString();
				
				var cookiesString = "";
				cookiesString += key + "=" + value.escape() + "; ";
				cookiesString += "expires" + "=" + date.toGMTString() + "; ";
				cookiesString += "path" + "=" + path;
				
				document.cookie = cookiesString;
			}

			this._getCookies = function() {
				var cookies = [];
				
				var splittedCookies = RegEx.split(/;/, document.cookie);
				for(var i = 0; i < splittedCookies.length; i++) {
					var match = RegEx.match(new RegExp('([^=]+)=([^;]+)', 'g'), splittedCookies[i].trim());
					if(match) {
						cookies[match.variables[0]] = match.variables[1].unescape();
						cookies.length++;
					}
				}
				
				return cookies;
			}
			
			this._getCookie = function(key) {
				var cookies = self._getCookies();
				if(typeof cookies[key] == "undefined") {
					return null;
				} else {
					return cookies[key];
				}
				
				/*var match = RegEx.match(new RegExp(key + '=([^;]+)', 'g'), document.cookie);
				if(match === null) {
					return null;
				} else {
					return match.variables[0].unescape() ;
				}*/
			}
			
			this._removeCookies = function() {
				var cookies = self._getCookies();
				for(key in cookies) {
					self._removeCookie(key);
				}
			}
			
			this._removeCookie = function(key) {
				self._createCookie(key, "", -1);
			}

		
		
		/**
			localStorage
		**/
		
			this._localStorage_set = function(key, value) {
				localStorage.setItem(key, value);
			}
			
			this._localStorage_getAll = function() {
				var cookies = [];
	
				for(var i = 0; i < localStorage.length; i++) {
					var key = localStorage.key(i);
					cookies[key] = self._localStorage_get(key);
					cookies.length++;
				}
				
				return cookies;
			}
			
			this._localStorage_get = function(key) {
				return localStorage.getItem(key);
			}
			
			this._localStorage_removeAll = function() {
				localStorage.clear();
			}
			
			this._localStorage_remove = function(key) {
				localStorage.removeItem(key);
			}
			
		/**
			css webhistory
		**/
			this._getStyle = function(domElement, cssRule){
				if(document.defaultView && document.defaultView.getComputedStyle){
					return document.defaultView.getComputedStyle(domElement, null).getPropertyValue(cssRule);
				} else if(domElement.currentStyle){
					cssRule = cssRule.replace(/\-(\w)/g, function (strMatch, p1){
						return p1.toUpperCase();
					});
					return domElement.currentStyle[cssRule];
				} else {
					return null;
				}
			}
		
		
		/**
			fingerprint
		**/
		
			this.fingerprint = function() {
				var fingerprint = {
					"screenWidth"			: screen.width,
					"screenHeight"			: screen.height,
					"screenAvailableWidth"	: screen.availWidth,
					"screenAvailableHeight"	: screen.availHeight,
					"screenAvailableLeft"	: screen.availLeft,
					"screenAvailableTop"	: screen.availTop,
					"screenColorDepth"		: screen.colorDepth,
					"screenPixelDepth"		: screen.pixelDepth,
					
					
					"windowInnerWidth"		: window.innerWidth,
					"windowInnerHeight"		: window.innerHeight,
					"windowOuterWidth"		: window.outerWidth,
					"windowOuterHeight"		: window.outerHeight,
					
					"navigatorPlatform"		: navigator.platform,
					"navigatorLanguage"		: navigator.language,
					"navigatorPlugins"		: navigator.plugins,
					"navigatorCodeName"		: navigator.appCodeName,
					"navigatorName"			: navigator.appName,
					"navigatorVersion"		: navigator.appVersion,
					"navigatorProduct"		: navigator.product,
					"navigatorUserAgent"	: navigator.userAgent,
					"navigatorVendor"		: navigator.vendor,
					
					"availablesFonts"		: self.getAvailablesFonts()
					
				};
				
				return fingerprint;
			}
			
			this._initFontDetection = function() {
				self.font = {
					'container'		: DOM.createElement('span'),
					'defaultWidth'	: 0,
					'defaultHeight'	: 0,
					'list'			: [
							// OS-X Core
						'American Typewriter',
						'Gill Sans',
						'Hoefler Text',
						'Marker Felt', 
						'Chalkboard',
						'Helvetica',
						'Times New Roman',
						'Futura',

						'Courier New',

						'Jazz LET',
						'Santa Fe LET',
						'Savoye LET',

						'Monaco',

						'Skia',
						
						'Snell Roundhand',
						'Apple Chancery',
						
						'Zapfino',
						
						
							// iphone
						'American Typewriter Condensed',
						'Arial Rounded MT Bold',

						
						
							// added
						'Sketch Rockwell',
						'Trajan',
						"Univers CE 55 Medium",
						

							// vista?
						'Cambria',
						'Book antiqua',
						'Century gothic',
						'Century',
						'Corbel',


						'Franklin Gothic Medium',


						'Andale Mono', 
						'Arial Black', 
						'Arial Narrow', 
						'Arial',
						'Ayuthaya', 
						'Bandy',
						'Bank Gothic',
						'Baskerville', 
						'Big Caslon', 
						'Comic Sans',
						'Cochin',
						'Geneva',
						'Georgia',
						'Impact',
						'Krungthep', 
						'Minion Pro', 
						'Nadeem', 
						'Papyrus',
						'PetitaBold',
						'Styllo',
						'Synchro LET',
						'Tahoma',
						'Times',
						'Trebuchet MS', 
						'Verdana',
						
						'Mona Lisa Solid ITC TT',
						'Palatino',
						
						
						'Centaur',
						'Jenson',
						'Bembo',
						'Adobe Garamond',
						'Minion',
						'Times New Roman',
						'Mrs Eaves',
						'Bauer Bodoni',
						'Didot',
						'Clarendon',
						'Rockwell',
						'Serifa',
						'Franklin Gothic',
						'News Gothic',
						'Helvetica Neue',
						'Univers',
						'Fruitger',
						'Copperplate Gothic',
						'BlairMdITC TT',
						
							// from added.txt requests
						'CALIBRI', // 98
						'HELV', // 97
						'COURIER', // 86
						'COMIC SANS MS', // 63
						'GARAMOND', // 60
						// 'WINGDINGS', // 52
						'GOTHAM', // 50
						'MYRIAD PRO', // 41
						// 'WEBDINGS', // 33
						'CONSOLAS', // 26
						'DIN', // 21
						'LUCIDA SANS', // 19
						// 'SYMBOL', // 17
						'OPTIMA', // 17
						'FRUTIGER', // 16
						'BAUHAUS 93', // 15
						'CHILLER', // 15
						'TRAJAN PRO', // 14
						'SCRIPT', // 14
						'LATHA', // 13
						'ARNO PRO', // 13
						'BOOKMAN OLD STYLE', // 13
						'DELICIOUS', // 12
						'SEGOE UI', // 12
						'ALGERIAN', // 12
						'AVENIR', // 12
						'LUCIDA CONSOLE', // 12
						'PALATINO LINOTYPE', // 11
						'BELL MT', // 10
						'ADOBE CASLON PRO', // 10
						'LUCIDA GRANDE', // 10
						'STENCIL', // 10
						'ARIAL', // 10
						'MUSEO', // 9
						'ARCHER', // 9
						'CANDARA', // 9
						'CURLZ MT', // 9
						'KARTIKA', // 8
						'EUROSTILE', // 8
						'TUNGA', // 8
						'MONO', // 8
						'SCRIPTINA', // 8
						'BATANG', // 8
						'GILL SANS MT', // 8
						'AGENCY FB', // 8
						'BROADWAY', // 7
						'INCONSOLATA', // 7
						'MONOTYPE CORSIVA', // 7
						'PERPETUA', // 7
						'JOKERMAN', // 7
						'FONTIN', // 7
						'SYSTEM', // 7
						// 'ZAPF DINGBATS', // 7
						'CONSTANTIA', // 7
						'ADOBE GARAMOND PRO', // 6
						'ELEPHANT', // 6
						'SILKSCREEN', // 6
						'GAUTAMI', // 6
						'PLAYBILL', // 6
						'GOUDY OLD STYLE', // 6
						'MAGNETO', // 6
						'VRINDA', // 6
						'WHITNEY', // 6
						'MYRIAD', // 6
						'CASTELLAR', // 6
						'INTERSTATE', // 6
						'MANGAL', // 6
						'BLACKADDER ITC', // 6
						'FORTE', // 6
						'EDWARDIAN SCRIPT ITC', // 6
						'NEVIS', // 6
						'GOTHAM BOLD', // 6
						'HARRINGTON', // 6
						'OSAKA', // 6
						
						'PRINCETOWN LET',
						
							// ios 5
						"Academy Engraved LET",
						"Apple Color Emoji",
						'Apple SD Gothic Neo',
						"Arial Hebrew",
						'Bangla Sangam MN',
						'Bodoni 72',
						'Bodoni 72 Oldstyle',
						'Bodoni 72 Smallcaps',
						'Bradley Hand',
						'Chalkboard SE',
						'Chalkduster',
						'Copperplate',
						'DB LCD Temp',
						'Devanagari Sangam MN',
						'Euphemia UCAS',
						'Geeza Pro',
						'Gujarati Sangam MN',
						'Gurmukhi MN',
						'Heiti SC',
						'Heiti TC',
						'Hiragino Kaku Gothic ProN',
						'Hiragino Mincho ProN',
						'Kailasa',
						'Kannada Sangam MN',
						'Malayalam Sangam MN',
						'Marion',
						'Marker Felt',
						'Noteworthy',
						'Oriya Sangam MN',
						'Party LET',
						'Sinhala Sangam MN',
						'Tamil Sangam MN',
						'Telugu Sangam MN',
						'Thonburi',
						'Times New Roman'
					]
				};

				self.font.container.setStyleProperties([
					['position', 'fixed'],
					['left', '-1000px'],
					['top', '-1000px']
				]);
				self.font.container.setInnerHTML("font test");
				
				var bodyElement = new DOMElement(document.body);
				bodyElement.append(self.font.container);
				
				//self.font.container.setStyleProperty('font-family', 'Sans Serif');
				
				self.font.defaultWidth = self.font.container.domElement.offsetWidth;
				self.font.defaultHeight = self.font.container.domElement.offsetHeight;
			}
			
			this.getAvailablesFonts = function() {
				var availablesFonts = [];
				for(var i = 0; i < self.font.list.length; i++) {
					var font = self.font.list[i];
					if(!self.fontAbsent(font)) {
						availablesFonts.push(font);
					}
				}
				return availablesFonts;
			}
			
			this.fontAbsent = function(font) { // COMIC SANS MS
				self.font.container.setStyleProperty('font-family', font);
				
				return 	(self.font.container.domElement.offsetWidth == self.font.defaultWidth) &&
						(self.font.container.domElement.offsetHeight == self.font.defaultHeight);
			}


		this.init();
	}
	
	Cookies = new CookiesConstructor();
	fnc.libs['Cookies'] = Cookies;
});