<?php

require_once('./config.php'); //provides $allowed_users

$user = $_SERVER['REMOTE_USER'];

if(!in_array($user, $allowed_users)) {
    echo 'Permission denied.';
    if($user) {
        echo " ($user)";
    }
    exit(0);
}

?>
