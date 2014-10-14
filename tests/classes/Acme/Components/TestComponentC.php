<?php
namespace Acme\Components;

use Acme\Services\MailService;
use Acme\Services\HTTPService;

/**
 * @Provider Acme\Providers\MailServiceProvider
 */
class TestComponentC {
	public $name;
	public $mail;
	public $http;
	
	/**
	 * @Inject($service) mail
	 * @Inject($http) http
	 */
	public function __construct($name, MailService $service, HTTPService $http) {
		$this->name = $name;
		$this->mail = $service;
		$this->http = $http;
	}
}