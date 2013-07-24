<?php
namespace Injector;

class Injector extends \Pimple implements \Iterator {
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
	 * Injects a list of services into an object
	 * @param unknown $obj
	 * @throws InvalidArgumentException
	 */
	public function inject(&$obj) {
		if (!is_object($obj)) {
			throw new InvalidArgumentException(sprintf("Parameter must be of type 'object'. Type '%s' not supported.", gettype($object)));
		}

		$nargs = func_num_args();
		
		//if no arguments are passed then all services are injected
		if ($nargs == 1) {
			$args = array_keys($this->values);
			$nargs = count($args);
		}
		else {
			//get services ids
			$args = func_get_args();
			array_shift($args);
			$nargs = count($args);
		}

		//inject services
		for ($i = 0; $i < $nargs; $i++) {
			$id = $args[$i];
			
			if (is_array($id)) {
				for ($j = 0, $m = count($id); $j < $m; $j++) {
					$this->inject($obj, $id[$j]);
				}
			}
			else {
				$obj->$id = $this->offsetGet($id);
			}
		}
	}
}