<?php

/*
	
	Query builder class

	usage   : Instantiated
	version : 1
	author  : Nick Sheffield

	=====================================

	Example

		$db = new Database('localhost', 'username', 'password', 'dbname');

	Select

		$users = $db->select('*')
				   ->from('tb_users')
				   ->get();

	Update

		$db->set(array(
				'email' => 'newemail@gmail.com'
			))
		   ->where('id', 1)
		   ->update('tb_users');

	Insert

		$db->set(array(
		   		'email' => 'user@example.com',
		   		'password' => '123'
		   	))
		   ->insert('tb_users');

*/

class Database{

	# Each of these properties stores a part of a query
	private $select = '';
	private $from = '';
	private $order_by = '';
	private $where = '';
	private $where_and = '';
	private $where_or = '';
	private $sets = array();
	private $show_errors = false;

	# This property remembers what the last query we tried was
	public  $last_query = '';
	# This property holds the id of the last inserted item
	public  $last_insert_id = false;
	# This property is our connection to the db
	private $connection = null;

	public function __construct($h, $u, $p, $d){
		# Hostname, Username, Password, Database
		$this->connection = new mysqli($h, $u, $p, $d);

		# If there was an error connecting to the db
		if($this->connection->connect_error){
			# echo the error
			echo '<b>Connection Error:</b> '.$this->connection->connect_error;
			# and quit the php code
			exit;
		}
	}

	# Send a query to the database
	public function query($q){
		# Store the query in the last_query property
		$this->last_query = $q;

		# Send the query to the db, store the result
		$result = $this->connection->query($q);

		$this->last_insert_id = $this->connection->insert_id;

		# If there was a query error
		if($this->connection->error && $this->show_errors){
			# Echo it
			echo '<b>Query Error:</b> '.$this->connection->error;
			echo '<br>'.$q;

			return false;
		}

		# Reset all the query parts to blank
		$this->select = '';
		$this->from = '';
		$this->where = '';
		$this->where_and = '';
		$this->where_or = '';
		$this->order_by = '';
		$this->sets = array();

		# Output the result of the query
		return $result;
	}

	# Write the SELECT statement
	public function select($fields){
		$filtered_fields = $this->connection->real_escape_string($fields);
		$this->select = "SELECT $filtered_fields ";

		return $this;
	}

	# Write the FROM statement
	public function from($table){
		$filtered_table = $this->connection->real_escape_string($table);
		$this->from = " FROM $filtered_table ";

		return $this;
	}

	# Write the ORDER BY Statement.
	# $dir is an optional parameter, defaults to ASC
	public function order_by($field, $dir = 'ASC'){
		$filtered_field = $this->connection->real_escape_string($field);
		$this->order_by = " ORDER BY $filtered_field $dir ";

		return $this;
	}

	# Write a simple WHERE statement
	public function where($field, $value){
		$filtered_field = $this->connection->real_escape_string($field);
		$this->where = " WHERE $filtered_field = '$value' ";

		return $this;
	}

	# Write a simple AND statement
	public function where_and($field, $value){
		$filtered_value = $this->connection->real_escape_string($value);
		$this->where_and .= " AND $field = '$filtered_value' ";

		return $this;
	}

	# Write a simple OR statement
	public function where_or($field, $value){
		$filtered_value = $this->connection->real_escape_string($value);
		$this->where_or .= " OR $field = '$filtered_value' ";

		return $this;
	}

	public function get_result(){
		$q  = $this->select;
		$q .= $this->from;
		$q .= $this->where;
		$q .= $this->where_and;
		$q .= $this->where_or;
		$q .= $this->order_by;

		return $this->query($q);
	}

	# Join all the query parts together, and send it to the db
	public function get(){
		$result = $this->get_result();

		# Change the query result into a usable array
		return $this->assoc($result);
	}

	public function get_one(){
		$result = $this->get();

		return $result[0];
	}

	# Store items into the sets property.
	# this method takes an array, and will merge any new data
	# with any existing data
	public function set($data){
		# If there are any items in sets already
		if(count($this->sets)){
			# merge the arrays
			$this->sets = array_merge($this->sets, $data);
		}else{
			$this->sets = $data;
		}

		return $this;
	}

	# Turn the sets array into a string for the SET statement
	public function make_set(){
		$set = ' SET ';

		foreach($this->sets as $field => $value){
			$filtered_value = $this->connection->real_escape_string($value);
			$set .= " $field = '$filtered_value', ";
		}

		# Chop off the last 2 characters of the SET statement
		return substr($set, 0, -2);
	}

	# Assemble the INSERT query, and send it to the db
	public function insert($table){
		$q  = "INSERT INTO $table ";
		$q .= $this->make_set();

		return $this->query($q);
	}

	# Assemble the UPDATE query and send it to the db
	public function update($table){
		$q  = "UPDATE $table ";
		$q .= $this->make_set();
		$q .= $this->where;

		return $this->query($q);
	}

	# Turn SELECT query results into an associative array
	public function assoc($result_object){
		$rows = array();

		while($item = $result_object->fetch_assoc()){
			$rows[] = $item;
		}

		return $rows;
	}
}