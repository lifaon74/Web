function DOM_formatProperty(property) {
	//property[0] = DOM_standardToUpperPropertyName(property[0]);
	var propertyValue = property[1];
	if(typeof propertyValue != 'function') {
		property[1] = function() { return propertyValue; }
	}
}

function DOM_formatProperties(properties) {
	for(var i = 0; i < properties.length; i++) {
		DOM_formatProperty(properties[i]);
	}
}

function DOM_formatRule(rule) {
	var ruleProperties = rule[1];
	if(typeof ruleProperties != 'function') {
		rule[1] = function() { return ruleProperties; }
	}
}

function DOM_standardToUpperPropertyName(propertyName) {
	return propertyName.replace(/\-(\w)/, function(match, $1, offset, string) {
		return $1.toUpperCase();
	});
}

function DOM_upperToStandardPropertyName(propertyName) {
	return propertyName.replace(/([A-Z])/, function(match, $1, offset, string) {
		return "-" + $1.toLowerCase();
	});
}
		
function strings_equals(string_1, string_2) {
	if(string_1.length == string_2.length) {
		for(var i = 0; i < string_1.length; i++) {
			if(string_1[i] != string_2[i]) {
				return false;
			}
		}
		
		return true;
	} else {
		return false;
	}
}


var DOMElement;

