<?php
header("Content-Type: text/plain");
echo "result : \n";

require_once('../../../Libraries/php/Tree.class.php');
require_once('../scripts/php/fnc.php');

class Parser {
	
	public function __construct() {
	}
	
	private function _pushOffsetRange($offsetRange, $insertAfterElement) {
		
		if(($offsetRange->end - $offsetRange->start) > 0) {
			$tree = new Tree("raw", $offsetRange);
			$insertAfterElement->insertAfter($tree);
			return $tree;
		} else {
			return $insertAfterElement;
		}
	}
	
	private function _splitTree($tree, $subTrees) {
		$lastElement = $tree;
		$elements = [];
		
		$i = 0;
		
		foreach($subTrees as $subTree) {
			$i++;
			switch(get_class($subTree)) {
				case "Tree":
					$lastElement->insertAfter($subTree);
					$lastElement = $subTree;
				break;
				case "OffsetRange":
					$lastElement = $this->_pushOffsetRange($subTree, $lastElement);
				break;
				default:
					continue;
			}
			$elements[] = $lastElement;	
		}
		
		$tree->detach();
		
		return $elements;
	}
	
	private function _seekCommonParent($startTreePath, $endTreePath) {
		for($i = 0, $size = min(count($startTreePath), count($endTreePath)); $i < $size; $i++) {
			if($startTreePath[$i] !== $endTreePath[$i]) {
				break;
			}
		}
		
		return $i - 1;
	}
	
	public function getRootTree($text) {
		$offsetRange = new OffsetRange(0, strlen($text));
		$tree = new Tree('root', $offsetRange);
		$tree->pushChild(new Tree("raw", $offsetRange));
		return $tree;
	}
	
	public function getTreeWithValueBetweenOffsetRange($root, $value) {
		$correspondingTree = null;
		$root->forEachSubTrees(function($tree) use(&$value, &$correspondingTree) {
			$treeOffsetRange = $tree->value;
			if($tree->name == "raw") {
				if(($value >= $treeOffsetRange->start) && ($value <= $treeOffsetRange->end)) {
					$correspondingTree = $tree;
					return false;
				}
			}
		});
		
		return $correspondingTree;
	}
	
	public function replaceOffsetRangeInTree($root, $offsetRanges, $treeName) {
		foreach($offsetRanges as $offsetRange) {
			$newTree = new Tree($treeName, $offsetRange);
			
			$startTree = $this->getTreeWithValueBetweenOffsetRange($root, $offsetRange->start);
			
			$treeOffsetRange = $startTree->value;
			if(($offsetRange->end >= $treeOffsetRange->start) && ($offsetRange->end <= $treeOffsetRange->end)) { // the first element is too the last
				$endTree = $startTree;
			} else {	
				$endTree = $this->getTreeWithValueBetweenOffsetRange($root, $offsetRange->end);
			}
			
			if($startTree === $endTree) {
				$newTree->pushChild(new Tree("raw", $offsetRange));
				$this->_splitTree($startTree, [
					new OffsetRange($treeOffsetRange->start, $offsetRange->start),
					new OffsetRange($offsetRange->end, $treeOffsetRange->end)/*$newTree*/,
					new OffsetRange($offsetRange->end, $treeOffsetRange->end)
				]);
			} else {
					// seek for a common parent
				$startTreePath	= $startTree->getPath();
				$endTreePath	= $endTree->getPath();
				$i = $this->_seekCommonParent($startTreePath, $endTreePath);
				$commonParent = $startTreePath[$i];
				
				$startChild	= $startTreePath[$i + 1];
				$endChild	= $endTreePath[$i + 1];
				
				$treeOffsetRange = $startTree->value;
				if($startTree->getParent() === $commonParent) {
					$elements = $this->_splitTree($startTree, [
						new OffsetRange($treeOffsetRange->start, $offsetRange->start),
						new OffsetRange($offsetRange->start, $treeOffsetRange->end)
					]);
					
					$startChild = $elements[count($elements) - 1];
				} else {
					if($treeOffsetRange->start != $offsetRange->start) {
						throw new Exception('START OF TAG : Crossing offset');
					}
				}
				
				
				$treeOffsetRange = $endTree->value;
				if($endTree->getParent() === $commonParent) {
					$elements = $this->_splitTree($endTree, [
						new OffsetRange($treeOffsetRange->start, $offsetRange->end),
						new OffsetRange($offsetRange->end, $treeOffsetRange->end)
					]);
					
					$endChild = $elements[0];
				} else {
					if($treeOffsetRange->end != $offsetRange->end) {
						echo $treeOffsetRange->end . " - " . $offsetRange->end;
						throw new Exception('END OF TAG : Crossing offset');
					}
				}
				
				$startChild->insertBefore($newTree);
				
				$newTree->pushChild($startChild);
				do {
					$startChild = $newTree->getNextTree();
					$newTree->pushChild($startChild);
				} while($startChild != $endChild);
			}
			
			
		}

		return $root;
	}

