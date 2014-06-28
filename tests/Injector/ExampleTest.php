<?php
namespace Injector;

use Pimple\Container;
use Acme\Components\ExampleComponent;
use Acme\Services\MySQLConnection;
use Acme\Services\Logger;
use Acme\Services\SQLiteConnection;

/**
 * 
 * @author emaphp
 * @group example
 */
class ExampleTest extends \PHPUnit_Framework_TestCase {
	public function testCreate() {
		$component = Injector::create('Acme\Components\ExampleComponent', 'My Component');
		$this->assertTrue($component instanceof ExampleComponent);
		$this->assertEquals('My Component', $component->getName());
		$this->assertEquals('development', $component->getEnvironment());
		$this->assertTrue($component->getConnection() instanceof MySQLConnection);
		$this->assertTrue($component->getLogger() instanceof Logger);
	}
	
	public function testOverrideConstructorArgument() {
		$component = Injector::create('Acme\Components\ExampleComponent', ['My Component', 'production']);
		$this->assertTrue($component instanceof ExampleComponent);
		$this->assertEquals('My Component', $component->getName());
		$this->assertEquals('production', $component->getEnvironment());
		$this->assertTrue($component->getConnection() instanceof MySQLConnection);
		$this->assertTrue($component->getLogger() instanceof Logger);
	}
	
	public function testFilterDependencies() {
		$component = Injector::create('Acme\Components\ExampleComponent', 'My Component', ['logger']);
		$this->assertTrue($component instanceof ExampleComponent);
		$this->assertEquals('My Component', $component->getName());
		$this->assertNull($component->getEnvironment());
		$this->assertNull($component->getConnection());
		$this->assertTrue($component->getLogger() instanceof Logger);
	}
	
	public function testOverrideDependencies() {
		$component = Injector::create('Acme\Components\ExampleComponent', 'My Component', null, ['environment' => 'stage', 'conn' => new SQLiteConnection('file.db')]);
		$this->assertTrue($component instanceof ExampleComponent);
		$this->assertEquals('My Component', $component->getName());
		$this->assertEquals('stage', $component->getEnvironment());
		$this->assertTrue($component->getConnection() instanceof SQLiteConnection);
		$this->assertTrue($component->getLogger() instanceof Logger);
	}
}

?>