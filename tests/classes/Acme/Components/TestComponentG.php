<?php
namespace classes\Acme\Components;

/**
 * @Inject Acme\Provider\HTTPServiceProvider
 * @StrictInject
 */
class TestComponentG {
	/**
	 * @Inject mail
	 */
	public $mail;
	
	/**
	 * @Inject http
	 */
	public $http;
}

?>