<?php
namespace Acme\Providers;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Acme\Services\MailService;

class MailServiceProvider implements ServiceProviderInterface {
	public function register(Container $pimple) {
		$pimple['mail'] = function ($c) {
			return new MailService();
		};
	}
}
?>