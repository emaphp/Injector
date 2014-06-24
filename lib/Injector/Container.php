<?php
namespace Injector;

/**
 * Extended container class
 * @author emaphp
 * @package Injector
 */
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
				
		//when stdClass do a simple injection
		if (preg_match('/^(\\\\)?stdClass$/', $class)) {
			$instance = new \stdClass();
			
			//inject additional dependencies
			Injector::inject($instance, $this);
			
			return $instance;
		}
		
		//create class profile
		$profile = Profiler::getClassProfile($class);
		
		//constructor parameters
		$params = array();
		
		//check if class has a constructor method
		if (!is_null($profile->constructor)) {
			//build constructor parameter list
			$parameters = $profile->constructor->getParameters();
		
			foreach ($parameters as $param) {
				if (!empty($args)) {
					$params[] = array_shift($args);
				}
				elseif (array_key_exists($param->getName(), $profile->constructorParams)) {
					//get parameter id
					$parameterId = $profile->constructorParams[$param->getName()];
		
					if ($this->offsetExists($parameterId)) {
						//add service to constructor arguments
						$params[] = $this->offsetGet($parameterId);
					}
					elseif ($param->isOptional()) {
						$params[] = $param->getDefaultValue();
					}
					else {
						throw new \RuntimeException(sprintf("Argument %s in class '%s' constructor is associated to a unknown service '%s'", $param->getName(), $class, $parameterId));
					}
				}
				elseif ($param->isOptional()) {
					$params[] = $param->getDefaultValue();
				}
				else {
					throw new \RuntimeException("Not enough arguments provided for '$class' constructor");
				}
			}
		}
		
		//create instance
		$instance = $profile->class->newInstanceArgs($params);
		
		//inject additional dependencies
		Injector::inject($instance, $this);
		
		return $instance;
	}
	
	/**
	 * Injects all specified dependencies on an instance
	 * @param object $instance
	 * @param array $filter
	 * @param array $override
	 */
	public function inject(&$instance, $filter = null, $override = null) {
		return Injector::inject($instance, $this, $filter, $override);
	}
} 