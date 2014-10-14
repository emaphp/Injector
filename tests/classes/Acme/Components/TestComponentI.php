<?php
namespace Acme\Components;

/**
 * @Provider Acme\Providers\AllServiceProvider 
 */
class TestComponentI {
	/**
	 * @Inject mail
	 */
	private $mail;
	
	/**
	 * @Inject http
	 */
	protected $http;
	
	public function getMail() {
		return $this->mail;
	}
	
	public function getHttp() {
		return $this->http;
	}
}