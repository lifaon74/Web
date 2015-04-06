var core = {};
fnc.registerLib('ThingBookAPI', '../ThingBookAPI.js');

fnc.require(['jQuery', 'ThingBookAPI', 'Cookies'], function() {
	
	
	core.createAccounts = function() {
		var t1 = new Date().getTime();
		
		core._createAccount(0, core.size, function() {
			var t2 = new Date().getTime();
			console.log("createAccounts took " + (t2 - t1) + "ms");
			
			core.createRelationships();
			core.postPublications(100);
		});	
	}
		
		core._createAccount = function(i, max, endCallback) {
			if(i < max) {
				var account =  {
					"id"	: ThingBookAPI.generateRandomKey(),
					"key"	: ThingBookAPI.generateRandomKey()
				};
				
				core.accounts[i] = account;
				
				ThingBookAPI.setCredentials(account.id, account.key);
				ThingBookAPI.register_object("smartphone_" + i, "smartphone", function() {
					core._createAccount(i + 1, max, endCallback);
				});
			} else {
				endCallback();
			}
		}
	
	
	core.createRelationships = function() {
		var t1 = new Date().getTime();
		
		core._createRelationships(0, 3, function() {
			var t2 = new Date().getTime();
			console.log("createRelationships took " + (t2 - t1) + "ms");
		});
	}
	
		core._createRelationships = function(i, max, endCallback) {
			if(i < max) {
				core._requestForANewRelationship(0, core.size, i + 1, function() {
					core._createRelationships(i + 1, max, endCallback);
				});
			} else {
				core._getNotifications(0, core.size, endCallback);
			}
		}
	
		core._requestForANewRelationship = function(i, max, step, endCallback) {
			if(i < max) {
				var account		= core.accounts[i];
				var j			= i + step;
				
				var callback = function() {
					core._requestForANewRelationship(i + 1, max, step, endCallback);
				}
				
				if((j < core.accounts.length) & (j >= 0)) {
					var withAccount	= core.accounts[i + step];
					
					ThingBookAPI.setCredentials(account.id, account.key);
					ThingBookAPI.request_for_a_new_relationship("friend",withAccount.id, callback);
				} else {
					callback();
				}
			} else {
				endCallback();
			}
		}
		
		core._getNotifications = function(i, max, endCallback) {
			if(i < max) {
				var account =  core.accounts[i];
				ThingBookAPI.setCredentials(account.id, account.key);
				ThingBookAPI.get_notifications(
					function(response) {
						for(var j = 0; j < response.notifications.length; j++) {
							var notification = response.notifications[j];
							switch(notification.type) {
								case "request_for_a_new_relationship":
									ThingBookAPI.answer_notification(
										notification.id,
										{ "answer": true }
									);
								break;
							}
						}
						
						core._getNotifications(i + 1, max, endCallback);
					}
				);
			} else {
				endCallback();
			}
		}

	
	
	core.postPublications = function(size) {
		var t1 = new Date().getTime();
		
		var account =  core.accounts[0];
		ThingBookAPI.setCredentials(account.id, account.key);
				
		core._postPublication(0, size, function() {
			var t2 = new Date().getTime();
			console.log("postPublications took " + (t2 - t1) + "ms");
		});
	}
	
		core._postPublication = function(i, max, endCallback) {
			if(i < max) {
				ThingBookAPI.post_publication(
					ThingBookAPI.id,
					[
						{
							"value"			: i,
							"type"			: "number",
							"tags"			: ["i"],
							"relationships"	: ["friend"]
						}
					],
					function() {
						core._postPublication(i + 1, max, endCallback);
					}
				);
			} else {
				endCallback();
			}
		}
	
	core.accounts	= [];
	core.size		= 100;

	core.createAccounts();
});
