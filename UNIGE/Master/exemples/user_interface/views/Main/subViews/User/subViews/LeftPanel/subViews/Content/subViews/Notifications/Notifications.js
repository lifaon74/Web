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