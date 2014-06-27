<?php
namespace Acme\Components;

/**
 * @inject.provider Acme\Providers\AllServiceProvider 
 */
class TestComponentI {
	/**
	 * @inject.service mail
	 */
	private $mail;
	
	/**
	 * @inject.service http
	 */
	protected $http;
	
	public function getMail() {
		return $this->mail;
	}
	
	public function getHttp() {
		return $this->http;
	}
}