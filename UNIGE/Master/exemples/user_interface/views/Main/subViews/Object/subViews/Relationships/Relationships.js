Views.register('Main/Object/Relationships', 'div', function(self) {
	self.addRelationship = function(objectId, relationships) {
		var relationshipView = Views.new('Main/Object/Relationships/Relationship', {
			"objectId"		: objectId,
			"relationships"	: relationships
		});
		
		self.relationships.push(relationshipView);
		self.container.appendChildView(relationshipView);
	}
	
	self.refresh = function() {
		var objectView = self.findParentView('Main/Object');
		self.container.removeChildrenViews();
		
		objectView.sendQuery('get_relationships', {
			'of_object'	: objectView.object.id
		}, function(response) {
			self.relationships = [];
			
			for(objectId in response.relationships) {
				self.addRelationship(objectId, response.relationships[objectId]);
			}
		});
	}
	
	self.relationships = [];
	
	self.container = new View('', 'table');
	self.appendChildView(self.container);
});



Views.register('Main/Object/Relationships/Relationship', 'tr', function(self, params) {
	self.objectId		= params.objectId;
	self.relationships	= params.relationships;

	self._loadTemplate = function() {
		self.template  = '	<td class="objectId">';
		self.template += '		<span></span>';
		self.template += '	</td>';
		self.template  += '	<td class="relationships">';
		self.template += '		<span></span>';
		self.template += '	</td>';
		
		self.setInnerHTML(self.template);
		
		self.objectIdDOMElement			= self.find('.objectId span')[0];
		self.relationshipsDOMElement	= self.find('.relationships span')[0];
	}
	
	self._loadContent = function() {
		self.objectIdDOMElement.setInnerHTML(self.objectId);
		
		var relationshipsString = "";
		for(var i = 0; i < self.relationships.length; i++) {
			if(i > 0) {
				relationshipsString += ", ";
			}
			
			relationshipsString += self.relationships[i].relationship_name;
		}
		
		self.relationshipsDOMElement.setInnerHTML(relationshipsString);
	}
	
	
	self._loadTemplate();
	self._loadContent();
});