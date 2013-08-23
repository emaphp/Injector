<?php
namespace Acme\Containers;

use Injector\Container;
use Acme\Services\SharedService;

class ShareContainer extends Container {
	public function __construct() {
		$this['shared'] = $this->share(function ($c) {
			return new SharedService('first!');
		});
	}
} 