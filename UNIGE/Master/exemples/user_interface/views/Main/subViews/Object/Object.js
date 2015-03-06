Views.register('Main/Object', 'div', function(self) {
	self.refresh = function(object) {
		self.object	= object;
		self.panelView.menuView.setTitle(self.object.name);
		self.panelView.displayView(self.findChildrenViews('Main/Object/Publications')[0]);
		//self.panelView.displayView(self.findChildrenViews('Main/Object/Relationships')[0]);
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
		'view'		: Views.new('Main/Object/Relationships'),
		'button'	: {
			'url'	: 'share_2_white.png',
			'title'	: 'Afficher les relations'
		}
	});
	
	
	self.panelView.appendView({
		'view'		: Views.new('Main/User/LeftPanel/Content/NoResults')
	});

});