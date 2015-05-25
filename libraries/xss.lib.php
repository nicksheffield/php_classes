<?php

/**
*	
*	Output filtering library.
*	Used to prevent XSS vulnerabilities.
*	
*	Used in static context.
*
*	@version 1.0
*	@author Nick Sheffield
*
*/


class XSS {
	
	/**
	*
	*	Create the whitelist string.
	*
	*	If any tag names are provided in the $blacklist parameter,
	*	those tag names will not be present in the whitelist string
	*
	*	@param  mix $blacklist either a single string, or an array
	*				of strings of tag names that are not allowed
	*
	*	@return str All the html tags that make up the whitelist
	*
	**/
	private static function create_whitelist($blacklist = []) {
		
		if (!is_array($blacklist)) {
			$blacklist = [$blacklist];
		}
		
		$output = '';
		
		$list = [
			'p', 'a', 'b', 'i', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
			'div', 'span', 'hr', 'br', 'img', 'form', 'input', 'fieldset',
			'legend', 'textarea', 'button', 'table', 'thead', 'label',
			'tbody', 'tfoot', 'tr', 'th', 'td', 'q', 'blockquote', 'pre',
			'code', 'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'em', 'strong'
		];
		
		foreach ($list as $key => $tagname) {
			foreach ($blacklist as $bad_tagname) {
				if ($tagname == $bad_tagname) {
					array_splice($list, $key, 1);
				}
			}
		}
		
		foreach ($list as $key => $tagname) {
			$list[$key] = '<'.$tagname.'>';
		}
		
		return implode($list);
	}

	/**
	*
	*	Filter the tags out of a string using the default whitelist.
	*
	*	@param  str $value The html content to be filtered through
	*
	*	@return str The filtered html
	*
	**/
	public static function filter($value) {
		return strip_tags($value, self::create_whitelist());
	}
	
	/**
	*
	*	Filter the tags out of a string using a blacklist.
	*
	*	@param  str $value The html content to be filtered through
	*	@param  mix $value The html content to be filtered through
	*
	*	@return str The filtered html
	*
	**/
	public static function remove_tags($value, $blacklist) {
		return strip_tags($value, self::create_whitelist($blacklist));
	}
}