<?php

class Binds {

	private $_binds = [];
	
	public function bind($eventName, $callback, $id = null) {
		if(!is_callable($callback)) {
			return false;
		}
		
		if($id === null) {
			$id = uniqid();
		}
		
		if(!isset($this->_binds[$eventName])) {
			$this->_binds[$eventName] = [];
		}
		
		$this->_binds[$eventName][] = (object) array(
			'id' => $id,
			'callback' => $callback
		);
		
		if($eventName != '_onbind') {
			$this->trigger('_onbind', [$eventName, $callback]);
		}
	}
	
	public function unbind($eventName, $id = null) {
			if(isset($this->_binds[$eventName])) {
				if($id === null) {
					unset($this->_binds[$eventName]);
				} else {
					for($i = 0; i < count($this->_binds[$eventName]); $i++) {
						if($this->_binds[$eventName][$i].id == $id) {
							array_splice($this->_binds[$eventName], $i, 1);
							break;
						}
					}
				}
			}
		}
	
	public function trigger($eventName, $params = [], $triggerNextBinds = false) {
			// if we want trigger event even if the event is already triggered.
		if($triggerNextBinds) {
			$this->bind('_onbind', function($eventName2, $callback) {
				if($eventName2 == $eventName) {
					$this->trigger($eventName, $params);
				}
			});
		}
	
		if(isset($this->_binds[$eventName])) {
			for($i = 0; $i < count($this->_binds[$eventName]); $i++) {
				call_user_func_array($this->_binds[$eventName][$i]->callback, $params);
			}
		}
		
		$onEventName = 'on' + $eventName;
		if(isset($this->$onEventName)) {
			call_user_func_array($this->$onEventName, $params);
		}
	}
}
?>