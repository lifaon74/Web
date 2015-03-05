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