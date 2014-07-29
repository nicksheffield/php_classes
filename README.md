PHP Classes
===========

A set of useful php libraries

* [Cart library](https://raw.githubusercontent.com/nicksheffield/php_classes/master/cart.lib.php)
* [Config library](https://raw.githubusercontent.com/nicksheffield/php_classes/master/config.lib.php)
* [Database library](https://raw.githubusercontent.com/nicksheffield/php_classes/master/database.lib.php)
* [Form Builder library](https://raw.githubusercontent.com/nicksheffield/php_classes/master/form.lib.php)
* [Hash library](https://raw.githubusercontent.com/nicksheffield/php_classes/master/hash.lib.php)
* [Login library](https://raw.githubusercontent.com/nicksheffield/php_classes/master/login.lib.php)
* [Model library](https://raw.githubusercontent.com/nicksheffield/php_classes/master/model.lib.php)
* [Collection library](https://raw.githubusercontent.com/nicksheffield/php_classes/master/collection.lib.php)
* [File Upload library](https://raw.githubusercontent.com/nicksheffield/php_classes/master/upload.lib.php)

**Example of a model that extends model.lib.php**
```php
<?php

	require_once '../model.lib.php'; # You need this always
	require_once '../hash.lib.php';  # This is just for the example

	class User extends Model{
		protected $table = 'tb_users';

		function __construct(){
			parent::__construct($this->table);
		}

		# Put your own custom methods for this model here
		public function authenticate(){
			$user = $this->db
				->select('id, password')
				->from($this->table)
				->where('username', $this->data['username'])
				->get_one();

			if(Hash::check($this->data['password'],$user['password'])){
				$this->load($user['id']);
				return true;
			}else{
				return false;
			}
		}
	}

?>
```