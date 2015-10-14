<?php

abstract class AlternativeFunctions {
	protected		$_functions;

	protected function _createTryCatchFunction($functionName, $try, $catch, $equivalentCode) {
		$this->_functions[$functionName] = [
			"try"				=> $try,
			"catch"				=> $catch,
			"equivalentCode"	=> $equivalentCode,
			"included"			=> false
		];
	}
	
		protected function _autoCreateTryCatchFunction($functionName, $try, $options, $isRef) {
			$this->_createTryCatchFunction($functionName, $try, function() use($functionName, $isRef) {
				return ($isRef ? "*" : "") . $functionName . "(" . arrayToString(func_get_args()) . ")";
			}, function() use($functionName, $options, $isRef) {
				return $this->_AToB_TryCatchFunction($functionName, $options, $isRef);
			});
		}
		
		protected function _registerTryCatchFunction($functionName) {
			$this->_createTryCatchFunction(
				$functionName,
				[$this, 'try_' . $functionName],
				[$this, 'catch_' . $functionName],
				[$this, 'code_' . $functionName]
			);
		}
	
	
	protected function _callTryCatchFunction() {
		$args			= func_get_args();
		$functionName	= $args[0];
		$arguments		= array_slice($args, 1);

		for($i = 0, $size_i = count($arguments); $i < $size_i; $i++) {
			$arguments[$i] = $this->constructor->reduceExpression($arguments[$i]);
		}
		
		return $this->_callTryCatchFunctionErrorInsensitive($functionName, $arguments);
	}
	
		protected function _callTryCatchFunctionErrorInsensitive($functionName, $arguments) {
			try {
				return $this->_callTryCatchFunctionErrorSensitive($functionName, $arguments);
			} catch (Exception $e) {
				if(!$this->_functions[$functionName]['included']) {
					$this->_functions[$functionName]['included'] = true;
					$this->constructor->inFunctionsH($this->_functions[$functionName]['equivalentCode']());
				}
		
				return call_user_func_array($this->_functions[$functionName]['catch'], $arguments);
			}
		}
		
		protected function _callTryCatchFunctionErrorSensitive($functionName, $arguments) {
			return call_user_func_array($this->_functions[$functionName]['try'], $arguments);
		}
		
		
		// generate a cpp function on which convert $options into something else
	protected function _AToB_TryCatchFunction($functionName, $options, $isRef = false) {
		$AToB = [];
		
		for($i = 0, $size_i = count($options); $i < $size_i; $i++) {
			try {
				$AToB[$options[$i]] = $this->_callTryCatchFunctionErrorSensitive($functionName, [$this->constructor->reduceExpression($options[$i])]);
			} catch (Exception $e) {
			}
		}
		return $this->constructor->_AToB_function($functionName, $AToB, $isRef);
	}
		
		// generate a cpp function which convert a pin number into something else
	protected function _pinToB_function($functionName, $isRef = false) {
		return $this->constructor->_AToB_TryCatchFunction($functionName, $this->_pinIndexes, $isRef);
	}
		
}

?>