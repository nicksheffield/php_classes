<?php

/**
*	
*	General model class
*
*	@uses Config, for db details. Database, for db connection
*
*	@version 3.0
*	@author  Nick Sheffield
*
*/

require_once 'config.lib.php';
require_once 'database.lib.php';
require_once 'xss.lib.php';

class Model {

	protected $fields        = array();
	protected $data          = array();
	protected $db            = null;
	public    $primary_key   = 'id';
	public    $table         = '';
	protected $check_fields  = true;

	/**
	*
	*	This method is called automatically when this class is constructed.
	*
	*	@param  string  $table          The table that this model is supposed to represent
	*	@param  boolean $check_fields   Whether or not to create a field whitelist of this table
	*
	*	@return $this
	*
	*/
	function __construct($table = '', $check_fields = true){
		
		$this->check_fields = $check_fields;
		$this->db = new Database(Config::$database);
		
		if($table){
			$this->table = $table;
		}
		
		if($this->table && $this->check_fields){
			$this->load_fields();
		}
		
		return $this;
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
		if($var == 'primary_key'){
			return $this->primary_key;
		} else if(isset($this->data[$var])){
			return XSS::filter($this->data[$var]);
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
		if($var == 'primary_key'){
			$this->primary_key = $val;
		}

		if($this->check_fields){
			if(in_array($var, $this->fields)){
				$this->data[$var] = $val;
				return true;
			}else{
				return false;
			}
		}else{
			$this->data[$var] = $val;
			return true;
		}
	}
	
	/**
	*
	*
	*/
	public function load_fields(){
		$this->fields = Field_Provider::table($this->table, $this->db);
		
		return $this;
	}
	
	/**
	*
	*
	*/
	public function set_table($table){
		$this->table = $table;
		
		return $this;
	}

	/**
	*
	*	Load information from the database table
	*
	*	@param  int   $id The value of the id field in the table
	*
	*	@return $this
	*
	*/
	public function load($data){

		if (!is_array($data) && Model_Provider::has($this->table, $data)) {
			$d = Model_Provider::get($this->table, $data);
			
			$this->fill($d->to_array());
			
			return $d;
		} else {
			
			$this->db->select('*')->from($this->table);
			
			if (is_array($data)) {
				$this->db->where($data);
			} else {
				$this->db->where($this->primary_key, $data);
			}
			
			$result = $this->db->get_one();

			$this->data = $result;
			
			Model_Provider::set($this->table, $this);
			
			return $this;
		}
		
	}

	/**
	*
	*	Fill the data array of this model. Useful for adding data from $_POST quickly
	*
	*	@param  array $data An associative array containing one or more fields => value pairs
	*
	*	@return $this
	*
	*/
	public function fill($data){

		$not_added = array();
		
		if($this->check_fields){
			foreach($data as $key => $value){
				if(in_array($key, $this->fields)){
					$this->data[$key] = $value;
				}else{
					$not_added[$key] = $value;
				}
			}
		}else{
			$this->data = $data;
		}
		
		Model_Provider::set($this->table, $this);

		return $this;
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
		if($this->data[$this->primary_key]){
			$this->data['deleted'] = 1;
			return $this->save();
		}else{
			return false;
		}
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
		if($this->data[$this->primary_key]){
			return $this->db
				->where($this->primary_key, $this->data[$this->primary_key])
				->delete($this->table);
		}else{
			return false;
		}
	}
	
	
	public function hasOne($model, $local_key, $foreign_key){
		$m = new $model();
		
		$m->load([$foreign_key => $this->$local_key]);
		
		return $m;
	}
	
	
	public function hasMany($model, $foreign_key = null, $where = []){
		$c = new Collection();
		
		$id = $this->primary_key;
		
		$c->where($foreign_key, $this->$id);
		$c->where($where);
		
		$c->get($model);
		
		return $c->items;
	}


	public function __TOSTRING(){
		$data = $this->data;
		
		if(!$data){
			$data = [];
		}
		
		foreach($data as $key => $val){
			$data[$key] = XSS::filter($val);
		}
		
		return json_encode($data);
	}
	
	public function to_array(){
		return $this->data;
	}


}


class Field_Provider {
	
	private static $tables = [];
	
	public static function table($name, $db){
		if(self::$tables[$name] === null){
			self::$tables[$name] = $db->get_columns($name);
		}
		
		return self::$tables[$name];
	}	
}

class Model_Provider {
	
	private static $models = [];
	
	public static function set($model_name, $model){
		$id = $model->primary_key;
		self::$models[$model_name][$model->$id] = $model;
	}
	
	public static function has($model_name, $id){
		return isset(self::$models[$model_name][$id]);
	}
	
	public static function get($model_name, $key){
		return self::$models[$model_name][$key];
	}
	
}