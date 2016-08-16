<?php

$user = $_SERVER['REMOTE_USER'];
$allowed_users = array_slice(file('./users.php', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES), 1, -1);

if(!in_array($user, $allowed_users)) {
    echo 'Permission denied.';
    exit(0);
}

?>
