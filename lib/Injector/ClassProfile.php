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
	public $constuctor;
	
	/**
	 * Class name
	 * @var string
	 */
	public $className;
	
	/**
	 * Class container
	 * @var mixed
	 */
	public $container;
	
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
	
	public function __construct($className, $container = null) {
		$this->className = $className;
		$this->container = $container;
		
		if (is_null($container)) {
			$this->containerClass = $container;
		}
		elseif (is_object($container)) {
			if (!($container instanceof \Pimple)) {
				throw new \RuntimeException("Container is not a valid instance of Pimple");
			}
			
			$this->containerClass = get_class($container);
		}
		elseif (is_string($container)) {
			$this->containerClass = $container;
		}
		else {
			throw new \RuntimeException("Container must be defined as an string or object");	
		}
		
		$this->build();
	}
	
	protected function build() {
		$this->class = new \ReflectionClass($this->className);
		
		/**
		 * PARSE CLASS
		 */
		//extract doc comments
		$doc = $this->class->getDocComment();
		
		//check if class has doc comments
		if ($doc !== false && preg_match('/@container[ ]+([\w|\\\\]+)/', $doc, $matches)) {
			if (is_null($this->container)) {
				$this->container = $this->containerClass = $matches[1];
			}
		}
		
		/**
		 * PARSE PROPERTIES
		 */
		$properties = $this->class->getProperties();
		
		foreach ($properties as $property) {
			//get doc comments
			$doc = $property->getDocComment();
			
			if ($doc !== false) {
				if (preg_match('/@inject[ ]+(([\w]+)\(([\w|\\\\]+::)?([\w]+)\)|([\w|\\\\]+::)?([\w]+))/', $doc, $matches)) {
					//is setter defined?
					if (!empty($matches[2])) {
						$setter = $matches[2];
						
						//check container
						if (empty($matches[3])) {
							if (is_null($this->container)) {
								throw new \RuntimeException("No default container specified for class '{$this->className}'");
							}
							
							$container = $this->containerClass;
						}
						else {
							$container = substr($matches[3], 0, -2);
						}
						
						$service = $matches[4];
					}
					//no setter
					else {
						$setter = null;
						$service = $matches[6];
						
						//check container
						if (empty($matches[5])) {
							if (is_null($this->container)) {
								throw new \RuntimeException("No default container specified for class '{$this->className}'");
							}
								
							$container = $this->containerClass;
						}
						else {
							$container = substr($matches[5], 0, -2);
						}
					}
				}
				
				$this->properties[$property->getName()] = array('setter' => $setter,
																'container' => $container,
																'service' => $service);
			}
		}
		
		/**
		 * PARSE CONSTRUCTOR
		 */
		if ($this->class->hasMethod('__construct')) {
			$this->constuctor = new \ReflectionMethod($this->className, '__construct');
			$doc = $this->constuctor->getDocComment();
			
			if (preg_match('/@inject/', $doc)) {
				$tmatches = preg_match_all('/@inject[ ]+\$([\w]+)[ ]+([\w|\\\\]+::)?([\w]+)/', $doc, $matches);
					
				for ($i = 0; $i < $tmatches; $i++) {
					//check container
					if (empty($matches[2][$i])) {
						if (is_null($this->container)) {
							throw new \RuntimeException("No default container specified for class '{$this->className}'");
						}
							
						$container = $this->containerClass;
					}
					else {
						$container = substr($matches[2][$i], 0, -2);
					}
			
					$this->constructorParams[$matches[1][$i]] = array('container' => $container, 'service' => $matches[3][$i]);
				}
			}
		}
		else {
			$this->constuctor = null;
		}
	}	
}