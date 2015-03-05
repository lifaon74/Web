fnc.require(['jQuery'], function() {

	var TreeLine = function(settings) {
		var self = this;
		
		this.init = function(settings) {
			self.subTree = null;
			
			self.$line = $('<div class="line"></div>');
			
				self.$head = $('<div class="head"></div>');
				
					self.$leftIcons = $('<div class="icons"></div>');
						self.$indentIcon = $('<div class="icon"></div>');
							self.$leftIcons.append(self.$indentIcon);
						self.$head.append(self.$leftIcons);
					
					self.$title = $('<div class="title"></div>');
						self.$head.append(self.$title);
			
				self.$line.append(self.$head);
			
			if(settings) {
				self.setSettings(settings);
			}
		}
		
		this.setSettings = function(settings) {
			/*if(typeof settings.icons != 'undefined') {
				self.setIcon(settings.icon);
			}*/
			
			if(typeof settings.title != 'undefined') {
				self.setTitle(settings.title);
			}
			
			if(typeof settings.subTree != 'undefined') {
				self.addSubTree(settings.subTree);
			}
		}
		
		/*this.setIcon = function(url) {
			var $icon = $('<div class="icon"></div>');
			$icon.css('backgroundImage', "url('" + url + "')");
			self.$icons.append($icon);
		}*/
		
		this.setTitle = function(title) {
			self.$title.html(title);
		}
	
	
	
		this.addSubTree = function(subTree) {
			self.subTree = subTree;
			self.$line.append(subTree.$tree);
		}
		
		this._setIndentIcon = function(url) {
			self.$indentIcon.css('backgroundImage', "url('" + url + "')");
		}
		
		this.init(settings);
	}
	
	var Tree = function(settings) {
		var self = this;
		
		this.init = function(settings) {
			self.lines = [];
			self.isRootTree = false;
			self.folded = true;
			
			var root = 'ressources/images/sub_elements/';
			self.indentImages = [];
			
			for(var i = 0; i < 9; i++) {
				self.indentImages[i] = root + 'sub_element_0' + (i + 1) + '.png';
			}
			
			self.$tree = $('<div class="tree"></div>');
			
			if(settings) {
				self.setSettings(settings);
			}
		}
		
		this.setSettings = function(settings) {
			if(typeof settings.isRootTree != 'undefined') {
				self.isRootTree = settings.isRootTree;
			}
			
			if(typeof settings.lines != 'undefined') {
				for(var i = 0; i  < settings.lines.length; i++) {
					self.addLine(settings.lines[i]);
				}
			}
			
		}
		
		this.append = function(container) {
			container.append(self.$tree);
		}
		
		
		this.addLine = function(treeLine) {
			self.lines.push(treeLine);
			self.$tree.append(treeLine.$line);
			
			self._updateIndentIcons();
		}
		
		this.fold = function() {
			self.$tree.slideUp(100, function() {
				self.folded = true;
				self._updateIndentIcons();
			});
			
		}
		
		this.unfold = function() {
			self.folded = false;
			self.$tree.slideDown(100);
		}
		
		
		this._updateIndentIcons = function() {
			for(var i = 0; i < self.lines.length; i++) {
				var j = -1;
				if(i == 0 && self.lines.length > 1 && self.isRootTree) {
					j = 0;
				} else if(i == self.lines.length - 1) {
					j = 6;
				} else {
					j = 3;
				}
				
				if(self.lines[i].subTree) {
					if(self.lines[i].subTree.folded) {
						j += 2;
					} else {
						j += 1;
					}
				}
				
				self.lines[i]._setIndentIcon(self.indentImages[j]);
			}
			
			
		}
		
		this.init(settings);
	}
	
	var url1 = 'ressources/images/sub_elements/sub_element_01.png';
	var url2 = 'ressources/images/sub_elements/sub_element_02.png';
	var url3 = 'ressources/images/sub_elements/sub_element_03.png';
	var url4 = 'ressources/images/sub_elements/sub_element_04.png';
	var url5 = 'ressources/images/sub_elements/sub_element_05.png';
	var url6 = 'ressources/images/sub_elements/sub_element_06.png';
	
	suTree = new Tree({
		lines: [
			new TreeLine({
				title: 'lol',
				icon: url3
			}),
			new TreeLine({
				title: 'lol',
				icon: url5
			})
		]
	});
						
	tree = new Tree({
		isRootTree: true,
		lines: [
			new TreeLine({
				title: 'salut',
				icon: url2,
				subTree: new Tree({
					lines: [
						new TreeLine({
							title: 'lol',
							icon: url3,
							subTree: suTree
						}),
						new TreeLine({
							title: 'lol',
							icon: url4,
							subTree: new Tree({
								lines: [
									new TreeLine({
										title: 'lol',
										icon: url3
									}),
									new TreeLine({
										title: 'lol',
										icon: url5
									})
								]
							})
						}),
						new TreeLine({
							title: 'lol',
							icon: url3
						}),
						new TreeLine({
							title: 'lol',
							icon: url6,
							subTree: new Tree({
								lines: [
									new TreeLine({
										title: 'lol',
										icon: url3
									}),
									new TreeLine({
										title: 'lol',
										icon: url5
									})
								]
							})
						}),
					]
				})
			}),
			
			new TreeLine({
				title: 'salut',
				subTree: new Tree({
					lines: [
						new TreeLine({
							title: 'lol'
						})
					]
				})
			})
		]
	});
	
	tree.append($('.panel.left'));

	fnc.libReady('Tree', Tree);
});