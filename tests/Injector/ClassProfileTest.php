<?php
use Injector\ClassProfile;

/**
 * 
 * @author emaphp
 * @group profile
 */
class ClassProfileTest extends \PHPUnit_Framework_TestCase {
	public function testComponentA() {
		$profile = new ClassProfile('Acme\Components\TestComponentA');
		$this->assertNull($profile->defaultContainer);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals('mail', $profile->constructorParams['service']);
	}
	
	public function testComponentB() {
		$profile = new ClassProfile('Acme\Components\TestComponentB');
		$this->assertEquals('Acme\Containers\TestContainer', $profile->defaultContainer);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals('mail', $profile->constructorParams['service']);
	}
	
	public function testComponentC() {
		$profile = new ClassProfile('Acme\Components\TestComponentC');
		$this->assertEquals('Acme\Containers\TestContainer', $profile->defaultContainer);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals('mail', $profile->constructorParams['service']);
		$this->assertArrayHasKey('http', $profile->constructorParams);
		$this->assertEquals('http', $profile->constructorParams['http']);
	}
	
	public function testComponentE() {
		$profile = new ClassProfile('Acme\Components\TestComponentE');
		$this->assertArrayHasKey('mail', $profile->properties);
		$this->assertEquals('mail', $profile->properties['mail']);
	}
}