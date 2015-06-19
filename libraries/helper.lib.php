<?php

# This file is a collection of useful functions

public function asset($url){
	return url($url);
}

public function url($url){
	return dirname($_SERVER['PHP_SELF']).'/'.$url;
}