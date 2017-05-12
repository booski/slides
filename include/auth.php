<?php

$basedir = dirname(__FILE__);
require_once $basedir.'/functions.php';

if(!execute($get_allowed_users)) {
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Databasfel: '.$get_allowed_users->error;
    exit(1);
}

$allowed_users = result($get_allowed_users);

$user = '';
if(isset($_SERVER['REMOTE_USER'])) {
    $user = $_SERVER['REMOTE_USER'];
}

if(empty($allowed_users)) {
    return true;
}

if($user && in_array(array('user' => $user), $allowed_users)) {
    return true;
}

header('Content-Type: text/plain; charset=UTF-8');
echo 'Åtkomst nekad.';
if($user) {
    echo " ($user)";
}
exit(1);
?>
