var PHPBridge;

fnc.require(['Class', 'AJAXRequest'], function() {
	
	var PHPBridgeClass = function(container) {
		var self = this;
		ClassWithBinds(self);
		
		this.geolocalisation = {};
		
		this.init = function(container) {
			self.url = fnc.getScriptRootPath('fnc.js') + '../php/JSBridge.script.php'
		}
		
		this._request = function(params, success, error) {
			if(typeof success != 'function') { var success = function() {}; }
			if(typeof error != 'function') { var error = function(errorCode, errorMessage) { console.error('Error in PHPBridge : ' + errorCode + ' - ' + errorMessage); }; }
			
			new AJAXRequest({
				url : self.url,
				type : 'get',
				responseType : 'json',
				data: params,
				oncomplete : function(response) {
					switch(response.status) {
						case 'OK':
							success(response.content);
						break;
						case 'ERROR':
							error(response.errorCode, response.errorMessage);
						break;
					}
				}
			});
		}
		
		this.getPage = function(url, success, error) {
			self._request({
				ACTION: 'GET_PAGE',
				URL: url
			}, success, error);
		}
		
		
		this.geolocalisation.getPath = function(params) {
			/*if(typeof params.waypoints == 'undefined') { var waypoints = []; }
			if(typeof mode == 'undefined') { var mode = null; }
			if(typeof decodeCoords == 'undefined') { var decodeCoords = true; }*/
			
			self._request({
				ACTION: 'GEOLOCALISATION/GET_PATH',
				ORIGIN: JSON.stringify(params.origin),
				DESTINATION: JSON.stringify(params.destination),
			}, params.success || null, params.error || null);
		}
		
		this.init();
	}
	
	PHPBridge = new PHPBridgeClass();
	fnc.libs['PHPBridge'] = PHPBridge;
});