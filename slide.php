<?php

require_once './include/functions.php';

$show = '';
if(!isset($_GET['show'])) {
    return false;
}

$show = $_GET['show'];
$dim = get_dimensions($show);

$file = '';
if(isset($_GET['name'])) {
    $file = $_GET['name'];
}

$im = '';
if(!$file) {
    
    $im = create_image($dim['x'], $dim['y'], 'black', 'gray', $dim['x'].' x '.$dim['y']);
    
} else if(!file_exists($uldir.$file)) {

    $im = create_image($dim['x'], $dim['y'], 'darkred', 'white', ":(\nNot found");
    
} else {

    $file_scaled = $uldir.$dim['x'].'_'.$dim['y'].'_'.$file;

    if(!file_exists($file_scaled)) {
    
        $im = new Imagick($uldir.$file);
        $im->scaleImage($dim['x'], $dim['y'], true);
        $im->writeImage($file_scaled);
    
    } else {
        
        $im = new Imagick($file_scaled);
    }
}

header('Content-type:', $im->getImageMimeType());
echo $im;
exit(0);

?>
