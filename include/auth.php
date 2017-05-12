<?php

$basedir = dirname(__FILE__);
require_once $basedir.'/functions.php';

$user = '';
if(isset($_SERVER['REMOTE_USER'])) {
    $user = $_SERVER['REMOTE_USER'];
}

if($user && (empty($allowed_users) || in_array($user, $allowed_users))) {
    return true;
}

echo 'Permission denied.';
if($user) {
    echo " ($user)";
}
exit(1);
?>
