<?php

require_once 'input.lib.php';

class Sticky{
	
	private static $data = array();
	
	public static function get($var, $alt = null){
		# Check if the var is in the $data, if so return that
		if(isset(self::$data[$var])){
			return self::$data[$var];
			# else, check if it's in Input, and if so return that
		}else if(Input::get($var)){
			return Input::get($var);
			# else, if we provided an alt, use that
		}else if($alt != null){
			return $alt;
			# If not found at all, return false
		}else{
			return false;
		}
	}
	
	public static function set($var, $val){
		self::$data[$var] = $val;
	}
	
	public static function fill($data){
		self::$data = array_merge(self::$data, $data);
	}
}