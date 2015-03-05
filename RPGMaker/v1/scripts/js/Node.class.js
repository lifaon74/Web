fnc.require(['Class', 'jQuery', 'GUI/DraggableElement'], function() {
	
	var $leftPanel = $('.panel.left');
	$separationLine = $('<div class="separationLine"></div>');
	$leftPanel.append($separationLine);
	
	var Node = function(settings) {
		var self = this;
		ClassWithBinds(self);
		
		this.init = function(settings) {
			self.isRoot = false;
			self.subNodes = [];
			self.collapsed = false;
			self.selected = false;
			self.isLink = false;
			
			self.parentNode = null;
			self.position = 0;
			
			self.indentIndex = 0;
			self.indentImages = [];
			var roots = ['ressources/images/sub_elements/', 'ressources/images/sub_elements_v02/'];
			for(var i = 0; i < 3; i++) {
				self.indentImages[i] = [];
				for(var j = 0; j < 3; j++) {
					self.indentImages[i][j] = [];
					for(var k = 0; k < 2; k++) {
						var url = roots[k] + 'sub_element_0' + ((i * 3 + j) + 1) + '.png';
						self.indentImages[i][j][k] = url;
					}
				}
			}
			
			
			
			self.$node = $('<div class="node"></div>');
			self.$subNodes = $('<div class="subNodes"></div>');
			
			self._initHeader();
			
			if(settings) {
				self.setSettings(settings);
			}
		}
		
		this._initHeader = function() {
			self.$head = $('<div class="head"></div>');
			self.head = self.$head.get(0);
					
				// icons
			self.$leftIcons = $('<div class="icons left"></div>');
		
				self.$indentIcon = $('<div class="icon indent"></div>');
				self.$indentIcon.on('click', function() {
					self.toogleDisplayOfSubNodes();
					document.body.focus();
					
					event.returnValue = false;
					if(event.preventDefault) { event.preventDefault(); }
					return false;
				});
							
				self.$leftIcons.append(self.$indentIcon);
				
			self.$head.append(self.$leftIcons);
			
			
				// node name
			self.$nodeName = $('<div autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="nodeName"></div>');
			self.nodeName = self.$nodeName.get(0);
			self.nodeName.focused = false;
			self.$head.append(self.$nodeName);
				
				self.$nodeName.on('click', function() {
					self.select();
				});
				
				self.$nodeName.on('dblclick', function() {
					self.activateUserRename();
				});
				
				self.$nodeName.on('blur', function() {
					self.deactivateUserRename();
				});
				
				self.$nodeName.on('keydown', function(event) {
					if(event.keyCode == 13) {
						self.deactivateUserRename();
						
						event.returnValue = false;
						if(event.preventDefault) { event.preventDefault(); }
						return false;
					}
				});
				
				// right icons
			self.$rightIcons = $('<div class="icons right"></div>');
			self.$head.append(self.$rightIcons);
			
			
				// bind head for dragging
			self.draggableNodeName = new DraggableElement(self.head);
			self.draggableNodeName.node = self;
			
			self.droppableNodeName = new DroppableElement(self.head);
			self.droppableNodeName.timer = null;
				
				function getRelativePosition(absolutePosition, event, draggedElement) {
					var cursorRelativePosition = [
						event.clientX - absolutePosition[0],
						event.clientY - absolutePosition[1]
					];
					
					if(self.isChildOfNode(draggedElement.node)) {
					//if(draggedElement.node == self) {
						return 'none';
					} else {
						if(cursorRelativePosition[1] < 6) {
							if(self.parentNode) {
								return 'top';
							} else {
								return 'middle';
							}
						} else if(cursorRelativePosition[1] < 14) {
							return 'middle';
						} else {
							if(self.parentNode) {
								return 'bottom';
							} else {
								return 'middle';
							}
						}
					}
				}
				
				function clearTimer() {
					if(self.droppableNodeName.timer) {
						clearTimeout(self.droppableNodeName.timer);
						self.droppableNodeName.timer = null;
					}
				}
				
				self.draggableNodeName.bind('mousedown', function(event) {
					if(!self.nodeName.focused) {
						event.preventDefaultAction = true;
					}
				});
				
				self.draggableNodeName.bind('dragend', function(event) {
					$separationLine.hide();
				});
				
				
				self.droppableNodeName.bind('dragout', function(draggedElement, event) {
					clearTimer();
					$separationLine.hide();
				});
					
				self.droppableNodeName.bind('dragmoveover', function(draggedElement, event) {
					var headAbsolutePosition = fnc.getAbsolutePosition(self.head);

					var top = left = 0;
					var relativePosition = getRelativePosition(headAbsolutePosition, event, draggedElement);
					switch(relativePosition) {
						case 'top':
							left = headAbsolutePosition[0] + 9;
							top = headAbsolutePosition[1];
						break;
						case 'middle':
							left = headAbsolutePosition[0] + 9 + 17;
							top = headAbsolutePosition[1] + self.head.offsetHeight;
						break;
						case 'bottom':
							left = headAbsolutePosition[0] + 9;
							top = headAbsolutePosition[1] + self.head.offsetHeight;
						break;
						case 'none':
						break;
					}
					
					switch(relativePosition) {
						case 'top':
						case 'bottom':
						case 'none':
							clearTimer();
						break;
						case 'middle':
							if(!self.droppableNodeName.timer) {
								self.droppableNodeName.timer = setTimeout(function() {
									self.showSubNodes();
								}, 500);
							}
						break;
					}
					
					if(relativePosition == 'none') {
						$separationLine.hide();
					} else {
						$separationLine.show();
						$separationLine.css('left', left + 'px');
						$separationLine.css('top', top + 'px');
					}
				});
				
				self.droppableNodeName.bind('drop', function(draggedElement, event) {
					clearTimer();
							
					var headAbsolutePosition = fnc.getAbsolutePosition(self.head)
					switch(getRelativePosition(headAbsolutePosition, event, draggedElement)) {
						case 'top':
							draggedElement.node.appendBeforeNode(self);
						break;
						case 'middle':
							if(self.collapsed) {
								draggedElement.node.appendToNode(self);
							} else {
								draggedElement.node.appendToNode(self, 0);
							}
						break;
						case 'bottom':
							draggedElement.node.appendAfterNode(self);
						break;
						case 'none':
						break;
					}
				});
				
	
			self.$node.append(self.$head);			
		}
		
		
		this.setSettings = function(settings) {
			if(typeof settings.isRoot != 'undefined') {
				self.isRoot = settings.isRoot;
			}
			
			if(typeof settings.isLink != 'undefined') {
				self.isLink = settings.isLink;
			}

			if(typeof settings.name != 'undefined') {
				self.setName(settings.name);
			}
			
			if(typeof settings.icons != 'undefined') {
				if(typeof settings.icons.length != 'undefined') {
					for(var i = 0; i  < settings.icons.length; i++) {
						self.addIcon(settings.icons[i]);
					}
				} else {
					if(typeof settings.icons.left != 'undefined') {
						for(var i = 0; i  < settings.icons.left.length; i++) {
							self.addIcon(settings.icons.left[i], 'left');
						}
					}
					
					if(typeof settings.icons.right != 'undefined') {
						for(var i = 0; i  < settings.icons.right.length; i++) {
							self.addIcon(settings.icons.right[i], 'right');
						}
					}
				}
			}
			
			if(typeof settings.subNodes != 'undefined') {
				for(var i = 0; i  < settings.subNodes.length; i++) {
					self.addSubNode(settings.subNodes[i]);
				}
			}
			
		}
		
		
		this.appendToDomElement = function(container) {
			container.append(self.$node);
		}
		
		
	/*
	*	Methods as child
	*/
	
		this.appendToNode = function(node, position) {
			if(node != self) {
				if(typeof position == 'number') {
					node.addSubNode(self, position);
				} else {
					node.addSubNode(self);
				}
			}
		}
		
		this.appendBeforeNode = function(node) {
			if(self.parentNode && node.parentNode && node != self) {
				if(self.parentNode == node.parentNode && self.position < node.position) {
					node.parentNode.addSubNode(self, node.position - 1);
				} else {
					node.parentNode.addSubNode(self, node.position);
				}
			}	
		}
		
		this.appendAfterNode = function(node) {
			if(self.parentNode && node.parentNode && node != self) {
				if(self.parentNode == node.parentNode && self.position < node.position) {
					node.parentNode.addSubNode(self, node.position);
				} else {
					node.parentNode.addSubNode(self, node.position + 1);
				}
			}
		}
		
		this.detachFromParentNode = function() {
			if(self.parentNode) {
				self.parentNode.detachSubNode(self);
			}
		}
		
		
		this.getAboveNode = function() {
			if(self.parentNode) {
				if(self.position == 0) {
					return self.parentNode;
				} else {
					var node = self.parentNode.subNodes[self.position - 1];
					
					while(node.subNodes.length > 0 && !node.collapsed) {
						node = node.subNodes[node.subNodes.length - 1];
					}
					
					return node;
				}
			} else {
				return null;
			}
		}
		
		this.getBelowNode = function() {
			if(self.collapsed || self.subNodes.length == 0) {
				var node = self;
				
				while(node.parentNode && node.position == node.parentNode.subNodes.length - 1) { // is last
					node = node.parentNode;
				}
				
				if(node.parentNode) {
					return node.parentNode.subNodes[node.position + 1];
				} else {
					return null;
				}
			} else { // if not collasped and have some elements
				return self.subNodes[0];
			}
		}
		
		this.isChildOfNode = function(parentNode) {
			var node = self;
			
			while(node) {
				if(node == parentNode) { return true; }
				node = node.parentNode;
			}
			
			return false;
		}
		
		this.setName = function(name) {
			self.name = name;
			self.$nodeName.html(name);
		}
		
		this.addIcon = function(url, position) {
			if(typeof position == 'undefined') { var position = 'left'; }
			var $icon = $('<div class="icon custom"></div>');
			$icon.css('backgroundImage', "url('" + url + "')");
			
			switch(position) {
				case 'left':
					self.$leftIcons.append($icon);
				break;
				case 'right':
					self.$rightIcons.append($icon);
				break;
			}
		}
		
		
	/*
	*	Methods as parent
	*/
		
		this.addSubNode = function(subNode, position) {
			if(typeof position != 'number') { position = self.subNodes.length; }
			
				// prevent to put node in one of its subNodes
			if(self.isChildOfNode(subNode)) { return; }
					
				// change parent ?
			if(subNode.parentNode) {
				if(subNode.parentNode == self) {
					self.changeSubNodePosition(subNode, position);
					return;
				} else {
					subNode.detachFromParentNode();
				}
			}
			
			subNode.parentNode = self;
			self._addSubNode(subNode, position);
			self._updateSubNodesIndentIcon();
		}
				// append node to DOM
			this._addSubNode = function(subNode, position) {
				//console.log('change DOM');
				if(self.subNodes.length == 0) {
					self.$node.append(self.$subNodes);
					self.$subNodes.append(subNode.$node);
					self.subNodes.push(subNode);
				} else {
					position = Math.min(self.subNodes.length, position);
					
					if(position == 0) {
						self.subNodes[0].$node.before(subNode.$node);
					} else {
						self.subNodes[position - 1].$node.after(subNode.$node);
					}
					
					self.subNodes.splice(position, 0, subNode);
				}
			}
		
		this.detachSubNode = function(subNode) {
			if(subNode.parentNode && subNode.parentNode == self) {
				subNode.parentNode = null;
				subNode.position = -1;
				
				self._detachSubNode(subNode);
				
				self._updateSubNodesIndentIcon();
			}
		}
				// remove from DOM
			this._detachSubNode = function(subNode) {
				for(var i = 0; i < self.subNodes.length; i++) {
					if(self.subNodes[i] == subNode) {
						self.subNodes.splice(i, 1);
						break;
					}
				}
	
				if(self.subNodes.length == 0) {
					self.$subNodes.detach();
				}
				
				subNode.$node.detach();
			}
			
		this.changeSubNodePosition = function(subNode, position) {
			if(subNode.parentNode && subNode.parentNode == self && subNode.position != position) {
				self._detachSubNode(subNode);
				self._addSubNode(subNode, position);
				
				self._updateSubNodesIndentIcon();
			}
		}
		
		
		this.showSubNodes = function() {
			if(self.collapsed && self.subNodes.length > 0) {
				self.collapsed = false;
				self.$subNodes.slideDown(100, function() {
					self._updateIndentIcon();
				});
			}
		}
		
		this.hideSubNodes = function() {
			if(!self.collapsed && self.subNodes.length > 0) {
				self.collapsed = true;
				self.$subNodes.slideUp(100, function() {
					self._updateIndentIcon();
				});
			}
		}
		
		this.toogleDisplayOfSubNodes = function() {
			if(self.collapsed) {
				self.showSubNodes();
			} else {
				self.hideSubNodes();
			}
		}
	
	
	/*
	*	Methods on header
	*/
	
		this.select = function() {
			if(!self.selected) {
				if(!self.nodeName.focused) {
					self.$nodeName.addClass('selected');
				}
				
				self.selected = true;
			}
		}
		
		this.deselect = function() {
			if(self.selected) {
				self.$nodeName.removeClass('selected');
				self.selected = false;
			}
		}
		
		
		this.activateUserRename = function() {
			if(!self.nodeName.focused) {
				if(self.selected) {
					self.$nodeName.removeClass('selected');
				}
				
				self.$nodeName.attr('contenteditable', 'true');
				self.$nodeName.addClass('focused');
				self.nodeName.focus();
				self.nodeName.focused = true;

				range = document.createRange();
				range.selectNodeContents(self.nodeName);
				range.setStart(self.nodeName.firstChild, self.nodeName.innerText.length);
				range.setEnd(self.nodeName.lastChild, self.nodeName.innerText.length);

				selection = window.getSelection();
				selection.removeAllRanges();
				selection.addRange(range);
			}
		}
		
		this.deactivateUserRename = function() {
			if(self.selected) {
				self.$nodeName.addClass('selected');
			}
				
			self.$nodeName.removeAttr('contenteditable');
			self.$nodeName.removeClass('focused');
			self.nodeName.focused = false;
			
			var newName = self.nodeName.innerText;
			if(newName != self.name) {
				self.name = newName;
				console.log(newName);
			}
		}
		
	
	/*
	*	Others
	*/
		this._setIndentIcon = function(index) {
			self.indentIndex = index;
			self._updateIndentIcon();
		}
		
		this._updateIndentIcon = function() {
			var j = k = - 1;
			if(self.subNodes.length > 0) {
				if(self.collapsed) {
					j = 1;
				} else {
					j = 2;
				}
			} else {
				self.collapsed = false;
				j = 0;
			}
			
			if(self.isLink) {
				k = 1;
			} else {
				k = 0;
			}

			self.$indentIcon.css('backgroundImage', "url('" + self.indentImages[self.indentIndex][j][k] + "')");
		}
		
		this._updateSubNodesIndentIcon = function() {
			for(var ind = 0; ind < self.subNodes.length; ind++) {
				var node = self.subNodes[ind];
				node.position = ind;
				
				var i = j = k = - 1;
				
				if(node.isRoot == 1) {
					i = 0;
				} else {
					if(ind == self.subNodes.length - 1) {
						i = 2;
					} else {
						i = 1;
					}
				}
				
				node._setIndentIcon(i);
			}
			
			self._updateIndentIcon();
		}
		
		this.init(settings);
	}
	
	node = new Node({
		isRoot: true,
		name: 'salut0',
		subNodes: [
			new Node({
				name: 'salut1'
			}),
			new Node({
				name: 'salut2',
				subNodes: [
					new Node({
						name: 'salut3',
						icons: { left: [/*'ressources/images/icons/sound_01.png'*/], right: ['ressources/images/icons/eye_01.png'/*, 'ressources/images/icons/eye_02.png'*/, 'ressources/images/icons/add_05.png'] }
					}),
					new Node({
						isLink: true,
						name: 'salut4______________________________________________________________________________________________-h',
						icons: ['ressources/images/icons/images_01.png']
					}),
					new Node({
						name: 'salut5',
						subNodes: [
							new Node({
								name: 'salut8',
								icons: ['ressources/images/icons/sound_01.png']
							}),
							new Node({
								isLink: true,
								name: 'salut9',
								icons: ['ressources/images/icons/images_01.png']
							}),
							new Node({
								name: 'salut10',
								icons: ['ressources/images/icons/flow_01.png']
							})
						]
					})
				]
			}),
			new Node({
				name: 'salut6',
				subNodes: [
					new Node({
						name: 'salut7'
					})
				]
			})
		]
	});
	
	node.appendToDomElement($leftPanel);
	
	fnc.libReady('Node', Node);
});