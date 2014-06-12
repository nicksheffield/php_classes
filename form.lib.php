<?php

/**
*	Form creation class
*
*	@version 1
*	@author  Nick Sheffield
*
*/

class Form{

	/**
	*	Creates a form open tag
	*	
	*	@param  string $action The url for the form to post to
	*	@param  string $method Either "post" or "get"
	*
	*	@return string $html   The completed form tag
	*	
	*/
	public static function open($action = '', $method = 'post'){
		$html = "<form action='$action' method='$method'>";
		return $html;
	}

	/**
	*	Creates a form open tag that works with uploads
	*	
	*	@param  string $action The url for the form to post to
	*
	*	@return string $html   The completed form tag
	*	
	*/
	public static function open_multipart($action = ''){
		$html = "<form action='$action' method='post' enctype='multipart/form-data'>";
		return $html;
	}

	/**
	*	Creates a form close tag
	*
	*	@return string $html   The form close tag
	*	
	*/
	public static function close(){
		$html = '</form>';
		return $html;
	}

	/**
	*	Creates a input tag
	*	
	*	@param  string $type   The type of input it will be
	*	@param  string $name   The name (and id) attribute of the input
	*	@param  string $value  The value to pre-fill the field with
	*	@param  string $extras Any extra attributes we want to add
	*
	*	@return string $html   The completed input tag
	*	
	*/
	public static function input($type, $name, $value = '', $extras = ''){
		$html = "<input type='$type' id='$name' name='$name' value='$value' $extras>";
		return $html;
	}

	/**
	*	Creates a label tag
	*	
	*	@param  string $for    The id attribute of the field this label is for
	*	@param  string $text   The text to appear on the label
	*
	*	@return string $html   The completed label tag
	*	
	*/
	public static function label($for, $text){
		$html = "<label for='$for'>$text</label>";
		return $html;
	}

	/**
	*	Creates a textarea tag
	*	
	*	@param  string $name   The name attribute of the textarea
	*	@param  string $value  The value to pre-fill the textarea with
	*
	*	@return string $html   The completed textarea tag
	*	
	*/
	public static function textarea($name, $value = ''){
		$html = "<textarea id='$name' name='$name'>$value</textarea>";
		return $html;
	}

	/**
	*	Creates a set of option tags from an array
	*
	*	@used-by self::select() to get option tags
	*	
	*	@param  array  $values        An associative array including the value, and text of each option tag
	*	@param  string $pre_selected  The value of the option element to show as selected
	*
	*	@return string $html          The completed option tags
	*	
	*/
	public static function options($values, $pre_selected){
		$html = '';
		foreach($values as $value => $text){
			
			$selected = $pre_selected == $value ? 'selected' : '';
			
			$html .= "<option value='$value' $selected>$text</option>";
		}
		return $html;
	}

	/**
	*	Creates a select tag with option tags within it
	*	
	*	@uses   self::options() to get option tags
	*
	*	@param  string $name         The name attribute of the <select> tag
	*	@param  array  $values       An array sent to self::options()
	*	@param  string $pre_selected A string sent to self::options()
	*
	*	@return string $html         The completed select tag
	*	
	*/
	public static function select($name, $values, $pre_selected){
		$html = "<select name='$name' id='$name'>";
		$html .= self::options($values. $pre_selected);
		$html .= '</select>';
		return $html;
	}

	/* Shortcut functions for common input types */

	/**
	*	Creates a hidden input tag
	*	
	*	@uses   self::input() to create the tag
	*
	*	@param  string $name  The name attribute
	*	@param  string $value The value attribute
	*	
	*	@return string $html  The completed hidden input tag
	*	
	*/
	public static function hidden($name, $value){
		$html = self::input('hidden', $name, $value);
		return $html;
	}

	/**
	*	Creates a text input tag
	*	
	*	@uses   self::input() to create the tag
	*
	*	@param  string $name  The name attribute
	*	@param  string $value The value attribute
	*	
	*	@return string $html  The completed text input tag
	*	
	*/
	public static function text($name, $value = ''){
		$html = self::input('text', $name, $value);
		return $html;
	}

	/**
	*	Creates a password input tag
	*	
	*	@uses   self::input() to create the tag
	*
	*	@param  string $name  The name attribute
	*	@param  string $value The value attribute
	*	
	*	@return string $html   The completed password input tag
	*	
	*/
	public static function password($name, $value = ''){
		$html = self::input('password', $name, $value);
		return $html;
	}

	/**
	*	Creates a file input tag
	*	
	*	@uses   self::input() to create the tag
	*
	*	@param  string $name The name attribute. '[]' will be added to the end.
	*	
	*	@return string $html   The completed file input tag
	*	
	*/
	public static function file($name = 'file'){
		$html = self::input('file', $name.'[]', '', 'multiple');
		return $html;
	}

	/**
	*	Creates a submit button
	*	
	*	@uses   self::input() to create the tag
	*
	*	@param  string $text  Text to show on the button
	*	
	*	@return string $html  The completed submit button
	*	
	*/
	public static function submit($text = 'Submit'){
		$html = self::input('submit', '', $text);
		return $html;
	}

	/**
	*	Creates a hidden input tag for to MAX_FILE_SIZE
	*	
	*	@uses   self::input() to create the tag
	*
	*	@param  string $value The max size in bytes
	*	
	*	@return string $html  The completed hidden input tag
	*	
	*/
	public static function max_file_size($size = '4194304'){
		$html = self::hidden('MAX_FILE_SIZE', $size);
		return $html;
	}

	/**
	*	Creates a number input tag
	*	
	*	@uses   self::input() to create the tag
	*
	*	@param  string $name   The name attribute
	*	@param  string $value  The value attribute
	*	@param  string $extras Any extra attributes to be added to the tag
	*	
	*	@return string $html   The completed number input tag
	*	
	*/
	public static function number($name, $value = '', $extras = ''){
		$html = self::input('number', $name, $value, $extras);
		return $html;
	}
}