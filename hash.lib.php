<?php

/*
	
	Password hashing class

	usage   : Static
	version : 1
	author  : Nick Sheffield

	=====================================

	Example

	$db->set(array(
		'email'    => 'user@example.com',
		'password' => Hash::make_password('123'),
		'salt'     => Hash::salt()
	))->insert('tb_users');

*/

class Hash{

	public function encrypt($password, $salt){
		return hash('sha256', $salt.$password);
	}

	public function salt(){
		return hash('sha256', time());
	}

	public function make_password($password){
		return self::encrypt($password, self::salt());
	}

}