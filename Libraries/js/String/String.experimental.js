fnc.require(['RegEx'], function() {
	String.prototype.tab = function() {
		return "\t" + RegEx.replace(/(\n\r?)/g, "$1\t", this);
	}

	String.prototype.cut = function(startOffset, endOffset){
		return this.substr(0, startOffset) + this.substr(endOffset);
	}
	
	String.prototype.escape = function(){
		return escape(this);
	}
	
	String.prototype.unescape = function(){
		return unescape(this);
	}

	fnc.libs['String'] = String;
});