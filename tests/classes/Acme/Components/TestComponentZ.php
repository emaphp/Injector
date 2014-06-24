<?php
namespace Acme\Components;

class TestComponentZ extends TestComponentA {
	/**
	 * @inject.service http
	 */
	private $http;
	
	public function getHTTP() {
		return $this->http;
	}
}