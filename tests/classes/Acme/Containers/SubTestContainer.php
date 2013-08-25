<?php
namespace Acme\Containers;

class SubTestContainer extends TestContainer {
	public function __construct() {
		parent::__construct();
		
		$this['class'] = 'SubTestContainer';
	}
}