<?php
namespace Acme\Components;

use Acme\Services\MailService;

class TestComponentD {
	/**
	 * 
	 * @param MailService $service
	 * @inject $service Acme\Containers\TestContainer::mail
	 */
	public function __construct(MailService $service) {
		$this->mail = $service;
	}
}