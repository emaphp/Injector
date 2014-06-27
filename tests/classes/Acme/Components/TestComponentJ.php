<?php
namespace Acme\Components;

use Acme\Services\HTTPService;

/**
 * @inject.provider Acme\Providers\AllServiceProvider
 */
class TestComponentJ {
	public $name;
	
	public $id;
	
	/**
	 * @inject.service mail
	 */
	private $mail;
	
	protected $http;
	
	/**
	 * @inject.param $http http
	 */
	public function __construct($name, HTTPService $http, $id = 1) {
		$this->name = $name;
		$this->http = $http;
		$this->id = $id;
	}
	
	public function getMail() {
		return $this->mail;
	}
	
	public function getHttp() {
		return $this->http;
	}
}