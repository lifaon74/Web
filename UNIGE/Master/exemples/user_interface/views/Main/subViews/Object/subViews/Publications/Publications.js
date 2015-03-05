Views.register('Main/Object/Publications', 'div', function(self) {
	self.addPublication = function(publication) {
		var publicationView = Views.new('Main/Object/Publications/Publication', publication);
		self.publications.push(publicationView);
		self.container.appendChildView(publicationView);
	}
	
	self.refresh = function() {
		var objectView = self.findParentView('Main/Object');
		self.container.removeChildrenViews();
		
		objectView.sendQuery('get_publications', {
			'of_object'	: objectView.object.id,
			'limit'		: 10,
		}, function(response) {
			self.publications = [];
			
			for(var i = 0; i < response.publications.length; i++) {
				self.addPublication(response.publications[i]);
			}
		});
	}
	
	self.publications = [];
	
	self.container = new View('', 'table');
	self.appendChildView(self.container);
});



Views.register('Main/Object/Publications/Publication', 'tr', function(self, publication) {
	self.publication	= publication;
	
	self.addData = function(data) {
		var dataContainer = DOM.createElement('tr');
		dataContainer.addClass('data');
		
		//var a = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut scelerisque nibh a egestas interdum. Aliquam molestie nulla nulla, vitae imperdiet lectus placerat at. Proin vestibulum mattis nibh vel rhoncus. Pellentesque risus tellus, tempor sit amet sodales et, cursus at lectus. In porta leo ac rutrum semper. Mauris in pretium dui. Curabitur consequat volutpat tellus, quis viverra nulla viverra nec. Donec efficitur vitae nibh a finibus. Maecenas at enim tempor, accumsan augue at, laoreet urna. Donec suscipit nec mauris vitae tempus. Ut sagittis at ipsum eu pellentesque.";
		var a = null;
		var html = '';
		html += '	<td  title="valeur" class="value">';
		html += 		data.value;
		html += '	</td>';
		
		html += '	<td title="tags" class="tags">';
		html += 		data.tags.toString();
		html += '	</td>';
		
		html += '	<td  title="relations autorisées à consulter cette donnée" class="relationships">';
		html += 		data.relationships.toString();
		html += '	</td>';
		
		dataContainer.setInnerHTML(html);
		self.dataSetDOMElement.append(dataContainer);
	}
	
	
	self._loadTemplate = function() {
		self.template = '	<td>';
		self.template += '		<div class="date">';
		self.template += '			<span></span>';
		self.template += '		</div>';
		self.template += '		<table class="dataSet"></table>';
		self.template += '	</td>';
		
		self.setInnerHTML(self.template);
		
		self.dateDOMElement	= self.find('.date span')[0];
		self.dataSetDOMElement	= self.find('.dataSet')[0];
	}
	
	self._loadContent = function() {
		var time = new Time(self.publication.timestamp);
		var date = time.getMonthDay() + "/" + time.getMonth() + "/" + time.getYear() + " - " + time.getHours() + ":" + time.getMinutes() + ":" + time.getSeconds();
		//self.dateDOMElement.setInnerHTML(date + " - " + Math.floor(self.publication.timestamp / 1000) + " - " . self.publication.id);
		self.dateDOMElement.setInnerHTML(date);
		
		for(var i = 0; i < self.publication.data.length; i++) {
			self.addData(self.publication.data[i]);
		}
		
	}
	
	
	self._loadTemplate();
	self._loadContent();
});