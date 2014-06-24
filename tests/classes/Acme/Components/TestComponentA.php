<?php
namespace Acme\Components;

use Acme\Services\MailService;

class TestComponentA {
	private $mail;
	
	/**
	 * @inject.param $service mail
	 */
	public function __construct(MailService $service) {
		$this->mail = $service;
	}
}