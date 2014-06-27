<?php
namespace classes\Acme\Components;

/**
 * @inject.strict
 * @inject.provider Acme\Provider\HTTPServiceProvider
 */
class TestComponentG {
	/**
	 * @inject.service mail
	 */
	public $mail;
	
	/**
	 * @inject.service http
	 */
	public $http;
}

?>