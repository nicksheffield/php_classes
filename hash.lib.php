<?php

/**
*	
*	Password hashing class
*
*	@version 2
*	@author  Nick Sheffield
*
*/

class Hash{

	/**
	*
	*	Generate a random 13 character salt.
	*	This function is based on the current time, so it will return the exact same value if used more than once during runtime
	*
	*	@return string 13 random alphanumeric characters
	*
	*/
	public static function salt(){
		return substr(hash('sha256', time()), 0, 16);
	}

	/**
	*
	*	Encrypt a salted password with sha256
	*
	*	@param  string $password The password
	*	@param  string $salt     The salt
	*
	*	@return string The hashed password
	*
	*/
	public static function encrypt($password, $salt){
		return hash('sha256', substr($salt.$password, 0, 300));
	}

	/**
	*
	*	Create a full password hash.
	*
	*	@param string $password The password to use to create the hash
	*
	*	@return string The full hash. The first 13 characters of this is salt, the rest is the encrypted password
	*
	*/
	public static function make($password){
		return self::salt() . self::encrypt($password, self::salt());
	}

	/**
	*
	*	Check if a given password was used to create a given hash
	*
	*	@param string $password The password in question
	*	@param string $hash     The hash we already know
	*
	*	@return boolean Whether the password was indeed used to create the hash
	*
	*/
	public static function verify($password, $hash){
		$extracted_salt = substr($hash, 0, 16);

		$matches = $hash == $extracted_salt . self::encrypt($password, $extracted_salt);

		return $matches;
	}

}