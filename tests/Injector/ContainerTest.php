<?php
use Pimple\Container;
use Acme\Services\MailService;
use Acme\Services\HTTPService;
use Acme\Containers\TestContainer;
use Acme\Components\TestComponentA;
use Acme\Components\TestComponentE;
use Acme\Components\TestComponentB;
use Acme\Components\TestComponentD;
use Acme\Components\TestComponentF;
use Acme\Containers\BigContainer;
use Acme\Components\TestComponentI;
use Acme\Components\TestComponentH;
use Acme\Components\TestComponentJ;
use Acme\Containers\AnotherContainer;
use Acme\Containers\ShareContainer;
use Acme\Services\SharedService;
use Acme\Components\TestComponentZ;
use Acme\Containers\SubTestContainer;
use Acme\Components\TestComponentC;
use Acme\Providers\MailServiceProvider;
use Injector\Injector;
use Acme\Providers\AllServiceProvider;
use Acme\Providers\HTTPServiceProvider;

/**
 * 
 * @author emaphp
 * @group container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {
	
	/*
	 * Creation tests
	 */
	
	public function testCreationA() {
		$container = new Container();
		$provider = new MailServiceProvider();
		$provider->register($container);
		
		$a = Injector::createWith('Acme\Components\TestComponentA', $container);
		$this->assertTrue($a instanceof TestComponentA);
		$this->assertTrue($a->mail instanceof MailService);
	}
	
	public function testCreationB() {
		$container = new Container();
		$provider = new MailServiceProvider();
		$provider->register($container);
		$b = Injector::createWith('Acme\Components\TestComponentB', $container);
		$this->assertTrue($b instanceof TestComponentB);
		$this->assertTrue($b->mail instanceof MailService);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 * Type hinting does not allow NULL argument
	 */
	public function testCreationC1() {
		$container = new Container();
		$provider = new MailServiceProvider();
		$provider->register($container);
		$c = Injector::createWith('Acme\Components\TestComponentC', $container, "testing");
	}
	
	public function testCreationC2() {
		$container = new Container();
		$provider = new AllServiceProvider();
		$provider->register($container);
		$c = Injector::createWith('Acme\Components\TestComponentC', $container, ["testing"]);
		$this->assertTrue($c instanceof TestComponentC);
		$this->assertEquals("testing", $c->name);
		$this->assertTrue($c->mail instanceof MailService);
		$this->assertTrue($c->http instanceof HTTPService);
	}
	
	public function testCreationC3() {
		$container = new Container();
		$provider = new MailServiceProvider();
		$provider->register($container);
		$provider = new HTTPServiceProvider();
		$provider->register($container);
		$c = Injector::createWith('Acme\Components\TestComponentC', $container, "testing");
		$this->assertTrue($c instanceof TestComponentC);
		$this->assertEquals("testing", $c->name);
		$this->assertTrue($c->mail instanceof MailService);
		$this->assertTrue($c->http instanceof HTTPService);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 * Type hinting does not allow NULL argument
	 */
	public function testCreationC4() {
		$c = Injector::create('Acme\Components\TestComponentC', "testing");
		$this->assertTrue($c instanceof TestComponentC);
		$this->assertEquals("testing", $c->name);
		$this->assertTrue($c->mail instanceof MailService);
		$this->assertTrue($c->http instanceof HTTPService);
	}
	

	public function testCreationD1() {
		$container = new Container();
		$provider = new MailServiceProvider();
		$provider->register($container);
		
		$d = Injector::createWith('Acme\Components\TestComponentD', $container, 'Test');
		$this->assertTrue($d instanceof TestComponentD);
		$this->assertEquals('Test', $d->name);
		$this->assertTrue($d->mail instanceof MailService);
		$this->assertNull($d->http);
	}
	
	public function testCreationD2() {
		$container = new Container();
		$provider = new AllServiceProvider();
		$provider->register($container);
		$d = Injector::createWith('Acme\Components\TestComponentD', $container, 'Test');
		$this->assertTrue($d instanceof TestComponentD);
		$this->assertEquals('Test', $d->name);
		$this->assertTrue($d->mail instanceof MailService);
		$this->assertTrue($d->http instanceof HTTPService);
	}
	
	/**
	 * @expectedException \RuntimeException
	 * Dependencies not satisfied
	 */
	public function testCreationF1() {
		$container = new Container();
		$f = Injector::createWith('Acme\Components\TestComponentF', $container);	
	}
	
	public function testCreationF2() {
		$f = Injector::create('Acme\Components\TestComponentF');
		$this->assertTrue($f instanceof TestComponentF);
		$this->assertTrue($f->mail instanceof MailService);
	}
	
	/**
	 * @expectedException \RuntimeException
	 * Dependencies not satisfied
	 */
	public function testCreationG() {
		$g = Injector::create('Acme\Components\TestComponentG');
	}
	
	public function testCreationH() {
		$h = Injector::create('Acme\Components\TestComponentH', 'H');
		$this->assertTrue($h instanceof TestComponentH);
		$this->assertEquals('H', $h->name);
		$this->assertEquals(1, $h->id);
		$this->assertTrue($h->mail instanceof MailService);
		$this->assertTrue($h->http instanceof HTTPService);
	}
	
	public function testCreationI() {
		$i = Injector::create('Acme\Components\TestComponentI');
		$this->assertTrue($i instanceof TestComponentI);
		$this->assertTrue($i->getMail() instanceof MailService);
		$this->assertTrue($i->getHttp() instanceof HTTPService);
	}
	
	public function testCreationJ1() {
		$j = Injector::create('Acme\Components\TestComponentJ', 'j_object');
		$this->assertTrue($j instanceof TestComponentJ);
		$this->assertEquals('j_object', $j->name);
		$this->assertEquals(1, $j->id);
		$this->assertTrue($j->getMail() instanceof MailService);
		$this->assertTrue($j->getHttp() instanceof HTTPService);
	}
		
	/**
	 * @expectedException \Exception
	 */
	public function testCreationJ2() {
		$j = Injector::create('Acme\Components\TestComponentJ');
	}
	
	public function testCreationZ() {
		$container = new Container();
		$provider = new AllServiceProvider();
		$provider->register($container);
		
		$z = Injector::createWith('Acme\Components\TestComponentZ', $container);
		$this->assertTrue($z instanceof TestComponentZ);
		$this->assertTrue($z->mail instanceof MailService);
		$this->assertTrue($z->getHTTP() instanceof HTTPService);
	}
	
	public function testStdClass() {
		$container = new Container();
		$provider = new AllServiceProvider();
		$provider->register($container);
		$s = Injector::createWith('stdClass', $container);
		$this->assertTrue($s instanceof \stdClass);
		$this->assertObjectHasAttribute('mail', $s);
		$this->assertTrue($s->mail instanceof MailService);
		$this->assertObjectHasAttribute('http', $s);
		$this->assertTrue($s->http instanceof HTTPService);
	}
	
	/*
	 * Injection tests
	 */
	
	public function testInjectionB() {
		$b = new TestComponentB();
		$container = new Container();
		$provider = new MailServiceProvider();
		$provider->register($container);
		Injector::inject($b, $container);
		$this->assertTrue($b instanceof TestComponentB);
		$this->assertTrue($b->mail instanceof MailService);
	}
	
	public function testInjectionE1() {
		$e = new TestComponentE();
		$container = new Container();
		$provider = new MailServiceProvider();
		$provider->register($container);
		Injector::inject($e, $container);
		$this->assertTrue($e instanceof TestComponentE);
		$this->assertTrue($e->mail instanceof MailService);
	}
	
	/**
	 * @expectedException \RuntimeException
	 */
	public function testInjectionE2() {
		$e = new TestComponentE();
		$container = new Container();
		$provider = new HTTPServiceProvider();
		$provider->register($container);
		Injector::inject($e, $container);
	}
	
	public function testInjectionI1() {
		$i = new TestComponentI();
		$container = new Container();
		$provider = new MailServiceProvider();
		$provider->register($container);
		Injector::inject($i, $container);
		$this->assertTrue($i->getMail() instanceof MailService);
		$this->assertNull($i->getHttp());
	}
	
	public function testInjectionI2() {
		$i = new TestComponentI();
		$container = new Container();
		$provider = new AllServiceProvider();
		$provider->register($container);
		Injector::inject($i, $container, ['http']);
		$this->assertNull($i->getMail());
		$this->assertTrue($i->getHttp() instanceof HTTPService);
	}
	
	public function testInjectionI3() {
		$i = new TestComponentI();
		$container = new Container();
		$provider = new AllServiceProvider();
		$provider->register($container);
		Injector::inject($i, $container, ['mail']);
		$this->assertTrue($i->getMail() instanceof MailService);
		$this->assertNull($i->getHttp());
	}
	
	public function testInjectionZ() {
		$container = new Container();
		$provider = new HTTPServiceProvider();
		$provider->register($container);
		
		$z = new TestComponentZ(new MailService());
		Injector::inject($z, $container);
		$this->assertTrue($z->getHTTP() instanceof HTTPService);
		$this->assertTrue($z->mail instanceof MailService);
	}
	
	public function testCombinedInjection() {
		$i = new TestComponentI();
		$http_container = new Container();
		$provider = new HTTPServiceProvider();
		$provider->register($http_container);
		
		$mail_container = new Container();
		$provider = new MailServiceProvider();
		$provider->register($mail_container);
		
		Injector::inject($i, $mail_container);
		Injector::inject($i, $http_container);
		$this->assertTrue($i->getMail() instanceof MailService);
		$this->assertTrue($i->getHttp() instanceof HTTPService);
	}
	
	public function testInjectionStdClass() {
		$container = new Container();
		$provider = new AllServiceProvider();
		$provider->register($container);
		$s = new stdClass();
		Injector::inject($s, $container);
		$this->assertObjectHasAttribute('mail', $s);
		$this->assertTrue($s->mail instanceof MailService);
		$this->assertObjectHasAttribute('http', $s);
		$this->assertTrue($s->http instanceof HTTPService);
	}
	
	public function testInjectionStdClass2() {
		$container = new Container();
		$provider = new AllServiceProvider();
		$provider->register($container);
		$s = new \stdClass();
		Injector::inject($s, $container, ['mail']);
		$this->assertObjectHasAttribute('mail', $s);
		$this->assertTrue($s->mail instanceof MailService);
		$this->assertObjectNotHasAttribute('http', $s);
	}
	
	public function testInjectionStdClass3() {
		$container = new Container();
		$provider = new AllServiceProvider();
		$provider->register($container);
		$s = new \stdClass();
		Injector::inject($s, $container, null, ['http' => new TestComponentB(), 'extra' => 100]);
		

		$this->assertObjectHasAttribute('mail', $s);
		$this->assertTrue($s->mail instanceof MailService);
		$this->assertObjectHasAttribute('http', $s);
		$this->assertTrue($s->http instanceof TestComponentB);
		$this->assertObjectHasAttribute('extra', $s);
		$this->assertEquals(100, $s->extra);
	}
}