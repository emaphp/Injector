<?php
namespace Acme\Components;

use Acme\Services\MailService;
use Acme\Services\HTTPService;

/**
 * @container Acme\Containers\TestContainer
 */
class TestComponentH {
	/**
	 * 
	 * @param MailService $service
	 * @inject $service mail
	 * @inject $http Acme\Containers\AnotherContainer::http
	 */
	public function __construct(MailService $service, HTTPService $http, $name, $id = 1) {
		$this->mail = $service;
		$this->http = $http;
		$this->name = $name;
		$this->id = $id;
	}
}