	public function displayTree($tree, $text) {
		$tree->forEachSubTrees(function($tree) use(&$text) {
			if($tree->name == "raw") { 
				$offsetRange = $tree->value;
				$string = "\"-" . substr($text, $offsetRange->start, $offsetRange->end - $offsetRange->start) . "-\"";
				$tree->name		= "text";
				$tree->value	= $string;
			}
		});
		
		echo $tree->asString() . "\n";
	}

}



class Node {
	
	public $name, $value;
	public $parentNode, $children, $arrayListElement;
	
	public function __construct($name, $value) {
		$this->name				= $name;
		$this->value			= $value;
		$this->parentNode		= null;
		$this->children			= new ArrayList();
		$this->arrayListElement	= null;
	} 
	
	
	public function getNext() {
		if($this->parentNode !== null) {
			if($this->arrayListElement->next === null) {
				return null;
			} else {
				return $this->arrayListElement->next->value;
			}
		} else {
			return null;
		}
	}
	
	public function getParentNode() {
		return $this->parentNode;
	}
	
	public function getPath() {
		$element = $this;
		$parentNodes = [];
		
		while($element !== null) {
			$parentNodes[] = $element;
			$element = $element->getParentNode();
		}
		return array_reverse($parentNodes);
	}
	
	public function setParentNode($parentNode) {
		$this->detach();
		
		$this->parentNode = $parentNode;
		
		$arrayListElement = new ArrayListElement($this);
		$this->arrayListElement = $arrayListElement;
		
		return $this->arrayListElement;
	}
	
	
	private function _nodeIsValid($node) {
		if($node === null) {
			return false;
		} else {
			switch(get_class($node)) {
				case 'Leaf':
				case 'Branch':		
					return true;
				break;
				default:
					return false;
			}
		}
	}
	
	
	
	
	public function insertAfter($node) {
		if($this->parentNode !== null) {
			if($this->_nodeIsValid($node)) {
				$this->arrayListElement->insertAfter($node->setParentNode($this->parentNode));
			}
		}
	}
	
	public function insertBefore($node) {
		if($this->parentNode !== null) {
			if($this->_nodeIsValid($node)) {
				$this->arrayListElement->insertBefore($node->setParentNode($this->parentNode));
			}
		}
	}
	
	public function replaceBy($node) {
		if($this->parentNode !== null) {
			if($this->_nodeIsValid($node)) {
				$this->arrayListElement->replaceBy($node->setParentNode($this->parentNode));
			}
		}
	}
	
	public function detach() {
		if($this->parentNode !== null) {
			$this->arrayListElement->detach();
			$this->parentNode = null;
		}
	}
	
	
	public function unshiftChild($node) {
		$this->children->unshift($node->setParentNode($this));
	}
	
	public function pushChild($node) {
		$this->children->push($node->setParentNode($this));
	}

	
	public function forEachNodes($callback) {
		$nextElement = null;
		
		$this->children->forEachElements(function($child) use(&$callback, &$nextElement) {
			$nextElement = $child->value->forEachNodes($callback);
			$nextElement = $this->_formatCallbackResponseForArrayList($nextElement, $child->value);
			return $nextElement;
		});
		
		if($nextElement === false) {
			return false;
		} else {
			return $callback($this);
		}
	}
	
