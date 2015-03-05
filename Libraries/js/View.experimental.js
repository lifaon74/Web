var View, Views;

fnc.require(['Class', 'DOM'], function() {

	ViewsClass = function() {
		var self = this;
		Class(self);
		
		this.__construct = function() {
			self.views = [];
		}
		
		this.register = function(viewName, viewType, constructorCallback) {
			self.views[viewName] = [viewName, viewType, constructorCallback];
		}
		
		this.new = function(viewName, args) {
			if(typeof args == 'undefined') { var args = null; }
			
			if(self.views[viewName]) {
				var viewParameters = self.views[viewName];
				var view = new View(viewParameters[0], viewParameters[1]);
				viewParameters[2](view, args);
				return view;
			} else {
				console.error(viewName + " not loaded");
			}
		}
	
		
		this.__construct();
	}
	
	View = function(viewName, viewType) {
		var self = this;
		Class(self);
		
		this.__construct = function(viewName, viewType) {
			self.parentView			= null;
			self.childrenViews		= [];
			self.viewName			= viewName;
			self.viewType			= viewType;
			self.className			= self.viewName.replace(/\//g, '_');
			
			self.extend(DOMElement, [document.createElement(self.viewType)]);
			self.addClass('view');
			self.addClass(self.className);
		}	
		
		this.getParentView = function() {
			return self.parentView;
		}
		
		this.setParentView = function(parentView) {
			self.parentView = parentView;
			return self;
		}
		
		
		this.getChildrenViews = function() {
			return self.childrenViews;
		}
		
		this.addChildView = function(childView) {
			self.childrenViews.push(childView);
		}
		
		this.removeChildView = function(childView) {
			var index = self.childrenViews.indexOf(childView);
			if(index >= 0) {
				self.childrenViews[index].setParentView(null);
				self.childrenViews.splice(index, 1);
			}
		}

		
		this.findParentView = function(parentViewName) {
			if(self.viewName == parentViewName) {
				return self;
			} else if(self.parentView) {
				return self.parentView.findParentView(parentViewName);
			} else {
				return null;
			}
		}
		
		this.findChildrenViews = function(childViewName) {
			var views = [];
			
			if(self.viewName == childViewName) {
				views.push(self);
			} 
			
			for(var i = 0; i < self.childrenViews.length; i++) {
				var _views = self.childrenViews[i].findChildrenViews(childViewName);
				for(var j = 0; j < _views.length; j++) {
					views.push(_views[j]);
				}
			}
				
			return views;
		}
		
		
		this.hideChildrenViews = function() {
			self.childrenViews.forEach(function(childView) {
				childView.hide();
			});
		}
		
	
		this.appendChildView = function(childView) {
			childView.setParentView(self);
			self.addChildView(childView);
			self.append(childView);
		}
		
		this.removeChildrenViews = function() {
			for(var i = 0; i < self.childrenViews.length; i++) {
				self.childrenViews[i].setParentView(null);
				self.childrenViews[i].detach();
			}
			
			self.childrenViews = [];
		}
		
		this.__construct(viewName, viewType);
	}
	
	Views = new ViewsClass();
	
	fnc.libs['View'] = Views;
});