<?php
namespace Acme\Components;

use Acme\Services\MailService;

/**
 * 
 * @author emaphp
 */
class TestComponentH {
	/**
	 * @inject setMail(mail)
	 * @var unknown
	 */
	protected $mail;
	
	public function setMail($mail) {
		$this->mail = $mail;
	}
}