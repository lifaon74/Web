var DraggableElement;
var DroppableElement;

fnc.require(['Class'], function() {

	window._draggedElement = null;
	
	window.addEventListener('mousemove', function(event) {
		if(window._draggedElement) {
			var draggedElement = window._draggedElement;
			
			event.movementX = event.clientX - draggedElement.originalMousePositionX;
			event.movementY = event.clientY - draggedElement.originalMousePositionY;
			
			event.originalElementPositionX = draggedElement.originalElementPositionX;
			event.originalElementPositionX = draggedElement.originalElementPositionX;
			
			event.originalMousePositionX = draggedElement.originalMousePositionX;
			event.originalMousePositionY = draggedElement.originalMousePositionY;
		
			var deadZone = 1;
			if(!draggedElement.mouseMoved) {
				if(Math.abs(event.movementX) > deadZone || Math.abs(event.movementY) > deadZone) {
					draggedElement.mouseMoved = true;
					draggedElement.trigger('dragstart', [event]);
				}
			}
			
			if(draggedElement.mouseMoved) {
				draggedElement.trigger('drag', [event]);
			}
		}
	});
	
	window.addEventListener('mouseup', function(_event) {
		if(window._draggedElement) {
			if(window._draggedElement.mouseMoved) {
				window._draggedElement.mouseMoved = false;
				
				window._draggedElement.trigger('dragend', [event]);
			}
			
			window._draggedElement = null;
		}
	});
	
	DraggableElement = function(element) {
		var self = this;
		ClassWithBinds(self);
		
		this.init = function(element) {
			self.draggableElement = element;
			
			self.draggableElement.addEventListener('mousedown', function(event) {
				if(event.button == 0) {		
					var absolutePosition = fnc.getAbsolutePosition(self.draggableElement);
					
					self.originalElementPositionX = absolutePosition[0];
					self.originalElementPositionY = absolutePosition[1];
					
					self.originalMousePositionX = event.clientX;
					self.originalMousePositionY = event.clientY;
					
					self.mouseMoved = false;
					
					window._draggedElement = self;
					
					event.preventDefaultAction = false;
					
					self.trigger('mousedown', [event]);
					
					if(event.preventDefaultAction) {
						event.returnValue = false;
						if(event.preventDefault) { event.preventDefault(); }
						return false;
					}
				}
			});
		}
		
		this.init(element);
	}
	
	DroppableElement = function(element) {
		var self = this;
		ClassWithBinds(self);
		
		this.init = function(element) {
			self.droppableElement = element;
			
			self.droppableElement.addEventListener('mousemove', function(event) {
				if(window._draggedElement && window._draggedElement.mouseMoved) {
					self.trigger('dragmoveover', [window._draggedElement, event]);
				}
			});
			
			self.droppableElement.addEventListener('mouseover', function(event) {
				if(window._draggedElement && window._draggedElement.mouseMoved) {
					self.trigger('dragover', [window._draggedElement, event]);
				}
			});
			
			self.droppableElement.addEventListener('mouseout', function(event) {
				if(window._draggedElement && window._draggedElement.mouseMoved) {
					self.trigger('dragout', [window._draggedElement, event]);
				}
			});
			
			self.droppableElement.addEventListener('mouseup', function(event) {
				if(window._draggedElement && window._draggedElement.mouseMoved) {
					self.trigger('drop', [window._draggedElement, event]);
				}
			});
		}
		
		this.init(element);
	}
	
	//a = new DraggableElement(document.querySelector('.panel.middle'));
	
	fnc.libs['GUI/DraggableElement'] = DraggableElement;
});
