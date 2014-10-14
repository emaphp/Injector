<?php
namespace Acme\Components;

use Acme\Services\MailService;
use Acme\Services\HTTPService;

/**
 * @Provider Acme\Providers\MailServiceProvider
 * @Provider Acme\Providers\HTTPServiceProvider
 */
class TestComponentH {
	public $name;
	public $mail;
	public $http;
	public $id;
	
	/**
	 * @Inject($service) mail
	 * @Inject($http) http
	 */
	public function __construct($name, MailService $service, HTTPService $http, $id = 1) {
		$this->name = $name;
		$this->mail = $service;
		$this->http = $http;
		$this->id = $id;
	}
}