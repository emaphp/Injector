<?php
namespace Acme\Containers;

use Injector\Container;
use Acme\Services\HTTPService;

class AnotherContainer extends Container {
	public function __construct() {
		$this['http'] = function ($c) {
			return new HTTPService();
		};
	}
}