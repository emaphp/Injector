<?php
namespace Injector;

/**
 * Dependency injection class
 * @author emaphp
 * @package Injector
 */
class Injector {
	/**
	 * Default container class name
	 * @var string
	 */
	protected static $defaultContainer = null;
	
	/**
	 * Set default container class
	 * @param string $container
	 */
	public static function setDefaultContainer($container) {
		self::$defaultContainer = $container;
	}
	
	/**
	 * Obtains the default container class
	 * @return string
	 */
	public static function getDefaultContiner() {
		return self::$defaultContainer;
	}
	
	/**
	 * Creates a new instance of $classname from a default container
	 * @param string $classname
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @return object
	 */
	public static function create($classname, $_ = null) {
		if (!is_string($classname) || empty($classname)) {
			throw new \InvalidArgumentException("Argument is not a valid class name");
		}
		
		//obtain default container for this class
		$profile = Profiler::getClassProfile($classname);
		$containerClass = is_null($profile->defaultContainer) ? self::$defaultContainer : $profile->defaultContainer;
		
		if (empty($containerClass)) {
			throw new \RuntimeException("No default container has been set for class $classname");
		}
		
		//create container and inject dependencies
		$container = self::getContainer($containerClass);
		
		if ($container instanceof Container) {
			return call_user_func_array([$container, 'create'], func_get_args());
		}
		elseif ($container instanceof \Pimple) {
			$instance = $profile->class->newInstance();
			self::inject($instance, $container);
			return $instance;
		}
		
		throw new \InvalidArgumentException("$containerClass is not a valid container class");
	}
	
	public static function inject(&$instance, $container, $filter, $override) {
		if (!is_object($instance)) {
			throw new \InvalidArgumentException("Argument is not a valid object");
		}
		
		if (!is_object($container)) {
			throw new \InvalidArgumentException("Container is not a valid object");
		}
		elseif (!($container instanceof \Pimple)) {
			throw new \InvalidArgumentException("Container is not a valid container");
		}
		
		//check if objects is a stdClass instance
		if ($instance instanceof \stdClass) {
			$services = $container->keys();
				
			if (empty($filter)) {
				//inject all
				for ($i = 0, $n = count($services); $i < $n; $i++) {
					if (is_array($override) && array_key_exists($services[$i], $override)) {
						continue;
					}
						
					$instance->$services[$i] = $container->offsetGet($services[$i]);
				}
			}
			else {
				foreach ($filter as $service) {
					//check if service exists
					if (!in_array($service, $services)) {
						throw new \RuntimeException(sprintf("Service '$service' does not exists in container class '%s'", get_class($container)));
					}
						
					if (is_array($override) && array_key_exists($service, $override)) {
						continue;
					}
						
					$instance->$service = $container->offsetGet($service);
				}
			}
				
			//override values
			if (is_array($override) && !empty($override)) {
				foreach ($override as $service => $value) {
					if (is_int($service)) {
						continue;
					}
						
					$instance->$service = $value;
				}
			}
				
			return;
		}
		
		//build class profile
		$classname = get_class($instance);
		$profile = Profiler::getClassProfile($classname);
		$services = $container->keys();
		
		if (empty($filter)) {
			foreach ($profile->reflectionProperties as $name => $property) {
				if (is_array($override) && array_key_exists($services[$i], $override)) {
					continue;
				}
		
				$serviceId = $profile->properties[$name];
		
				//check if service is available
				if (!$container->offsetExists($serviceId)) {
					throw new \RuntimeException("Property '$name' in class $classname is associated to a unknown service '$serviceId'");
				}
		
				//set property value
				if ($property->isStatic()) {
					$property->setValue(null, $container->offsetGet($serviceId));
				}
				else {
					$property->setValue($instance, $container->offsetGet($serviceId));
				}
			}
		}
		else {
			foreach ($filter as $property) {
				if (is_array($override) && array_key_exists($property, $override)) {
					continue;
				}
			
				if (!array_key_exists($property, $profile->properties)) {
					throw new \RuntimeException("Property '$property' does not appear to be an injectable property");
				}
			
				$serviceId = $profile->properties[$property];
			
				if (!$container->offsetExists($serviceId)) {
					throw new \RuntimeException("Property '$name' in class $classname is associated to a unknown service '$serviceId'");
				}
			
				//set property value
				if ($profile->reflectionProperties[$property]->isStatic()) {
					$profile->reflectionProperties[$property]->setValue(null, $container->offsetGet($serviceId));
				}
				else {
					$profile->reflectionProperties[$property]->setValue($instance, $container->offsetGet($serviceId));
				}
			}
		}
		
		//override properties
		if (is_array($override) && !empty($override)) {
			foreach ($override as $service => $value) {
				if (array_key_exists($service, $profile->reflectionProperties)) {
					if ($profile->reflectionProperties[$service]->isStatic()) {
						$profile->reflectionProperties[$service]->setValue(null, $value);
					}
					else {
						$profile->reflectionProperties[$service]->setValue($instance, $value);
					}
				}
				elseif ($profile->class->hasProperty($service)) {
					$property = $profile->class->getProperty($service);
					$property->setAccessible(true);
						
					if ($property->isStatic()) {
						$property->setValue(null, $value);
					}
					else {
						$property->setValue($instance, $value);
					}
				}
				else {
					throw new \RuntimeException("Cannot override non existant property '$service'");
				}
			}
		}
	}
	
	/**
	 * Obtains/Stores a container
	 * @param string $container_class
	 * @throws \InvalidArgumentException
	 * @return \Pimple
	 */
	public static function getContainer($container_class) {
		static $ccontainer;

		//initialize main container
		if (is_null($ccontainer)) {
			$ccontainer = new \Pimple();
		}
		
		if (!$ccontainer->offsetExists($container_class)) {
			if (!class_exists($container_class, true)) {
				throw new \InvalidArgumentException("Class $container_class could not be found");
			}
			
			$ccontainer[$container_class] = function ($c) {
				return new $container_class;
			};
		}
		
		return $ccontainer[$container_class];
	}
}