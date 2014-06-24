<?php
namespace Acme\Components;

/**
 * @inject.container Acme\Containers\TestContainer
 */
class TestComponentF {
	/**
	 * @inject.service mail
	 */
	public $mail;
}