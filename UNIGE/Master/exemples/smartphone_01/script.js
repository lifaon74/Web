var core = {};
fnc.registerLib('ThingBookAPI', '../ThingBookAPI.js');

fnc.require(['ThingBookAPI', 'Cookies'], function() {
	core.prefix = "smartphone_01_";

	core.loadCredentials = function() {
		//var id	= Cookies.get(core.prefix + 'id');
		var id	= "JKlX1OMExkyo4qksqIqUUvYGIrGGQHZQKI6FOR8PJ00=";
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
	
	core.displayFeedback = function(html) {
		core.$feedbackContainer.html(html);
		console.log(html);
		
		if(core.timer !== null) {
			clearTimeout(core.timer);
			core.timer = null;
		}
		
		core.$feedbackContainer.fadeIn(500, function() {
			core.timer = setTimeout(function() {
				core.$feedbackContainer.fadeOut(500);
			}, 2000);
		});
		
	}

	core.update = function() {
		if(!core.updating) {
			core.updating = true;
			var t1 = new Date().getTime();
			ThingBookAPI.post_publication(
				ThingBookAPI.id,
				[
					{
						"value"			: parseInt(core.$slider.val()),
						"type"			: "number",
						"tags"			: ["range"],
						"relationships"	: ["friend"]
					},
					{
						"value"			: parseInt(core.$switch.val()),
						"type"			: "number",
						"tags"			: ["switch"],
						"relationships"	: ["public"]
					}
				],
				function(response) {
					console.log(response);
					var t2 = new Date().getTime();
					console.log("update took " + (t2 - t1) + "ms");
					core.updating = false;
					core.endOfUpdate();
				},
				function(code, message) {
					core.displayFeedback("[ERREUR] : while posting");
					console.warn(code, message);
				}
			);
		}
	}
	
	core.endOfUpdate = function() {
		if(core.sendPublications) {
			setTimeout(function() {
				core.update();
			}, core.updateTime);
		}
	}
	
	core.loadCredentials();
	
	core.updateTime	= 1;
	core.ownerId	= "e0hqhHnxFDniKfOA/OlKweZVkW/ALhhhHQQVJCJa+wc=";
	core.friendId	= "zjFMRIZw3hecoss7EDkbKa0bO8w3xyAK16SeWkS8EDc=";
	
	core.$feedbackContainer = $('#feedback');
	core.timer = null;
	
	
	core.$registerButton = $('#registerButton');
	core.$registerButton.on("click", function() {
		ThingBookAPI.register_object(
			"smartphone_01",
			"smartphone",
			function(response) {
				core.displayFeedback("L'objet <span class=\"id\">" + response.id + "</span> a bien été enregistré sur ThingBook");
			},
			function(code, message) {
				core.displayFeedback("[ERREUR] : L'objet est déjà enregistré sur ThingBook");
				console.warn(code, message);
			}
		);
	});
	
	core.$askForNewOwnerButton = $('#askForNewOwnerButton');
	core.$askForNewOwnerButton.on("click", function() {
		ThingBookAPI.request_for_a_new_owner(
			core.ownerId,
			function(response) {
				core.displayFeedback("L'utilisateur <span class=\"id\">" + core.ownerId + "</span> a bien reçu une demande pour devenir le nouveau propriétaire");
			},
			function(code, message) {
				core.displayFeedback("[ERREUR] : L'objet est possédé par cet utilisateur");
				console.warn(code, message);
			}
		);
	});
	
	core.$createRelationshipButton = $('#createRelationshipButton');
	core.$createRelationshipButton.on("click", function() {
		ThingBookAPI.get_notifications(
			function(response) {
				for(var i = 0; i < response.notifications.length; i++) {
					var notification = response.notifications[i];
					switch(notification.type) {
						case "request_for_a_new_relationship":
							ThingBookAPI.answer_notification(
								notification.id,
								{ "answer": true },
								fnc.closure(function(response, notification) {
									core.displayFeedback("Création d'une relation \"" + notification.parameters.relationship_name + "\" avec <span class=\"id\">" + notification.parameters.from_object + "</span>");
								}, notification),
								function(code, message) {
									core.displayFeedback("[ERREUR] : ?");
									console.warn(code, message);
								}
							);
						break;
					}
				}
			},
			function(code, message) {
				core.displayFeedback("[ERREUR] : ?");
				console.warn(code, message);
			}
		);
	});
	
	
	core.sendPublications = false;
	core.updating = false;
	core.$startStopPublicationsButton = $('#startStopPublicationsButton');
	core.$startStopPublicationsButton.on("click", function() {
		if(core.sendPublications) {
			core.$startStopPublicationsButton.val("Lancer les publications").button("refresh");
			core.sendPublications = false;
		} else {
			core.$startStopPublicationsButton.val("Stopper les publications").button("refresh");
			core.sendPublications = true;
			core.update();
		}
	});
	
	
	core.$slider = $('#slider');
	core.$switch = $('#switch');
});
