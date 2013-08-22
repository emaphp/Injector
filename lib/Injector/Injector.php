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

		if (is_null($ccontainer)) {
			$ccontainer = new \Pimple();
		}
		
		if ($class_or_container instanceof \Pimple) {
			$class = get_class($class_or_container);
			
			$ccontainer[$class] = $ccontainer->share(function ($c) use ($class_or_container) {
				return $class_or_container;
			});
			
			return true;
		}
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
	
	public static function createWith($container, $class, $_ = null) {
		//new instance parameters
		$params = array();
		
		//get method parameters
		$args = func_get_args();
		$container = array_shift($args);
		$class = array_shift($args);
		
		//build class profile
		$profile = new ClassProfile($class, $container);
		
		if (!is_null($profile->constuctor)) {
			//build constructor parameter list
			$parameters = $profile->constuctor->getParameters();
			
			foreach ($parameters as $param) {
				if (array_key_exists($param->getName(), $profile->constructorParams)) {
					//get parameter injection data
					$paramData = $profile->constructorParams[$param->getName()];
					//add service to instance parameters
					$params[] = self::container($paramData['container'])[$paramData['service']];
				}
				elseif (!empty($args)) {
					$params[] = array_shift($args);
				}
				else {
					throw new \RuntimeException("Not enough parameters provided for '$class' constructor");
				}
			}
		}
		
		//store default container
		if (is_object($profile->container)) {
			self::container($profile->container);
		}
		
		//create instance
		$instance = $profile->class->newInstanceArgs($params);
		
		//inject properties
		foreach ($profile->properties as $name => $property) {
			$setter = $property['setter'];
						
			if (isset($setter)) {
				$instance->$setter(self::container($property['container'])[$property['service']]);
			}
			else {
				$instance->$name = self::container($property['container'])[$property['service']];
			}
		}
		
		return $instance;
	}
	
	public static function create($class, $_ = null) {
		$args = func_get_args();
		array_unshift($args, null);
		return call_user_func_array(array('self', 'createWith'), $args);
	}
	
	/**
	 * Injects a list of services into an object
	 * @param unknown $obj
	 * @throws InvalidArgumentException
	 */
	public function inject(&$obj) {
	}
}