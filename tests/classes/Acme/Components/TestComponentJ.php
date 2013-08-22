<?php
namespace Acme\Components;

use Acme\Services\MailService;

/**
 * 
 * @author emaphp
 * @container Acme\Containers\TestContainer
 */
class TestComponentJ {
	/**
	 * @inject setHTTP(Acme\Containers\AnotherContainer::http)
	 * @var unknown
	 */
	protected $http;
	
	public function setHTTP($http) {
		$this->http = $http;
	}
	
	public function getHTTP() {
		return $this->http;
	}
}