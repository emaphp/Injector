<?php

use Injector\Container;
use Acme\Services\MailService;
use Acme\Services\HTTPService;
use Acme\Containers\TestContainer;
use Acme\Components\TestComponentA;
use Acme\Components\TestComponentE;
use Acme\Components\TestComponentB;
use Acme\Components\TestComponentD;
use Acme\Components\TestComponentF;
use Acme\Components\TestComponentG;
use Acme\Containers\BigContainer;
use Acme\Components\TestComponentI;
use Acme\Components\TestComponentH;
use Acme\Components\TestComponentJ;
use Acme\Containers\AnotherContainer;
use Acme\Containers\ShareContainer;
use Acme\Services\SharedService;
use Acme\Components\TestComponentK;

/**
 * 
 * @author emaphp
 * @group container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {
	public function testIterator() {
		$container = new Container();
		$container['mail'] = function ($c) {
			return new MailService();
		};
		
		$container['http'] = function ($c) {
			return new HTTPService();
		};
		
		foreach ($container as $name => $service) {
			$this->assertTrue(in_array($name, array('mail', 'http')));
		}
	}
	
	public function testCreationA() {
		$container = new TestContainer();
		$a = $container->create('Acme\Components\TestComponentA');
		$this->assertTrue($a instanceof TestComponentA);
		$this->assertTrue($a->mail instanceof MailService);
	}
	
	public function testCreationB() {
		$container = new TestContainer();
		$b = $container->create('Acme\Components\TestComponentB');
		$this->assertTrue($b instanceof TestComponentB);
		$this->assertTrue($b->mail instanceof MailService);
	}
	
	/**
	 * @expectedException \RuntimeException
	 */
	public function testCreationC() {
		$container = new TestContainer();
		$c = $container->create('Acme\Components\TestComponentC');
	}
	
	public function testCreationD() {
		$container = new TestContainer();
		$d = $container->create('Acme\Components\TestComponentD');
		$this->assertTrue($d instanceof TestComponentD);
		$this->assertTrue($d->mail instanceof MailService);
	}
	
	public function testCreationE() {
		$container = new TestContainer();
		$e = $container->create('Acme\Components\TestComponentE');
		$this->assertTrue($e instanceof TestComponentE);
		$this->assertTrue($e->mail instanceof MailService);
	}
	
	public function testCreationF() {
		$container = new TestContainer();
		$f = $container->create('Acme\Components\TestComponentF');
		$this->assertTrue($f instanceof TestComponentF);
		$this->assertTrue($f->mail instanceof MailService);
	}
	
	public function testCreationG() {
		$container = new TestContainer();
		$g = $container->create('Acme\Components\TestComponentG');
		$this->assertTrue($g instanceof TestComponentG);
		$this->assertTrue($g->mail instanceof MailService);
	}
	
	public function testCreationI() {
		$container = new BigContainer();
		$i = $container->create('Acme\Components\TestComponentI');
		$this->assertTrue($i instanceof TestComponentI);
		$this->assertTrue($i->getMail() instanceof MailService);
		$this->assertTrue($i->getHttp() instanceof HTTPService);
	}
	
	public function testCreationJ() {
		$container = new BigContainer();
		$j = $container->create('Acme\Components\TestComponentJ', 'j_object');
		$this->assertTrue($j instanceof TestComponentJ);
		$this->assertEquals('j_object', $j->name);
		$this->assertEquals(1, $j->id);
		$this->assertTrue($j->getMail() instanceof MailService);
		$this->assertTrue($j->getHttp() instanceof HTTPService);
	}
	
	public function testCreationJ2() {
		$container = new BigContainer();
		$j = $container->create('Acme\Components\TestComponentJ', 'j_object', 5);
		$this->assertTrue($j instanceof TestComponentJ);
		$this->assertEquals('j_object', $j->name);
		$this->assertEquals(5, $j->id);
		$this->assertTrue($j->getMail() instanceof MailService);
		$this->assertTrue($j->getHttp() instanceof HTTPService);
	}
	
	/**
	 * @expectedException \RuntimeException
	 */
	public function testCreationJ3() {
		$container = new BigContainer();
		$j = $container->create('Acme\Components\TestComponentJ');
	}
	
	public function testStdClass() {
		$container = new BigContainer();
		$s = $container->create('stdClass');
		$this->assertTrue($s instanceof \stdClass);
		$this->assertObjectHasAttribute('mail', $s);
		$this->assertTrue($s->mail instanceof MailService);
		$this->assertObjectHasAttribute('http', $s);
		$this->assertTrue($s->http instanceof HTTPService);
	}
	
	public function testInjectionE() {
		$container = new TestContainer();
		$e = new TestComponentE();
		$container->inject($e);
		$this->assertTrue($e instanceof TestComponentE);
		$this->assertTrue($e->mail instanceof MailService);
	}
	
	public function testInjectionI() {
		$c1 = new TestContainer();
		$i = new TestComponentI();
		$c1->inject($i);
		$this->assertTrue($i->getMail() instanceof MailService);
		$this->assertNull($i->getHttp());
	}
	
	public function testInjectionI2() {
		$c1 = new BigContainer();
		$i = new TestComponentI();
		$c1->inject($i, 'http');
		$this->assertNull($i->getMail());
		$this->assertTrue($i->getHttp() instanceof HTTPService);
	}
	
	public function testCombinedInjection() {
		$c1 = new TestContainer();
		$c2 = new AnotherContainer();
		$i = new TestComponentI();
		$c1->inject($i);
		$c2->inject($i);
		$this->assertTrue($i->getMail() instanceof MailService);
		$this->assertTrue($i->getHttp() instanceof HTTPService);
	}
	
	public function testInjectionStdClass() {
		$container = new BigContainer();
		$s = new \stdClass();
		$container->inject($s);
		$this->assertTrue($s instanceof \stdClass);
		$this->assertObjectHasAttribute('mail', $s);
		$this->assertTrue($s->mail instanceof MailService);
		$this->assertObjectHasAttribute('http', $s);
		$this->assertTrue($s->http instanceof HTTPService);
	}
	
	public function testInjectionStdClass2() {
		$container = new BigContainer();
		$s = new \stdClass();
		$container->inject($s, 'mail');
		$this->assertTrue($s instanceof \stdClass);
		$this->assertObjectHasAttribute('mail', $s);
		$this->assertTrue($s->mail instanceof MailService);
		$this->assertObjectNotHasAttribute('http', $s);
	}
	
	public function testSharedService() {
		$container = new ShareContainer();
		$a = $container->create('Acme\Components\TestComponentK');
		$b = $container->create('Acme\Components\TestComponentK');
		$this->assertTrue($a->shared instanceof SharedService);
		$this->assertTrue($b->shared instanceof SharedService);
		$a->shared->id = 'second!';
		$this->assertEquals('second!', $b->shared->id);
	}
	
	public function testSharedService2() {
		$container = new ShareContainer();
		$a = $container->create('Acme\Components\TestComponentK');
		$b = new TestComponentK();
		$container->inject($b, 'shared');
		$this->assertTrue($a->shared instanceof SharedService);
		$this->assertTrue($b->shared instanceof SharedService);
		$a->shared->id = 'second!';
		$this->assertEquals('second!', $b->shared->id);
	}
}