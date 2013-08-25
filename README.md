Injector
========

A dependency injection class based on Pimple

**Author**: Emmanuel Antico<br/>
**Last Modification**: 2013/08/24<br/>
**Version**: 2.0.0

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
		"injector/injector": "2.0.*"
	}
}
```

<br/>
Introduction
------------

<br/>
Injector is a dependency injection library based on Pimple. Its main feature is resolving all dependencies in a class by looking through its documentation comments. The next example illustrates how to define an object dependency through the *@inject* tag. This class depends on a service called *mail_service* which we'll declare using a **Container**.

**Example class**

```php
<?php
namespace Acme;

class MyClass {
    /**
     * We inject a mail service here
     * @inject mail_service
     */
    protected $mail;
    
    /**
     * And a XML parser here
     * @inject xml_parser
     */
    public $parser;

    public function getMail() {
        return $this->mail();
    }
}
```
<br/>
Containers are classes that extend directly from *Pimple* and also implement the *Iterable* interface. The next example shows a container class which declares a service named *mail_service*.

**Example container**
```php
<?php
namespace Acme;

use Injector\Container;

class MyContainer extends Container {
    public function __construct() {
        //declaring the 'mail_service' element
        $this['mail_service'] = new MailService();
    }
}
```

<br/>
There are 2 possible ways of injecting properties. We can either use the *inject* method in the Container class or simply creating a new instance behind the scenes through the *create* method.

**Inject dependencies**
```php
<?php
use Acme\MyClass;
use Acme\MyContainer;

$o = new MyClass();
$c = new MyContainer();

//inject dependencies
$c->inject($o);

//do something useful
$o->getMail()->send('Hello', 'j.doe@nsa.gov');

```

<br/>
**Create instance from container**

```php
<?php
use Acme\MyContainer;

$c = new MyContainer();

//container creates a new instance of MyClass with all dependencies resolved
$o = $c->create('Acme\MyClass');

//do something useful
$o->getMail()->send('Hello', 'j.doe@nsa.gov');

```

<br/>
With *inject* we can also tell which services must be injected by adding the property names as additional parameters.

**Additional parameters on injection**
```php
<?php
use Acme\MyClass;
use Acme\MailService;
use Acme\XMLParser;
use Injector\Container;

$o = new MyClass();

//create a custom container
$c = new Container();
$c['mail_service'] = function ($c) {
    return new MailService();
};
$c['xml_parser'] = function ($c) {
    return new XMLParser();
};
$c['curl_service'] = function ($c) {
    $curl = curl_init();
    return $curl;
};

//inject dependencies
$c->inject($o, 'mail', 'parser');

//do something useful
$o->getMail()->send('Hello', 'j.doe@nsa.gov');
$o->parser->parseXML('example.xml');
```

<br/>
Any additional parameters used when creating an instance from a container will be sent directly to the class constructor.

**Example class**
```php
<?php
namespace Acme;

class AnotherClass extends MyClass {
    private $id;
    private $status;
    
    public function __construct($id, $status = 1) {
        $this->id = $id;
        $this->status = $status;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getStatus() {
        return $this->status;
    }
}
```
<br/>
**Send parameters to class constructor**
```php
use Acme\MyContainer;

$c = new MyContainer();
$o = $c->create('Acme\AnotherClass', 'another_class');

//will print 'another_class'
echo $o->getId();

//will print 1
echo $o->getStatus();

//'mail' was resolved as it was declared in MyClass
$o->getMail()->send('Sup', 'j.doe@nsa.gov');
```

<br/>
The *create* method also checks if dependencies must be resolved before invoking a class constructor. Syntax is slightly different as we need to tell which variable must hold the resolved dependency.

**Example class**
```php
<?php
namespace Acme;

class ThirdClass {
    private $encrypt;
    private $message;
    
    /**
     * @inject $encrypt aes256
     */
    public function __construct($encrypt, $message) {
        $this->encrypt = $encrypt;
        $this->message = $message;
    }
    
    public function getMessage() {
        return $this->encrypt->crypt($this->message);
    }
}
```
<br/>
**Injecting constructor dependencies**
```php
<?php
use Acme\AES128;
use Acme\AES256;
use Injector\Container;

//create custom container
$c = new Container();
$c['salt'] = 'qwerty123';
$c['aes128'] = function ($c) {
    return new AES128($c['salt']);
};
$c['aes256'] = function ($c) {
    return new AES256($c['salt']);
};

//create instance
$o = $c->create('Acme\ThirdClass', 'Hello World');
//do something useful
echo $o->getMessage();
```

<br/>
The Injector class
------------------

<br/>
The Injector class provides some additional features that couldn't be covered by Containers, that is, being able to resolve dependencies from various containers.

<br/>
**Example class**

<br/>
This class obtains its dependencies from 2 different sources: *Acme\MyContainer* and *Acme\AnotherContainer*. Notice that service names must be prefixed using the container class full name.
```php
<?php
namespace Acme;

class ComplexClass {
    /**
     * @inject Acme\MyContainer::mail_service
     */
    private $mail;
    
    private $crypt;
    
    private $message;
    
    /**
     * @inject $crypt Acme\AnotherContainer::aes128
     */
    public function __construct($crypt, $message) {
        $this->crypt = $crypt;
        $this->message = $message;
    }
    
    public function getMessage() {
        return $this->crypt->crypt($this->message);
    }
    
    public function sendMail() {
        $this->mail->send($this->getMessage(), 'jdoe@nsa.gov');
    }
}
```

<br/>
**Using the Injector class**
```php
<?php
//create instance
$o = Injector::create('Acme\ComplexClass', 'My message');
//do something useful
$o->sendMail();
```

<br/>
**Setting a defaut container**

<br/>
The Injector class works as a container of containers which is useful to determine dependencies from various sources. Still, defining a default container might result more productive. This is done by adding the container class full name in the class doc comments after a *@container* tag.

```php
<?php
namespace Acme;

/**
 * @container Acme\MyContainer
 */
class ComplexClass {
    /**
     * We can now avoid telling the container class here
     * @inject mail_service
     */
    private $mail;
    
    private $crypt;
    
    private $message;
    
    /**
     * @inject $crypt Acme\AnotherContainer::aes128
     */
    public function __construct($crypt, $message) {
        $this->crypt = $crypt;
        $this->message = $message;
    }
    
    public function getMessage() {
        return $this->crypt->crypt($this->message);
    }
    
    public function sendMail() {
        $this->mail->send($this->getMessage(), 'jdoe@nsa.gov');
    }
}
```

<br/>
**Overriding a default container**

We can override a default container by calling the *createFrom* method in the Injector class. This method expects a Container object or class name as first parameter. Dependencies that were previously resolved from the default container specified in the class now are obtained from this new container.

```php
<?php
use Injector\Container;
use Acme\SendMailService;

//create custom container
$c = new Container();
$c['mail_service'] = function ($c) {
    return new SendMailService();
}

//create instance
$o = Injector::createFrom($c, 'Acme\ComplexClass', 'A message');
//do something useful
$o->sendMail();
```

<br/>
License
-------
<br/>
This code is licensed under the BSD 2-Clause license.