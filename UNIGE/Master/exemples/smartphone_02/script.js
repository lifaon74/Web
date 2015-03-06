var core = {};
fnc.registerLib('ThingBookAPI', '../ThingBookAPI.js');

fnc.require(['jQuery', 'ThingBookAPI', 'Cookies'], function() {
	
	core.loadCredentials = function() {
		var id	= Cookies.get(core.prefix + 'id');
		var key	= Cookies.get(core.prefix + 'key');

		if(id === null) {
			id = ThingBookAPI.generateRandomKey();
			Cookies.set(core.prefix + 'id', id);
		}
		
		if(key === null) {
			key = ThingBookAPI.generateRandomKey();
			Cookies.set(core.prefix + 'key', key);
		}
		
		ThingBookAPI.setCredentials(id, key);
	}

	core.objectRegistered = function() {
		console.log("object is registered");
		core.waitFriendship();
	}
	
	core.waitFriendship = function() {
		core.is_friend(function(is_friend) {
			if(is_friend) {
			core.updateColor();
			} else {
				core.createRelationship(function() {
					setTimeout(core.waitFriendship, core.updateTime);
				});
			}
		});
	}
	
	core.is_friend = function(callback) {
		ThingBookAPI.get_relationships(
			ThingBookAPI.id,
			function(response) {
				if(typeof response.relationships[core.friendId] != "undefined") {
					console.log("objects are friends");
					callback(true);
				} else {
					console.log("objects are not friends");
					callback(false);
				}
			},
			function(code, message) {
				console.warn(code, message);
				callback(false);
			}
		);
	}
	
	core.createRelationship = function(callback) {
		
		/*ThingBookAPI.get_relationships(
			ThingBookAPI.id,
			function(response) {
				if(typeof response.relationships[core.friendId] != "undefined") {
					core.relationExists = true;
					console.log("objects are friends");
					
					core.updateColor();
				} else {
					core.relationExists	= false;
					ThingBookAPI.request_for_a_new_relationship(
						"friend",
						core.friendId,
						function(response) {
							console.log("request_for_a_new_relationship");
						},
						function(code, message) {
							console.warn(code, message);
						}
					);
				}
			},
			function(code, message) {
				console.warn(code, message);
			}
		);*/
		
		if(core.askedAFriendship) {
			callback();
		} else {
			core.askedAFriendship = true;
			ThingBookAPI.request_for_a_new_relationship(
				"friend",
				core.friendId,
				function(response) {
					console.log("request_for_a_new_relationship");
					callback();
				},
				function(code, message) {
					console.warn(code, message);
					callback();
				}
			);
		}
	}
	
	core.updateColor = function() {
		ThingBookAPI.get_publications(
			core.friendId,
			{ "limit": 1 },
			function(response) {
				if(response.publications.length > 0) {
					var publication = response.publications[0];
					for(var i = 0; i < publication.data.length; i++) {
						var data = publication.data[i];
						if(typeof data.value != 'undefined') {
							for(var j = 0; j < data.tags.length; j++) {
								var tag = data.tags[j];
								if(tag == "range") {
									console.log(data.value);
									var r = g = b = data.value;
									document.body.style.backgroundColor = "rgb(" + r + ", " + g + ", " + b + ")";
								}
							}
						}
					}
				}
				
				setTimeout(core.updateColor, core.updateTime);
			},
			function(code, message) {
				console.warn(code, message);
			}
		);
	}
	
	core.updateTime	= 1;
	core.friendId		= "JKlX1OMExkyo4qksqIqUUvYGIrGGQHZQKI6FOR8PJ00=";
	core.askedAFriendship = false;
	
	core.loadCredentials();
	
	self.started = false;
	$(document.body).on('click', function() {
		if(!self.started) {
			self.started = true;
			console.log("start");
			ThingBookAPI.register_object("smartphone_02", "smartphone", core.objectRegistered, core.objectRegistered);
		}
	});
});
