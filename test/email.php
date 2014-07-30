<?php

require_once '../libraries/email.lib.php';

$email = new Email();

$email->to      = 'numbereft@gmail.com';
$email->from    = 'email@example.com';
$email->subject = 'yo';
$email->message = 'Hello <h1>Hello</h1>';
$email->html    = false;

$email->send();

if($email->success){
	echo 'success';
}else{
	echo 'fail';
}