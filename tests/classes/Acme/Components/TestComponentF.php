<?php
namespace Acme\Components;

/**
 * @Provider Acme\Providers\MailServiceProvider
 * @StrictInject
 */
class TestComponentF {
	/**
	 * @Inject mail
	 */
	public $mail;
}