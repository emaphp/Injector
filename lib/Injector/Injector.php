<?php
namespace Injector;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Dependency injection class
 * @author emaphp
 * @package Injector
 */
class Injector {
	/**
	 * Providers array
	 * @var array
	 */
	protected static $providers = array();
	
	/**
	 * Obtains a provider class instance
	 * @param string $classname
	 * @throws \InvalidArgumentException
	 * @return ServiceProviderInterface
	 */
	public static function getProvider($classname) {
		if (array_key_exists($classname, self::$providers)) {
			return self::$providers[$classname];
		}
		
		$provider = new $classname;
		
		if (!($provider instanceof ServiceProviderInterface)) {
			throw new \InvalidArgumentException("$classname is not a valid ServiceProviderInterface instance");
		}

		self::$providers[$classname] = $provider;
		return self::$providers[$classname];
	}
	
	/**
	 * Creates a new instance of $classname with the specified arguments
	 * @param string $classname
	 * @param Container $container
	 * @param string $args
	 * @param array $filter
	 * @param array $override
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @return object
	 */
	public static function createWith($classname, Container $container, $args = null, $filter = null, $override = null) {
		if (!is_string($classname) || empty($classname)) {
			throw new \InvalidArgumentException("Argument is not a valid class name");
		}
		
		//obtain provider list for this class
		$profile = Profiler::getClassProfile($classname);
				
		if (isset($profile->constructor)) {
			//build constructor parameter list
			$parameters = $profile->constructor->getParameters();
			
			if (is_null($args)) {
				$args = [];
			}
			else {
				$args = is_array($args) ? $args : [$args];
			}
				
			foreach ($parameters as $param) {
				if (!empty($args)) {
					$params[] = array_shift($args);
				}
				elseif (is_array($override) && array_key_exists($param->getName(), $profile->constructorParams) && array_key_exists($profile->constructorParams[$param->getName()], $override)) {
					$params[] = $override[$profile->constructorParams[$param->getName()]];
				}
				elseif (array_key_exists($param->getName(), $profile->constructorParams)) {
					//get parameter id
					$parameterId = $profile->constructorParams[$param->getName()];
					
					if (is_array($filter) && !in_array($parameterId, $filter)) {
						if ($param->isOptional()) {
							$params[] = $param->getDefaultValue();
						}
						elseif (!$profile->isStrict) {
							$params[] = null;
						}
						else {
							throw new \RuntimeException(sprintf("Argument %s in class '%s' constructor is associated to a filtered service '%s'", $param->getName(), $classname, $parameterId));
						}
					}
					elseif ($container->offsetExists($parameterId)) {
						//add service to constructor arguments
						$params[] = $container->offsetGet($parameterId);
					}
					elseif ($param->isOptional()) {
						$params[] = $param->getDefaultValue();
					}
					elseif (!$profile->isStrict) {
						$params[] = null;
					}
					else {
						throw new \RuntimeException(sprintf("Argument %s in class '%s' constructor is associated to a unknown service '%s'", $param->getName(), $classname, $parameterId));
					}
				}
				elseif ($param->isOptional()) {
					$params[] = $param->getDefaultValue();
				}
				else {
					throw new \RuntimeException("Not enough arguments provided for '$classname' constructor");
				}
			}
			
			$instance = $profile->class->newInstanceArgs($params);
		}
		else {
			$instance = new $classname;
		}
		
		self::inject($instance, $container, $filter, $override);
		return $instance;
	}
	
	/**
	 * Creates a new instance of $classname from the associated providers
	 * @param string $classname
	 * @param array $args
	 * @param array $filter
	 * @param array $override
	 * @throws \RuntimeException
	 * @return object
	 */
	public static function create($classname, $args = null, $filter = null, $override = null) {
		//obtain provider list for this class
		$profile = Profiler::getClassProfile($classname);
		$providers = $profile->providers;
		
		if (empty($providers)) {
			throw new \RuntimeException("Class $classname does not have any provider associated with it");
		}

		//create new container and register all providers
		$container = new Container();
		
		foreach ($providers as $provider) {
			$providerInstance = self::getProvider($provider);
			$providerInstance->register($container);
		}

		return self::createWith($classname, $container, $args, $filter, $override);
	}
	
	/**
	 * Injects a set of dependencies into an instance
	 * @param object $instance
	 * @param Pimple\Container $container
	 * @param array $filter Which dependencies must be injected
	 * @param array $override An associative array that overrides a set of injected properties
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public static function inject(&$instance, $container, $filter = null, $override = null) {
		if (!is_object($instance)) {
			throw new \InvalidArgumentException("Argument is not a valid object");
		}
		
		if (!is_object($container)) {
			throw new \InvalidArgumentException("Container is not a valid object");
		}
		elseif (!($container instanceof Container)) {
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
		
		foreach ($profile->reflectionProperties as $name => $property) {
			$serviceId = $profile->properties[$name];
		
			if (is_array($override) && array_key_exists($serviceId, $override)) {
				if ($property->isStatic()) {
					$property->setValue(null, $override[$serviceId]);
				}
				else {
					$property->setValue($instance, $override[$serviceId]);
				}
			}
			else {
				if (is_array($filter) && !in_array($serviceId, $filter)) {
					continue;
				}
				
				if (!$container->offsetExists($serviceId)) {
					if ($profile->isStrict) {
						throw new \RuntimeException("Property '$name' in class $classname is associated to a unknown service '$serviceId'");
					}
					
					continue;
				}
				else {
					$value = $container->offsetGet($serviceId);
					
					if ($property->isStatic()) {
						$property->setValue(null, $value);
					}
					else {
						$property->setValue($instance, $value);
					}
				}				
			}
		}
	}
}