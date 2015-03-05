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