fnc.onDocumentReady(function() {
	
	var DOMClass = function() {
		var self = this;
		
		this.__construct = function() {
			self.document			= null;
			self.dynamicCSSenabled	= false;
			
			
			self.document = new DOMElement(document);
			var AllDOMElements = self.find('*');
			for(var i = 0; i < AllDOMElements.length; i++) {
				var properties = AllDOMElements[i].getStyleProperties();
				for(var j = 0; j < properties.length; j++) {
					var property = properties[j];
					AllDOMElements[i].setCSSProperty(property[0], property[1]);
				}
			}
				
			self.cssRules = [];
			//self.enableDynamicCSS();
			self.disableDynamicCSS();
		}
		

		this.getWindowWidth = function() {
			return window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
		}
		
		this.getWindowHeight = function() {
			return window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;
		}
		
		
		this.enableDynamicCSS = function() {
			if(!self.dynamicCSSenabled) {
				self.dynamicCSSenabled = true;
				self.renderLoop();
			}
		}
		
		this.disableDynamicCSS = function() {
			if(self.dynamicCSSenabled) {
				self.dynamicCSSenabled = false;
			}
		}
		
		
		this.createElement = function(elementName) {
			return new DOMElement(document.createElement(elementName));
		}
		
		this.find = function(selectorPath) {
			return self.document.find(selectorPath);
		}
		
		
		this.setCSSRule = function(selectorPath, properties) {
			var rule = [selectorPath, properties];
			DOM_formatRule(rule);
			self.cssRules.push(rule);
		}
		
		
		this.renderLoop = function() {
			var t1 = new Date().getTime();
			
			self.render();
			
			var elapsedTime = new Date().getTime() - t1;
			var timeout = Math.max(elapsedTime, 100);
			if(elapsedTime > 1000) { console.warn("rendering time too high : " + elapsedTime); }
			
			if(self.dynamicCSSenabled) { setTimeout(self.renderLoop, timeout); }
		}
		
		this.render = function() {
			
			var AllDOMElements = self.find('*');
			for(var i = 0; i < AllDOMElements.length; i++) {
				AllDOMElements[i].clearCSSPropertiesFromRules();
			}
			
			for(var i = 0; i < self.cssRules.length; i++) {
				var cssRule = self.cssRules[i];
				var DOMElements = self.getElementFromSelector(cssRule[0]);
				
				for(var k = 0; k < DOMElements.length; k++) {
					var _DOMElement = DOMElements[k];
					var properties = cssRule[1](_DOMElement);
					DOM_formatProperties(properties);
					
					for(var j = 0; j < properties.length; j++) {
						_DOMElement.setCSSPropertyFromRules(properties[j]);
					}
				}
			}
			
			for(var i = 0; i < AllDOMElements.length; i++) {
				AllDOMElements[i].renderCSSProperties();
			}
		}
		
		this.__construct();
	}


	DOMElement = function(domElement) {
		var self = this;
		
		this.__construct = function(domElement) {
			self.domElement						= domElement;
			self.domElement.DOMElement			= self;
			
			self.hidden							= false;
			self.displayStyle 					= null;
			
			self.cssPropertiesFromRules			= [];
			self.cssPropertiesFromRulesIndex	= 0;
			
			self.cssProperties = [];
			self.cssPropertiesIndex = 0;
			
			if(self.domElement != document) {
				self.style = self.getAttribute('style');
				if(self.style == null) { self.style = ""; }
			}
		}
		
		
		this._getElementFromDOM = function(domElement) {
			//if(typeof domElement.DOMElement != 'undefined') {
			if(domElement) {
				if(domElement.DOMElement) {
					return domElement.DOMElement;
				} else {
					return new DOMElement(domElement);
				}
			} else {
				return null;
			}
		}
	
	
		this.bind = function(eventName, callback) {
			self.domElement.addEventListener(eventName, callback, false);
		}
		
		this.unbind = function(eventName, callback) {
			self.domElement.removeEventListener(eventName, callback, false);
		}
		
		
		this.hide = function() {
			var displayStyle = self.getStyleProperty('display');
			
			if(displayStyle != "none") {
				self.displayStyle = displayStyle;
				self.setStyleProperty('display', 'none');
			}
			
			self.hidden = true;
			
			return self;
		}
		
		this.show = function() {
			var displayStyle = self.getStyleProperty('display');
			
			if(displayStyle == "none") {
				if(self.displayStyle) {
					self.setStyleProperty('display', self.displayStyle);
				} else {
					self.removeStyleProperty('display');
				}
			}
			
			self.hidden = false;

			return self;
		}
		
		
			/** Related Elements **/
		this.getParent = function() {
			return self._getElementFromDOM(self.domElement.parentElement);
		}
		
		this.getChildren = function() {
			var children = [];
			for(var i = 0; i < self.domElement.children.length; i++) {
				children[i] = self._getElementFromDOM(self.domElement.children[i]);
			}
			return children;
		}
		
		this.find = function(selectorPath) {
			var DOMElements = [];
			var domElements = self.domElement.querySelectorAll(selectorPath);
			
			for(var i = 0; i < domElements.length; i++) {
				DOMElements[i] = self._getElementFromDOM(domElements[i]);
			}
			
			return DOMElements;
		}
		
		this.getNext = function() {
			return self._getElementFromDOM(self.domElement.nextElementSibling);
		}
		
		this.getPrevious = function() {
			return self._getElementFromDOM(self.domElement.previousElementSibling);
		}
		
		
			/** DOM Structure Modifications **/
		this.get = function() {
			return self.domElement;
		}
		
		this.val = function() {
			if(typeof self.domElement.value != 'undefined') {
				return self.domElement.value;
			} else {
				return null;
			}
		}
		
		this.append = function(domElement) {
			self.domElement.appendChild(domElement.domElement);
			return self;
		}
		
		this.appendTo = function(domElement) {
			domElement.append(self);
			return self;
		}
		
		this.insertBefore = function(domElement) {
			domElement.getParent().domElement.insertBefore(self.domElement, domElement.domElement);
			return self;
		}
		
		this.insertAfter = function(domElement) {
			domElement.getParent().domElement.insertBefore(self.domElement, domElement.domElement.nextSibling);
			return self;
		}
		
		this.detach = function() {
			self.domElement.remove();
			return self;
		}
		
		this.clone = function() {
			return self._getElementFromDOM(self.domElement.cloneNode());
		}
		
		this.replaceWith = function(domElement) {
			domElement.insertBefore(self);
			self.detach();
			return self;
		}
		
		
			/** Content Modifications **/
		this.getInnerHTML = function() {
			return self.domElement.innerHTML;
		}
		
		this.setInnerHTML = function(html) {
			self.domElement.innerHTML = html;
			return self;
		}
		
		
			/** Attributes Modifications **/
		this.getAttributes = function() {
			var attributes = [];
			for(var i = 0; i < self.domElement.attributes.length; i++) {
				var attribute = self.domElement.attributes[i];
				attributes[i] = {
					'name': attribute.name,
					'value': attribute.value
				};
			}
			return attributes;
		}
		
		this.setAttribute = function(attributeName, attributeValue) {
			if(attributeValue == "") {
				self.removeAttribute(attributeName);
			} else {
				self.domElement.setAttribute(attributeName, attributeValue);
			}
			
			return self;
		}
		
		this.getAttribute = function(attributeName) {
			return self.domElement.getAttribute(attributeName);
		}
		
		this.removeAttribute = function(attributeName) {
			self.domElement.removeAttribute(attributeName);
			return self;
		}
		
		
			/** Style Modifications **/
		this.setStyleProperties = function(properties) {
			DOM_formatProperties(properties);
			var styleString = "";
			
			for(var i = 0; i < properties.length; i++) {
				var property = properties[i];
				styleString += property[0] + ": " + property[1]() + ";";
			}
			
			self.setAttribute('style', styleString);
			
			return self;
		}
		
		this.getStyleProperties = function() {
			var properties = [];
			var propertiesIndex = 0;
			var styleAttribute = self.getAttribute('style');
			
			if(styleAttribute !== null) {
				var splitedProperties = styleAttribute.split(';');
			
				for(var i = 0; i < splitedProperties.length; i++) {
					var property = splitedProperties[i].trim();
					if(property != "") {
						var splitedProperty = property.split(':');
						properties[propertiesIndex++] = [splitedProperty[0].trim(), splitedProperty[1].trim()];
					}
				}
			}
			
			return properties;
		}
		
		this.setStyleProperty = function(propertyName, propertyValue) {
			if(typeof propertyValue == 'function') { propertyValue = propertyValue(self); }
			self.domElement.style.setProperty(propertyName, propertyValue);
			return self;
		}
		
		this.getStyleProperty = function(propertyName) {
			return self.domElement.style.getPropertyValue(propertyName);
		}
		
		this.removeStyleProperty = function(propertyName) {
			self.domElement.style.removeProperty(propertyName);
			return self;
		}
		
			
			/** Class Modifications **/
		this.setClasses = function(classes) {
			var classString = "";
			
			var firstClass = false;
			for(var i = 0; i < classes.length; i++) {
				var className = classes[i].trim();
				if(className != "") {
					if(firstClass) {
						classString += " ";
					} else {				
						firstClass = true;
					}
					
					classString += className;
				}
			}
			
			self.setAttribute('class', classString);
			
			return self;
		}
			
		this.getClasses = function() {
			var classAttribute = self.getAttribute('class');
			if(classAttribute !== null) {
				var classes = classAttribute.split(' ');
				for(var i = 0; i < classes.length; i++) {
					var className = classes[i].trim();
					if(className == "") { // match(/^[\s]*$/g)
						classes.splice(i, 1);
						i--;
					} else {
						classes[i] = className;
					}
				}
			} else {
				var classes = [];
			}
			
			return classes;
		}
		
		this.addClass = function(className) {
			var classes = self.getClasses();
			if(classes.indexOf(className) < 0) {
				classes.push(className);
				self.setClasses(classes);
			}
			
			return self;
		}
		
		this.removeClass = function(className) {
			var classes = self.getClasses();
			var index = classes.indexOf(className);
			if(index >= 0) {
				classes.splice(index, 1);
				self.setClasses(classes);
			}
			
			return self;
		}
		
		
			/** Extended CSS **/
		this.clearCSSPropertiesFromRules = function() {
			self.cssPropertiesFromRules = [];
			self.cssPropertiesFromRulesIndex = 0;
		}
		
		this.setCSSPropertyFromRules = function(property) {
			self.cssPropertiesFromRules[self.cssPropertiesFromRulesIndex++] = property;
		}
		
		this.renderCSSProperties = function() {
			var styleString = "";
			
			for(var i = 0; i < self.cssPropertiesFromRulesIndex; i++) {
				var property = self.cssPropertiesFromRules[i];
				//self.domElement.style[property[0]] = property[1](self);
				styleString += property[0] + ": " + property[1](self) + ";";
			}
			
			for(var i = 0; i < self.cssPropertiesIndex; i++) {
				var property = self.cssProperties[i];
				//self.domElement.style[property[0]] = property[1](self);
				styleString += property[0] + ": " + property[1](self) + ";";
			}
			
			if(!strings_equals(styleString, self.style)) {
				self.style = styleString;
				self.setAttribute('style', styleString);
			}
		}
		
		this.cssPropertyIndexOf = function(propertyName, cssProperties) {
			for(var i = 0; i < cssProperties.length; i++) {
				if(cssProperties[i][0] == propertyName) {
					return i;
				}
			}
			return -1;
		}
		
		this.setCSSProperty = function(propertyName, propertyValue) {
			var property = [propertyName, propertyValue];
			DOM_formatProperty(property);
			
			var index = self.cssPropertyIndexOf(propertyName, self.cssProperties);
			if(index >= 0) {
				self.cssProperties[index] = property;
			} else {
				self.cssProperties[self.cssPropertiesIndex++] = property;
			}
		}
		
		this.getCSSProperty = function(propertyName) {
			var index = self.cssPropertyIndexOf(propertyName, self.cssProperties);
			if(index >= 0) {
				return self.cssProperties[index][1];
			} else {
				var index = self.cssPropertyIndexOf(propertyName, self.cssPropertiesFromRules);
				if(index >= 0) {
					return self.cssPropertiesFromRules[index][1];
				} else {
					return null;
				}
			}
		}
		
		this.removeCSSProperty = function(propertyName) {
			var index = self.cssPropertyIndexOf(propertyName);
			if(index >= 0) {
				self.cssProperties[index].splice(index, 1);
				self.cssPropertiesIndex--;
			}
		}
		
		
		this.getComputedStyleProperty = function(propertyName){
			if(document.defaultView && document.defaultView.getComputedStyle){
				return document.defaultView.getComputedStyle(self.domElement, null).getPropertyValue(propertyName);
			} else if(self.domElement.currentStyle){
				return self.domElement.currentStyle[DOM_standardToUpperPropertyName(propertyName)];
			} else {
				return null;
			}
		}
		
		
		this.__construct(domElement);
	}

	DOM = new DOMClass();

	fnc.libs['DOM'] = DOM;
});

