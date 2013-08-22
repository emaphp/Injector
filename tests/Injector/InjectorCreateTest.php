<?php
use Injector\Injector;
use Acme\Components\TestComponentB;
use Acme\Components\TestComponentC;
use Acme\Services\MailService;
use Acme\Components\TestComponentD;
use Acme\Components\TestComponentF;
use Acme\Components\TestComponentG;
use Acme\Components\TestComponentI;
use Acme\Components\TestComponentJ;
use Acme\Services\HTTPService;

class InjectorCreateTest extends \PHPUnit_Framework_TestCase {
	public function testComponentB() {
		$b = Injector::createWith(null, 'Acme\Components\TestComponentB');
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
	
	public function testComponentC() {
		$c = Injector::createWith(null, 'Acme\Components\TestComponentC', 'c_object');
		$this->assertTrue($c instanceof TestComponentC);
		$this->assertObjectHasAttribute('mail', $c);
		$this->assertTrue($c->mail instanceof MailService);
		$this->assertObjectHasAttribute('name', $c);
		$this->assertEquals('c_object', $c->name);
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
		$d = Injector::createWith(null, 'Acme\Components\TestComponentD');
		$this->assertTrue($d instanceof TestComponentD);
		$this->assertObjectHasAttribute('mail', $d);
		$this->assertTrue($d->mail instanceof MailService);
	}
	
	public function testComponentF() {
		$f = Injector::createWith(null, 'Acme\Components\TestComponentF');
		$this->assertTrue($f instanceof TestComponentF);
		$this->assertObjectHasAttribute('mail', $f);
		$this->assertTrue($f->mail instanceof MailService);
	}
	
	public function testComponentG() {
		$g = Injector::createWith(null, 'Acme\Components\TestComponentG');
		$this->assertTrue($g instanceof TestComponentG);
		$this->assertObjectHasAttribute('mail', $g);
		$this->assertTrue($g->mail instanceof MailService);
	}
	
	public function testComponentI() {
		$i = Injector::createWith(null, 'Acme\Components\TestComponentI');
		$this->assertTrue($i instanceof TestComponentI);
		$this->assertObjectHasAttribute('mail', $i);
		$this->assertTrue($i->getMail() instanceof MailService);
	}
	
	public function testComponentJ() {
		$j = Injector::createWith(null, 'Acme\Components\TestComponentJ');
		$this->assertTrue($j instanceof TestComponentJ);
		$this->assertObjectHasAttribute('http', $j);
		$this->assertTrue($j->getHTTP() instanceof HTTPService);
	}
}