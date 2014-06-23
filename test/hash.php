<?php

require_once '../hash.lib.php';

$pw = 'aaabbbccc';

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<style>
		body{ font-family: Helvetica; color: #333; line-height: 1.5; }
		td{ padding: 0.5em; border: 1px solid #ddd; }
	</style>
</head>
<body>
	<h1>Hash Test</h1>
	
	<table>
		<tr>
			<td>Password</td>
			<td><?=$pw?></td>
		</tr>
		<tr>
			<td>Salt</td>
			<td><?=Hash::salt()?></td>
		</tr>
		<tr>
			<td>Encrypted Password</td>
			<td><?=Hash::encrypt($pw, Hash::salt())?></td>
		</tr>
		<tr>
			<td>Full Hash</td>
			<td><?=Hash::make($pw)?></td>
		</tr>

		<tr>
			<td>Check 123</td>
			<td><?=Hash::verify($pw, Hash::make($pw)) ? 'true' : 'false';?></td>
		</tr>

		<tr>
			<td>Check 1234</td>
			<td><?=Hash::verify('1234', Hash::make($pw)) ? 'true' : 'false';?></td>
		</tr>
		
	</table>
	
</body>
</html>