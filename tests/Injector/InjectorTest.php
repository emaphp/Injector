<?php
class InjectorTest extends \PHPUnit_Framework_TestCase {
	public function setUp() {
		/*
		 * Iterator test
		 */
		$this->itcontainer = new \Injector();
		
		//declare some services
		$this->itcontainer['dummy_service'] = function ($c) {
			$obj = new stdClass();
			$obj->name = 'service';
			return $obj;
		};
		
		/*
		 * Dependency test
		 */
		$this->dcontainer = new \Injector();
		
		$this->dcontainer['service_a'] = function ($c) {
			$obj = new stdClass();
			$obj->name = 'service_a';
			return $obj;
		};
		
		$this->dcontainer['service_b'] = function ($c) {
			$obj = new stdClass();
			$obj->name = 'service_b';
			$obj->service_a = $c['service_a'];
			return $obj;
		};
		
		/**
		 * Injection test
		 */
		$this->container = new \Injector();
		
		$this->container['x'] = function ($c) {
			$obj = new stdClass();
			$obj->name = 'service_x';
			return $obj;
		};
		
		$this->container['y'] = function ($c) {
			$obj = new stdClass();
			$obj->name = 'service_y';
			return $obj;
		};
		
		$this->container['z'] = function ($c) {
			$obj = new stdClass();
			$obj->name = 'service_z';
			return $obj;
		};
	}
	
	public function testIterator() {
		foreach ($this->itcontainer as $k => $v) {
			$this->assertEquals('dummy_service', $k);
			$this->assertTrue(is_object($v));
			$this->assertEquals('stdClass', get_class($v));
			$this->assertObjectHasAttribute('name', $v);
			$this->assertEquals('service', $v->name);
		}
	}
	
	public function testDependency() {
		$service_b = $this->dcontainer['service_b'];
		$this->assertTrue(is_object($service_b));
		$this->assertEquals('stdClass', get_class($service_b));
		$this->assertObjectHasAttribute('name', $service_b);
		$this->assertEquals('service_b', $service_b->name);
		$this->assertObjectHasAttribute('service_a', $service_b);
		$this->assertEquals('stdClass', get_class($service_b->service_a));
		$this->assertObjectHasAttribute('name', $service_b->service_a);
		$this->assertEquals('service_a', $service_b->service_a->name);
	}
	
	public function testInject() {
		$foo = new \stdClass();
		$this->container->inject($foo, 'x');
		$this->assertObjectHasAttribute('x', $foo);
		$this->assertTrue(is_object($foo->x));
		$this->assertEquals('stdClass', get_class($foo->x));
		$this->assertObjectHasAttribute('name', $foo->x);
		$this->assertEquals('service_x', $foo->x->name);
	}
	
	public function testInjectMany() {
		$foo = new \stdClass();
		$this->container->injectMany($foo, 'x', 'z');
		
		$this->assertObjectHasAttribute('x', $foo);
		$this->assertTrue(is_object($foo->x));
		$this->assertEquals('stdClass', get_class($foo->x));
		$this->assertObjectHasAttribute('name', $foo->x);
		$this->assertEquals('service_x', $foo->x->name);
		
		$this->assertObjectHasAttribute('z', $foo);
		$this->assertTrue(is_object($foo->z));
		$this->assertEquals('stdClass', get_class($foo->z));
		$this->assertObjectHasAttribute('name', $foo->z);
		$this->assertEquals('service_z', $foo->z->name);
	}
	
	public function testInjectAll() {
		$foo = new \stdClass();
		$this->container->injectAll($foo);
		
		$this->assertObjectHasAttribute('x', $foo);
		$this->assertTrue(is_object($foo->x));
		$this->assertEquals('stdClass', get_class($foo->x));
		$this->assertObjectHasAttribute('name', $foo->x);
		$this->assertEquals('service_x', $foo->x->name);
		
		$this->assertObjectHasAttribute('y', $foo);
		$this->assertTrue(is_object($foo->y));
		$this->assertEquals('stdClass', get_class($foo->y));
		$this->assertObjectHasAttribute('name', $foo->y);
		$this->assertEquals('service_y', $foo->y->name);
		
		$this->assertObjectHasAttribute('z', $foo);
		$this->assertTrue(is_object($foo->z));
		$this->assertEquals('stdClass', get_class($foo->z));
		$this->assertObjectHasAttribute('name', $foo->z);
		$this->assertEquals('service_z', $foo->z->name);
	}
}