/*window.addEventListener('load', function() {

	DOM.getElementFromSelector('*');
	
	html = DOM.getElementFromSelector('html')[0];
	menu = DOM.getElementFromSelector('.menu')[0];
	content = DOM.getElementFromSelector('.content')[0];
	
	//menu.setCSSProperty('background-color', function() { return 'green'; });
	//menu.setCSSProperty('background-color', 'cyan');
	
	
	DOM.setCSSRule('html, body', [
		['width', '100%'],
		['height', '100%'],
		['margin', '0px'],
		['padding', '0px']
	]);
	
	DOM.setCSSRule('.menu, .content', [
		['position', 'relative'],
		['width', function() { return (DOM.getWindowWidth() / 2) + 'px'; }],
		['height', function() { return (DOM.getWindowWidth() / 8) + 'px'; }],
		['font-size', function() { return (DOM.getWindowWidth() / 10) + 'px'; }],
		['line-height', function() { return (DOM.getWindowWidth() / 8) + 'px'; }]
	]);
	
	DOM.setCSSRule('.menu', [
		['background-color', 'red']
	]);
	
	
	DOM.setCSSRule('.content', [
		['background-color', 'blue']
	]);
	
	
	DOM.setCSSRule('.centred', function(element) {
		var parent = element.getParent();
		var parentWidth = parseInt(parent.getCSSProperty('width')());
		var parentHeight = parseInt(parent.getCSSProperty('height')());
		
		return [
			['position', 'absolute'],
			['left', (parentWidth / 4) + 'px'],
			['top', (parentHeight / 4) + 'px'],
			['width', (parentWidth / 2) + 'px'],
			['height', (parentHeight / 2) + 'px'],
			['background-color', 'green']
		];
	});*/
	
	
	/*DOM.setCSSRule('.menu0', [
		['width', function() { return (DOM.getWindowWidth() / 2) + 'px'; }],
		['height', function() { return (DOM.getWindowWidth() / 8) + 'px'; }],
		['background-color', 'red']
	]);*/
	
	
	
	/*for(var i = 0; i < 100; i++) {
		var properties = [];
		for(var k = 0; k < 100; k++) {
			properties[k] = ['prop' + k, k + 'px'];
		}
		DOM.setCSSRule('.menu' + i, properties);
	}*/
	
	
	//menu.setStyleProperty('width', '300px');
	/*menu.setStyleProperty('height', '200px');
	menu.setStyleProperty('backgroundColor', 'red');*/
	
	/*var a = [];
	var length = 10000000;
	for(var i = 0; i < length; i++) {
		if((i % 2) == 0) {
			a[i] = function() { return '100px'; }
		} else {
			a[i] = '200px';
		}
	}
	
	var str = "";
	var t1 = new Date().getTime();
	
	//DOM.render();
	for(var i = 0; i < length; i++) {
		var attr = a[i];
		if(typeof attr == 'function') { attr = attr(); }
		str += a[i];
	}
	
	var t2 = new Date().getTime();
	console.log(t2 - t1);*/
	
//}, false);