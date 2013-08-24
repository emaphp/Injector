<?php
namespace Injector;

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
	 * Class default container
	 * @var string
	 */
	public $defaultContainer;
	
	/**
	 * Container class
	 * @var string
	 */
	public $containerClass;
	
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
	 * Creates a new class profile
	 * @param string $className
	 * @param string $container
	 * @throws \RuntimeException
	 */
	public function __construct($className, $container = null) {
		$this->className = $className;
		$this->build($container);
	}
	
	protected function build($requestedContainer) {
		$this->class = new \ReflectionClass($this->className);
		
		/**
		 * PARSE CLASS
		 */
		//extract doc comments
		$doc = $this->class->getDocComment();
		
		//check if class has doc comments (@container {container_class})
		if ($doc !== false && preg_match('/@container[ ]+([\w|\\\\]+)/', $doc, $matches)) {
			//set only if profile does not provide a default container
			$this->defaultContainer = $matches[1];
		}
		
		/**
		 * PARSE PROPERTIES
		 */
		$properties = $this->class->getProperties();
		
		foreach ($properties as $property) {
			//get doc comments
			$doc = $property->getDocComment();
			
			if ($doc !== false) {
				//get injected properties (@inject [container_class::]{service_name})
				if (preg_match('/@inject[ ]+([\w|\\\\]+::)?([\w]+)/', $doc, $matches)) {
					$service = $matches[2];
						
					//check container
					if (empty($matches[1])) {
						//set default container, if any
						if (is_null($requestedContainer) && is_null($this->defaultContainer)) {
							throw new \RuntimeException("No default container specified for class '{$this->className}'");
						}
							
						$container = true;
					}
					else {
						$container = substr($matches[1], 0, -2);
					}
				}
				
				//add property profile
				$this->properties[$property->getName()] = array('container'  => $container,
																'service'    => $service,
																'reflection' => $property);
			}
		}
		
		/**
		 * PARSE CONSTRUCTOR
		 */
		//get constructor, if any
		if ($this->class->hasMethod('__construct')) {
			$this->constructor = new \ReflectionMethod($this->className, '__construct');
			$doc = $this->constructor->getDocComment();
			
			//get injected parameters
			if (preg_match('/@inject/', $doc)) {
				//@inject {$var} [container_class::]{service_name}
				$tmatches = preg_match_all('/@inject[ ]+\$([\w]+)[ ]+([\w|\\\\]+::)?([\w]+)/', $doc, $matches);
					
				for ($i = 0; $i < $tmatches; $i++) {
					//check container
					if (empty($matches[2][$i])) {
						//set default container, if any
						if (is_null($requestedContainer) && is_null($this->defaultContainer)) {
							throw new \RuntimeException("No default container specified for class '{$this->className}'");
						}
							
						$container = true;
					}
					else {
						$container = substr($matches[2][$i], 0, -2);
					}
			
					//add parameter profile
					$this->constructorParams[$matches[1][$i]] = array('container' => $container,
																	  'service' => $matches[3][$i]);
				}
			}
		}
		else {
			$this->constructor = null;
		}
	}	
}