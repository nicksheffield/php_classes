<?php

namespace App;

session_start();

class URL{

	/**
	 * Save a specific url into the session for use in the URL::restore() method
	 *
	 * @param $url string If this argument is provided, it will be saved. If not, the current url will be pulled from $_SERVER
	 */
	public static function save($url = false){
		$_SESSION['saved_url'] = $url ? $url : $_SERVER['REQUEST_URI'];
	}

	/**
	 * Redirect to the saved url, and delete the saved_url
	 */
	public static function restore(){
		if($_SESSION['saved_url']){
			$url = $_SESSION['saved_url'];

			unset($_SESSION['saved_url']);

			self::redirect($url);
		}
	}

	/**
	 * Redirect to a given url
	 *
	 * @param $url string The url or file to redirect to.
	 */
	public static function redirect($url){
		header('location: '.url($url));
		exit;
	}

	public static function current() {
		return $_SERVER['QUERY_STRING'] ?: '/';
	}

}

function url($url) {
	$base = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']);
	$base = str_replace('index.php', '', $base);

	if(strpos($url, '/') === 0) {
		$url = substr($url, 1);
	}

	return $base.$url;
}