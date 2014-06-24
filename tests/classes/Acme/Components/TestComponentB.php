<?php
namespace Acme\Components;

use Acme\Services\MailService;

/**
 * @inject.container Acme\Containers\TestContainer
 */
class TestComponentB {
	/**
	 * @inject.param $service mail
	 */
	public function __construct(MailService $service) {
		$this->mail = $service;
	}
}