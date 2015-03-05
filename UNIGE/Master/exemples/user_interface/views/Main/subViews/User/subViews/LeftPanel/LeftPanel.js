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