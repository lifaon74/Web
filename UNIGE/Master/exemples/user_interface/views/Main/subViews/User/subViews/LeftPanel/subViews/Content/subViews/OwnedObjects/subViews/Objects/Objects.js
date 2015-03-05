Views.register('Main/User/LeftPanel/Content/OwnedObjects/Object', 'tr', function(self, object) {
	self.object	= object;
	
	self.bind('click', function() {
		var userView = self.findParentView('Main/User');
		var rightPanelView = userView.findChildrenViews('Main/User/RightPanel')[0];
		var objectView = rightPanelView.findChildrenViews('Main/Object')[0];
		
		rightPanelView.displayView(objectView, self.object);
	});
	
	
	var imageUrl;
	switch(self.object.type) {
		case 'thermometer':
			imageUrl = 'thermometer_1.png';
		break;
		default:
			imageUrl = 'question_mark_2.png';
	}
		
	self.template  = '	<td class="icon type">';
	self.template += '		<img title="Type : ' + self.object.type + '" src="ressources/icons/16x16/' + imageUrl + '"/>';
	self.template += '	</td>';
	self.template += '	<td class="name">';
	self.template += '		<span>' + self.object.name + '</span>';
	self.template += '	</td>';
	
	self.setInnerHTML(self.template);
});