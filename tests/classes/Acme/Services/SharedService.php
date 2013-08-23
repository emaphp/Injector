<?php
namespace Acme\Services;

class SharedService {
	public $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
}