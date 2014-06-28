<?php
namespace Acme\Providers;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Acme\Services\MySQLConnection;
use Acme\Services\Logger;

class ExampleProvider implements ServiceProviderInterface {
	public function register(Container $pimple) {
		$pimple['conn'] = function ($c) {
			return new MySQLConnection('usr', 'psw');
		};
		
		$pimple['logger'] = function ($c) {
			return new Logger();
		};
		
		$pimple['environment'] = 'development';
	}
}
?>