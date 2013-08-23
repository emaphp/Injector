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

	/**
	 * Generates a new instance of $class with all dependencies satisfied
	 * @param string $class
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @return object
	 */
	public function create($class, $_ = null) {
		if (!is_string($class) || empty($class)) {
			throw new \InvalidArgumentException("Argument is not a valid class name");
		}
		
		//get constructor parameters
		$args = func_get_args();
		array_shift($args);
		
		//container class
		$cclass = get_class($this);
		
		//when stdClass do a simple injection
		if (preg_match('/^(\\\\)?stdClass$/', $class)) {
			$instance = new \stdClass();
			
			//inject additional dependencies
			$this->inject($instance);
			
			return $instance;
		}
		
		//create class profile
		$profile = new ClassProfile($class, $cclass);
		$params = array();//constructor params
		
		if (!is_null($profile->constructor)) {
			//build constructor parameter list
			$parameters = $profile->constructor->getParameters();
		
			foreach ($parameters as $param) {
				//is parameter injectable?
				if (array_key_exists($param->getName(), $profile->constructorParams)) {
					//get parameter injection data
					$paramData = $profile->constructorParams[$param->getName()];
		
					//check container class
					if ($paramData['container'] != $cclass) {
						throw new \RuntimeException("Class '{$profile->className}' depends on {$paramData['container']}::{$paramData['service']}. Use Injector::create instead.");
					}
		
					//add service to instance parameters
					$params[] = $this->offsetGet($paramData['service']);
				}
				//check for additional parameters
				elseif (!empty($args)) {
					$params[] = array_shift($args);
				}
				else {
					//obtain default value, if any
					if ($param->isOptional()) {
						$params[] = $param->getDefaultValue();
					}
					else {
						throw new \RuntimeException("Not enough parameters provided for '$class' constructor");
					}
				}
			}
		}
		
		//create instance
		$instance = $profile->class->newInstanceArgs($params);
		
		//inject additional dependencies
		$this->inject($instance);
		
		return $instance;
	}
	
	/**
	 * Injects all specified dependencies on an instance
	 * @param object $instance
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function inject(&$instance, $_ = null) {
		if (!is_object($instance)) {
			throw new \InvalidArgumentException("Argument is not a valid object");
		}
		
		//get properties
		$properties = func_get_args();
		array_shift($properties);
		
		//container class
		$cclass = get_class($this);
		
		if ($instance instanceof \stdClass) {
			$services = $this->keys();
			
			//is there any property?
			if (empty($properties)) {
				//inject all
				for ($i = 0, $n = count($services); $i < $n; $i++) {
					$instance->$services[$i] = $this->offsetGet($services[$i]);
				}
			}
			else {
				for ($i = 0, $n = count($services); $i < $n; $i++) {
					if (!in_array($services[$i], $properties)) continue;
					$instance->$services[$i] = $this->offsetGet($services[$i]);
				}
			}
			
			return;
		}

		//build class profile
		$profile = new ClassProfile(get_class($instance), $cclass);
		$keys = $this->keys();
		
		if (empty($properties)) {
			foreach ($profile->properties as $name => $property) {
				if ($property['container'] != $cclass) {
					throw new \RuntimeException("Class '{$profile->className}' depends on {$property['container']}::{$property['service']}. Use Injector::create instead.");
				}
			
				//check if service is available
				if (!in_array($property['service'], $keys)) continue;
				
				//make property accesible
				$property['reflection']->setAccessible(true);
				
				//set property value
				if ($property['reflection']->isStatic()) {
					$property['reflection']->setValue(null, $this->offsetGet($property['service']));
				}
				else {
					$property['reflection']->setValue($instance, $this->offsetGet($property['service']));
				}
			}
		}
		else {
			foreach ($properties as $property) {
				if (!array_key_exists($property, $profile->properties)) {
					throw new \RuntimeException("Property '$property' does not appear to be an injectable property");
				}
				
				$propertyData = $profile->properties[$property];
				
				if ($propertyData['container'] != $cclass) {
					throw new \RuntimeException("Class '{$profile->className}' depends on {$propertyData['container']}::{$propertyData['service']}. Use Injector::create instead.");
				}
			
				//check if service is available
				if (!in_array($propertyData['service'], $keys)) continue;
				
				//make property accesible
				$propertyData['reflection']->setAccessible(true);
				
				//set property value
				if ($propertyData['reflection']->isStatic()) {
					$propertyData['reflection']->setValue(null, $this->offsetGet($propertyData['service']));
				}
				else {
					$propertyData['reflection']->setValue($instance, $this->offsetGet($propertyData['service']));
				}
			}
		}
	}
} 