		private function _formatCallbackResponseForArrayList($returnedNextElement, $currentElement) {
			if($returnedNextElement === null) {
				$nextElement = $currentElement->getNext();
			} else {
				if(!is_object($returnedNextElement) ||
					!(
						(get_class($returnedNextElement) === 'Branch') ||
						(get_class($returnedNextElement) === 'Leaf')
					)
				) {
					return false;
				} else {
					$nextElement = $returnedNextElement;
				}
			}
			
			if($nextElement !== null && $nextElement !== $this) {	
				return $nextElement->arrayListElement;
			} else {
				return null;
			}
		}

	
	public function display() {
		$node = (object) [
			"name"	=> $this->name
		];
		
		if($this->children->size > 0) {
			$node->children = [];
			$this->children->forEachElements(function($child) use(&$node) {
				$child = $child->value;
				$node->children[] = $child->display();
			});
		}
		
		return $node;
	}

	public function asString() {
		$string = "";
		$string .= $this->name . " {";
		
		$this->children->forEachElements(function($child) use(&$string) {
			global $fnc;
			
			$child = $child->value;
			$string .= "\n";
			$string .= $fnc->tab($child->asString());
		});
		
		$string .= "\n}";
		
		return $string;
	}

}


class Branch extends Node {
	public $parentNode, $children;
	
	public function __construct($name) {
		$this->name			= $name;
		$this->parentNode	= null;
		$this->children		= new ArrayList();
	}

	public function copy() {
		return new Branch($this->name);
	}
	
	public function clear() {
		$this->forEachChildren(function($child) {
			$next = $child->getNext();
			$child->detach();
			return $next;
		});
	}
	
	public function unshiftChild($node) {
		$this->children->unshift($node->setParentNode($this));
	}
	
	public function pushChild($node) {
		$this->children->push($node->setParentNode($this));
		
		/*if($this->children->last === null) {
			$this->children->push($node->setParentNode($this));
		} else {
			$this->children->last->value->insertAfter($node);
		}*/
	}
	
	
	public function forEachLeaf($callback) {
		$nextElement = null;
		
		$this->children->forEachElements(function($child) use(&$callback, &$nextElement) {
			$nextElement = $child->value->forEachLeaf($callback);
			$nextElement = $this->_formatCallbackResponseForArrayList($nextElement, $child->value);
			return $nextElement;
		});
		
		return $nextElement;
	}
	
	public function forEachBranch($callback) {
		$nextElement = null;
		
		$this->children->forEachElements(function($child) use(&$callback, &$nextElement) {
			$nextElement = $child->value->forEachBranch($callback);
			$nextElement = $this->_formatCallbackResponseForArrayList($nextElement, $child->value);
			return $nextElement;
		});
		
		if($nextElement === false) {
			return false;
		} else {
			return $callback($this);
		}
	}
	
	public function forEachChildren($callback) {
		$this->children->forEachElements(function($child) use(&$callback) {
			$nextElement = $callback($child->value);
			$nextElement = $this->_formatCallbackResponseForArrayList($nextElement, $child->value);
			return $nextElement;
		});
	}
	
	
	

		
	public function display() {
		$node = (object) [
			"name"	=> $this->name
		];
		
		if($this->children->size > 0) {
			$node->children = [];
			$this->children->forEachElements(function($child) use(&$node) {
				$child = $child->value;
				$node->children[] = $child->display();
			});
		}
		
		return $node;
	}

	public function asString() {
		$string = "";
		$string .= $this->name . " {";
		
		$this->children->forEachElements(function($child) use(&$string) {
			global $fnc;
			
			$child = $child->value;
			$string .= "\n";
			$string .= $fnc->tab($child->asString());
		});
		
		$string .= "\n}";
		
		return $string;
	}

}


class Leaf extends Node {
	public $value;

	public function __construct($name, $value) {
		$this->name		= $name;
		$this->value	= $value;
	}
	
	public function copy() {
		return new Leaf($this->name, $this->value);
	}
	
	public function forEachLeaf($callback) {
		return $callback($this);
	}
	
	public function forEachBranch($callback) {
	}
	
	
	public function display() {
		return $this->value;
	}
	
