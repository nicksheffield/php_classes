<?php

/**
*
*	Collection style model library
*
*	@uses Config, for db credentials. Database, for db connection
*
*	@version 1
*	@author Nick Sheffield
*
*/

require_once 'config.lib.php';
require_once 'database.lib.php';

class Collection{

	public     $items  = array();
	protected  $db     = null;
	protected  $table  = '';

	/**
	*
	*	This function is triggered automatically when this class is instantiated
	*
	*	If $field and $value are both provided, the load method is triggered with those params
	*
	*	@param string $table The name of the table this collection represents
	*	@param string $field The field to qualify which records are retrieved
	*	@param string $val   The value to quality which records are retrieved
	*
	*/
	public function __construct($table, $field = false, $value = false){
		$this->db = new Database(
			Config::$hostname,
			Config::$username,
			Config::$password,
			Config::$database
		);

		$this->table = $table;

		$this->load($field, $value);
	}

	/**
	*
	*	This is used to narrow down results
	*
	*	@example only gather products from a specific category
	*			 $products = new Collection('tb_products', 'cat_id', $_GET['cat_id']);
	*
	*	@param string $table The name of the table this collection represents
	*	@param string $field The field to qualify which records are retrieved
	*	
	*/
	public function load($field = false, $value = false){
		$this->db->select('*')->from($this->table);

		if($field && $value){
			$this->db->where($field, $value);
		}

		$this->items = $this->db->get();
	}

}