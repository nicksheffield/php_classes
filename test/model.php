<?php


require_once '../config.lib.php';

Config::$username = 'ns_cmsuser';
Config::$password = 'cms123';
Config::$database = 'ns_cms';

require_once '../model.lib.php';
require_once '../hash.lib.php';

# Create

$user = new Model('tb_users');

$user->email = 'bob';
$user->password = Hash::encrypt('123', Hash::salt());
$user->salt     = Hash::salt();

$user->save();



# Modify

$user->email = 'bob2';

$user->save();



# Read

echo 'Email: '.$user->email.'<br>';
echo 'Password: '.$user->password.'<br>';
echo 'Salt: '.$user->salt.'<br>';



# Delete

$user->delete();