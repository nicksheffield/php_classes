PHP Classes
===========

A set of useful php libraries

---

###[Database](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/database.lib.php)

Connect to a database, construct queries, and run them.

---

###[Form](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/form.lib.php)

Create HTML forms.

---

###[XSS](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/xss.lib.php)

Filter HTML content to prevent Cross-Site Scripping attacks

---

###[Cart](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/cart.lib.php)

Manage storing products in a cart.

---

###[Collection](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/collection.lib.php)

Load multiple records from a database table.

---

###[Config](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/config.lib.php)

Store data that can be accessed easily from anywhere in your code.

---

###[Email](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/email.lib.php)

Send an email with PHP.

---

###[Auth](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/auth.lib.php)

Handle user details in the session.

---

###[Upload](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/upload.lib.php)

Handle uploading files.

---

###[URL](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/url.lib.php)

Handle redirecting, and saving/restoring urls.

---

###[Model](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/model.lib.php)

Easily perform CRUD on a database table.

---

###[Input](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/input.lib.php)

Provide an interface for dealing with $_POST content.

---

###[Sticky](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/sticky.lib.php)

Used for filling form fields with prefilled data, or data from the previous request.

---

###[Image](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/image.lib.php)

> Experimental

Make resized copies of images. This library is experemental.

---

###[Route](https://raw.githubusercontent.com/nicksheffield/php_classes/master/libraries/route.lib.php)

Create custom URL routes that point wherever you want. This library is experemental.

If you use this, make sure you include the .htaccess file

```
<Files .htaccess>
	order allow,deny
	deny from all
</Files>
 
RewriteEngine on
 
# Don't use rewrite if its a real file or folder
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
 
# In addition to the above rules, also ignore the index.php 
# file, anything in the assets folder and the robots.txt file
RewriteCond $1 !^(index\.php|assets|robots\.txt)
 
# Route everything else through the index.php file
RewriteRule ^(.*)$ index.php?/$1 [L]
```