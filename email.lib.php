<?php

class Email{

	public $to      = '';
	public $from    = '';
	public $subject = '';
	public $message = '';
	public $html    = true;
	public $success = false;

	public function send(){
		$headers = '';

		# If html is turned on,
		if($this->html){
			# set the headers that enable html
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		}

		# Set the header for the From address
		$headers .= 'From: '.$this->from."\r\n";

		# Make sure the message has no lines longer than 70 characters
		$wrapped_message = wordwrap($this->message, 70, "\r\n");

		# Send the email
		$this->success = mail(
			$this->to,
			$this->subject,
			$wrapped_message,
			$headers
		);

		# Return whether it was successfully sent
		return $this->success;
	}

}