	public function asString() {
		global $fnc;
		
		$string = "";
		$string .= $this->name . " {\n";
		$string .= $fnc->tab(print_r($this->value, true));
		$string .= "\n}";
		
		return $string;
	}

}




		
function replaceOffsetRangeInTree_old($root, $offsetRanges, $callback, $text) {
	foreach($offsetRanges as $offsetRange) {
		
		$minStartOffsetRange = (object) [
			"offsetRange"	=> $root->value,
			"tree"			=> $root
		];
		
		$startTree	= null;
		$endTree	= null;
		
		$root->forEachSubTrees(function($tree) use(&$offsetRange, &$starttree,  /* useless */&$callback, &$text, &$root) {
			$treeOffsetRange = $tree->value;
			
			if($tree->name == "raw") { 
				if(($offsetRange->start >= $treeOffsetRange->start) && ($offsetRange->start <= $treeOffsetRange->end)) { // we have find the first element
					$startTree = $tree;
					return false;
				}
			}
			
			
			if($tree->name == "offsetRange") { 
				$startTreeOffsetRange = $tree->value;
				
				if(($offsetRange->start >= $startTreeOffsetRange->start) && ($offsetRange->start <= $startTreeOffsetRange->end)) {
					
					$startTree = $tree;
					
					if(($offsetRange->end >= $startTreeOffsetRange->start) && ($offsetRange->end <= $startTreeOffsetRange->end)) { // split offsetRange
						$newElement = $callback($offsetRange, $startTreeOffsetRange);
					
						if($newElement !== null) {
							$lastElement = $tree;
							
							if(($offsetRange->start - $startTreeOffsetRange->start) > 0) {
								$_tree = new Tree("offsetRange", new OffsetRange($startTreeOffsetRange->start, $offsetRange->start));
								$lastElement->insertAfter($_tree);
								$lastElement = $_tree;
							}
								
							$lastElement->insertAfter($newElement);
							$lastElement = $newElement;
								
							if(($startTreeOffsetRange->end - $offsetRange->end) > 0) {
								$_tree = new Tree("offsetRange", new OffsetRange($offsetRange->end, $startTreeOffsetRange->end));
								$lastElement->insertAfter($_tree);
								$lastElement = $_tree;
							}
							
							$tree->detach();
						}
						
						return false;
					} else {	// we search for a children which could close this offsetRange
						/*echo "\nseek:\n";
						
						$tree->forEachLeaf(function($leaf) use(&$offsetRange, &$callback, &$text, &$tree, &$startLeaf, &$startTreeOffsetRange) {
							if($leaf->name == "offsetRange") { 
								$endLeafOffsetRange = $leaf->value;
								if(($offsetRange->end >= $endLeafOffsetRange->start) && ($offsetRange->end <= $endLeafOffsetRange->end)) {
									$endLeaf = $leaf;
									
									$startLeafPath	= $startLeaf->getPath();
									$endLeafPath	= $endLeaf->getPath();
									
									for($i = 0, $size = min(count($startLeafPath), count($endLeafPath)); $i < $size; $i++) {
										if($startLeafPath[$i] !== $endLeafPath[$i]) {
											break;
										}
									}
									
									$i--;
									
									if($i < 0) {
										// SOULD NEVER APPEND : no common parent node
									} else {
										$commonParentNode = $startLeafPath[$i];
										
										$children = [];
										
										if(($offsetRange->start - $startTreeOffsetRange->start) > 0) {
											$children[] = new Leaf("offsetRange", new OffsetRange($startTreeOffsetRange->start, $offsetRange->start));
										}
										
										//$commonParentNode->clear();
										
										foreach($children as $child) {
											$commonParentNode->pushChild($child);
										}
										
										//$commonParentNode->replaceBy($newParentNode);
										echo $size . " - " . $i . "\n";
										print_r($commonParentNode->asString());
										echo "\n";
										print_r($startLeaf->asString());
										echo "\n";
										print_r($endLeaf->asString());
										echo "\n\n";
									
									}
									return false;
								}
							}
						});
						
						return false;
						$element = $leaf->getNext();
						while($element !== null) {
							if($element->name == "offsetRange") {
								print_r($element->value->asString($text));
								$elementOffsetRange = $element->value;
								if($elementOffsetRange->end >= $offsetRange->end) {
									echo "okkkk\n";
								}
							}
							
							$element = $element->getNext();
						}*/
					}
				}
			}
		});
	}
	
	return $root;
}


