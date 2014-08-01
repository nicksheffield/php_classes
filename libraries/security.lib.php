<?php

class Security{

	public static function filter_html($value){
		return strip_tags($value, '<p><a><b><i><h1><h2><h3><h4><h5><h6>');
	}
}