<?php

class Route {
	
	private static $params = [];
	
	public static function param($name){
		return self::$params[$name];
	}
	
	public static function get($path, $file){
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			self::check($path, $file);
		}
	}
	
	public static function post($path, $file){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			self::check($path, $file);
		}
	}
	
	private static function check($path, $file){
		# use QUERY_STRING or default to ''
		$server_path = $_SERVER['QUERY_STRING'] ?: '';
		
		# remove the first slash
		if(substr($server_path, 0, 1) == '/'){
			$server_path = substr($server_path, 1);
		}
		
		# break up $server_path into segments
		$server_path = explode('/', $server_path);
		
		# break up $path into segments
		$path = explode('/', $path);
		
		# identify a match
		# if both $server_path and $path have the same amount of segments
		if(count($server_path) == count($path)){
			$match = true;
			
			# then check if they have the same segments
			foreach($server_path as $key => $sp_segment){
				# check if this one is a param
				if(substr($path[$key], 0, 1) == ':'){
					# if it is, then add it to $params
					self::$params[substr($path[$key], 1)] = $sp_segment;
				}else{
					# if not, then check if the segments are the same
					if($server_path[$key] != $path[$key]){
						$match = false;
					}
				}
			}
			
			if($match){
				# then require the file
				require_once $file;
				exit;
			}
			
		}
	}
	
	public static function fallback($file){
		require_once $file;
		exit;
	}
	
}