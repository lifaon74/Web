fnc.registerLib('Tree', 'scripts/js/Tree.class.js');
fnc.registerLib('Node', 'scripts/js/Node.class.js');

fnc.require(['jQuery', 'Node'], function() {

	var Group = function(name) {
		var self = this;
		
		this.init = function(name) {
			self.name = name;
			self.elements = [];
		}
		
		this.init(name);
	}
	
	
	var Core = function() {
		var self = this;
		
		this.init = function() {
			self.root = new Group('map');
		}
		
		
		this.refreshTree = function() {
		}
		
		this.init();
	}
	
	var core = new Core();
});