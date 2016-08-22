<?php
require_once('./auth.php');

header('Content-Type: text/html; charset=UTF-8');

$imgdir = '../images/';
$thumbdir = '../thumbs/';

$exts = array(
    'image/gif' => 'gif',
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
);

if(isset($_GET['delete'])) {
    
    $file = $_GET['delete'];
    
    if(!file_exists($imgdir.$file)) {
        echo "File doesn't exist.";
        exit(1);
    }

    unlink($imgdir.$file);
    unlink($thumbdir.$file);
    
} else if(isset($_FILES['uploadfile'])) {
    
    $file = $_FILES['uploadfile'];
    $im = new Imagick($file['tmp_name']);
    $mime = $im->getImageMimeType();

    if($file['error'] != 0) {
        echo 'The file could not be uploaded. (error code '.$file['error'].')';
        exit(1);

    } else if(!array_key_exists($mime, $exts)) {
        echo 'Invalid format ('.$mime.'). Allowed formats are gif, jpg and png.';
        exit(1);
        
    }

    $filename = date('ymd-His').'.'.$exts[$mime];
    $im->writeImage($imgdir.$filename);
    
    $im->scaleImage($thumb_width, $thumb_height, true);
    $im->writeImage($thumbdir.$filename);

}

header('Location: '.$_SERVER['HTTP_REFERER']);

?>
