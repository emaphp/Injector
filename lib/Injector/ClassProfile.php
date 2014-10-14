<?php
namespace Injector;

use Omocha\Omocha;
use Omocha\Filter;

/**
 * Stores a class profile
 * @author emaphp
 * @package Injector
 */
class ClassProfile {
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
	 * Service providers
	 * @var array
	 */
	public $providers;
		
	/**
	 * Constructor injection values
	 * @var array
	 */
	public $constructorParams = [];
	
	/**
	 * Injection properties
	 * @var array
	 */
	public $properties = [];
	
	/**
	 * Reflection properties array
	 * @var array
	 */
	public $reflectionProperties = [];
	
	/**
	 * Indicates if all dependecies must be fullfiled
	 * When true, a RuntimeException is thrown if a dependency is not declared within the container
	 * @var boolean
	 */
	public $isStrict = false;
	
	/**
	 * Creates a new class profile
	 * @param string $className
	 * @throws \RuntimeException
	 */
	public function __construct($className) {
		$this->className = $className;
		
		//obtain class annotations
		$this->class = new \ReflectionClass($this->className);
		$annotations = Omocha::getAnnotations($this->class);
		$this->providers = $this->getClassProviders($this->class);
		
		//check if injection is strict
		if ($annotations->has('StrictInject')) {
			$this->isStrict = (boolean) $annotations->get('StrictInject')->getValue();
		}
		
		//parse properties
		$properties = $this->class->getProperties();
		
		foreach ($properties as $property) {
			$propertyAnnotations = Omocha::getAnnotations($property);
				
			//check if is a valid injected property
			if ($propertyAnnotations->has('Inject')) {
				//service to inject
				$service = (string) $propertyAnnotations->get('Inject')->getValue();
		
				if (!empty($service)) {
					$propertyName = $property->getName();
						
					//store ReflectionProperty instance and make it accesible
					$this->properties[$propertyName] = $service;
					$this->reflectionProperties[$propertyName] = $property;
					$this->reflectionProperties[$propertyName]->setAccessible(true);
				}
			}
		}
		
		//parse constructor
		$this->constructor = $this->class->getConstructor();
		
		if (!is_null($this->constructor)) {
			$constructorAnnotations = Omocha::getAnnotations($this->constructor);
				
			//obtain injected arguments
			if ($constructorAnnotations->has('Inject')) {
				$injectedArgs = $constructorAnnotations->find('Inject', Filter::HAS_ARGUMENT | Filter::TYPE_STRING);
		
				foreach ($injectedArgs as $argument) {
					$service = $argument->getValue();
						
					if (!empty($service)) {
						$argumentName = $this->parseArgumentName($argument->getArgument());
						$this->constructorParams[$argumentName] = $service;
					}
				}
			}
		}
	}
	
	/**
	 * Obtains all providers for a given class
	 * @param \ReflectionClass $class
	 * @return array
	 */
	protected function getClassProviders(\ReflectionClass $class) {
		$annotations = Omocha::getAnnotations($class);
		
		//get class providers
		$providers = $annotations->find('Provider', Filter::TYPE_STRING);
		
		if (!empty($providers)) {
			$classes = [];
			
			foreach ($providers as $provider) {
				$classes[] = $provider->getValue();
			}
			
			return $classes;
		}
		
		//if no providers are specified try getting them from parent class
		if ($annotations->has('ExtendInject')) {
			$extend = $annotations->get('ExtendInject')->getValue();
			
			if ($extend) {
				$parent = $class->getParentClass();
				return $parent instanceof \ReflectionClass ? $this->getClassProviders($parent) : [];
			}
		}
		
		return [];
	}

	/**
	 * Parses a contructor argument expression
	 * @param string $argumentName
	 * @throws \RuntimeException
	 * @return string
	 */
	protected function parseArgumentName($argumentName) {
		if (!preg_match('@^\$?(\w+)$@', $argumentName, $matches)) {
			throw new \RuntimeException(sprintf("Invalid annotation expression found in %s __construct", $this->className));
		}
		
		return $matches[1];
	}
}