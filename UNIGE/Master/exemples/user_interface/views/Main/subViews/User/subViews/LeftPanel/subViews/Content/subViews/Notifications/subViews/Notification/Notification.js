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