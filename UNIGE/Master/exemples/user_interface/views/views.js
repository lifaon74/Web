
	
fnc.registerLib('Views', 'views/views.js');
fnc.require(['View'], function() {
	
	Views.register('Content', 'div', function(self) {
	self.appendView = function(view) {
		view.hide();
		self.appendChildView(view);
	}
	
	self.displayView = function(view, parameters) {
		if(typeof parameters == 'undeifined') {
			parameters = [];
		}
		
		self.hideChildrenViews();
		view.show();
		
		if(typeof view.refresh == 'function') {
			view.refresh(parameters);
		}
	}
});
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
Views.register('Main/Object', 'div', function(self) {
	self.refresh = function(object) {
		self.object	= object;
		self.panelView.menuView.setTitle(self.object.name);
		self.panelView.displayView(self.findChildrenViews('Main/Object/Publications')[0]);
	}
	
		
	self.sendQuery = function(action, parameters, successCallback) {
		var query = {
			"id"			: self.object.id,
			"key"			: self.object.key,
			"action"		: action,
			"parameters"	: parameters
		};
		
		var mainView = self.findParentView('Main');
		mainView.sendQuery(query, successCallback);
	}
	
	
	self.panelView = Views.new('Panel');
	self.appendChildView(self.panelView);
	
	self.panelView.menuView.title.show();
	
	self.panelView.appendView({
		'view'		: Views.new('Main/Object/Publications'),
		'button'	: {
			'url'	: 'newspaper_1_white.png',
			'title'	: 'Afficher les publications'
		}
	});
	
	self.panelView.appendView({
		'view'		: Views.new('Main/User/LeftPanel/Content/OwnedObjects'),
		'button'	: {
			'url'	: 'share_2_white.png',
			'title'	: 'Afficher les relations'
		}
	});
	
	
	self.panelView.appendView({
		'view'		: Views.new('Main/User/LeftPanel/Content/NoResults')
	});

});
Views.register('Main/Object/Publications', 'div', function(self) {
	self.addPublication = function(publication) {
		var publicationView = Views.new('Main/Object/Publications/Publication', publication);
		self.publications.push(publicationView);
		self.container.appendChildView(publicationView);
	}
	
	self.refresh = function() {
		var objectView = self.findParentView('Main/Object');
		self.container.removeChildrenViews();
		
		objectView.sendQuery('get_publications', {
			'of_object'	: objectView.object.id,
			'limit'		: 10,
		}, function(response) {
			self.publications = [];
			
			for(var i = 0; i < response.publications.length; i++) {
				self.addPublication(response.publications[i]);
			}
		});
	}
	
	self.publications = [];
	
	self.container = new View('', 'table');
	self.appendChildView(self.container);
});



Views.register('Main/Object/Publications/Publication', 'tr', function(self, publication) {
	self.publication	= publication;
	
	self.addData = function(data) {
		var dataContainer = DOM.createElement('tr');
		dataContainer.addClass('data');
		
		//var a = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut scelerisque nibh a egestas interdum. Aliquam molestie nulla nulla, vitae imperdiet lectus placerat at. Proin vestibulum mattis nibh vel rhoncus. Pellentesque risus tellus, tempor sit amet sodales et, cursus at lectus. In porta leo ac rutrum semper. Mauris in pretium dui. Curabitur consequat volutpat tellus, quis viverra nulla viverra nec. Donec efficitur vitae nibh a finibus. Maecenas at enim tempor, accumsan augue at, laoreet urna. Donec suscipit nec mauris vitae tempus. Ut sagittis at ipsum eu pellentesque.";
		var a = null;
		var html = '';
		html += '	<td  title="valeur" class="value">';
		html += 		data.value;
		html += '	</td>';
		
		html += '	<td title="tags" class="tags">';
		html += 		data.tags.toString();
		html += '	</td>';
		
		html += '	<td  title="relations autorisées à consulter cette donnée" class="relationships">';
		html += 		data.relationships.toString();
		html += '	</td>';
		
		dataContainer.setInnerHTML(html);
		self.dataSetDOMElement.append(dataContainer);
	}
	
	
	self._loadTemplate = function() {
		self.template = '	<td>';
		self.template += '		<div class="date">';
		self.template += '			<span></span>';
		self.template += '		</div>';
		self.template += '		<table class="dataSet"></table>';
		self.template += '	</td>';
		
		self.setInnerHTML(self.template);
		
		self.dateDOMElement	= self.find('.date span')[0];
		self.dataSetDOMElement	= self.find('.dataSet')[0];
	}
	
	self._loadContent = function() {
		var time = new Time(self.publication.timestamp);
		var date = time.getMonthDay() + "/" + time.getMonth() + "/" + time.getYear() + " - " + time.getHours() + ":" + time.getMinutes() + ":" + time.getSeconds();
		//self.dateDOMElement.setInnerHTML(date + " - " + Math.floor(self.publication.timestamp / 1000) + " - " . self.publication.id);
		self.dateDOMElement.setInnerHTML(date);
		
		for(var i = 0; i < self.publication.data.length; i++) {
			self.addData(self.publication.data[i]);
		}
		
	}
	
	
	self._loadTemplate();
	self._loadContent();
});
Views.register('Main/User', 'div', function(self) {
	self.loadUser = function(user_id, user_key) {
		self.user_id	= user_id;
		self.user_key	= user_key;

		self.leftPanelView.refresh();
	}
		
	self.sendQuery = function(action, parameters, successCallback) {
		var query = {
			"id"			: self.user_id,
			"key"			: self.user_key,
			"action"		: action,
			"parameters"	: parameters
		};
		
		var mainView = self.findParentView('Main');
		mainView.sendQuery(query, successCallback);
	}
	
	self.leftPanelView = Views.new('Main/User/LeftPanel');
	self.appendChildView(self.leftPanelView);
	
	self.rightPanelView = Views.new('Main/User/RightPanel');
	self.appendChildView(self.rightPanelView);

	
});
Views.register('Main/User/LeftPanel', 'div', function(self) {
	
	self.refresh = function() {
		self.panelView.displayView(self.findChildrenViews('Main/User/LeftPanel/Content/OwnedObjects')[0]);
	}
	
	self.panelView = Views.new('Panel');
	self.appendChildView(self.panelView);
	
	self.panelView.appendView({
		'view'		: Views.new('Main/User/LeftPanel/Content/OwnedObjects'),
		'button'	: {
			'url'	: 'cube_1_white.png',
			'title'	: 'Afficher les objets possédés'
		}
	});
	
	self.panelView.appendView({
		'view'		: Views.new('Main/User/LeftPanel/Content/Notifications'),
		'button'	: {
			'url'	: 'speak_1_white.png',
			'title'	: 'Afficher les notifications'
		}
	});
	
	self.panelView.appendView({
		'view'		: Views.new('Main/User/LeftPanel/Content/NoResults')
	});
	
});
Views.register('Main/User/LeftPanel/Content/NoResults', 'div', function(self) {
	self.refresh = function(viewName) {
		self.hideChildrenViews();
		var childrenViews = self.findChildrenViews(viewName);
		if(childrenViews.length == 1) {
			childrenViews[0].show();
		} else {
			console.error("childrenViews must be 1");
		}
	}
	
	self.appendChildView(Views.new('Main/User/LeftPanel/Content/NoResults/NoOwnedObjects'));
	self.appendChildView(Views.new('Main/User/LeftPanel/Content/NoResults/NoNotifications'));
	
	self.hideChildrenViews();
});

Views.register('Main/User/LeftPanel/Content/NoResults/NoOwnedObjects', 'span', function(self) {
	self.setInnerHTML("Aucun object connecté.");
});

Views.register('Main/User/LeftPanel/Content/NoResults/NoNotifications', 'span', function(self) {
	self.setInnerHTML("Aucune notification.");
});
Views.register('Main/User/LeftPanel/Content/Notifications', 'tr', function(self, object) {
	
	self.addNotification = function(notification) {
		var notificationView = Views.new('Main/User/LeftPanel/Content/Notifications/Notification', notification);
		self.notifications.push(notificationView);
		self.container.appendChildView(notificationView);
	}
	
	self.removeNotification = function(notificationView) {
		notificationView.detach();
		
		var index = self.notifications.indexOf(notificationView);
		if(index >= 0) {
			self.notifications.splice(index, 1);
		}
		
		if(self.notifications.length == 0) {
			self.refresh();
		}
	}
	
	self.refresh = function() {
		var userView = self.findParentView('Main/User');

		userView.sendQuery('get_notifications', {}, function(response) {
			if(response.notifications.length == 0) {
				var panelView = self.findParentView('Panel');
				var noResultsView = panelView.findChildrenViews('Main/User/LeftPanel/Content/NoResults')[0];
				panelView.displayView(noResultsView, 'Main/User/LeftPanel/Content/NoResults/NoNotifications');
			} else {
				self.container.removeChildrenViews();
				self.notifications = [];
				
				for(var i = 0; i < response.notifications.length; i++) {
					self.addNotification(response.notifications[i]);
				}
			}
		});
	}
	
	self.notifications = [];
	
	self.container = new View('', 'table');
	self.appendChildView(self.container);
});
Views.register('Main/User/LeftPanel/Content/Notifications/Notification', 'tr', function(self, notification) {
	self.notification	= notification;
	
	self.answer_notification = function(answer) {
		var parameters = {};
		parameters.notification_id = self.notification.id;
		
		if(typeof answer == 'boolean') {
			parameters.answer = answer;
		}
		
		var userView = self.findParentView('Main/User');
		userView.sendQuery('answer_notification', parameters, function(response) {
			var notificationsView = self.findParentView('Main/User/LeftPanel/Content/Notifications');
			notificationsView.removeNotification(self);
		}, function(response) {
			debugger;
		});
	}
		
	self.addButton = function(buttonClass, buttonName) {
		var button = DOM.createElement('div');
		button.addClass('button').addClass(buttonClass).setInnerHTML(buttonName);
		
		switch(buttonClass) {
			case 'accept':
				button.bind('click', function() {
					switch(self.notification.type) {
						case 'request_for_changing_of_owner':
							if(confirm("Attention, vous êtes sur le point de vous séparer de l'objet " + self.notification.parameters.from_object + ". Confirmez vous ce choix ?")) {
								self.answer_notification(true);
							}
						break;
						default:
							self.answer_notification(true);
					}
				});
			break;
			case 'refuse':
				button.bind('click', function() {
					self.answer_notification(false);
				});
			break;
			case 'read':
				button.bind('click', function() {
					self.answer_notification();
				});
			break;
		}	
		
		self.buttonsDOMElement.append(button);
	}
	
	self._loadTemplate = function() {
		self.template = '	<td>';
		self.template += '		<div class="date">';
		self.template += '			<span></span>';
		self.template += '		</div>';
		self.template += '		<div class="message">';
		self.template += '			<p></p>';
		self.template += '		</div>';
		self.template += '		<div class="buttons">';
		self.template += '		</div>';
		self.template += '	</td>';
		
		self.setInnerHTML(self.template);
		
		self.dateDOMElement		= self.find('.date span')[0];
		self.messageDOMElement	= self.find('.message p')[0];
		self.buttonsDOMElement	= self.find('.buttons')[0];
	}
	
	self._loadContent = function() {
		var time = new Time(self.notification.timestamp);
		var date = time.getMonthDay() + "/" + time.getMonth() + "/" + time.getYear() + " - " + time.getHours() + ":" + time.getMinutes();

		switch(self.notification.type) {
			case 'request_for_a_new_owner':
				var message	= "L'objet <a href=\"#\">" + fnc.escapeHTMLString(self.notification.parameters.from_object) + "</a> souhaite faire de vous son nouveau propriétaire.";
				self.addButton('refuse', 'Refuser');
				self.addButton('accept', 'Accepter');
			break;
			case 'have_a_new_object':
				var message = "Vous êtes désormais propriétaire de l'objet : <a href=\"#\">" + fnc.escapeHTMLString(self.notification.parameters.new_object) + "</a>.";
				self.addButton('read', 'OK');
			break;
			case 'request_for_changing_of_owner':
				var message = "L'objet <a href=\"#\">" + fnc.escapeHTMLString(self.notification.parameters.from_object) + "</a> souhaite changer de propriétaire " + fnc.escapeHTMLString(self.notification.parameters.new_owner);
				self.addButton('refuse', 'Refuser');
				self.addButton('accept', 'Accepter');
			break;
			case 'no_more_possess_an_object':
				var message = "L'objet <a href=\"#\">" + fnc.escapeHTMLString(self.notification.parameters.released_object) + "</a> a désormais pour nouveau propriétaire " + fnc.escapeHTMLString(self.notification.parameters.new_owner);
				self.addButton('read', 'OK');
			break;
			default:
				var message = "Notification inconnue : " + self.notification.type;
		}
		
		self.dateDOMElement.setInnerHTML(date);
		self.messageDOMElement.setInnerHTML(message);
	}
	
	
	self._loadTemplate();
	self._loadContent();
});
Views.register('Main/User/LeftPanel/Content/OwnedObjects', 'div', function(self) {
	self.refresh = function() {
		var userView = self.findParentView('Main/User');

		userView.sendQuery('list_owned_objects', {}, function(response) {
			if(response.owned_objects.length == 0) {
				var panelView = self.findParentView('Panel');
				var noResultsView = panelView.findChildrenViews('Main/User/LeftPanel/Content/NoResults')[0];
				panelView.displayView(noResultsView, 'Main/User/LeftPanel/Content/NoResults/NoOwnedObjects');
			} else {
				self.container.removeChildrenViews();

				for(var i = 0; i < response.owned_objects.length; i++) {
					var objectView = Views.new('Main/User/LeftPanel/Content/OwnedObjects/Object', response.owned_objects[i]);
					self.container.appendChildView(objectView);
				}
			}
		});
	}
	
	self.container = new View('', 'table');
	self.appendChildView(self.container);
});

Views.register('Main/User/LeftPanel/Content/OwnedObjects/Object', 'tr', function(self, object) {
	self.object	= object;
	
	self.bind('click', function() {
		var userView = self.findParentView('Main/User');
		var rightPanelView = userView.findChildrenViews('Main/User/RightPanel')[0];
		var objectView = rightPanelView.findChildrenViews('Main/Object')[0];
		
		rightPanelView.displayView(objectView, self.object);
	});
	
	
	var imageUrl;
	switch(self.object.type) {
		case 'thermometer':
			imageUrl = 'thermometer_1.png';
		break;
		default:
			imageUrl = 'question_mark_2.png';
	}
		
	self.template  = '	<td class="icon type">';
	self.template += '		<img title="Type : ' + self.object.type + '" src="ressources/icons/16x16/' + imageUrl + '"/>';
	self.template += '	</td>';
	self.template += '	<td class="name">';
	self.template += '		<span>' + self.object.name + '</span>';
	self.template += '	</td>';
	
	self.setInnerHTML(self.template);
});
Views.register('Main/User/RightPanel', 'div', function(self) {
	self.appendView = function(view) {
		view.hide();
		self.appendChildView(view);
	}
	
	self.displayView = function(view, parameters) {
		if(typeof parameters == 'undeifined') {
			parameters = [];
		}
		
		self.hideChildrenViews();
		view.show();
		
		if(typeof view.refresh == 'function') {
			view.refresh(parameters);
		}
	}
	
	self.appendView(Views.new('Main/Object'));
});
Views.register('Panel', 'div', function(self) {
	
	self.appendView = function(parameters) {
		if(typeof parameters.button == 'object') {
			parameters.view._menuButton = self.menuView.addButton(parameters.button.url, parameters.button.title, function() {
				self.displayView(parameters.view);
			});
		}
		
		self.contentView.appendView(parameters.view);
	}
	
	self.displayView = function(view, parameters) {
		if(typeof parameters == 'undeifined') {
			parameters = [];
		}
		
		if(typeof view._menuButton != 'undefined') {
			self.menuView.selectButton(view._menuButton);
		}
		
		self.contentView.displayView(view, parameters);
	}
	
	self.menuView = Views.new('Panel/Menu');
	self.appendChildView(self.menuView);
	
	self.contentView = Views.new('Content');
	self.appendChildView(self.contentView);
});
Views.register('Panel/Menu', 'div', function(self) {
	self.setTitle = function(titleValue) {
		self.title.setInnerHTML(titleValue);
	}
	
	self.title = DOM.createElement('div');
	self.title.addClass('title');
	self.title.hide();
	self.append(self.title);
		
	self.addButton = function(path, title, onclick) {
		var button = Views.new('Panel/Menu/Button');
		
		button.setAttribute('src', 'ressources/icons/32x32/' + path);
		button.setAttribute('title', title);
		button.bind('click', onclick);
		
		self.appendChildView(button);
		
		return button;
	}
	
	self.selectButton = function(button) {
		self.deselectButtons();
		button.select();
	}
	
	self.deselectButtons = function() {
		self.getChildrenViews().forEach(function(childView) {
			childView.deselect();
		});
	}
	
	
		
});

Views.register('Panel/Menu/Button', 'img', function(self) {
	self.select = function() {
		self.addClass('selected');
	}
	
	self.deselect = function() {
		self.removeClass('selected');
	}
});

	
	fnc.libReady('Views', {});
});
	