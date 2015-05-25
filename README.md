PHP Classes
===========

A set of useful php libraries

---

###[Database](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/database.lib.php)

Connect to a database, construct queries, and run them.

Make sure you use the most up to date version of this.

---

###[Form](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/form.lib.php)

Create HTML forms.

---

###[Security](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/security.lib.php)

Filter content for script tags.

---

###[Cart](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/cart.lib.php)

Manage storing products in a cart.

---

###[Collection](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/collection.lib.php)

Load many records from a database table.

---

###[Config](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/config.lib.php)

Store database credentials and other pieces of information for access anywhere in your code.

---

###[Email](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/email.lib.php)

Create and send simple html emails.

---

###[Login](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/login.lib.php)

Handle user details in the session.

---

###[Upload](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/upload.lib.php)

Handle uploading files.

---

###[URL](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/url.lib.php)

Handle redirecting, and saving/restoring urls.

---

###[Model](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/model.lib.php)

Save, Load, Update and Delete records from any database table.

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

---

###[Image](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/image.lib.php)

> Experimental

Load an image that already exists on your server, resize it, and save it as another file.