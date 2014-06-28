<?php
namespace Acme\Components;

/**
 * @inject.provider Acme\Providers\ExampleProvider
 */
class ExampleComponent {
	private $name;
	private $env;
	
	/**
	 * @inject.service logger
	 */
	private $logger;
	
	/**
	 * @inject.service conn
	 */
	private $connection;
	
	/**
	 * @inject.param $env environment
	 */
	public function __construct($name, $env) {
		$this->name = $name;
		$this->env = $env;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getEnvironment() {
		return $this->env;
	}
	
	public function getLogger() {
		return $this->logger;
	}
	
	public function getConnection() {
		return $this->connection;
	}
}
?>