var IntervalArray;

fnc.require(['ChainedNode'], function() {
	
	Interval = function(start, end, value) {
		this.start	= start;
		this.end	= end;
		this.value	= value;
	}
	
	Interval.prototype.toString = function() {
		return "[" + this.start + ", " + this.end + ", \"" + this.value.toString() + "\"]";
	}
	
	
	
	
	IntervalArray = function(size, value) {
		this.size		= size;
		this.intervals	= new ChainedNodeList();
		this.intervals.push(new ChainedNode(new Interval(0, this.size, value)));
	}
	
	IntervalArray.prototype.getIntervalContaining = function(index) {
		if((index < 0) || (index > this.size)) {
			throw "index is out of range (searching for " + index + " in [0, " + this.size + "])";
		}
		
		if(index == this.size) {
			return this.intervals.last();
		}
			
		var iterator	= this.intervals.iterator();
		var node, interval;
		do {
			node		= iterator.getNode();
			interval	= node.value;
			
			if((interval.start <= index) && (index < interval.end)) {
				return node;
			}	
		} while(iterator.next());
		
		//throw "no interval found for index = " + index;
	}
	
	
	IntervalArray.prototype.modify = function(start, end , value) {
		var startNode	= this.getIntervalContaining(start);
		var end			= this.getIntervalContaining(end);
	}
	
	
	IntervalArray.prototype.toString = function() {
		return this.intervals.iterator().toString();
	}

	fnc.libs['IntervalArray'] = IntervalArray;
	
});