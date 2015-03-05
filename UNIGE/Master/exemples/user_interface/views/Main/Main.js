Views.register('Main', 'div', function(self) {
	self.sendQuery = function(query, successCallback, errorCallback) {
		try {
			var data = { 'query' : JSON.stringify(query) };
		} catch (e) {
			debugger;
		}
		var req = new AJAXRequest({
			'url'			: self.APIurl,
			'type'			: 'post',
			'responseType'	: 'json',
			'data'			: data,
			'oncomplete'	: function(response) {
				switch(response.code) {
					case 0:
						successCallback(response.response);
					break;
					default:
						if(typeof errorCallback == 'function') {
							errorCallback(response.code, response.message);
						} else {
							console.warn("[ERROR " + response.code + "] : " + response.message);
						}
					break;
				}
			},
			'onfail'		: function() {
				console.error("failed to load content !");
			}
		});
	}
	
		// has vocation to change
	self.anthenticate_user = function(email, password, callback) {
		self.sendQuery({
			"action"		: "authenticate",
			"parameters"	: {
				"email"		: email,
				"password"	: password
			}
		}, callback);
	}
		
	
	self.APIurl = '../../api/api.php';
	
	self.userView = Views.new('Main/User');
	self.appendChildView(self.userView);
	
	
	setTimeout(function() {
		self.anthenticate_user('test', 'test', function(response) {
			self.userView.loadUser(response.id, response.key);
			
			/*self.sendQuery({
				"id"		: "vkDkYJli/gOzNqeY8Jx3EJk6PTrEg9iOVmxSens4TYQ=",
				"key"		: "affOXUwF0GhkZYsBJ/1BiF7sH64HpfZ0mxp6Mq5fJyU=",
				"action"	: "request_for_a_new_owner",
				"parameters": {
					"user_id": response.id
				}
			}, function(response) {
				//debugger;
			});*/
		});
	}, 100);
});