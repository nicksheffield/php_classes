<?php

/*
	
	Model class

	usage   : Instantiated
	version : 1
	author  : Nick Sheffield

	=====================================

	Example

	$page = new Model('tb_pages');

	$page->load(1);
	$page->title = 'New title';
	$page->save();

*/

require_once 'config.class.php';

class Model{

	# Properties -----------------------------------

	private $fields      = array();
	private $db          = null;
	private $table       = '';

	# Magic Methods --------------------------------

	function __construct($table){
		$this->table = $table;

		$this->db = new Database(
			Config::$hostname,
			Config::$username,
			Config::$password,
			Config::$database
		);
	}

	function __get($var){
		if(isset($this->fields[$var])){
			return $this->fields[$var];
		}else{
			return false;
		}
	}

	function __set($var, $val){
		if(isset($this->fields[$var])){
			$this->fields[$var] = $val;
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

		$this->fields = $result;
	}

	public function save(){
		
		if(isset($this->fields['id'])){
			$this->db
				->set($this->fields)
				->insert($this->table);

			$this->fields['id'] = $this->db->last_insert_id;
		}else{
			$this->db
				->set($this->fields)
				->where('id', $this->fields['id'])
				->update($this->table);
		}
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