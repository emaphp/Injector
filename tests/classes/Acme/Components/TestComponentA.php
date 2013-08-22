<?php
namespace Acme\Components;

use Acme\Services\MailService;

class TestComponentA {
	/**
	 * 
	 * @param MailService $service
	 * @inject $service mail
	 */
	public function __construct(MailService $service) {
		$this->mail = $service;
	}
}