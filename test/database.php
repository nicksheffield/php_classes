<?php

require_once '../libraries/database.lib.php';

$db = new Database('localhost', 'root', '', 'login');

$user = $db
	->select('*')
	->from('tb_users')
	->where('id', 2)
	->where_or(array('email' => 'user@example.com'))
	->get_one();

echo $db->last_query;

?>

<pre><?php print_r($user);?></pre>


<?php


// select
$db
	->select('fields')
	->from('table1, table2')
	->where('this', 'that')
	->where('something >', 1)
	->where_or('something <', 2)
	->order_by('date', 'desc')
	->join('table1.id = table2.table1_id')
	->limit(10,10)
	->get();

echo $db->last_query();

?>