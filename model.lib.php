<?php

/**
*	
*	General model class
*
*	@uses Config, for db details. Database, for db connection
*
*	@version 1.2
*	@author  Nick Sheffield
*
*/

require_once 'config.lib.php';
require_once 'database.lib.php';

class Model{

	protected $fields = array();
	protected $data   = array();
	protected $db     = null;
	protected $table  = '';

	function __construct($table){
		$this->table = $table;

		$this->db = new Database(
			Config::$hostname,
			Config::$username,
			Config::$password,
			Config::$database
		);

		$this->fields = $this->db->get_fields($this->table);
	}

	function __get($var){
		if(isset($this->data[$var])){
			return $this->data[$var];
		}else{
			return false;
		}
	}

	function __SET($var, $val){
		if(in_array($var, $this->fields)){
			return $this->data[$var] = $val;
		}else{
			return false;
		}
	}

	# Normal Methods --------------------------------

	public function load($id){
		$result = $this->db
			->select('*')
			->from($this->table)
			->where('id', $id)
			->get_one();

		$this->data = $result;
	}

	public function save(){
		
		if(!isset($this->data['id'])){
			$this->db
				->set($this->data)
				->insert($this->table);

			$this->data['id'] = $this->db->last_insert_id;
		}else{
			$this->db
				->set($this->data)
				->where('id', $this->data['id'])
				->update($this->table);
		}

		echo $this->db->last_query;

	}

	public function delete(){
		$this->soft_delete();
	}

	public function soft_delete(){
		$this->fields['deleted'] = 1;
		$this->save();
	}

	public function hard_delete(){
		$this->db
			->where('id', $this->fields['id'])
			->delete();
	}

}