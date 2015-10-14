var reg;

fnc.require(['RegEx'], function() {
	//a = SuperRegExp('(d)#((a)|(b)|(?!c))#{var}', 'g');
	//reg = new SuperRegExp('#((a)|(b))#{var}', 'g');
	//reg = new SuperRegExp('<(a)|(b)<(c)>{var1}>{var0}', 'g');
	reg = new RecursiveRegExp('<([a-zA-Z0-9]+)>', 'g', '</$1>', 'g');
	//console.log(reg.matchAll('</tag><tag>a<tag>b</tag>c<tag>d</tag>e</tag><tag>', false));
	//console.log(reg.matchAll('</tag><tag>a<tag>b</tag>c<tag>d</tag>e</tag>', true));
	//console.log(reg.matchAll('<tag>a<tag>b</tag>c<tag>d</tag>e</tag><tag>', true));
	//console.log(reg.matchAll('<tag1>a<tag2>b</tag1>c</tag2>', false));
	//reg = new RegExp('(a)', 'g');
	//reg.replace('abc', function(match) { debugger; return 'ok'; });
	//console.log(reg.matchAll('aba'));
	
	//Array.prototype.
	var sample = ""		+
	"<tag1>"			+
		"<tag2>"		+
			"a"			+
		"</tag2>"		+
		"<tag3>"		+
			"b"			+
		"</tag3>"		+
	"</tag1>"			+
	"";
	
	str = new VisibilityString(sample);
	str.setVisibility(6, 20, VisibilityString.transparent);
	//console.log(reg.matchAll(str, false));
	var reg_1 = /<(?!\/)([^>]+)>/g;
	console.log(reg_1._match(str));
	console.log(reg_1._match(str));
	console.log(reg_1._match(str));
	console.log(reg_1._match(str));
	
	/*a = str.getVisibility();
	console.log(a);*/
	
	/*a = /a./g;
	a.deepInspection = true;
	console.log(a.matchAll("aab"));*/
	
	/*a = new SplitedString('0123456789');
	a.setVisibility(2, 4, SplitedString.invisible);
	console.log(a.toString());
	
	console.log(a.getVisibility());*/
	
	/*a.split(2, 4, SplitedString.visible);
	console.log(a.toString());*/
	
});