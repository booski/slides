<?php

require_once './include/functions.php';

header('Content-Type: text/html; charset=UTF-8');

if(isset($_GET['id'])) {

    echo build_public_slide($_GET['id']);
    exit(0);

} else {

    echo build_public_page();
    exit(0);
}

?>
