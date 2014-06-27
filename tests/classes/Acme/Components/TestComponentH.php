<?php
namespace Acme\Components;

use Acme\Services\MailService;
use Acme\Services\HTTPService;

/**
 * @inject.provider Acme\Providers\MailServiceProvider
 * @inject.provider Acme\Providers\HTTPServiceProvider
 */
class TestComponentH {
	public $name;
	public $mail;
	public $http;
	public $id;
	
	/**
	 * @inject.param $service mail
	 * @inject.param $http http
	 */
	public function __construct($name, MailService $service, HTTPService $http, $id = 1) {
		$this->name = $name;
		$this->mail = $service;
		$this->http = $http;
		$this->id = $id;
	}
}