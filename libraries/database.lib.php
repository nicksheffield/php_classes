<?php

class Database{

	
	private $debug = true;
	private $select;
	private $where;
	private $from;
	private $order;
	private $limit;
	private $group;

	public $last_query;
	public $last_insert_id;
	public $num_updated_rows;

	private $sets = array();
	private $connection = null;
	




	public function __construct($credentials){

		$this->connection = new mysqli(
			$credentials['hostname'],
			$credentials['username'],
			$credentials['password'],
			$credentials['database']
		);

		if($this->connection->errno){
			$this->report_error('There was an error connecting to the database. <br>'.$this->connection->error, true);
		}
	}
	




	public function select($fields){
		$this->select = "SELECT $fields ";

		return $this;
	}
	




	public function where($param1, $param2 = null, $param3 = null, $param4 = null){

		if(is_array($param1)){
			$data       = $param1;
			$use_quotes = is_null($param2) ? true : $param2;
			$or         = is_null($param3) ? false : $param3;
		}else{
			$data       = array($param1 => $param2);
			$use_quotes = is_null($param3) ? true : $param3;
			$or         = is_null($param4) ? false : $param4;
		}

		$this->make_where($data, $use_quotes, $or);

		return $this;
	}
	




	public function where_or($data, $value = null, $use_quotes = true){
		return $this->where($data, $value, $use_quotes, true);
	}
	




	public function where_and($param1, $param2 = null, $param3 = null, $param4 = null){
		return $this->where($param1, $param2, $param3, $param4);
	}
	




	public function order_by($data, $dir = null){
		$order = ' ORDER BY ';

		if(is_array($data)){
			foreach($data as $field => $direction){
				$order .= $field.' '.$direction.', ';
			}

			$this->order = substr($order, 0, -2);
		}else{
			$this->order .= $order.$data.' '.$dir;
		}

		return $this;
	}
	




	public function group_by($data){
		$group = ' GROUP BY ';

		if(is_array($data)){
			foreach($data as $field){
				$group .= $field.', ';
			}

			$this->group = substr($group, 0, -2);
		}else{
			$this->group = $group.$data;
		}

		return $this;
	}
	




	public function from($table){
		$this->from = ' FROM '.$table;

		return $this;
	}
	




	public function join($join){
		if(!strpos($this->where, 'WHERE')){
			$this->where .= ' WHERE '.$join.' ';
		}else{
			$this->where .= ' AND '.$join.' ';
		}

		return $this;
	}
	




	public function limit($from, $count){
		$this->limit = " LIMIT $from, $count ";

		return $this;
	}
	




	public function get(){
		$q  = $this->select;
		$q .= $this->from;
		$q .= $this->where;
		$q .= $this->order;
		$q .= $this->limit;

		$this->reset();

		return $this->assoc($this->run($q));
	}
	




	public function get_one(){
		$result = $this->get(true);
		return $result[0];
	}
	




	public function get_field($field){
		$result = $this->get_one();
		return $result[$field];
	}
	




	public function get_fields($table){
		return $this->get_columns($table);
	}
	




	public function get_columns($table){
		$field_query = 'SELECT column_name FROM information_schema.columns WHERE table_name = "'.$table.'" ORDER BY ordinal_position';
		$result = $this->assoc($this->connection->query($field_query));

		foreach($result as $key => $field){
			$fields[] = $field['column_name'];
		}

		return $fields;
	}
	




	public function set($data, $value = null){
		if(is_array($data)){
			$this->sets = array_merge($data, $this->sets);
		}else{
			if($value != null){
				$this->sets[$data] = $value;
			}
		}

		return $this;
	}
	




	public function insert($table, $data = null){

		if($data != null && is_array($data)){
			$this->set($data);
		}

		if(!count($this->sets)){
			$this->report_error('Database::insert() - No data to insert.', true);
		}

		$insert_query = 'INSERT INTO '.$table.$this->make_set($this->sets);

		$this->sets = array();

		$success = $this->run($insert_query);

		$this->last_insert_id = $this->connection->insert_id;

		return $success;
	}
	




	public function update($table, $where = null, $data = null){

		if($data != null && is_array($data)){
			$this->set($data);
		}

		if(!count($this->sets)){
			$this->report_error('Database::update() - Missing SET clause.', true);
		}

		$update_query = 'UPDATE '.$table.$this->make_set($this->sets);

		if($where != null){
			$update_query .= $this->make_where($where);
		}else if($this->where != null){
			$update_query .= $this->where;
		}else{
			$this->report_error('Database::update() - Missing WHERE clause.');
		}

		$this->reset();

		$success = $this->run($update_query);

		$this->num_updated_rows = $this->connection->affected_rows;

		return $success;
	}
	




	public function delete($table, $where = null){

		# If the supplied $where is supplied ...
		if($where != null){
			# ... then add to the existing WHERE clause
			$this->where .= $this->make_where($where);

		# If $where is not supplied, and there is not an existing WHERE clause...
		}else if($this->where == null){
			# ... report an error
			$this->report_error('Database::delete() - Missing WHERE clause.');
		}

		$delete_query = 'DELETE FROM '.$table.$this->where;

		$this->run($delete_query);

		$this->reset();

		return $this;
	}
	




	private function make_set($data){
		$set = ' SET ';

		foreach($data as $field => $val){
			$val = $this->connection->real_escape_string($val);
			$set .= $field.' = "'.$val.'", ';
		}

		return substr($set, 0, -2);
	}
	




	private function make_where($data, $add_quotes = true, $or = false){

		if(is_array($data)){
			foreach($data as $field => $value){
				$field = trim($field);

				$quotes = $add_quotes ? '"' : '';

				$op = strpos($field, ' ') ? '' : '=';

				$value = $this->connection->real_escape_string($value);

				if(!strpos($this->where, 'WHERE')){
					$this->where .= ' WHERE '.$field.$op.$quotes.$value.$quotes.' ';
				}elseif($or){
					$this->where .= ' OR '.$field.$op.$quotes.$value.$quotes.' ';
				}else{
					$this->where .= ' AND '.$field.$op.$quotes.$value.$quotes.' ';
				}
			}
		}else{
			$this->where = ' WHERE '.$data;
		}

		return $this->where;
	}
	




	private function reset(){
		$this->select = '';
		$this->from   = '';
		$this->where  = '';
		$this->order  = '';
		$this->limit  = '';
		$this->sets   = array();
	}
	




	private function assoc($result){
		$rows = array();

		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$rows[] = $row;
		}
		
		return $rows;
	}
	




	private function run($query){
		$result = $this->connection->query($query);

		$this->last_query = $query;

		if(!$result) $this->report_query_error($query, true);

		return $result;
	}
	




	private function report_query_error($query, $exit = false){
		if($this->debug){
			echo '
				<div style="
					border: 2px red dotted;
					padding: 1em;
					background: pink;
					line-height: 1.5;
					font-family: monospace;
				">
					<b>There is something wrong with this query: </b>
					<br>
					<span style="font-size: 1.2em;">'.$query.'</span>
					<hr>
					<b>Error message:</b>
					<br>
					'.$this->connection->error.'
				</div>
			';
			
			if($exit) exit;
		}
	}
	




	private function report_error($error, $exit = false){
		if($this->debug){
			echo '
				<div style="
					border: 2px red dotted;
					padding: 1em;
					background: pink;
					line-height: 1.5;
					font-family: monospace;
				">
					<b>Database Error: </b>
					'.$error.'
				</div>
			';
			
			if($exit) exit;
		}
	}

}