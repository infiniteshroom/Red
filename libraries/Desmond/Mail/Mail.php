<?php

Application::Import('Swift::Mail::swift_required.php');
class DesmondMail {

	protected $swift = null;
	protected $variables = array();
	protected $body = null;
	protected $viewname = null;
	protected $from = array();
	protected $to = array();
	protected $subject = array();

	function __construct() {

		$type = Application::Setting('mail::type');

		if($type == 'sendmail') {
			$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
		}

		else if($type == 'mail') {
			$transport = Swift_MailTransport::newInstance();
		}

		else if($type == 'smtp') {
			$transport = Swift_SmtpTransport::newInstance(Application::Setting('mail::host'), Application::Setting('mail::port'), 
				Application::Setting('mail::security'))
			->setUsername(Application::Setting('mail::username'))
	  		->setPassword(Application::Setting('mail::password'));

	  		$this->swift = Swift_Mailer::newInstance($transport);
  		}

	}

	public function Set($name, $value) {
		$this->variables[$name] = $value;
		return $this;
	}


	public function setBody($content) {
		$this->body = $content;

		return $this;
	}

	public function setView($name) {
		$this->viewname = $name;

		return $this;
	}

	public function setFrom($name, $email) {
		$this->from[] = array($name, $email);

		return $this;
	}

	public function setTo($name, $email) {
		$this->to[] = array($name, $email);

		return $this;
	}

	public function setSubject($subject) {
		$this->subject = $subject;

		return $this;
	}

	public function Send() {
		$message = Swift_Message::newInstance($this->subject)
	  ->setFrom($this->from)
	  ->setTo($this->to);

	  if($this->body != null) {
	  		$message->setBody($this->body);
	  	}

		else {
			/* get template content */
			$this->setBody(Template::render($this->viewname, $this->variables));
		}

		return $result = $this->swift->send($message);
	}
}
	
?>