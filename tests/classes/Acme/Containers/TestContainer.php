<?php
namespace Acme\Containers;

use Injector\Container;
use Acme\Services\MailService;

class TestContainer extends Container {
	public function __construct() {
		$this['mail'] = function ($c) {
			return new MailService();
		};
	}
}