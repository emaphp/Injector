<?php
use Injector\ClassProfile;

/**
 * 
 * @author emaphp
 * @group profile
 */
class ClassProfileTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @expectedException \RuntimeException
	 */
	public function testComponentA() {
		$profile = new ClassProfile('Acme\Components\TestComponentA');
	}
	
	public function testComponentB() {
		$profile = new ClassProfile('Acme\Components\TestComponentB');
		$this->assertEquals('Acme\Containers\TestContainer', $profile->defaultContainer);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals(array('container' => true, 'service' => 'mail'), $profile->constructorParams['service']);
	}
	
	public function testComponentC() {
		$profile = new ClassProfile('Acme\Components\TestComponentC');
		$this->assertEquals('Acme\Containers\TestContainer', $profile->defaultContainer);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals(array('container' => true, 'service' => 'mail'), $profile->constructorParams['service']);
		$this->assertArrayHasKey('http', $profile->constructorParams);
		$this->assertEquals(array('container' => 'Acme\Containers\AnotherContainer', 'service' => 'http'), $profile->constructorParams['http']);
	}
	
	public function testComponentD() {
		$profile = new ClassProfile('Acme\Components\TestComponentD');
		$this->assertNull($profile->defaultContainer);
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
		$this->assertEquals('Acme\Containers\TestContainer', $profile->defaultContainer);
		$this->assertArrayHasKey('mail', $profile->properties);
		$this->assertEquals(array('container' => true, 'service' => 'mail', 'reflection' => new \ReflectionProperty('Acme\Components\TestComponentF', 'mail')), $profile->properties['mail']);
	}
	
	public function testComponentG() {
		$profile = new ClassProfile('Acme\Components\TestComponentG');
		$this->assertNull($profile->containerClass);
		$this->assertArrayHasKey('mail', $profile->properties);
		$this->assertEquals(array('container' => 'Acme\Containers\TestContainer', 'service' => 'mail', 'reflection' => new \ReflectionProperty('Acme\Components\TestComponentG', 'mail')), $profile->properties['mail']);
	}
}