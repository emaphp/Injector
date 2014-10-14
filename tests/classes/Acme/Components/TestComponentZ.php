<?php
namespace Acme\Components;

class TestComponentZ extends TestComponentA {
	/**
	 * @Inject http
	 */
	private $http;
	
	public function getHTTP() {
		return $this->http;
	}
}