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