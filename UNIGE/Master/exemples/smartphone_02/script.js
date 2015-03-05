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
		console.log("register_object");
		core.createRelationship();
	}
	
	core.createRelationship = function() {
		ThingBookAPI.get_relationships(
			ThingBookAPI.id,
			function(response) {
				if(typeof response.relationships[core.friendId] != "undefined") {
					core.relationExists = true;
					console.log("objects are friends");
					
					setInterval(function() {
						core.updateColor();
					}, 1000);
		
				} else {
					core.relationExists	= false;
					ThingBookAPI.request_for_a_new_relationship(
						"friend",
						core.friendId,
						function(response) {
							console.log("request_for_a_new_relationship");
							setTimeout(core.createRelationship, 1000);
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
		);
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
				
			},
			function(code, message) {
				console.warn(code, message);
			}
		);
	}
	
	core.friendId		= "JKlX1OMExkyo4qksqIqUUvYGIrGGQHZQKI6FOR8PJ00=";

	core.loadCredentials();
	
	
	ThingBookAPI.register_object("smartphone_02", "smartphone", core.objectRegistered, core.objectRegistered);
});
