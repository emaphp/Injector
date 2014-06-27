<?php
namespace Acme\Components;

/**
 * @inject.strict
 */
class TestComponentE {
	/**
	 * @inject.service mail
	 */
	public $mail;
}