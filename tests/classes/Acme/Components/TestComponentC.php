<?php
namespace Acme\Components;

use Acme\Services\MailService;
use Acme\Services\HTTPService;

/**
 * @inject.provider Acme\Providers\MailServiceProvider
 */
class TestComponentC {
	public $name;
	public $mail;
	public $http;
	
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