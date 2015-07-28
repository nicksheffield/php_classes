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
require_once 'collection.lib.php';

class Model {

	public    $fields        = array();
	public    $primary_key   = 'id';
	public    $table         = '';
	protected $data          = array();
	protected $db            = null;
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
		if(method_exists($this, $var)){
			return $this->$var();
		}else if(isset($this->data[$var])){
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
			
		$this->db->select('*')->from($this->table);
		
		if (is_array($data)) {
			$this->db->where($data);
		} else {
			$this->db->where($this->primary_key, $data);
		}
		
		$q = $this->db->build_query();
		
		if (Model_Provider::has($q)) {
			$this->db->reset();
			
			$obj = Model_Provider::get($q);
			// echo "<div class='alert alert-success'>$q</div>";
			
			$this->fill($obj->to_array());
			
			return $obj;
		} else {
			$result = $this->db->get_one();

			$this->data = $result;
			
			Model_Provider::set($this->db->last_query, $this);
			
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
	
	
	public function hasOne($model, $foreign_key = null, $local_key = null){
		$m = new $model();
		
		# assume the local_key
		if(is_null($foreign_key)){
			$foreign_key = strtolower(get_class($this)).'_id';
		}
		
		if(is_null($local_key)){
			$local_key = $m->primary_key;
		}
		
		$m->load([$foreign_key => $this->$local_key]);
		
		return $m;
	}
	

	public function belongsTo($model, $local_key = null, $parent_key = null){
		$m = new $model();
		
		if(is_null($parent_key)){
			$parent_key = strtolower($model).'_id';
		}
		
		if(is_null($local_key)){
			$local_key = $m->primary_key;
		}
		
		$m->load([$local_key => $this->$parent_key]);
		
		return $m;
	}
	
	
	public function hasMany($model, $foreign_key = null, $where = []){
		$c = new Collection();
		
		if(is_null($foreign_key)){
			$foreign_key = strtolower($model)+'_id';
		}
		
		$id = $this->primary_key;
		
		$c->where($foreign_key, $this->$id);
		$c->where($where);
		
		$c->get($model);
		
		return $c->items;
	}
	
	public function belongsToMany($model, $join_table, $this_id = null, $that_id = null, $where = []){
		
		$m = new $model();
		
		if(is_null($this_id)){
			$this_id = strtolower(get_class($this)).'_id';
		}
		
		if(is_null($that_id)){
			$that_id = strtolower($model).'_id';
		}
		
		$id = $this->primary_key;
		
		$fields = [];
		
		foreach($m->fields as $field){
			$fields[] = $m->table.'.'.$field;
		}
		
		$this->db
			->select(implode(', ', $fields))
			->from(implode(', ', [$join_table, $m->table, $this->table]))
			->where($m->table.'.'.$m->primary_key, $join_table.'.'.$that_id, false)
			->where($this->table.'.'.$this->primary_key, $join_table.'.'.$this_id, false)
			->where($this->table.'.'.$this->primary_key, $this->$id)
			->where($where);
			
		$q = $this->db->build_query();
		
		if(Model_Provider::has($q)){
			// echo "<div class='alert alert-success'>$q</div>";
			return Model_Provider::get($q);
		} else {
			$results = $this->db->get();
			$items = [];
			
			foreach($results as $result){
				$m = new $model();
				
				$m->fill($result);
				
				$items[] = $m;
			}
			
			Model_Provider::set($q, $items);
			
			return $items;
		}
		
		
	}


	public function to_array(){
		$data = $this->data;
		
		if(!$data){
			$data = [];
		}
		
		foreach($data as $key => $val){
			$data[$key] = XSS::filter($val);
		}
		
		return $this->data;
	}
	
	public function __TOSTRING(){
		return json_encode($this->to_array());
	}
	
	public function to_json(){
		return $this->__TOSTRING();
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
	
	private static $queries = [];
	
	public static function set($query, $data){
		self::$queries[$query] = $data;
	}
	
	public static function has($query){
		return isset(self::$queries[$query]);
	}
	
	public static function get($query){
		return self::$queries[$query];
	}
	
}