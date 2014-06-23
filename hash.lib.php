<?php

/**
*	
*	Password hashing class
*
*	@version 1
*	@author  Nick Sheffield
*
*/

class Hash{

	public static function salt(){
		return substr(hash('sha256', time()), 0, 13);
	}

	public static function encrypt($password, $salt){
		return hash('sha256', substr($salt.$password, 0, 300));
	}

	public static function make($password){
		# Return the full hash
		return self::salt() . self::encrypt($password, self::salt());
	}

	public static function verify($password, $hash){
		# Extract the salt from the hash
		$extracted_salt = substr($hash, 0, 13);

		# Check if the full hash is the same as what we get from encrypting the
		# given pw with the extracted salt
		$matches = $hash == $extracted_salt . self::encrypt($password, $extracted_salt);

		return $matches;
	}

}