<?php
class Injector extends Pimple implements Iterator {
	/*
	 * Iterator methods
	 */
	public function current() {
		//obtain current key
		$k = key($this->values);
		
		if (!is_null($k)) {
			//preserve the logic in 'offsetGet'
			return $this->offsetGet($k);
		}
		
		return null;
	}
	
	public function key() {
		return key($this->values);
	}
	
	public function next() {
		return next($this->values);
	}
	
	public function rewind() {
		return reset($this->values);
	}
	
	public function valid() {
		return key($this->values) !== null;
	}
	
	/*
	 * Injection methods
	 */
	
	/**
	 * Injects a value by id into an object
	 * @param object $obj
	 * @param string $id
	 * @throws InvalidArgumentException
	 */
	public function inject(&$obj, $id) {
		if (!is_object($obj)) {
			throw new InvalidArgumentException(sprintf("Parameter must be of type 'object'. Type '%s' not supported.", gettype($object)));
		}
		
		$obj->$id = $this->offsetGet($id);
	}
	
	/**
	 * Injects a list of services into an object
	 * @param unknown $obj
	 * @throws InvalidArgumentException
	 */
	public function injectMany(&$obj) {
		if (!is_object($obj)) {
			throw new InvalidArgumentException(sprintf("Parameter must be of type 'object'. Type '%s' not supported.", gettype($object)));
		}
		
		//count parameters
		$nargs = func_num_args();
		
		if ($nargs <= 1) {
			//nothing to inject
			return;
		}
		
		//get services ids
		$args = func_get_args();
		
		//inject services
		for ($i = 1; $i < $nargs; $i++) {
			$id = $args[$i];
			$obj->$id = $this->offsetGet($id);
		}
	}
	
	/**
	 * Injects all declared services into an object
	 * @param object $obj
	 * @throws InvalidArgumentException
	 */
	public function injectAll(&$obj) {
		if (!is_object($obj)) {
			throw new InvalidArgumentException(sprintf("Parameter must be of type 'object'. Type '%s' not supported.", gettype($object)));
		}
		
		//get service ids
		$keys = array_keys($this->values);
		
		foreach ($keys as $id) {
			$obj->$id = $this->offsetGet($id);
		}
	}
}