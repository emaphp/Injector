<?php
namespace Acme\Components;

/**
 * @Provider Acme\Providers\ExampleProvider
 */
class ExampleComponent {
	private $name;
	private $env;
	
	/**
	 * @Inject logger
	 */
	private $logger;
	
	/**
	 * @Inject conn
	 */
	private $connection;
	
	/**
	 * @Inject($env) environment
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