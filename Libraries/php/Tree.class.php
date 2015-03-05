<?php

require_once(__DIR__ . '/ArrayList.class.php');

class OffsetRange {
	public $start, $end;
	
	public function __construct($start, $end) {
		$this->start	= $start;
		$this->end	= $end;
	}
	
	public function asString($text) {
		return substr($text, $this->start, $this->end - $this->start);
	}
}



class Tree {
	
	public $name, $value;
	private $_parent, $_children;
	private $_arrayListElement;
	
	public function __construct($name, $value) {
		$this->name					= $name;
		$this->value				= $value;
		$this->_parent				= null;
		$this->_children			= new ArrayList();
		$this->_arrayListElement	= new ArrayListElement($this);
	}
	
	public function getParent() {
		return $this->_parent;
	}
	
	public function getPath() {
		$element = $this;
		$parents = [];
		
		while($element !== null) {
			$parents[] = $element;
			$element = $element->getParent();
		}
		return array_reverse($parents);
	}
	
	public function getPreviousTree() {
		if($this->getParent() !== null) {
			if($this->_arrayListElement->previous === null) {
				return null;
			} else {
				return $this->_arrayListElement->previous->value;
			}
		} else {
			return null;
		}
	}
	
	public function getNextTree() {
		if($this->getParent() !== null) {
			if($this->_arrayListElement->next === null) {
				return null;
			} else {
				return $this->_arrayListElement->next->value;
			}
		} else {
			return null;
		}
	}
	
	
	public function unshiftChild($tree) {
		$this->_children->unshift($tree->attach($this));
	}
	
	public function pushChild($tree) {
		$this->_children->push($tree->attach($this));
	}
	
	
	public function insertAfter($tree) {
		if($this->getParent() !== null) {		
			$this->_arrayListElement->insertAfter($tree->attach($this->_parent));
		}
	}
	
	public function insertBefore($tree) {
		if($this->getParent() !== null) {
			$this->_arrayListElement->insertBefore($tree->attach($this->_parent));
		}
	}
	
	public function replaceBy($tree) {
		if($this->getParent() !== null) {
			$this->_arrayListElement->replaceBy($tree->attach($this->_parent));
		}
	}
	
	
	public function attach($parent) {
		$this->detach();
		$this->_parent = $parent;
		return $this->_arrayListElement;
	}
	
	public function detach() {
		if($this->getParent() !== null) {
			$this->_arrayListElement->detach();
			$this->_parent = null;
		}
	}

	
	public function forEachSubTrees($callback) {
		$nextElement = null;
		
		$this->_children->forEachElements(function($child) use(&$callback, &$nextElement) {
			$nextElement = $child->value->forEachSubTrees($callback);
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
				$nextElement = $currentElement->getNextTree();
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
				return $nextElement->_arrayListElement;
			} else {
				return null;
			}
		}
	
	
	public function asString() {
		global $fnc;

		$content = "";
		$content .= "value => " . preg_replace('#(\r?\n)$#sU', '', print_r($this->value, true));
		if($this->_children->size > 0) {
			$content .= "\n";
		}

		$this->_children->forEachElements(function($child) use(&$content) {
			$content .= "\n" . $child->value->asString();
		});
		
		$string = "";
		$string .= "[" . $this->name . "] {\n";
			$string .= $fnc->tab($content);
		$string .= "\n}";
		
		return $string;
	}
	
}

?>