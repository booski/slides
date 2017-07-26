<?php

require_once './include/functions.php';

$showid = '';
if(isset($_GET['showid'])) {
    $showid = $_GET['showid'];
}

$slideid = '';
if(isset($_GET['slideid'])) {
    $slideid = $_GET['slideid'];
}

$im = build_show_slide($showid, $slideid);
header('Content-type:', $im->getImageMimeType());
echo $im;
exit(0);

?>
