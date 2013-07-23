<?php
class TestContainer extends Injector\Container {
	public function configure() {
		$this['test_service'] = function ($c) {
			return new TestService();
		};
		
		$this['test_object'] = function ($c) {
			$obj = new stdClass();
			$obj->name = 'testObject';
			return $obj;
		};
	}
}