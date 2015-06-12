<?php

/**
*
*	Collection style model library
*
*	@uses 	Config,   for db credentials.
*			Database, for db connection.
*			Model,    for containing records.
*
*	@version 1
*	@author  Nick Sheffield
*
*/

require_once 'config.lib.php';
require_once 'database.lib.php';
require_once 'model.lib.php';

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
	*	@param string $where An array that represents the where query
	*
	*/
	public function __construct($table, $where = false, $order_by = false){
		$this->db = new Database( Config::$database );

		$this->table = $table;

		$this->load($where, $order_by);
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
	public function load($where = false, $order_by = false) {
		$this->items = [];
		
		$this->db->select('*')->from($this->table);

		if($where) {
			$this->db->where($where);
		}
		
		if($order_by){
			$this->db->order_by($order_by);
		}

		$this->items = $this->db->get();
		
		foreach($this->items as $key => $item){
			$model = new Model($this->table, false);
			
			$model->fill($item);
			
			$this->items[$key] = $model;
		}
	}
	
	public function limit($count, $start = 0){
		
	}
	
	public function paginate($count, $page = 1){
		
	}

}