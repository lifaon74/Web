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