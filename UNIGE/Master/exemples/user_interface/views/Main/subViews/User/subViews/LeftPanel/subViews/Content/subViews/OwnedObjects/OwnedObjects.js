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
