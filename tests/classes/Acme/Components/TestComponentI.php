<?php
namespace Acme\Components;

/**
 * @container Acme\Containers\BigContainer
 * @author emaphp
 */
class TestComponentI {
	/**
	 * @inject mail
	 */
	private $mail;
	
	/**
	 * @inject http
	 */
	protected $http;
	
	public function getMail() {
		return $this->mail;
	}
	
	public function getHttp() {
		return $this->http;
	}
}