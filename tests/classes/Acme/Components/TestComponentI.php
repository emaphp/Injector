<?php
namespace Acme\Components;

use Acme\Services\MailService;

/**
 * 
 * @author emaphp
 * @container Acme\Containers\TestContainer
 */
class TestComponentI {
	/**
	 * @inject setMail(mail)
	 * @var unknown
	 */
	protected $mail;
	
	public function setMail($mail) {
		$this->mail = $mail;
	}
	
	public function getMail() {
		return $this->mail;
	}
}