function replaceTag($tag, $html, $callback) {
	return preg_replace_callback('#&lt;(' . $tag . ')&gt;(.*)&lt;/' . $tag . '&gt;#isU', function($matches) {
		print_r($matches);
	}, $html);
}



function _extract_matches($_matches, $i) {
	$matches = [];
	
	for($j = 1, $size_j = count($_matches); $j < $size_j; $j++) {
		$match = $_matches[$j][$i];
		if($match) {
			$matches[] = (object) [
				"value"		=> $match[0],
				"offset"	=> $match[1]
			];
		} else {
			$matches[] = null;
		}
	}
	
	return $matches;
}

function preg_recursive_match($start, $end, $string) {
	$matches = [];
	
	preg_match_all('#' . $start . '#sU', $string, $matchesForStartOfTag, PREG_OFFSET_CAPTURE, 0);
	
	$_matchesForStartOfTag = &$matchesForStartOfTag[0];

	for($i = 0, $size_i = count($_matchesForStartOfTag); $i < $size_i; $i++) {
		$endString = preg_replace_callback('#\$([0-9]+)#sU', function($matchesForEnd) use(&$matchesForStartOfTag, $i){
			return $matchesForStartOfTag[$matchesForEnd[1]][$i][0];
		}, $end);
		
		
		
		$matches[$i] = (object) [
			"startTag"	=> (object) [
				"value"		=> $_matchesForStartOfTag[$i][0],
				"offsetRange"	=> new OffsetRange($_matchesForStartOfTag[$i][1], $_matchesForStartOfTag[$i][1] + strlen($_matchesForStartOfTag[$i][0])),
				"matches"	=> _extract_matches($matchesForStartOfTag, $i)
			],
			
			"endTag"	=> (object) [
				"value"		=> $endString,
				"offsetRange"	=> null,
				"matches"	=> []
			]
		];
	}
	
	//print_r($matches);
	
	for($i = 0, $size_i = count($matches); $i < $size_i; $i++) {
		$match = &$matches[$i];
		
		if($match->endTag->offsetRange === null) {
			preg_match_all('#' . $match->endTag->value . '#sU', $string, $matchesForEndOfTag, PREG_OFFSET_CAPTURE, 0);
			$_matchesForEndOfTag = &$matchesForEndOfTag[0];
			
			for($j = 0, $size_j = count($_matchesForEndOfTag); $j < $size_j; $j++) {
				$endIndex = $_matchesForEndOfTag[$j][1];

				for($k = $size_i - 1; $k >= 0; $k--) {
					$_match = &$matches[$k];
					if($match->endTag->offsetRange === null) {
						if($_match->endTag->value == $match->endTag->value) {
							if($endIndex > $_match->startTag->offsetRange->start) {
								$_match->endTag->value			= $_matchesForEndOfTag[$j][0];
								$_match->endTag->offsetRange	= new OffsetRange($endIndex, $endIndex + strlen($_match->endTag->value));
								$_match->endTag->matches		= _extract_matches($matchesForEndOfTag, $j);
								break;
							}
						}
					}
				}
			}
		}
	}
	
	return $matches;
}

function convert_recursive_match_as_tree($text, $matches) {
	$tree = new Branch('root');
	
	$tree->pushChild(new Leaf("offsetRange", new OffsetRange(0, strlen($text))));
	
	foreach($matches as $match) {
		$tree->forEachLeaf(function($leaf) use(&$match) {
			if(is_object($leaf->value) && (get_class($leaf->value) == 'OffsetRange')) { 
				$offsetRange = $leaf->value;
				if(
					$offsetRange->start	<= $match->startTag->offsetRange->start &&
					$offsetRange->end		>= $match->endTag->offsetRange->end
				) {
					
					$lastElement = $leaf;
					if(($match->startTag->offsetRange->start - $offsetRange->start) > 0) {
						$_leaf = new Leaf("offsetRange", new OffsetRange($offsetRange->start, $match->startTag->offsetRange->start));
						$lastElement->insertAfter($_leaf);
						$lastElement = $_leaf;
					}
					
					
					$tag = new Branch("tag");
						
					$tag->pushChild(new Leaf("startTag", $match->startTag));
					
						$content = new Branch("content");
						$content->pushChild(new Leaf("offsetRange", new OffsetRange($match->startTag->offsetRange->end, $match->endTag->offsetRange->start)));
						$tag->pushChild($content);
						
					$tag->pushChild(new Leaf("endTag", $match->endTag));
					
					$lastElement->insertAfter($tag);
					$lastElement = $tag;
					
					if(($offsetRange->end - $match->endTag->offsetRange->end) > 0) {
						$_leaf = new Leaf("offsetRange", new OffsetRange($match->endTag->offsetRange->end, $offsetRange->end));
						$lastElement->insertAfter($_leaf);
						$lastElement = $_leaf;
					}
					
					$leaf->detach();
					
					return false;
				}
			}
		});
	}
	
	return $tree;
}

