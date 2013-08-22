<?php
namespace Acme\Components;

use Acme\Services\MailService;

/**
 * @container Acme\Containers\TestContainer
 */
class TestComponentB {
	/**
	 * 
	 * @param MailService $service
	 * @inject $service mail
	 */
	public function __construct(MailService $service) {
		$this->mail = $service;
	}
}