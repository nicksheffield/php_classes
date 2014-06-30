<?php

class Email{

	public $to = '';
	public $from = '';
	public $subject = '';
	public $message = '';
	public $html = false;

	public function send(){

		if($this->html){
			$headers  .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		}

		$headers = 'From: '.$this->from."\r\n";

		return mail($this->to, $this->subject, $this->message, $headers);
	}
}