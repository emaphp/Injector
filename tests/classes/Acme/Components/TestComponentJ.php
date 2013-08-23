<?php
namespace Acme\Components;

use Acme\Services\HTTPService;
/**
 * @container Acme\Containers\BigContainer
 * @author emaphp
 */
class TestComponentJ {
	public $name;
	
	public $id;
	
	/**
	 * @inject mail
	 */
	private $mail;
	
	protected $http;
	
	/**
	 * 
	 * @param HTTPService $http
	 * @param unknown $name
	 * @param number $id
	 * @inject $http http
	 */
	public function __construct(HTTPService $http, $name, $id = 1) {
		$this->http = $http;
		$this->name = $name;
		$this->id = $id;
	}
	
	public function getMail() {
		return $this->mail;
	}
	
	public function getHttp() {
		return $this->http;
	}
}