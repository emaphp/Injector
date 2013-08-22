<?php
use Injector\ClassProfile;

class ClassProfileTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @expectedException \RuntimeException
	 */
	public function testComponentA() {
		$profile = new ClassProfile('Acme\Components\TestComponentA');
	}
	
	public function testComponentB() {
		$profile = new ClassProfile('Acme\Components\TestComponentB');
		$this->assertEquals('Acme\Containers\TestContainer', $profile->containerClass);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals(array('container' => 'Acme\Containers\TestContainer', 'service' => 'mail'), $profile->constructorParams['service']);
	}
	
	public function testComponentC() {
		$profile = new ClassProfile('Acme\Components\TestComponentC');
		$this->assertEquals('Acme\Containers\TestContainer', $profile->containerClass);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals(array('container' => 'Acme\Containers\TestContainer', 'service' => 'mail'), $profile->constructorParams['service']);
		$this->assertArrayHasKey('http', $profile->constructorParams);
		$this->assertEquals(array('container' => 'Acme\Containers\AnotherContainer', 'service' => 'http'), $profile->constructorParams['http']);
	}
	
	public function testComponentD() {
		$profile = new ClassProfile('Acme\Components\TestComponentD');
		$this->assertNull($profile->containerClass);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals(array('container' => 'Acme\Containers\TestContainer', 'service' => 'mail'), $profile->constructorParams['service']);
	}
	
	/**
	 * @expectedException \RuntimeException
	 */
	public function testComponentE() {
		$profile = new ClassProfile('Acme\Components\TestComponentE');
	}
	
	public function testComponentF() {
		$profile = new ClassProfile('Acme\Components\TestComponentF');
		$this->assertEquals('Acme\Containers\TestContainer', $profile->containerClass);
		$this->assertArrayHasKey('mail', $profile->properties);
		$this->assertEquals(array('container' => 'Acme\Containers\TestContainer', 'service' => 'mail', 'setter' => null), $profile->properties['mail']);
	}
	
	public function testComponentG() {
		$profile = new ClassProfile('Acme\Components\TestComponentG');
		$this->assertNull($profile->containerClass);
		$this->assertArrayHasKey('mail', $profile->properties);
		$this->assertEquals(array('container' => 'Acme\Containers\TestContainer', 'service' => 'mail', 'setter' => null), $profile->properties['mail']);
	}
	
	/**
	 * @expectedException \RuntimeException
	 */
	public function testComponentH() {
		$profile = new ClassProfile('Acme\Components\TestComponentH');
	}
	
	public function testComponentI() {
		$profile = new ClassProfile('Acme\Components\TestComponentI');
		$this->assertEquals('Acme\Containers\TestContainer', $profile->containerClass);
		$this->assertArrayHasKey('mail', $profile->properties);
		$this->assertEquals(array('container' => 'Acme\Containers\TestContainer', 'service' => 'mail', 'setter' => 'setMail'), $profile->properties['mail']);
	}
	
	public function testComponentJ() {
		$profile = new ClassProfile('Acme\Components\TestComponentJ');
		$this->assertEquals('Acme\Containers\TestContainer', $profile->containerClass);
		$this->assertArrayHasKey('mail', $profile->properties);
		$this->assertEquals(array('container' => 'Acme\Containers\AnotherContainer', 'service' => 'mail', 'setter' => 'setMail'), $profile->properties['mail']);
	}
}