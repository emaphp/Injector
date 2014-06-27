<?php
namespace Acme\Providers;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Acme\Services\HTTPService;

class HTTPServiceProvider implements ServiceProviderInterface {
	public function register(Container $pimple) {
		$pimple['http'] = function ($c) {
			return new HTTPService();
		};
	}
}
?>