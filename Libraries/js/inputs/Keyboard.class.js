var Keyboard;

fnc.require(['Class', 'HTML5'], function() {

	var KeyboardController = function() {
		var self = this;
		ClassWithBinds(self);
		
		this.init = function() {
			self.bindElement = document.body;
		
			self.key = [];
			self.bindedKeyList = [];
			
				// control commands
			self._preventDefaultAction = false;
			self.isGameInputsEnabled = false;
		
			self._initKeyList();
			
			self.bindElement.addEventListener('keydown', self._onkeydown, false);
			self.bindElement.addEventListener('keyup', self._onkeyup, false);
			
			//addHTML5EventListener(document, ['pointerlockchange'], self._pointerLockChange);
		}
		
		this._initKeyList = function() {
				// keyList
			self.keyList = [];
			self.bindKey('BACKSPACE',	8);
			self.bindKey('TAB',			9);
			self.bindKey('ENTER',		13);
			self.bindKey('MAJ',			16);
			self.bindKey('CTRL',		17);
			self.bindKey('ALT',			18);
			self.bindKey('CAPS_LOCK',	20);
			self.bindKey('SPACE',		32);
			self.bindKey('PAGE_UP',		33);
			self.bindKey('PAGE_DOWN',	34);
			self.bindKey('END',			35);
			self.bindKey('HOME',		36);
			self.bindKey('LEFT',		37);
			self.bindKey('UP',			38);
			self.bindKey('RIGHT',		39);
			self.bindKey('DOWN',		40);
			self.bindKey('*',			42);
			self.bindKey('INSERT',		45);
			self.bindKey('DELETE',		46);
			self.bindKey('/', 47);
			self.bindKey('0', 48);
			self.bindKey('1', 49);
			self.bindKey('2', 50);
			self.bindKey('3', 51);
			self.bindKey('4', 52);
			self.bindKey('5', 53);
			self.bindKey('6', 54);
			self.bindKey('7', 55);
			self.bindKey('8', 56);
			self.bindKey('9', 57);
			self.bindKey('A', 65);
			self.bindKey('B', 66);
			self.bindKey('C', 67);
			self.bindKey('D', 68);
			self.bindKey('E', 69);
			self.bindKey('F', 70);
			self.bindKey('G', 71);
			self.bindKey('H', 72);
			self.bindKey('I', 73);
			self.bindKey('J', 74);
			self.bindKey('K', 75);
			self.bindKey('L', 76);
			self.bindKey('M', 77);
			self.bindKey('N', 78);
			self.bindKey('O', 79);
			self.bindKey('P', 80);
			self.bindKey('Q', 81);
			self.bindKey('R', 82);
			self.bindKey('S', 83);
			self.bindKey('T', 84);
			self.bindKey('U', 85);
			self.bindKey('V', 86);
			self.bindKey('W', 87);
			self.bindKey('X', 88);
			self.bindKey('Y', 89);
			self.bindKey('Z', 90);
			self.bindKey('WINDOWS', 91);
			self.bindKey('CONTEXT_MENU', 93);
			self.bindKey('0', 96);
			self.bindKey('1', 97);
			self.bindKey('2', 98);
			self.bindKey('3', 99);
			self.bindKey('4', 100);
			self.bindKey('5', 101);
			self.bindKey('6', 102);
			self.bindKey('7', 103);
			self.bindKey('8', 104);
			self.bindKey('9', 105);
			self.bindKey('+', 107);
			self.bindKey('-', 109);
			self.bindKey('.', 110);
			self.bindKey('F1', 112);
			self.bindKey('F2', 113);
			self.bindKey('F3', 114);
			self.bindKey('F4', 115);
			self.bindKey('F5', 116);
			self.bindKey('F6', 117);
			self.bindKey('F7', 118);
			self.bindKey('F8', 119);
			self.bindKey('F9', 120);
			self.bindKey('F10', 121);
			self.bindKey('F11', 122);
			self.bindKey('F12', 123);
			self.bindKey('NUM_LOCK', 144);
		}
		
		this.preventDefaultAction = function(preventDefaultAction) {
			self._preventDefaultAction = preventDefaultAction;
		}
		
		
		this.getKeyCodesFromKeyName = function(keyName) {
			var keyCodes = [];
			
			for(var i = 0; i < self.keyList.length; i++) {
				if(typeof self.keyList[i] != 'undefined') {
					for(var j = 0; j < self.keyList[i].length; j++) {
						if(keyName == self.keyList[i][j]) {
							keyCodes.push(i);
							break;
						}
					}
				}
			}
			
			return keyCodes;
		}
		
		this.bindKey = function(keyName, keyCode) {
			switch(typeof keyCode) {
				case 'string':
					var keyCodes = self.getKeyCodesFromKeyName(keyCode);

					for(var i = 0; i < keyCodes.length; i++) {
						self.bindKey(keyName, keyCodes[i]);
					}
				break;
				case 'number':
					if(typeof self.keyList[keyCode] == 'undefined') {
						self.keyList[keyCode] = [];
					}
					
					self.keyList[keyCode].push(keyName);
				break;
			}
		}
		
		this.unbindKey = function(keyName, keyCode) {
			switch(typeof keyCode) {
				case 'string':
					var keyCodes = self.getKeyCodesFromKeyName(keyCode);
					
					for(var i = 0; i < keyCodes.length; i++) {
						self.unbindKey(keyName, keyCodes[i]);
					}
				break;
				case 'number':
					if(typeof self.keyList[keyCode] != 'undefined') {
						for(var i = 0; i < self.keyList[keyCode].length; i++) {
							if(self.keyList[keyCode][i] == keyName) {
								self.keyList[keyCode].splice(i, 1);
								i--;
							}
						}
					}
				break;
				case 'undefined':
					var keyCodes = self.getKeyCodesFromKeyName(keyName);
					
					for(var i = 0; i < keyCodes.length; i++) {
						self.unbindKey(keyName, keyCodes[i]);
					}
				break;
			}
		}
		
		
		this._onkeydown = function(event) {
			//alert(event.keyCode);
			
			self.key[event.keyCode] = true;
			
			for(var i = 0; i < self.keyList[event.keyCode].length; i++) {
				self.key[self.keyList[event.keyCode][i]] = true;
			}
			
			if(self._preventDefaultAction) {
				event.preventDefault();
				return false;
			}
		}
		
		this._onkeyup = function(event) {
			self.key[event.keyCode] = false;
			
			for(var i = 0; i < self.keyList[event.keyCode].length; i++) {
				self.key[self.keyList[event.keyCode][i]] = false;
			}
			
			if(self._preventDefaultAction) {
				event.preventDefault();
				return false;
			}
		}
		
		this.init();
	}
		
	Keyboard = new KeyboardController();
	fnc.libs['Inputs/Keyboard'] = Keyboard;
});