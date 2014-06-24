<?php
namespace Acme\Components;

use Acme\Services\MailService;
use Acme\Services\HTTPService;

/**
 * @inject.container Acme\Containers\TestContainer
 */
class TestComponentH {
	/**
	 * @inject.service $service mail
	 * @inject.service $http http
	 */
	public function __construct($name, MailService $service, HTTPService $http, $id = 1) {
		$this->name = $name;
		$this->mail = $service;
		$this->http = $http;
		$this->id = $id;
	}
}