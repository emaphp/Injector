<?php
use Injector\Injector;
use Acme\Components\TestComponentB;
use Acme\Components\TestComponentC;
use Acme\Services\MailService;
use Acme\Components\TestComponentD;
use Acme\Components\TestComponentF;
use Acme\Components\TestComponentG;
use Acme\Services\HTTPService;
use Acme\Components\TestComponentH;
use Acme\Containers\TestContainer;

/**
 * 
 * @author emaphp
 * @group injector
 */
class InjectorCreateTest extends \PHPUnit_Framework_TestCase {
	public function testComponentB() {
		$b = Injector::createFrom(null, 'Acme\Components\TestComponentB');
		$this->assertTrue($b instanceof TestComponentB);
		$this->assertObjectHasAttribute('mail', $b);
		$this->assertTrue($b->mail instanceof MailService);
	}
	
	public function testComponentB2() {
		$b = Injector::create('Acme\Components\TestComponentB');
		$this->assertTrue($b instanceof TestComponentB);
		$this->assertObjectHasAttribute('mail', $b);
		$this->assertTrue($b->mail instanceof MailService);
	}
		
	public function testComponentC2() {
		$c = Injector::create('Acme\Components\TestComponentC', 'c_object');
		$this->assertTrue($c instanceof TestComponentC);
		$this->assertObjectHasAttribute('mail', $c);
		$this->assertTrue($c->mail instanceof MailService);
		$this->assertObjectHasAttribute('name', $c);
		$this->assertEquals('c_object', $c->name);
	}
	
	public function testComponentD() {
		$d = Injector::create('Acme\Components\TestComponentD');
		$this->assertTrue($d instanceof TestComponentD);
		$this->assertObjectHasAttribute('mail', $d);
		$this->assertTrue($d->mail instanceof MailService);
	}
	
	public function testComponentF() {
		$f = Injector::create('Acme\Components\TestComponentF');
		$this->assertTrue($f instanceof TestComponentF);
		$this->assertObjectHasAttribute('mail', $f);
		$this->assertTrue($f->mail instanceof MailService);
	}

	public function testComponentH() {
		$h = Injector::create('Acme\Components\TestComponentH', 'h_object');
		$this->assertTrue($h instanceof TestComponentH);
		$this->assertObjectHasAttribute('mail', $h);
		$this->assertTrue($h->mail instanceof MailService);
		$this->assertObjectHasAttribute('name', $h);
		$this->assertEquals('h_object', $h->name);
		$this->assertObjectHasAttribute('id', $h);
		$this->assertEquals(1, $h->id);
	}
	
	public function testComponentJ() {
		$j = Injector::create('Acme\Components\TestComponentJ', 'j_object');
		$this->assertTrue($j instanceof Acme\Components\TestComponentJ);
		$this->assertEquals('j_object', $j->name);
		$this->assertEquals(1, $j->id);
		$this->assertTrue($j->getMail() instanceof MailService);
		$this->assertTrue($j->getHttp() instanceof HTTPService);
	}
	
	public function testComponentJ2() {
		$j = Injector::create('Acme\Components\TestComponentJ', 'j_object', 5);
		$this->assertTrue($j instanceof Acme\Components\TestComponentJ);
		$this->assertEquals('j_object', $j->name);
		$this->assertEquals(5, $j->id);
		$this->assertTrue($j->getMail() instanceof MailService);
		$this->assertTrue($j->getHttp() instanceof HTTPService);
	}
	
	/**
	 * 'From' tests
	 */
	public function testComponentA() {
		$a = Injector::createFrom(new TestContainer(), 'Acme\Components\TestComponentA');
		$this->assertTrue($a instanceof Acme\Components\TestComponentA);
		$this->assertTrue($a->mail instanceof MailService);
	}
	
	public function testComponentC() {
		$c = Injector::createFrom(null, 'Acme\Components\TestComponentC', 'c_object');
		$this->assertTrue($c instanceof TestComponentC);
		$this->assertObjectHasAttribute('mail', $c);
		$this->assertTrue($c->mail instanceof MailService);
		$this->assertObjectHasAttribute('name', $c);
		$this->assertEquals('c_object', $c->name);
	}
	
	public function testComponentD2() {
		$d = Injector::createFrom(null, 'Acme\Components\TestComponentD');
		$this->assertTrue($d instanceof TestComponentD);
		$this->assertObjectHasAttribute('mail', $d);
		$this->assertTrue($d->mail instanceof MailService);
	}
	
	public function testComponentE() {
		$e = Injector::createFrom('Acme\Containers\BigContainer', 'Acme\Components\TestComponentE');
		$this->assertTrue($e instanceof Acme\Components\TestComponentE);
		$this->assertTrue($e->mail instanceof MailService);
	}
	
	public function testComponentF2() {
		$f = Injector::createFrom(null, 'Acme\Components\TestComponentF');
		$this->assertTrue($f instanceof TestComponentF);
		$this->assertObjectHasAttribute('mail', $f);
		$this->assertTrue($f->mail instanceof MailService);
	}
	
	public function testComponentG() {
		$g = Injector::createFrom(null, 'Acme\Components\TestComponentG');
		$this->assertTrue($g instanceof TestComponentG);
		$this->assertObjectHasAttribute('mail', $g);
		$this->assertTrue($g->mail instanceof MailService);
	}
	
	public function testComponentH2() {
		$h = Injector::createFrom(null, 'Acme\Components\TestComponentH', 'h_object');
		$this->assertTrue($h instanceof TestComponentH);
		$this->assertObjectHasAttribute('mail', $h);
		$this->assertTrue($h->mail instanceof MailService);
		$this->assertObjectHasAttribute('name', $h);
		$this->assertEquals('h_object', $h->name);
		$this->assertObjectHasAttribute('id', $h);
		$this->assertEquals(1, $h->id);
	}
	
	public function testComponentH3() {
		$h = Injector::createFrom(null, 'Acme\Components\TestComponentH', 'h_object', 2);
		$this->assertTrue($h instanceof TestComponentH);
		$this->assertObjectHasAttribute('mail', $h);
		$this->assertTrue($h->mail instanceof MailService);
		$this->assertObjectHasAttribute('name', $h);
		$this->assertEquals('h_object', $h->name);
		$this->assertObjectHasAttribute('id', $h);
		$this->assertEquals(2, $h->id);
	}
	
	public function testComponentI() {
		$i = Injector::createFrom('Acme\Containers\TestContainer', 'Acme\Components\TestComponentI');
		$this->assertTrue($i instanceof Acme\Components\TestComponentI);
		$this->assertTrue($i->getMail() instanceof MailService);
		$this->assertNull($i->getHttp());
	}
}