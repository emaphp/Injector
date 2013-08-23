<?php
namespace Acme\Containers;

use Injector\Container;
use Acme\Services\MailService;
use Acme\Services\HTTPService;

class BigContainer extends Container {
	public function __construct() {
		$this['mail'] = function ($c) {
			return new MailService();
		};
		
		$this['http'] = function ($c) {
			return new HTTPService();
		};
	}
}