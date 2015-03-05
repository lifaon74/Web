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