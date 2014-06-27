<?php
namespace Acme\Components;

/**
 * @inject.strict
 * @inject.provider Acme\Providers\MailServiceProvider
 */
class TestComponentF {
	/**
	 * @inject.service mail
	 */
	public $mail;
}