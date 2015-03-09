<?php
	switch($_SERVER['HTTP_HOST']) {
		case 'localhost':
		case '127.0.0.1':
			$root = '../..';
		break;
		case 'documents.valentin-richard.com':
			$root = '..';
		break;
		default:
			$root = '../..';
	}
	
	$vars = array();
	
	function replaceIncludesCallback($matches) {
		global $url;
		return file_get_contents(dirname($url) . '/' . $matches[1]);
	}
	
	function replaceVariablesCallback($matches) {
		global $vars;
		$vars[removeSpace($matches[1])] = removeSpace($matches[2]);
		return '';
	}
	
	function replaceExtendCallback($matches) {
		global $vars;
		
		$parentPath = $vars[1];
		$extendPath = $vars[2];
		
		$extendPathLine = removeSpace($matches[2]);
		
		$newPath = $extendPathLine;
		$extendPathTable = explode(",", $extendPathLine);
		$parentPathTable = explode(",", $parentPath);
		
		for($i = 0; $i < count($extendPathTable); $i++) {
		
			$bloc = removeSpace($extendPathTable[$i]);
			
			if(preg_match('#' . preg_quote($extendPath) . '#isU', $bloc)) {
			
				for($j = 0; $j < count($parentPathTable); $j++) {
				
					$newPath .= ", " . preg_replace('#' . preg_quote($extendPath) . '#isU', removeSpace($parentPathTable[$j]), $bloc);
				}
			}
			
		}
		
		if($matches[1] != "") {
			$newPath = $matches[1] . "\n\n" . $newPath;
		}
		
		return  $newPath . " {";
	}
	
	function replaceColorCallback($matches) {
		switch($matches[1]) {
			case 'rgba' :
				for($i = 2; $i <= 4; $i++) {
					if(preg_match('#([0-9]+%)#isU', $matches[$i], $matches2)) {
						$matches[$i] = round(255 * $matches2[1] / 100);
					}
				}
				return 'rgb(' . $matches[2] . ', ' . $matches[3] . ', ' . $matches[4] . ')';
			break;
			case 'hsl' :
			case 'hsla' :
				$H = $matches[2] / 360;
				$S = $matches[3] / 100;
				$L = $matches[4] / 100;
				
				if($S == 0) {
					$R = $L * 255;
					$G = $L * 255;
					$B = $L * 255;
				} else {
					if($L < 0.5) {
						$var_2 = $L * (1 + $S);
					} else {
						$var_2 = ($L + $S) - ($S * $L);
					}

					$var_1 = 2 * $L - $var_2;

					$R = 255 * Hue_to_RGB( $var_1, $var_2, $H + (1/3));
					$G = 255 * Hue_to_RGB( $var_1, $var_2, $H);
					$B = 255 * Hue_to_RGB( $var_1, $var_2, $H - (1/3));
				}
				
				return 'rgb(' . $R . ', ' . $G . ', ' . $B . ')';
			break;
		}
		
		return $matches[0];
	}
		
	function findParentheses($matches) {
		$value = preg_replace('#,#isU', 'virgule', $matches[1]);
		return '(' . $value . ')';
	}
	
	
	function Hue_to_RGB($v1, $v2, $vH ) {
		if($vH < 0) { $vH += 1; }
		if($vH > 1) { $vH -= 1; }
		
		if(6 * $vH < 1) { return  $v1 + ($v2 - $v1) * 6 * $vH;	}
		
		if(2 * $vH < 1 ) { return $v2; }
		if(3 * $vH < 2 ) { return $v1 + ($v2 - $v1) * ((2/3) - $vH) * 6 ; }
		
		return $v1;
	}

	function tab($string) {
		$str = "";
		
		$table = preg_split("/(\r\n)|(\n)/", $string);
		
		for($i = 0; $i < count($table); $i++) {
			$str .= "\t" . $table[$i] . "\n";
		}
		
		return $str;
	}
	
	
	class CSSelement {
	
		function __construct($name, $css) {
							
			$this->name = $name;
			$this->attributes = array();
			$this->childrens = array();
			
			$this->prefixes = array('-webkit-', '-moz-', '-o-', '-ms-');
			
			$this->attributesToConvert = array('animation', 'animation-name', 'animation-duration', 'animation-iteration-count', 'animation-direction', 'animation-timing-function', 'animation-fill-mode', 'animation-delay',
											'background-size', 'background-origin', 'background-clip',
											'box-shadow', 'box-sizing',
											'resize',
											'text-shadow', 'text-overflow', 'word-wrap',
											'transform', 'transform-origin', 'transform-style', 'backface-visibility', 'perspective',
											'transition', 'transition-property', 'transition-duration', 'transition-timing-function', 'transition-delay',
											'user-select');
			
			$this->valuesToConvert = array('linear-gradient', 'radial-gradient');
			
			
			$this->convertToObject($css);
		}
		
		function insertFormatedAttribute($attribute, $value) {
		
			switch($attribute) {
				case 'text-border':
					$attribute = 'text-shadow';
					$value = preg_replace(
						'#^ *([0-9]+)px +(.*)$#isU',
						'0px $1px $1px $2, 0px -$1px $1px $2, $1px 0px $1px $2, -$1px 0px $1px $2, $1px $1px $1px $2, -$1px $1px $1px $2, $1px -$1px $1px $2, -$1px -$1px $1px $2;',
						$value
					);
				break;
				case 'border':
					$content = preg_replace_callback('#\((.*)\)#isU', 'findParentheses', $value);
					
					$table = explode(',', $content);
					
					if(count($table) > 1) {
						
						$attribute = 'box-shadow';
						$value = '';
						
						$px = 0;
						
						for($i = 0; $i < count($table); $i++) {
							$table[$i] = preg_replace('#virgule#isU', ',', $table[$i]);
							
							if($i > 0) { $value .= ', '; }

							if(preg_match('#^ *([0-9]+)px +[a-z]+ +(.+)$#isU', $table[$i], $matches)) {	
								$px += $matches[1];
								$value .= '0px 0px 0px ' . $px . 'px ' . $matches[2] . '';
							}
						}
						
						$this->insertFormatedAttribute('margin', $px . 'px');
					}
				break;
			}
			
			$rgbaRegExp = '#(rgba|hsl|hsla) *\( *([0-9]+%?) *, *([0-9]+%?) *, *([0-9]+%?) *(, *([\.0-9]+) *)?\)#isU';
			
			if(preg_match('#rgba|hsl#isU', $value)) {
				$this->insertAttribute($attribute, preg_replace_callback($rgbaRegExp, 'replaceColorCallback' , $value));
			}
				
				
			if(in_array($attribute, $this->attributesToConvert)) {
				for($i = 0; $i<count($this->prefixes); $i++) {
					$this->insertAttribute($this->prefixes[$i] . $attribute, $value);
				}
			}
		
			
			for($i = 0; $i < count($this->valuesToConvert); $i++) {
				if(preg_match('#' . preg_quote($this->valuesToConvert[$i]) . '#isU', $value, $matches)) {
					for($j = 0; $j < count($this->prefixes); $j++) {
						$newValue = preg_replace('#' . preg_quote($this->valuesToConvert[$i]) . '#isU', $this->prefixes[$j] . $this->valuesToConvert[$i], $value);
						$this->insertAttribute($attribute, $newValue);
					}
				}
			}
			
			$this->insertAttribute($attribute, $value);
		}
		
		function insertAttribute($attribute, $value) {
			$count = count($this->attributes);
			$this->attributes[$count] = array();
			$this->attributes[$count]['name'] = $attribute;
			$this->attributes[$count]['value'] = $value;
		}
		
		function convertToObject($css) {
			$index = 0;
			$string = "";
						
						
			//search childrens
			for($i = 0; $i < strlen($css); $i++){
				$char = $css[$i];
				
				switch($char) {
					case '{':
						if($index == 0) {
							$name = "";
							$strlen = strlen($string);
							
							for($j = $strlen - 1; $j >= 0; $j--) {
								$char2 = $string[$j];
								if($char2 == ';') {
									break;
								} else {
									$name .= $char2;
								}
							}
							
							$name = removeSpace(strrev($name));
							
							$string = substr($string, 0, $j - $strlen + 1);
							$childrenContent = "";
						} else {
							$childrenContent .= $char;
						}
							
						$index++;
					break;
					
					case '}':
						$index--;
						if($index == 0) {
							$this->childrens[] = new CSSelement($name, $childrenContent);
						} else {
							$childrenContent .= $char;
						}
					break;
					
					default:
						if($index == 0) {
							$string .= $char;
						} else {
							$childrenContent .= $char;
						}
				}
			}
	
			
			//search attributes
			$lignes = explode(';', $string);
			
			for($i = 0; $i < count($lignes); $i++) {
				
				$ligne = removeSpace($lignes[$i]);
			
				$table = explode(':', $ligne);
				
				if(isset($table[1])) {
					$this->insertFormatedAttribute(removeSpace($table[0]), removeSpace($table[1]));
				}
			}	
		}
		
		function convertToCSS($parentPath = "") {
			
			$css = "";
			$path = "";
			$parentPathTable = explode(',', $parentPath);
			$childrenPathTable = explode(',', $this->name);
			
			for($i = 0; $i < count($parentPathTable); $i++) {
			
				if($i > 0) {
					$path .= ", ";
				}
				
				for($j = 0; $j < count($childrenPathTable); $j++) {
					
					$name = removeSpace($childrenPathTable[$j]);
					
					if($name != "") {
						if($j > 0) {
							$path .= ", ";
						}
						
						
						if($name[0] == '&') {
							$name = substr($name, 1, strlen($name) - 1);
						} else if($parentPath != "" && $name[0] != ':'){
							$name = " " . $name;
						}
						
						$name = preg_replace('# \& #isU', '', $name);
						
						$path .= removeSpace($parentPathTable[$i]) . $name;
					}
				}
			}
						
			
			if($path != "" && count($this->attributes) > 0) {
				$css .=  $path . " {\n";
				
				for($i = 0; $i < count($this->attributes); $i++) {
					$attribute = $this->attributes[$i];
					
					$css .= "\t" . $attribute['name'] .": " . $attribute['value'] . ";\n";
				}
				
				$css .= "}\n\n";
			}
			
			
			if(false && isset($path[0]) && $path[0] == '@') {
					//replace @ selector elements
				$path = substr($path, 1, strlen($path) - 1);
				
				$string =  $path . " {\n";
				
				for($i = 0; $i < count($this->childrens); $i++) {
					$string .= tab($this->childrens[$i]->convertToCSS());
				}
				
				$string .= "}\n\n";
				
				for($i = 0; $i<count($this->prefixes); $i++) {
					$css .= "@" . $this->prefixes[$i] . $string;
				}
				
				$css .= "@" . $string;
				
			} else {
				for($i = 0; $i < count($this->childrens); $i++) {
					$css .= $this->childrens[$i]->convertToCSS($path);
				}
			}
			
			return $css;
		}
	}
	
	
	function removeSpace($str) {
		$str = preg_replace('#^ *#is', '', $str);
		$str = preg_replace('# *$#is', '', $str);
		$str = preg_replace('#  +#is', ' ', $str);
		$str = preg_replace('#[\t\n\r]*#is', '', $str);
		return $str;
	}
	
	function replaceIncludes($css) {

		$css = preg_replace_callback(
			'#include *["\'](.*)["\'] *;#isU',
			'replaceIncludesCallback',
			$css
		);
		
		return $css;
	}
	
	function replaceVariables($css) {
		global $vars;
		$var = array();
		
		$css = preg_replace_callback(
			'#\$([0-9a-zA-Z_]*) *[:=] *["\'](.*)["\'] *;#isU',
			'replaceVariablesCallback',
			$css
		);
		
		foreach($vars as $key => $value) {
			$css = preg_replace('#\$' .preg_quote($key) .'([\W])#isU', $value . '$1', $css);
		}
		
		return $css;
	}
	
	function getCssPath($css, $offset) {
	
		$parentPath = "";
		$startNameRecord = false;
		
		for($j = $offset; $j >= 0; $j--) {
			$char = $css[$j];
			
			if($char == '{') {
				$startNameRecord = true;
				continue;
			}
			
			if($startNameRecord) {
				if($char == '}' || $char == ';') {
					break;
				}
				
				$parentPath .= $char;
			}
		}
		
		$parentPath = removeSpace(strrev($parentPath));
		
		return $parentPath;
	}
	
	
	function replaceExtend($css) {

		global $vars;
		
		preg_match_all('#extend *: *["\'](.*)["\'] *;#isU', $css, $matches, PREG_OFFSET_CAPTURE);
		$newCss = preg_replace('#extend *: *["\'](.*)["\'] *;#isU', '', $css);
		
		$extendPathes = array();
		
		for($i = 0; $i < count($matches[1]); $i++) {
			
			$extendPath = $matches[1][$i][0];
			$offset = $matches[1][$i][1];
			
			$parentPath = getCssPath($css, $offset);
			
			$vars = array();
			$vars[1] = $parentPath;
			$vars[2] = $extendPath;
			
			$newCss = preg_replace_callback('#(^|\})([^\}]*' . preg_quote($extendPath) . '.*)\{#isU',
				'replaceExtendCallback',
				$newCss
			);
		}
		
		
		return $newCss;
	}
	
	
	function removeComments($css) {
		$css = preg_replace('#/\*(.*)\*/#isU', '', $css);
		return $css;
	}
	
	function compress($css) {
			//remove comments
		$css = preg_replace('#/\*(.*)\*/#isU', '', $css);
		
			//remove \t\n\r
		$css = preg_replace('#\t|\n|\r#isU', '', $css);
		
		return $css;
	}
	
	
	header("Status: 200 OK", false, 200);
	header("Content-Type: text/css");
	
	if($_SERVER['REQUEST_URI'] != '' && $_SERVER['REQUEST_URI'] != '/') {
		$url = $_SERVER['REQUEST_URI'];
	} else if($_SERVER['REDIRECT_URL'] != '' && $_SERVER['REDIRECT_URL'] != '/') {
		$url = $_SERVER['REDIRECT_URL'];
	}
	
	$url = $root . $url;
	
	if(!file_exists($url)) {
		exit('Fichier introuvable : ' . $url);
	}
	
	$css = file_get_contents($url);
	
		// replace includes
	$css = replaceIncludes($css);
	
		//remove comments
	$css = removeComments($css);
	
		//replace variables
	$css = replaceVariables($css);
	
		//create css object
	$cssObject = new CSSelement('', $css);
	
		//recompose css
	$css = $cssObject->convertToCSS();
	
	
	$css = replaceExtend($css);
	
	
		//compress css
	//$css = compress($css);
	
	echo $css;
?>