var Class;
var ClassWithBinds;

fnc.require(['Math'], function() {

	Class = function(_class) {
		if(typeof _class.extend != 'function') {
			_class.extend = function(extendedWithClass, args) {
				if(typeof args == 'undefined') {
					var args = [];
				} else if(typeof args != 'object' || typeof args.length == 'undefined') {
					args = [args];
				}
				
				extendedWithClass.apply(_class, args);
			}
			
			_class.className = function() {
				var constructor = _class.constructor.toString();
				
					// for IE
				var results = /^\[.* (.*)\]$/.exec(constructor);
				if(results && results.length > 1) {
					return results[1];
				} else { // for others
					var results = /function (.{1,})\(/.exec(constructor);
					if(results && results.length > 1) {
						return results[1];
					}
				}
				
				return "";
			}
		}
	}

	ClassWithBinds = function(_class) {
		Class(_class);
		
		if(typeof _class._binds != 'object') {
			_class._binds = [];
			//_class._autoTriggers = [];
			
			
			var trigger = function(eventName, params) {
				if(typeof _class._binds[eventName] != 'undefined') {
					for(var i = 0; i < _class._binds[eventName].length; i++) {
						_class._binds[eventName][i].callback.apply(this, params);
					}
				}
				
				if(typeof _class['on' + eventName] == 'function') {
					_class['on' + eventName].apply(this, params);
				}
			}
			
			
				// bind
			_class.bind = function(eventName, callback, id) {
				if(typeof callback != 'function') { return; }
				if(typeof id == 'undefined') { var id = Math.uniqid(); }
				
				if(typeof _class._binds[eventName] == 'undefined') {
					_class._binds[eventName] = [];
				}
				
				_class._binds[eventName].push({
					'id': id,
					'callback': callback
				});
				
				if(eventName != '_onbind') {
					_class.trigger('_onbind', [eventName, callback]);
				}
				
				/*if(typeof _class._autoTriggers[eventName] != 'undefined') {
					trigger(eventName, _class._autoTriggers[eventName]);
				}*/
				
				return id;
			}
				
				// unbind
			_class.unbind = function(eventName, id) {
				if(typeof _class._binds[eventName] != 'undefined') {
					if(typeof id == 'undefined') {
						delete _class._binds[eventName];
					} else {
						for(var i = 0; i < _class._binds[eventName].length; i++) {
							if(_class._binds[eventName][i].id == id) {
								_class._binds[eventName].splice(i, 1);
							}
						}
					}
				}
			}
			
				// trigger
			_class.trigger = function(eventName, params) {
			
				if(typeof params == 'undefined') {
					var params =  [];
				}
				
				/*if(typeof _class._autoTriggers[eventName] != 'undefined') {
					_class._autoTriggers[eventName] = params;
				}*/
				
				trigger(eventName, params);
			}
				
			
				// autoTrigger
			/*_class.autoTrigger = function(eventName, autoTrigger) {
				if(typeof autoTrigger == 'undefined') {
					var autoTrigger =  true;
				}
				
				if(autoTrigger) {
					_class._autoTriggers[eventName] = [];
				} else {
					delete _class._autoTriggers[eventName];
				}
			}*/
			
			
		}
	}

	fnc.libs['Class'] = Class;
});