<?php
namespace Acme\Components;

use Acme\Services\MailService;
use Acme\Services\HTTPService;

/**
 * @inject.container Acme\Containers\TestContainer
 */
class TestComponentC {
	/**
	 * @inject.param $service mail
	 * @inject.param $http http
	 */
	public function __construct($name, MailService $service, HTTPService $http) {
		$this->name = $name;
		$this->mail = $service;
		$this->http = $http;
	}
}