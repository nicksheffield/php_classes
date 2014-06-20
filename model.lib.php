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

	protected $fields      = array();
	protected $data        = array();
	protected $db          = null;
	protected $primary_key = 'id';
	protected $table       = '';
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

	function __set($var, $val){
		if(in_array($var, $this->fields)){
			$this->data[$var] = $val;
			return true;
		}else{
			return false;
		}
	}

	# Normal Methods --------------------------------

	public function load($id){
		$result = $this->db
			->select('*')
			->from($this->table)
			->where($this->primary_key, $id)
			->get_one();

		$this->data = $result;
	}

	public function save(){
		
		if(!isset($this->data[$this->primary_key])){
			$success = $this->db
				->set($this->data)
				->insert($this->table);

			$this->data[$this->primary_key] = $this->db->last_insert_id;
		}else{
			$success = $this->db
				->set($this->data)
				->where($this->primary_key, $this->data[$this->primary_key])
				->update($this->table);
		}

		return $success;

	}

	public function delete(){
		return $this->soft_delete();
	}

	public function soft_delete(){
		$this->fields['deleted'] = 1;
		return $this->save();
	}

	public function hard_delete(){
		return $this->db
			->where($this->primary_key, $this->fields[$this->primary_key])
			->delete();
	}

}