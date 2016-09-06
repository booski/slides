<?php
require_once('./admin/config.php'); //provides $screen_*

$uldir = './'.$uldir.'/';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

$special = false;
if(!$_GET['name']) {
    
    $file = './images/placeholder.png';
    $special = true;
    
} else {

    $file = $_GET['name'];

    if(!file_exists($uldir.$file)) {
        $file = './images/error.png';
        $special = true;
    }
}

$im = '';
$file_scaled = $uldir.$screen_width.'_'.$screen_height.'_'.$file;

if($special) {

    $im = new Imagick($file);
    $im->scaleImage($screen_width, $screen_height, true);

} else if(!file_exists($file_scaled)) {
    
    $im = new Imagick($uldir.$file);
    $im->scaleImage($screen_width, $screen_height, true);
    $im->writeImage($file_scaled);
    
} else {

    $im = new Imagick($file_scaled);
}

header('Content-type:', $im->getImageMimeType());
echo $im;

?>
