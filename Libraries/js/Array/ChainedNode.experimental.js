var ChainedNode, ChainedNodeIterator, ChainedNodeList;

fnc.require([], function() {
	
	ChainedNode = function(value) {
		if(typeof value == "undefined") { var value = null; }
		
		this.next		= this;
		this.previous	= this;
		
		this.value		= value;
	}
	
		// link two nodes together : set node_1 as the next of node_0
	ChainedNode.link = function(node_0, node_1) {
		node_0.next			= node_1;
		node_1.previous		= node_0;
		return ChainedNode;
	}

	
/**
	Prototype part, this functions are here to help to understand the mechanism
**/

	/*
		{ a, b, c, d, e, f } , { g, h, i, j, k, l }
		
		(c, i) => { a, b, c, i, j, k, l, g, h, d, e, f } : append next chainedList starting from i after c
		(i, c) => { a, b, j, k, l, g, h, i, c, d, e, f } : append previous chainedList ending by i before c

		(b, d) => { a, b, e, f }, { c, d } : detach nodes between b and e (both not included)
		(b, b) => { a, c, d, e, f }, { b } : detach node b
	*/
	
		// attach nodes starting from node2 after node1
	ChainedNode.attach = function(node1, node2) {
		ChainedNode.link(node2.previous, node1.next);
		ChainedNode.link(node1, node2);
		return ChainedNode;
	}
	
	
		// detach nodes between node1 and node2 (both included)
	ChainedNode.detach = function(node1, node2) {
		ChainedNode.attach(node1.previous, node2.next);
		return ChainedNode;
	}
	
	ChainedNode.replace = function(node_0_0, node_0_1, node_1_0, node_1_1) {
		var node_0_0_prev = node_0_0.previous;
		var node_1_0_prev = node_1_0.previous;
		var node_0_1_next = node_0_1.next;
		var node_1_1_next = node_1_1.next;
		
		ChainedNode.link(node_0_0_prev, node_1_0);
		ChainedNode.link(node_1_1, node_0_1_next);
		
		ChainedNode.link(node_1_0_prev, node_1_1_next);
		
		return this;
	}
	
	
	/*
		(b, j, h, d) = (b, d, h, j)
		
		node_0_0, node_0_1 => same chain
		node_1_0, node_1_1 => same chain
	*/
	
	ChainedNode.switch = function(node_0_0, node_0_1, node_1_0, node_1_1) {
		var node_0_0_prev = node_0_0.previous;
		var node_1_0_prev = node_1_0.previous;
		var node_0_1_next = node_0_1.next;
		var node_1_1_next = node_1_1.next;
		
		ChainedNode.link(node_0_0_prev, node_1_0);
		ChainedNode.link(node_1_0_prev, node_0_0);
		
		ChainedNode.link(node_0_1, node_1_1_next);
		ChainedNode.link(node_1_1, node_0_1_next);
		
		return this;
	}

	
/**
	END OF - Prototype part
**/


	
	ChainedNode.prototype.setValue = function(value) {
		this.value = value;
		return this;
	}
	
	ChainedNode.prototype.appendNext = function(node) {
		if(node !== this) {
			ChainedNode.link(node.previous, node.next); // properly detach the node
			ChainedNode.link(node, this.next);
			ChainedNode.link(this, node); // this -> node -> this.next
		}
		
		return this;
	}
	
	ChainedNode.prototype.appendPrevious = function(node) {
		return this.previous.appendNext(node);
	}
	
	ChainedNode.prototype.detach = function() {
		if(!this.isDetached()) {
			ChainedNode.link(this.previous, this.next);
			ChainedNode.link(this, this);
		}
		return this;
	}
	
	ChainedNode.prototype.isDetached = function() {
		return (this.next === this);
	}
	
	ChainedNode.prototype.replaceBy = function(node) {
		if((node !== this) && (!this.isDetached())) {
			ChainedNode.link(node.previous, node.next); // properly detach the node
			ChainedNode.link(this.previous, node);
			ChainedNode.link(node, this.next); // previous -> node -> next
			ChainedNode.link(this, this);
		}
		
		return this;
	}
	
	ChainedNode.prototype.toString = function() {
		return this.value.toString();
	}
	
	
	ChainedNodeIterator = function(node) {
		this._originNode	= node;
		this._currentNode	= node;
	}
	
	ChainedNodeIterator.prototype.looped = function() {
		return (this._currentNode === this._originNode);
	}

	ChainedNodeIterator.prototype.next = function() {
		this._currentNode	= this._currentNode.next;
		return !this.looped();
	}

	ChainedNodeIterator.prototype.previous = function() {
		this._currentNode = this._currentNode.previous;
		return !this.looped();
	}

	ChainedNodeIterator.prototype.getNode = function() {
		return this._currentNode;
	}

	ChainedNodeIterator.prototype.size = function() {
		var _size = 0;
		do { _size++; } while(this.next());
		return _size;
	}
	
	ChainedNodeIterator.prototype.toString = function() {
		var string		= "";
		var i			= 0;
		do {
			if(i > 0) { string += ", "; }
			string += this.getNode().toString();
			i++;
		} while(this.next());
		
		return string;
	}
	
	
	
	ChainedNodeList = function() {
		this._first	= null;
	}
	
	ChainedNodeList.prototype.first = function() {
		return this._first;
	}
	
	ChainedNodeList.prototype.last = function() {
		if(this._first === null) {
			return null;
		} else {
			return this._first.previous;
		}
	}
	
	ChainedNodeList.prototype.iterator = function() {
		if(this._first === null) {
			return null;
		} else {
			return new ChainedNodeIterator(this._first);
		}
	}
	
	
	ChainedNodeList.prototype.appendNext = function(node_0, node_1) {
		if(node_0 === null) { // insert at the beginning (equivalent of unshift)
			this.appendPrevious(this._first, node_1);
		} else {
			node_0.appendNext(node_1);
		}
		
		return this;
	}
	
	ChainedNodeList.prototype.appendPrevious = function(node_0, node_1) {
		if(node_0 === this._first) {
			if(this._first !== null) {
				this._first.appendPrevious(node_1);
			}
			
			this._first	= node_1;
		} else {
			node_0.appendPrevious(node_1);
		}
		
		return this;
	}
	
	ChainedNodeList.prototype.detach = function(node) {
		if(node === this._first) {
			if(node.isDetached()) {
				this._first = null;
			} else {
				this._first = node.next;
				node.detach();
			}
		} else {
			node.detach();
		}
		
		
		return this;
	}
	
	
	ChainedNodeList.prototype.push = function(node) {
		return this.appendNext(this.last(), node);
	}
	
	ChainedNodeList.prototype.pop = function() {
		return this.detach(this.last());
	}
	
	ChainedNodeList.prototype.shift = function(node) {
		return this.appendPrevious(this._first, node);
	}
	
	ChainedNodeList.prototype.unshift = function(node) {
		return this.detach(this._first);
	}
	
	
	fnc.libs['ChainedNode'] = ChainedNode;
	

	/*a = new ChainedNode('a');
	b = new ChainedNode('b');
	c = new ChainedNode('c');
	d = new ChainedNode('d');
	e = new ChainedNode('e');
	f = new ChainedNode('f');
	g = new ChainedNode('g');
	h = new ChainedNode('h');
	i = new ChainedNode('i');
	j = new ChainedNode('j');
	k = new ChainedNode('k');
	l = new ChainedNode('l');
	
	a.appendNext(b);
	b.appendNext(c);
	c.appendNext(d);
	d.appendNext(e);
	e.appendNext(f);
	
	g.appendNext(h);
	h.appendNext(i);
	i.appendNext(j);
	j.appendNext(k);
	k.appendNext(l);*/
	
	//c.appendNextMany(d);
	//b.detachMany(d);
	
	//b.appendNextMany(d);
	
	//ChainedNode.join(c, c);
	
	//ChainedNode.attach(f, g);
	//ChainedNode.detach(c, i);
	
	//ChainedNode.replace(b, j, d, h);
	//ChainedNode.replace(b, d, h, j);
	//ChainedNode.replace(b, g, c, e);
	//ChainedNode.replace(a, b, );
	
	//ChainedNode.replace(b, null, g, k);
	//ChainedNode.replace(b, b, i, i);
	//b.replaceBy(i);
	
	/*var it = new ChainedNodeIterator(a);
	console.log(it.toString());
	
	var it = new ChainedNodeIterator(g);
	console.log(it.toString());*/

	
	/*var it = new ChainedNodeIterator(c);
	console.log(it.toString());*/
	
	
	
});