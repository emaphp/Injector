<?php
namespace Injector;

/**
 * Manages a collection of class profiles
 * @author emaphp
 * @package Injector
 */
class Profiler {
	/**
	 * Class profiles list
	 * @var array
	 */
	protected static $profiles = array();
	
	/**
	 * Obtains a class profile by its name
	 * @param string $classname
	 * @throws \RuntimeException
	 */
	public static function getClassProfile($classname) {
		if (!is_string($classname) || empty($classname)) {
			throw new \InvalidArgumentException("Argument is not a valid class name");
		}
		
		//check if is already stored
		if (array_key_exists($classname, self::$profiles)) {
			return self::$profiles[$classname];
		}
		
		//validate classname
 		if (!class_exists($classname, true)) {
 			throw new \RuntimeException("Class %s could not be found");
 		}
		
		//build profile and return
		self::$profiles[$classname] = new ClassProfile($classname);
		return self::$profiles[$classname];
	}
}