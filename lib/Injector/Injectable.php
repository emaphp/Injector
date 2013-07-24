<?php
namespace Injector;

trait Injectable {
	public function __setup() {
		//check container class declaration
		if (!is_string($this->container) || empty($this->container)) {
			throw new \RuntimeException("No container class has been defined for this class");
		}
		
		//generate container object
		$container = new $this->container;
		$container->configure();
		
		//if no arguments are passed inject all declared dependencies
		if (func_num_args() == 0) {
			$container->inject($this);
		}
		else {
			$container->inject($this, func_get_args());
		}
	}
}