<?php

/**
*	Login handling class
*
*	@version 1.1
*	@author  Nick Sheffield
*
*/

session_start();

class Login{

	public static function log_in($id = 0, $admin = false){
		self::create_user();

		if($id != 0){
			$_SESSION['user']['id'] = $id;
		}

		$_SESSION['user']['is_admin'] = $admin;
		$_SESSION['user']['logged_in'] = true;
	}



	public static function log_out(){
		self::create_user();
		$_SESSION['user']['id'] = 0;
		$_SESSION['user']['is_admin'] = false;
		$_SESSION['user']['logged_in'] = false;
	}



	public static function kickout($url = 'login.php'){
		self::create_user();
		if($_SESSION['user']['logged_in'] == false){
			header('location: '.$url);
			exit;
		}
	}

	public static function kickout_non_admin($url = 'index.php'){
		self::create_user();
		if($_SESSION['user']['logged_in'] == false || $_SESSION['user']['is_admin'] == false){
			header('location: '.$url);
			exit;
		}
	}



	public static function user_id(){
		self::create_user();
		return $_SESSION['user']['id'];
	}


	public static function is_admin(){
		self::create_user();
		return !!$_SESSION['user']['is_admin'];
	}



	public static function is_logged_in(){
		self::create_user();
		return !!$_SESSION['user']['logged_in'];
	}



	private static function create_user(){
		if(!isset($_SESSION['user'])){
			$_SESSION['user'] = array();
		}
	}
}