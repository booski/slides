<?php
require_once('./auth.php'); // provides $db, $uldir, $thumb_*

header('Content-Type: text/html; charset=UTF-8');

$exts = array(
    'image/gif' => 'gif',
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
);

if(isset($_GET['delete'])) {
    
    $file = $_GET['delete'];
    
    if(!file_exists($uldir.$file)) {
        echo "File doesn't exist.";
        exit(1);
    }

    $db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
    if(!$db->query('delete from slide where `name`="'.$db->escape_string($file).'"')) {
        echo 'Error: '.$db->error;
        $db->close();
        exit(1);
    }
    unlink($uldir.$file);
    unlink($uldir.'thumb_'.$file);
    $db->commit();
    
} else if(isset($_FILES['uploadfile'])) {

    $file = $_FILES['uploadfile'];

    if($file['error'] != 0) {
        echo 'The file could not be uploaded. (error code '.$file['error'].')';
        exit(1);

    }    
    
    $im = new Imagick($file['tmp_name']);
    $mime = $im->getImageMimeType();
    
    if(!array_key_exists($mime, $exts)) {
        echo 'Invalid format ('.$mime.'). Allowed formats are gif, jpg and png.';
        exit(1);
        
    }

    $filename = date('ymd-His').'.'.$exts[$mime];
    $db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
    if(! $db->query('insert into slide set `name`="'.$db->escape_string($filename).'"')) {
        echo 'Error: '.$db->error;
        $db->close();
        exit(1);
    }
    
    $im->writeImage($uldir.$filename);
    $im->scaleImage($thumb_width, $thumb_height, true);
    $im->writeImage($uldir.'thumb_'.$filename);

    $db->commit();
}

header('Location: '.$_SERVER['HTTP_REFERER']);

?>
