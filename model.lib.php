<?php

/**
*	
*	General model class
*
*	@uses Config, for db details. Database, for db connection
*
*	@version 1.5
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

	/**
	*
	*	This method is called automatically when this class is constructed.
	*
	*	@param  string $table The table that this model is supposed to represent
	*
	*	@return $this
	*
	*/
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


	/**
	*
	*	This method automatically triggers whenever we try to get a property
	*	from this object.
	*
	*	Instead of actually getting a property from this object, it instead pulls
	*	it from the data property, which is an array.
	*
	*	@param  string $var The property being requested
	*
	*	@return mixed  The value of the property being requested, or false if the property doesn't exist
	*
	*/
	function __get($var){
		if(isset($this->data[$var])){
			return $this->data[$var];
		}else{
			return false;
		}
	}

	/**
	*
	*	This method automatically triggers whenever we try to set a property
	*	from this object.
	*
	*	Instead of actually setting a property on this object, it instead modifies
	*	or adds the value in the data property, which is an array.
	*
	*	It only works if the key ($var) exists in the fields property, which is an
	*	array of all the possible fields in this database table. If that property
	*	didn't exist, it just returns false, thus, failing silently.
	*
	*	@param  string  $var The name of the property being changed
	*	@param  string  $val The value of the property being changed
	*
	*	@return boolean Whether the property existed or not.
	*
	*/
	function __set($var, $val){
		if(in_array($var, $this->fields)){
			$this->data[$var] = $val;
			return true;
		}else{
			return false;
		}
	}

	/**
	*
	*	Load information from the database table
	*
	*	@param  int   $id The value of the id field in the table
	*
	*	@return array An assoc array of the fields and values that were loaded
	*
	*/
	public function load($id){
		$result = $this->db
			->select('*')
			->from($this->table)
			->where($this->primary_key, $id)
			->get_one();

		$this->data = $result;
		return $result;
	}

	/**
	*
	*	Fill the data array of this model. Useful for adding data from $_POST quickly
	*
	*	@param  array $data An associative array containing one or more fields => value pairs
	*
	*	@return array An associative array containing any data that was rejected
	*
	*/
	public function fill($data){

		$not_added = array();

		foreach($data as $key => $value){
			if(in_array($key, $this->fields)){
				$this->data[$key] = $value;
			}else{
				$not_added[$key] = $value;
			}
		}

		return $not_added;
	}

	/**
	*
	*	Insert or update this record in the table
	*
	*	@return boolean Whether the insert/update was successful or not
	*
	*/
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

	/**
	*
	*	Delete this record from the table. This is a soft delete
	*
	*	@return boolean Whether the delete was successful
	*
	*/
	public function delete(){
		return $this->soft_delete();
	}

	/**
	*
	*	Specifically perform a soft delete.
	*
	*	This only sets the 'deleted' field of this record to 1
	*
	*	@return boolean Whether the delete was successful
	*
	*/

	public function soft_delete(){
		$this->data['deleted'] = 1;
		return $this->save();
	}

	/**
	*
	*	Specifically perform a hard delete.
	*
	*	This is different to a soft delete because the record will
	*	be permanently removed from the db.
	*
	*	@return boolean Whether the delete was successful
	*
	*/

	public function hard_delete(){
		return $this->db
			->where($this->primary_key, $this->fields[$this->primary_key])
			->delete();
	}

}