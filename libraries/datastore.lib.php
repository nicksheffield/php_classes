<?php

namespace App;

session_start();

require_once 'config.lib.php';

class DataStore {
	
	protected $name = 'datastore';
	
	public static function get($var){
		if(isset($_SESSION[Config::$sitename][self::$name][$var])){
			return $_SESSION[Config::$sitename][self::$name][$var];
		}
	}
	
	public static function set($var, $val){
		return $_SESSION[Config::$sitename][self::$name][$var] = $val;
	}
	
	public static function remove($var){
		unset($_SESSION[Config::$sitename][self::$name][$var]);
	}
	
	public static function has($var){
		return isset($_SESSION[Config::$sitename][self::$name][$var]);
	}
	
	public static function all(){
		return $_SESSION[Config::$sitename][self::$name];
	}
}