/*function convert_recursive_match_as_offsetRange($matches) {
	$offsetRanges = [];
	
	foreach($matches as $match) {
		$offsetRanges[] = new OffsetRange($match->startTag->offsetRange->start, $match->endTag->offsetRange->end);
	}
	
	return $offsetRanges;
}*/

function convert_recursive_match_as_offsetRange($matches) {
	$offsetRanges = (object) [
		"startTag"		=> [],
		"endTag"		=> [],
		"fullTag"		=> []
	];
	
	foreach($matches as $match) {
		$offsetRanges->startTag[]	= new OffsetRange($match->startTag->offsetRange->start, $match->startTag->offsetRange->end);
		$offsetRanges->endTag[]		= new OffsetRange($match->endTag->offsetRange->start, $match->endTag->offsetRange->end);
		$offsetRanges->fullTag[]	= new OffsetRange($match->startTag->offsetRange->start, $match->endTag->offsetRange->end);
	}
	
	return $offsetRanges;
}


function cut_string($string, $offsets) {
	$cuttedString		= [];
	
	if(isset($offsets[0]) && $offsets[0] == 0) {
		unset($offsets[0]);
		$offsets = array_values($offsets);
	}
	
	$strlen = strlen($string);
	
	if($offsets[count($offsets) - 1] != $strlen) {
		$offsets[] = $strlen;
	}
		
	
	
	$offsetIndex		= 0;
	$cuttedStringIndex	= 0;
	
	$cuttedString[$cuttedStringIndex] = "";
	
	for($i = 0, $size = strlen($string); $i < $size; $i++) {
		if($i == $offsets[$offsetIndex]) {
			$offsetIndex++;
			$cuttedStringIndex++;
			$cuttedString[$cuttedStringIndex] = "";
		}
		
		$cuttedString[$cuttedStringIndex] .= $string[$i];
	}
	
	return $cuttedString;
}

function cutStringAsRawText($string, $offsets) {
	if(isset($offsets[0]) && $offsets[0] == 0) {
		unset($offsets[0]);
		$offsets = array_values($offsets);
	}
	
	$strlen = strlen($string);
	
	if($offsets[count($offsets) - 1] != $strlen) {
		$offsets[] = $strlen;
	}
	
	$cuttedStringAsRawText = [];
	
	$cuttedSting = cut_string($string, $offsets);
	$offset = 0;
	for($i = 0, $size = count($cuttedSting); $i < $size; $i++) {
		$cuttedStringAsRawText[] = new RawText($cuttedSting[$i], $offset);
		$offset = $offsets[$i];
	}
				
	return $cuttedStringAsRawText;
}



function parseStrings($string) {
	
	$state = "in_raw";
	$strings = [];
	$stringsIndex = 0;
	
	for($i = 0, $size = strlen($string); $i < $size; $i++) {
		$char = $string[$i];
		
		switch($state) {
			case "in_raw":
				switch($char) {
					case "\"":
						$state = "in_double_quote_string";
						$strings[$stringsIndex] = new OffsetRange($i + 1, $i + 1);
						//$strings[$stringsIndex]->value = "";
					break;
				}
			break;
			case "in_double_quote_string":
				switch($char) {
					case "\\":
						//$strings[$stringsIndex]->value .= $char;
						$i++;
						//$strings[$stringsIndex]->value .= $string[$i];
					break;
					case "\"":
						$state = "in_raw";
						$strings[$stringsIndex]->end = $i;
						$stringsIndex++;
					break;
					default:
						//$strings[$stringsIndex]->value .= $char;
				}
			break;
		}
		
		
	}
	
	return $strings;
}

