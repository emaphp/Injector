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
		$this->assertInstanceOf('\\ReflectionClass', $profile->class);
		$this->assertEquals('Acme\Components\TestComponentA', $profile->className);
		$this->assertInstanceOf('\\ReflectionMethod', $profile->constructor);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals('mail', $profile->constructorParams['service']);
	}
	
	public function testComponentB() {
		$profile = new ClassProfile('Acme\Components\TestComponentB');
		$this->assertInstanceOf('\\ReflectionClass', $profile->class);
		$this->assertEquals('Acme\Components\TestComponentB', $profile->className);
		$this->assertArrayHasKey('mail', $profile->properties);
		$this->assertEquals('mail', $profile->properties['mail']);
		$this->assertArrayHasKey('mail', $profile->reflectionProperties);
		$this->assertInstanceOf('\\ReflectionProperty', $profile->reflectionProperties['mail']);
	}
		
	public function testComponentC() {
		$profile = new ClassProfile('Acme\Components\TestComponentC');
		$this->assertFalse($profile->isStrict);
		$this->assertInternalType('array', $profile->providers);
		$this->assertContains('Acme\Providers\MailServiceProvider', $profile->providers);
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals('mail', $profile->constructorParams['service']);
		$this->assertArrayHasKey('http', $profile->constructorParams);
		$this->assertEquals('http', $profile->constructorParams['http']);
	}
	
	public function testComponentF() {
		$profile = new ClassProfile('Acme\Components\TestComponentF');
		$this->assertTrue($profile->isStrict);
	}
	
	public function testComponentH() {
		$profile = new ClassProfile('Acme\Components\TestComponentH');
		$this->assertContains('Acme\Providers\MailServiceProvider', $profile->providers);
		$this->assertContains('Acme\Providers\HTTPServiceProvider', $profile->providers);
	}
	
	public function testComponentZ() {
		$profile = new ClassProfile('Acme\Components\TestComponentZ');
		$this->assertArrayHasKey('service', $profile->constructorParams);
		$this->assertEquals('mail', $profile->constructorParams['service']);
		$this->assertArrayHasKey('http', $profile->properties);
		$this->assertEquals('http', $profile->properties['http']);
		$this->assertArrayHasKey('http', $profile->reflectionProperties);
		$this->assertInstanceOf('\\ReflectionProperty', $profile->reflectionProperties['http']);
	}
	
	public function testComponentY() {
		$profile = new ClassProfile('Acme\Components\TestComponentY');
		$this->assertNotEmpty($profile->providers);
		$this->assertContains('Acme\Providers\AllServiceProvider', $profile->providers);
	}
	
	public function testComponentX() {
		$profile = new ClassProfile('Acme\Components\TestComponentX');
		$this->assertEmpty($profile->providers);
	}
}