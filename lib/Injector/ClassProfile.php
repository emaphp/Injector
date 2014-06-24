<?php
namespace Injector;

use Minime\Annotations\Facade;

/**
 * Stores a class profile
 * @author emaphp
 * @package Injector
 */
class ClassProfile {
	/**
	 * Default annotation namespace
	 * @var string
	 */
	const NS = 'inject';
	
	/**
	 * Reflection class
	 * @var \ReflectionClass
	 */
	public $class;
	
	/**
	 * Class constructor
	 * @var \ReflectionMethod
	 */
	public $constructor;
	
	/**
	 * Class name
	 * @var string
	 */
	public $className;
	
	/**
	 * Class default container
	 * @var string
	 */
	public $defaultContainer;
		
	/**
	 * Constructor injection values
	 * @var array
	 */
	public $constructorParams = array();
	
	/**
	 * Injection properties
	 * @var array
	 */
	public $properties = array();
	
	/**
	 * Reflection properties array
	 * @var array
	 */
	public $reflectionProperties = array();
	
	/**
	 * Creates a new class profile
	 * @param string $className
	 * @throws \RuntimeException
	 */
	public function __construct($className) {
		$this->className = $className;
		$this->initialize();
	}
	
	/**
	 * Initializes a class profile instance
	 * @throws \RuntimeException
	 */
	protected function initialize() {
		$this->class = new \ReflectionClass($this->className);
		
		//parse class annotations
		$annotations = Facade::getAnnotations($this->class);
		$values = $annotations->useNamespace(self::NS)->export();
		
		//get default container class (if any)
		if (array_key_exists('container', $values)) {
			$this->defaultContainer = is_array($values['container']) ? array_shift($values['container']) : $values['container'];
			
			if (!is_string($this->defaultContainer) || empty($this->defaultContainer)) {
				throw new \RuntimeException(sprintf("Class '%s' must define a valid container class", $this->className));
			}
		}
		
		//parse properties
		$properties = $this->class->getProperties();
		
		foreach ($properties as $property) {
			$annotations = Facade::getAnnotations($property);
			$values = $annotations->useNamespace(self::NS)->export();
			
			if (array_key_exists('service', $values)) {
				//store service id
				$propertyName = $property->getName();
				$this->properties[$propertyName] = is_array($values['service']) ? array_shift($values['service']) : $values['service'];
				
				//store ReflectionProperty instance and make it accesible
				$this->reflectionProperties[$propertyName] = $property;
				$this->reflectionProperties[$propertyName]->setAccesible(true);
			}
		}
		
		//parse constructor
		$this->constructor = $this->class->getConstructor();
		$annotations = Facade::getAnnotations($this->constructor);
		$values = $annotations->useNamespace(self::NS)->export();
		
		if (array_key_exists('param', $values)) {
			if (is_array($values['param'])) {
				foreach ($values['param'] as $arg) {
					list($argname, $argid) = $this->parseParameter($arg);
					
					if ($argname) {
						$this->constructorParams[$argname] = $argid;
					}
				}
			}
			else {
				list($argname, $argid) = $this->parseParameter($values['param']);
				
				if ($argname) {
					$this->constructorParams[$argname] = $argid;
				}
			}
		}
	}
	
	/**
	 * Parses an injected argument expression
	 * @param string $str
	 * @return array
	 */
	protected function parseParameter($str) {
		$regex = '/(?:\s*)?\$?(\w+)(?:\s+)(\w+)(?:\s*)?$/';
		
		if (preg_match($regex, $str, $matches)) {
			return [$matches[1], $matches[2]];
		}
		
		throw new \RuntimeException(sprintf("Invalid expression found in %s __construct method annotation", $this->className));
	}
}