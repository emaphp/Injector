<?php
namespace Acme\Components;

use Acme\Services\MailService;
use Acme\Services\HTTPService;

/**
 * @container Acme\Containers\TestContainer
 */
class TestComponentC {
	/**
	 * 
	 * @param MailService $service
	 * @inject $service mail
	 * @inject $http Acme\Containers\AnotherContainer::http
	 */
	public function __construct(MailService $service, HTTPService $http, $name) {
		$this->mail = $service;
		$this->http = $http;
		$this->name = $name;
	}
}