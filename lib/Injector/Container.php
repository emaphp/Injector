<?php
namespace Injector;

class Container extends \Pimple implements \Iterator {
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
	
	public function create($class, $_ = null) {
		if (!is_string($class) || empty($class)) {
			throw new \InvalidArgumentException("Argument is not a valid class name");
		}
		
		$args = func_get_args();
		array_shift($args);
		$cclass = get_class($this);
	}
	
	public function inject(&$obj, $_ = null) {
		if (!is_object($obj)) {
			throw new \InvalidArgumentException("Argument is not a valid object");
		}
		
		$args = func_get_args();
		array_shift($args);
		$cclass = get_class($this);
		
		if ($obj instanceof \stdClass) {
			$services = array_keys($this);
			
			if (empty($args)) {
				for ($i = 0, $n = count($keys); $i < $n; $i++) {
					$obj->$services[$i] = $this->offsetGet($services[$i]);
				}
			}
			else {
				foreach ($args as $arg) {
					if (!is_string($arg) || empty($arg)) {
						throw new \InvalidArgumentException("Specified property is not a valid string");
					}
					
					$obj->$arg = $this->offsetGet($arg);
				}
			}
		}
		else {
			$profile = new ClassProfile(get_class($obj), $cclass);
			
			if (empty($args)) {
				foreach ($profile->properties as $name => $property) {
					if ($property['container'] != $cclass) {
						throw new \RuntimeException("Class '{$profile->className}' depends on {$property['container']}::{$property['service']}. Use Injector::create instead.");
					}
				
					if (!empty($property['setter'])) {
						$obj->$property['setter']($this->offsetGet($property['service']));
					}
					else {
						$obj->$name = $this->offsetGet([$property['service']]);
					}
				}
			}
			else {
				foreach ($args as $arg) {
					if (!is_string($arg) || empty($arg)) {
						throw new \InvalidArgumentException("Specified property is not a valid string");
					}
					
					if (!array_key_exists($arg, $profile->properties)) {
						throw new \InvalidArgumentException("Specified property is does not exists in class '{$profile->className}'.");
					}
					
					$property = $profile->properties[$arg];
					
					if ($property['container'] != $cclass) {
						throw new \RuntimeException("Class '{$profile->className}' depends on {$property['container']}::{$property['service']}. Use Injector::create instead.");
					}
					
					if (!empty($property['setter'])) {
						$obj->$property['setter']($this->offsetGet([$property['service']]));
					}
					else {
						$obj->$arg = $this->offsetGet($property['service']);
					}
				}
			}
		}
	}
} 