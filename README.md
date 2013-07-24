Injector
========

A dependency injection class based on Pimple

**Author**: Emmanuel Antico<br/>
**Last Modification**: 2013/07/24<br/>
**Version**: 1.1.0

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
		"injector/injector": "1.1.*"
	}
}
```

<br/>
Features
--------
<br/>
The *Injector* class extends *Pimple* ([http://pimple.sensiolabs.org/](http://pimple.sensiolabs.org/ "")) and implements the *Iterator* interface and the injection methods *inject* and *injectDependencies*.
<br/>
By adding support to the Iterator interface containers can be traversable using a **foreach**.

```php
<?php
use Injector\Injector;

//call composer autoloader
$loader = require 'vendor/autoload.php';

//create
$container = new Injector();

//log service
$container['logger'] = function ($c) {
    $logger = new Monolog\Logger('my_logger');
    $logger->pushHandler(new Monolog\Handler\StreamHandler('logs/' . date('Y-m-d') . '.log', Monolog\Logger::DEBUG));
};

//template service
$container['twig'] = function ($c) {
    $twig = Twig_Environment(new Twig_Loader_Filesystem('views/'));
    return $twig;
};

//using a foreach in our container
foreach ($container as $name => $service) {
    //...
}
```

<br/>
Dependency injection
--------------------
<br/>
The *inject* method receives an object by reference and adds the specified services declared in the container. This is done by setting the properties directly, no setter methods are called during the process.
```php
<?php
$foo = new \stdClass();
//inject the logger service
$container->inject($foo, 'logger');
```
We can define an arbitrary number of services. This example injects both services inside an object.
```php
<?php
$foo = new \stdClass();
//inject both the logger and template services
$container->inject($foo, 'logger', 'twig');
```
If no services are specified then all of them are injected.
```php
<?php
$foo = new \stdClass();
//inject all services
$container->inject($foo);
```
We can also specify the services as an array through the *injectDependencies* method.
```php
<?php
$foo = new \stdClass();
//inject all services
$container->injectDependencies($foo, array('logger', 'twig'));
```

<br/>
Containers
----------
<br/>
This library also comes with a default *Container* class. All containers must override the *configure* method, which must initialize all services within the container. This example shows a custom container which extends this class:

```php
<?php
namespace Acme;

use Injector\Container;

class MyContainer extends Container {
    public function configure() {
        $this['mailer'] = function ($c) {
            $transport = Swift_SmtpTransport::newInstance('smtp.domain.org', 25)
            ->setUsername('usr')
            ->setPassword('pswd');
            
            $mailer = Swift_Mailer::newInstance($transport);
            return $mailer;
        };
    }
}
```
<br/>
Injectable objects
------------------
<br/>
Classes that use the *Injector\Injectable* trait can specify a container class name that will be used to obtain all its dependencies. These classes must declare a string property named *container*.
```php
<?php
namespace Acme;

use Injector\Injectable;

class MyClass {
    use Injectable;
    public $container = 'Acme\\MyContainer';
    
    /*...*/
}
```
Solving its dependencies requires calling the *__setup* method. When called, an instance of the specified container is created and then used to load the specified services. Again, if no services are specified then all of them are injected.
```php
<?php
$obj = new Acme\MyClass();
$obj->__setup('mailer'):
```
<br/>
License
-------
<br/>
This code is licensed under the BSD 2-Clause license.