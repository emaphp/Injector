<?php
namespace Injector;

final class Injector {
	/**
	 * Obtains/Stores a container
	 * @param string|\Pimple $class_or_container
	 * @throws \RuntimeException
	 * @return \Pimple|boolean
	 */
	public static function container($class_or_container) {
		static $ccontainer;

		//initialize main container
		if (is_null($ccontainer)) {
			$ccontainer = new \Pimple();
		}
		
		//check if parameter must be stored
		if ($class_or_container instanceof \Pimple) {
			$class = get_class($class_or_container);
			
			$ccontainer[$class] = $ccontainer->share(function ($c) use ($class_or_container) {
				return $class_or_container;
			});
			
			return true;
		}
		//create container instance if not available
		elseif (is_string($class_or_container)) {
			if (!array_key_exists($class_or_container, $ccontainer)) {
				$ccontainer[$class_or_container] = $ccontainer->share(function ($c) use ($class_or_container) {
					return new $class_or_container;
				});
			}

			return $ccontainer[$class_or_container];
		}
		
		throw new \RuntimeException("Unsupported container type");
	}
	
	/**
	 * Generates a new instance withouta default container
	 * @param string $class
	 * @return mixed
	 */
	public static function create($class, $_ = null) {
		$args = func_get_args();
		array_unshift($args, null);
		return call_user_func_array(array('self', 'createFrom'), $args);
	}
	
	public static function createFrom($container, $class, $_ = null) {
		//new instance parameters
		$params = array();
		
		//get method parameters
		$args = func_get_args();
		$container = array_shift($args);
		$class = array_shift($args);

		//build class profile
		$profile = new ClassProfile($class, $container);
		
		//store default container
		if (is_object($profile->container)) {
			self::container($profile->container);
		}
		
		if (!is_null($profile->constructor)) {
			//build constructor parameter list
			$parameters = $profile->constructor->getParameters();
			
			foreach ($parameters as $param) {
				if (array_key_exists($param->getName(), $profile->constructorParams)) {
					//get parameter injection data
					$paramData = $profile->constructorParams[$param->getName()];
					//add service to instance parameters
					$params[] = self::container($paramData['container'])->offsetGet($paramData['service']);
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
		
		//inject properties
		self::injectFrom($container, $instance);
		
		return $instance;
	}
	
	public static function inject(&$instance, $_ = null) {
		$args = func_get_args();
		array_unshift($args, null);
		return call_user_func_array(array('self', 'injectFrom'), $args);
	}
	
	/**
	 * Injects a list of services into an object
	 * @param object $instance
	 * @throws InvalidArgumentException
	 */
	public static function injectFrom($container, &$instance, $_ = null) {
		if (!is_object($instance)) {
			throw new \InvalidArgumentException("Argument is not a valid object");
		}

		if ($instance instanceof \stdClass) {
			throw new \InvalidArgumentException("Cannot inject properties to stdClass instance from Injector class.");
		}
		
		//get properties
		$properties = func_get_args();
		$container = array_shift($properties);
		array_shift($properties);
		
		//build class profile
		$profile = new ClassProfile(get_class($instance), $container);
		
		if (empty($properties)) {
			foreach ($profile->properties as $name => $property) {
				if (!self::container($property['container'])->offsetExists($property['service'])) {
					continue;
				}
				
				//make property accesible
				$property['reflection']->setAccessible(true);
		
				//set property value
				if ($property['reflection']->isStatic()) {
					$property['reflection']->setValue(null, self::container($property['container'])->offsetGet($property['service']));
				}
				else {
					$property['reflection']->setValue($instance, self::container($property['container'])->offsetGet($property['service']));
				}
			}
		}
		else {
			foreach ($properties as $property) {
				if (!array_key_exists($property, $profile->properties)) {
					throw new \RuntimeException("Property '$property' does not appear to be an injectable property");
				}
		
				//get property data
				$propertyData = $profile->properties[$property];
		
				if (!self::container($property['container'])->offsetExists($propertyData['service'])) {
					continue;
				}
				
				//make property accesible
				$propertyData['reflection']->setAccessible(true);
		
				//set property value
				if ($propertyData['reflection']->isStatic()) {
					$propertyData['reflection']->setValue(null, self::container($propertyData['container'])->offsetGet([$propertyData['service']]));
				}
				else {
					$propertyData['reflection']->setValue($instance, self::container($propertyData['container'])->offsetGet([$propertyData['service']]));
				}
			}
		}
	}
}