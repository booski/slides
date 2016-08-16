<?php
require_once('./auth.php');

header('Content-Type: text/html; charset=UTF-8');

$dir = '../images/';

$exts = array(
    'image/gif' => 'gif',
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
);

if(isset($_GET['delete'])) {
    
    $file = $_GET['delete'];
    
    if(file_exists($dir.$file)) {
        
        unlink($dir.$file);
        $success = true;
    }
} else if(isset($_FILES['uploadfile'])) {
    
    $file = $_FILES['uploadfile'];
    $mime = getimagesize($file['tmp_name'])['mime'];

    if($file['error'] != 0) {
        echo 'The file could not be uploaded. (error code '.$file['error'].')';

    } else if(array_key_exists($mime, $exts)) {
        
        $filename = date('ymd-His').'.'.$exts[$mime];
        move_uploaded_file($file['tmp_name'], $dir.$filename);
        $success = true;
        
    } else {
        echo 'Invalid format ('.$mime.'). Allowed formats are gif, jpg and png.';
    }
}

if($success) {
    header('Location: '.$_SERVER['HTTP_REFERER']);
}
?>
