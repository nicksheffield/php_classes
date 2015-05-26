<?php

class Route {
	
	public static function add($path, $file){
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
					# if it is, then allow it
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
	
	public static function no_route($file){
		require_once $file;
		exit;
	}
	
}