function parseXML($text) {
	/*$str = ' color="red" style="ok\""';
	preg_match_all('#([\w]+)=\"([^"]|\\)\"#sU', $str, $matches);*/
	
	$parser = new Parser();
	$tree = $parser->getRootTree($text);
	echo $tree->asString() . "\n";
	exit();
	
	$strings = parseStrings($text);
	//$strings = array_slice($strings, 0, 1);
	$tree = $parser->replaceOffsetRangeInTree($tree, $strings, "string");
	
	/*$matches = preg_recursive_match('<([\w]+)( .*)?>', '</$1>', $text);
	$offsetRanges = convert_recursive_match_as_offsetRange($matches);
	
	$tree = $parser->replaceOffsetRangeInTree($tree, $offsetRanges->startTag, "startTag");
	$tree = $parser->replaceOffsetRangeInTree($tree, $offsetRanges->endTag, "endTag");
	$tree = $parser->replaceOffsetRangeInTree($tree, $offsetRanges->fullTag, "fullTag");*/
	
	
	//$parser->displayTree($tree, $text);
	//echo $tree->asString() . "\n";
	return;
	
	$tree->forEachBranch(function($branch) {
		if($branch->name == 'tag') {
			$tagBranch = new Branch("XML_TAG");

			//print_r($branch->display());
			
			$branch->forEachChildren(function($child) use(&$branch, &$tagBranch){
				//print_r($child->display());
					
				switch($child->name) {
					case 'startTag':
						print_r($child->value->matches);
						$tagBranch->pushChild(new Leaf("name", $child->value->matches[0]->value));
						$attributes = [];
					break;
					case 'content':
						$tagBranch->pushChild($child);
					break;
				}
			});
			
			$branch->replaceBy($tagBranch);
			return $tagBranch->getNext();
		}
	});
	

	//print_r($matches);
	
	
	
	/*$AB = new Branch('AB');
	$A = new Leaf("A");
	$B = new Leaf("B");
	$C = new Leaf("C");
	$D = new Leaf("D");
	$E = new Leaf("E");
	$F = new Leaf("F");
	$G = new Leaf("G");
	$Z = new Leaf("Z");
	
	$DE = new Branch('DE');
	$FG = new Branch('FG');
	
	$AB->pushChild($A);
	$AB->pushChild($B);
	$DE->pushChild($D);
	$DE->pushChild($E);
	$FG->pushChild($F);
	$FG->pushChild($F);
	$AB->pushChild($FG);
	$tree->pushChild($AB);
	$tree->pushChild($C);
	$tree->pushChild($DE);
	$tree->pushChild($D->copy());*/
	
	
	/*$tree->forEachLeaf(function($leaf) use(&$Z) {
		echo $leaf->value . " - \n";
		
		//return false;
		if($leaf->value == "D") {
			//$z = clone $Z;
			//$z = $Z->copy();
			$z = new Leaf("Z");
			$leaf->replaceBy($z);
			return $z->getNext();
			//return false;
		}
		
		
	});*/
	
	//print_r($tree->display());
	
	/*$tree->forEachBranch(function($branch) use(&$Z) {
		echo $branch->name . " - \n";

		if($branch->name == "AB") {
			$z = new Branch("Z");
			$z->pushChild(new Leaf("Z"));
			$branch->replaceBy($z);
			return $z->getNext();
		}
	});*/
	
	/*print_r($tree->display());
	
	return;*/
	
	
	
	//print_r($tree->display());

}


//$xml = file_get_contents('test.xml');
$xml = file_get_contents('documentation.xml');
$xml = preg_replace('#\<\!--.*--\>#sU', '', $xml);
parseXML($xml);

/*$tree = new Node('root');
$tree->pushChildren(new Node('child'));
$tree->pushChildren(new Terminal('terminal'));*/

//print_r($tree);

exit();

?>

<!DOCTYPE html>
<html>
	<head>
		<title>ThingBook - Documentation</title>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="fr"/>
	</head>
	
	
	<style>
		p {
			text-align: justify;
		}
		
		.keyword {
			color: #608be5;
		}
	</style>
		
	<body>
		<?php
			//echo parse(file_get_contents('documentation.xml'));
		?>
	</body>
</html>