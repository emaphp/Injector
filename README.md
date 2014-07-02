Injector
========

A dependency injection class based on Pimple 2

**Author**: Emmanuel Antico<br/>
**Last Modification**: 2014/07/02<br/>
**Version**: 3.1.0

<br/>
Installation
------------
<br/>
Installation is made via composer. Add the following lines to the composer.json file in your project.

<br/>
**Composer**

```json
{
    "require": {
		"injector/injector": "~3.0"
	}
}
```

<br/>
Dependecies
------------
<br/>
Injector requires the following packages to be installed:

* [Pimple 2.1](https://github.com/fabpot/Pimple "")
* [Minime\Annotations 1.13](https://github.com/marcioAlmada/annotations "")

<br/>
How to use
------------

<br/>
Injector is a dependency injection library that uses the *Pimple\Container* class to initialize a set of properties within an instance. This is done by adding the appropiate annotations to the class declaration.

<br/>
>Step 1: Create a Provider

A provider is a class that implements the *Pimple\ServiceProviderInterface* interface. This class sets a number of services (and parameters) inside a container.

```php
<?php
namespace Acme\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Acme\Services\Logger;
use Acme\Services\MySQLConnection;

class MainProvider implements ServiceProviderInterface {
    public function register(Container $container) {
        //add some services
        $container['logger'] = function ($c) {
            return new Logger();
        };
        
        $container['conn'] = function ($c) {
            return new MySQLConnection('usr', 'psw');
        };
        
        $container['environment'] = 'development';
    }
}
```
<br/>
>Step 2: Configure your class

In order to indicate a class provider we add the @inject.provider annotation followed by the provider class name. Dependencies can now be injected through the @inject.service and @inject.param annotations.

```php
<?php
namespace Acme\Components;

/**
 * @inject.provider Acme\Providers\MainProvider
 */
class MyComponent {
    private $name;
    private $env;    
    
    /**
     * @inject.service logger
     */
    private $logger;
    
    /**
     * @inject.service conn
     */
    private $connection;
    
    /**
     * @inject.param $env environment
     */
    public function __construct($name, $env) {
        $this->name = $name;
        $this->env = $env;
    }
    
    public function getEnvironment() {
        return $this->env;
    }

    public function getName() {
        return $this->name;
    }
    
    public function getLogger() {
        return $this->logger;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}
```

<br/>
>Step 3: Create an instance

Instances are created through the *create* static method in the *Injector\Injector* class. Additional arguments could be added as an array.

```php
<?php
use Injector\Injector;
use Acme\Services\SQLiteConnection;

$component = Injector::create('Acme\Components\MyComponent', ['My Component']);
$component->getEnvironment(); //returns 'development'
$component->getName(); //returns 'My Component'
$component->getLogger()->debug('Component initialized');

//overriding a constructor parameter
$component = Injector::create('Acme\Components\MyComponent', ['My Component', 'production']);
$component->getEnvironment(); //returns 'production'

//filtering dependencies
$component = Injector::create('Acme\Components\MyComponent', ['My Component'], ['logger']);
$component->getLogger(); //Logger
$component->getConnection(); // NULL

//overriding dependencies
$component = Injector::create('Acme\Components\MyComponent', ['My Component'], null, ['conn' => new SQLiteConnection('file.db')]);
$component->getConnection(); // SQLiteConnection
```

<br/>
>Step 3 (alt): Inject dependencies

You could also inject dependencies directly through the *inject* method using a custom made container.

```php
<?php
use Injector\Injector;

//create container
$container = new Pimple\Container;
$provider = new Acme\Providers\MainProvider();
$provider->register($container);

//inject dependencies
$component = new CustomComponent();
Injector::inject($component, $container);
//...
$component->getLogger()->debug('Component initialized');
```

<br/>
License
-------
<br/>
This code is licensed under the BSD 2-Clause license.