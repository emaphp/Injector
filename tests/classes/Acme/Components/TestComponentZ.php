<?php
namespace Acme\Components;


class TestComponentZ extends TestComponentA {
	/**
	 * 
	 * @var unknown
	 * @inject http
	 */
	private $http;
	
	public function getHTTP() {
		return $this->http;
	}
}