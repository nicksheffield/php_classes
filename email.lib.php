<?php

class Email{

	public $to = '';
	public $from = '';
	public $subject = '';
	public $message = '';
	public $html = false;
	public $success = false;

	public function send(){

		$headers = '';

		if($this->html){
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$this->message = '<html><head><title>'.$this->subject.'</title></head><body>'.$this->message.'</body></html>';
		}

		$headers .= 'From: '.$this->from."\r\n";

		$wrapped_message = wordwrap($this->message, 70, "\r\n");

		$this->success = mail($this->to, $this->subject, $wrapped_message, $headers);

		return $this->success;
	}
}