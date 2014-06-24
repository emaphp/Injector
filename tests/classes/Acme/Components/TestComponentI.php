<?php
namespace Acme\Components;

/**
 * @inject.container Acme\Containers\BigContainer
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