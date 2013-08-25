<?php
namespace Injector;

use Injector\ClassProfile;

class Profiler {
	/**
	 * Class profiles list
	 * @var array
	 */
	public static $profiles = array();
	
	/**
	 * Obtains a class profile for the given container
	 * @param string $class
	 * @param \Pimple|string|NULL $container
	 * @throws \RuntimeException
	 * @return ClassProfile
	 */
	public static function profile($class, $container = null) {
		//check container type
		if ($container instanceof \Pimple) {
			$container = get_class($container);
		}
		elseif (is_object($container)) {
			throw new \RuntimeException("Container is not a valid instance of Pimple.");
		}
		elseif (!is_null($container) && (!is_string($container) || (is_string($container) && empty($container)))) {
			throw new \RuntimeException("Container is not a valid class.");
		}
		
		//check if profile was already loaded
		if (!array_key_exists($class, self::$profiles)) {
			$profile = self::$profiles[$class] = new ClassProfile($class, $container);
		}
		
		//adapt profile and return
		$profile = clone self::$profiles[$class];
		return self::adapt($profile, is_null($container) ? $profile->defaultContainer : $container);
	}
	
	/**
	 * Adapts a class profile to a container
	 * @param ClassProfile $profile
	 * @param NULL|string $container
	 * @return ClassProfile
	 */
	protected static function adapt(ClassProfile $profile, $container) {
		if (is_null($container)) {
			return $profile;
		}
		
		$profile->containerClass = $container;
		
		//adapt properties
		$keys = array_keys($profile->properties);
		
		for ($i = 0, $n = count($keys); $i < $n; $i++) {
			if ($profile->properties[$keys[$i]]['container'] === true) {
				$profile->properties[$keys[$i]]['container'] = $container;
			}
		}
		
		//adapt constructor parameters
		$keys = array_keys($profile->constructorParams);
		
		for ($i = 0, $n = count($keys); $i < $n; $i++) {
			//set container on constructor parameters
			if ($profile->constructorParams[$keys[$i]]['container'] === true) {
				$profile->constructorParams[$keys[$i]]['container'] = $container;
			}
		}
		
		return $profile;
	}
}