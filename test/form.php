<?php
require '../libraries/form.lib.php';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	
	<?= Form::open('add_comment.php') ?>

		<?= Form::label('for', 'text', ['class' => 'class']) ?> 
		<?= Form::input('type', 'name', "mine's blog", ['class' => 'class']) ?> 
		<?= Form::text('name', 'value', ['class' => 'class']) ?> 
		<?= Form::hidden('name', 'value', ['class' => 'class']) ?> 
		<?= Form::password('name', 'value', ['class' => 'class']) ?> 
		<?= Form::range('name', 'value', 'min', 'max', 'step', ['class' => 'class']) ?> 
		<?= Form::textarea('name', 'value', ['class' => 'class']) ?> 

	<?= Form::close() ?>

</body>
</html>