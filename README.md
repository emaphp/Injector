Injector
========

A dependency injection class based on Pimple

**Author**: Emmanuel Antico<br/>
**Last Modification**: 2013/07/02<br/>
**Version**: 1.0.0

<br/>
Installation
--------------
<br/>
Installation is made via composer. Add the following lines to the composer.json file in your project.

<br/>
**Composer**

```json
{
    "require": {
		"injector/injector": "1.0.*"
	}
}
```

<br/>
Features
--------------
<br/>
The *Injector* class extends *Pimple* ([http://pimple.sensiolabs.org/](http://pimple.sensiolabs.org/ "")) and implements the *Iterator* interface and the injection methods *inject* and *injectAll*.

<br/>
By adding support to the Iterator interface containers can be traversable using a **foreach**.

```php
<?php
//call composer autoloader
$loader = require 'vendor/autoload.php';

//create
$container = new \Injector();

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
--------------
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
This last line can be replaced by calling the *injectAll* method.
```php
<?php
$foo = new \stdClass();
//inject all services
$container->injectAll($foo);
```
<br/>
License
--------------
<br/>
This code is licensed under the BSD 2-Clause license.