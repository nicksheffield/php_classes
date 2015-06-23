<?php

/**
*
*	Collection style model library
*
*	@uses 	Config,   for db credentials.
*			Database, for db connection.
*			Model,    for containing records.
*
*	@version 2.0
*	@author  Nick Sheffield
*
*/

require_once 'config.lib.php';
require_once 'database.lib.php';
require_once 'model.lib.php';

class Collection {

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
	*	@param string $where An array that represents the where query
	*
	*/
	public function __construct($table = false){
		$this->db = new Database( Config::$database );

		if($table != false){
			$this->table = $table;
		}
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
	public function get() {
		$this->items = [];
		
		$this->db->select('*')->from($this->table);

		$this->items = $this->db->get();
		
		foreach($this->items as $key => $item){
			$model = new Model($this->table, false);
			
			$model->fill($item);
			
			$this->items[$key] = $model;
		}
	}
	
	public function where($param1, $param2 = null, $param3 = null, $param4 = null){
		$this->db->where($param1, $param2, $param3, $param4);
		
		return $this;
	}
	
	public function order_by($data, $dir = null){
		$this->db->order_by($data, $dir);
				
		return $this;
	}
	
	public function limit($from, $count){
		$this->db->limit($from, $count);
		
		return $this;
	}
	
	public function paginate($count, $page = 1){
		$this->db->limit(($page - 1) * $count, $count);
		
		return $this;
	}
	

}