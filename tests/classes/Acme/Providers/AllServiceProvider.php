<?php
namespace Acme\Providers;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Acme\Services\MailService;
use Acme\Services\HTTPService;

class AllServiceProvider implements ServiceProviderInterface {
	public function register(Container $pimple) {
		$pimple['mail'] = function ($c) {
			return new MailService();
		};
		
		$pimple['http'] = function ($c) {
			return new HTTPService();
		};
	}
}
?>