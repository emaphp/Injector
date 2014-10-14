Injector
========

A dependency injection class based on Pimple 3

**Author**: Emmanuel Antico<br/>
**Last Modification**: 2014/10/14<br/>
**Version**: 4.0

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
		"injector/injector": "4.0.*"
	}
}
```

<br/>
Dependencies
------------
<br/>
Injector requires the following packages to be installed:

* [Pimple 3](https://github.com/fabpot/Pimple "")
* [Omocha 1.1](https://github.com/emaphp/omocha "")

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

In order to indicate a class provider we add the @Provider annotation followed by the provider class name. Dependencies can now be injected through the @Inject annotation. Notice that the syntax used for injecting contructor arguments differs a bit from the others.

```php
<?php
namespace Acme\Components;

/**
 * @Provider Acme\Providers\MainProvider
 */
class MyComponent {
    private $name;
    private $env;    
    
    /**
     * @Inject logger
     */
    private $logger;
    
    /**
     * @Inject conn
     */
    private $connection;
    
    /**
     * @Inject($env) environment
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
>Subclasses

Subclasses can extend the same set of providers from its parent class by adding the *@ExtendInject* annotation.

```php
<?php
//A.php

/*
 * Set class providers:
 * @Provider MainProvider
 * @Provider CustomProvider
 */
class A {
}

//B.php

/**
 * Obtain providers from parent class:
 * @ExtendInject
 */
class B extends A {
}

```

<br/>
License
-------
<br/>
This code is licensed under the BSD 2-Clause license.