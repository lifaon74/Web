var ThingBookAPI;
fnc.require(['AJAXRequest'], function() {
	
	var ThingBookAPIConstructor = function() {
		var self = this;
		
		this.init = function() {
			self.apiUrl	= '../../api/api.php';
			self.id		= null;
			self.key	= null;
		}
		
		this.setCredentials = function(id, key) {
			self.id		= id;
			self.key	= key;
		}
		
		this.generateRandomKey = function() {
			var key = "";
			for(var i = 0; i < 32; i++) {
				key += String.fromCharCode(Math.rand(0, 255));
			}
			return btoa(key);
		}
		
		this.sendQuery = function(query, successCallback, errorCallback) {
			var req = new AJAXRequest({
				'url'			: self.apiUrl,
				'type'			: 'post',
				'responseType'	: 'json',
				'data'			: { 'query' : JSON.stringify(query) },
				'oncomplete'	: function(response) {
					switch(response.code) {
						case 0:
							if(typeof successCallback == 'function') {
								successCallback(response.response);
							}
						break;
						default:
							if(typeof errorCallback == 'function') {
								errorCallback(response.code, response.message);
							} else {
								console.error("[ERROR " + response.code + "] : " + response.message);
							}
						break;
					}
				},
				'onfail'		: function() {
					if(typeof errorCallback == 'function') {
						errorCallback(-1, "failed to load content");
					} else {
						console.error("failed to load content !");
					}
				}
			});
		}

		
		this.register_object = function(name, type, successCallback, errorCallback) {
			self.sendQuery(
				{
					"action"		: "register_object",
					"parameters"	: {
						"id"	: self.id,
						"key"	: self.key,
						"name"	: name,
						"type"	: type
					}
				},
				successCallback,
				errorCallback
			);
		};
		
		this.request_for_a_new_owner = function(user_id, successCallback, errorCallback) {
			self.sendQuery(
				{
					"id"			: self.id,
					"key"			: self.key,
					"action"		: "request_for_a_new_owner",
					"parameters"	: {
						"user_id"	: user_id
					}
				},
				successCallback,
				errorCallback
			);
		};
	
	
		this.request_for_a_new_relationship = function(relationship, with_object, successCallback, errorCallback) {
			self.sendQuery(
				{
					"id"			: self.id,
					"key"			: self.key,
					"action"		: "request_for_a_new_relationship",
					"parameters"	: {
						"relationship"	: relationship,
						"with_object"	: with_object
					}
				},
				successCallback,
				errorCallback
			);
		};
	
		this.get_relationships = function(of_object, successCallback, errorCallback) {
			self.sendQuery(
				{
					"id"			: self.id,
					"key"			: self.key,
					"action"		: "get_relationships",
					"parameters"	: {
						"of_object"	: of_object
					}
				},
				successCallback,
				errorCallback
			);
		}
		
		
		this.post_publication = function(to_object, data, successCallback, errorCallback) {
			self.sendQuery(
				{
					"id"			: self.id,
					"key"			: self.key,
					"action"		: "post_publication",
					"parameters"	: {
						"to_object"		: to_object,
						"publication"	: {
							"data"	: data
						}
					}
				},
				successCallback,
				errorCallback
			);
		};
		
		this.get_publications = function(of_object, parameters, successCallback, errorCallback) {
			parameters.of_object = of_object;
			self.sendQuery(
				{
					"id"			: self.id,
					"key"			: self.key,
					"action"		: "get_publications",
					"parameters"	: parameters
				},
				successCallback,
				errorCallback
			);
		};
	
	
		this.get_notifications = function(successCallback, errorCallback) {
			self.sendQuery(
				{
					"id"			: self.id,
					"key"			: self.key,
					"action"		: "get_notifications"
				},
				successCallback,
				errorCallback
			);
		}
		
		this.answer_notification = function(notification_id, parameters, successCallback, errorCallback) {
			parameters.notification_id = notification_id;
			self.sendQuery(
				{
					"id"			: self.id,
					"key"			: self.key,
					"action"		: "answer_notification",
					"parameters"	: parameters
				},
				successCallback,
				errorCallback
			);
		}
		
		
		
		
		
		this.init();
	}
	
	ThingBookAPI = new ThingBookAPIConstructor();
	
	fnc.libReady('ThingBookAPI', ThingBookAPI);
});