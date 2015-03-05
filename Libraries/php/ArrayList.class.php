<?php
class ArrayListElement {
	public $value, $previous, $next, $arrayList;
	
	public function __construct($value) {
		$this->value		= $value;
		$this->previous	 	= null;
		$this->next			= null;
		$this->arrayList	= null;
	}
	
	public function copy() {
		return new ArrayListElement($this->value);
	}

	public function insertAfter($element) { // insert $element after this element
		if($this->arrayList !== null) {
			$element = $this->arrayList->_prepareElement($element);
			
			$element->previous		= $this;
			$element->next			= $this->next;
			
			if($this->next !== null) {
				$this->next->previous	= $element;
			}
			
			$this->next				= $element;

			
			if($this->arrayList !== null) {
				$element->arrayList	= $this->arrayList;
				$this->arrayList->size++;
				
				if($this->arrayList->last === $this) {
					$this->arrayList->last = $element;
				}
			}
		}
		
		return $this;
	}
	
	public function insertBefore($element) { // insert $element before this element
		if($this->arrayList !== null) {
			$element = $this->arrayList->_prepareElement($element);
		
			$element->previous	= $this->previous;
			$element->next		= $this;
			if($this->previous !== null) {
				$this->previous->next	= $element;
			}
			$this->previous 	= $element;
			
			if($this->arrayList !== null) {
				$element->arrayList	= $this->arrayList;
				$this->arrayList->size++;
				
				if($this->arrayList->first === $this) {
					$this->arrayList->first = $element;
				}
			}
		}
		
		return $this;
	}
	
	public function replaceBy($element) {
		if($this->arrayList !== null) {
			$element = $this->arrayList->_prepareElement($element);
			$this->insertAfter($element);
			$this->detach();
		}
		
		return $this;
	}
	
	public function detach() {
		if($this->arrayList !== null) {
			if($this->previous !== null) {
				$this->previous->next = $this->next;
			}
			
			if($this->next !== null) {
				$this->next->previous = $this->previous;
			}
			
			if($this->arrayList !== null) {
				if($this->arrayList->first === $this) {
					$this->arrayList->first = $this->next;
				}
				
				if($this->arrayList->last === $this) {
					$this->arrayList->last = $this->previous;
				}
				
				$this->arrayList->size--;
				$this->arrayList = null;
			}
			
			$this->previous = null;
			$this->next		= null;
		}
		
		return $this;
	}

	
	public function forEachElements($callback) {
		/*$nextElement = $callback($this);
		
		if($nextElement === null) {
			$nextElement = $this->next;
		} else {
			if(!is_object($nextElement) || (get_class($nextElement) !== 'ArrayListElement')) {
				$nextElement = null;
			}
		}

		if($nextElement !== null && $nextElement !== $this) {	
			$nextElement->forEachElements($callback);
		}*/
		
		
		$nextElement = $this;
		do {
			$returnedNextElement = $callback($nextElement);
			
			if($returnedNextElement === null) {
				$nextElement = $nextElement->next;
			} else {
				if(!is_object($returnedNextElement) || (get_class($returnedNextElement) !== 'ArrayListElement')) {
					$nextElement = null;
				} else {
					$nextElement = $returnedNextElement;
				}
			}
		} while($nextElement !== null && $nextElement !== $this);
	}
	
}


class ArrayList {
	public $first, $last, $size;
	
	public function __construct($element = null) {
		$this->first	= null;
		$this->last		= null;
		$this->size		= 0;
	}
	
	public function _prepareElement($element) {
			if(is_object($element)) {
				switch(get_class($element)) {
					case 'ArrayListElement':
						$element->detach();
					break;
					default:
						$element = new ArrayListElement($element);
				}	
			} else {
				$element = new ArrayListElement($element);
			}
			
			return $element;
		}
	
	public function clear() {
		$this->forEachElements(function($element) {
			$next = $element->next;
			$element->detach();
			return $next;
		});
	}
	
	public function push($element) {
		$element = $this->_prepareElement($element);

		if($this->first === null) {
			$element->arrayList		= $this;
			$this->first			= $element;
			$this->last				= $element;
			$this->size++;
		} else {
			$this->last->insertAfter($element);
		}

		return $this;
	}
	
	public function pop() {
		$element = $this->last;
		$element->detach();
		return $element;
	}
	
	public function unshift($element) {
		$element = $this->_prepareElement($element);

		if($this->first === null) {
			$element->arrayList		= $this;
			$this->first			= $element;
			$this->last				= $element;
			$this->size++;
		} else {
			$this->first->insertBefore($element);
		}

		return $this;
	}
	
	public function shift() {
		$element = $this->first;
		$element->detach();
		return $element;
	}

	public function get($index) {
		$directionTheNext = ($index >= 0);
		
		if($directionTheNext) {
			$element = $this->first;
		} else {
			$index = abs($index) - 1;
			$element = $this->last;
		}
		
		for($i = 0; $i < $index; $i++) {
			if($element === null) {
				return null;
			} else {
				if($directionTheNext) {
					$element = $element->next;
				} else {
					$element = $element->previous;
				}
				
			}
		}
		
		return $element;
	}
	
	public function forEachElements($callback, $startByLast = false) {
		if($startByLast) {
			$nextElement = $this->last;
		} else {
			$nextElement = $this->first;
		}
		
		if($nextElement !== null) {	
			$nextElement->forEachElements($callback);
		}
		
	}

	public function toArray() {
		$array = [];
		$this->forEachElements(function($element) use(&$array) {
			$array[] = $element->value;
		});
	}
}


$arrayList = new ArrayList();
/*for($i = 0; $i < 9000; $i++) {
	$arrayList->push($i);
}

for($i = 0; $i < 9000; $i++) {
	$arrayList->push($i);
}
echo memory_get_usage(true);
exit();*/

/*
$a = new ArrayListElement("a");
$b = new ArrayListElement("b");
$c = new ArrayListElement("c");
$d = new ArrayListElement("d");
$e = new ArrayListElement("e");

$arrayList->push($a);
$a->insertAfter($b);
$b->insertAfter($c);
$c->insertAfter($d);*/

/*$arrayList_2 = new ArrayList();
$arrayList_2->push($a);
$a->replaceBy($b);*/
//$b->insertAfter($c);
//$b->insertBefore($a);
//$arrayList->push($b->copy());

//$arrayList->unshift($b);
//$arrayList->pop();
//$b->replaceBy($d);

/*$arrayList->forEachElements(function($element) {
	//echo $element->value . "\n";
});*/

//print_r($arrayList);
//print_r($arrayList->get(0)->value);
//print_r($arrayList_2);

//exit();
?>