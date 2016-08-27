<?php
require_once('./auth.php'); // provides $db, $uldir, $thumb_*
$uldir = '../'.$uldir.'/';

header('Content-Type: text/html; charset=UTF-8');

if(isset($_POST['new_show']) && $_POST['name']) {

    $esc_name = $db->escape_string($_POST['name']);
    $db->query("insert into `show` set `name`='$esc_name'");

} else if(isset($_POST['remove'])) {
    
    $item = $_POST['remove'];
    $from = $_POST['from'];

    if($from === 'slides') {
        delete_slide($item);
        
    } else if($from === 'shows') {
        delete_show(explode('_', $item)[1]);
        
    } else if(explode('_', $from)[0] === 'show') {
        delete_from_show(explode('_', $from)[1], $item);
    }

} else if(isset($_FILES['uploadfile'])) {

    save_upload($_FILES['uploadfile']);

} else if(isset($_POST['add'])) {

    add_slide_to_show($_POST['add'], explode('_', $_POST['to'])[1]);
    
}

header('Location: '.$_SERVER['HTTP_REFERER']);



####### FUNCTIONS #######

function delete_slide($slide) {
    global $uldir, $db;
    
    if(!file_exists($uldir.$slide)) {
        error("Filen '$slide' finns inte.");
        return;
    }

    $db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
    $esc_slide = $db->escape_string($slide);

    $result = $db->query("select `image`,`show` from `show_image` where `image`='$esc_slide'");

    if($result->num_rows == 0) {
        
        if(!$db->query("delete from slide where `name`='$esc_slide'")) {
            
            $error = $db->error;
            $db->close();
            error('Databasfel: '.$error);
            return;
        }
        
        unlink($uldir.$slide);
        unlink($uldir.'thumb_'.$slide);

    } else {
        $i = $result->num_rows;
        error("Bilden används på en eller flera ytor.");
        return;
    }

    $db->commit();
}

function delete_show($show) {
    global $db;

    $esc_show = $db->escape_string($show);

    $db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
    $db->query("delete from `show` where `id`=$esc_show");
    $db->query("delete from `show_image` where `show`=$esc_show");
    $db->commit();
}

function delete_from_show($show, $slide) {
    global $db;

    $esc_show = $db->escape_string($show);
    $esc_slide = $db->escape_String($slide);
    $db->query("delete from `show_image` where `show`=$esc_show and `image`='$esc_slide'");
}

function save_upload($file) {
    global $db, $uldir, $thumb_width, $thumb_height;

    $exts = array(
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    );
    
    if($file['error'] != 0) {
        error('Filen kunde inte laddas upp. (Felkod: '.$file['error'].')');
        return;
    }    
    
    $im = new Imagick($file['tmp_name']);
    $mime = $im->getImageMimeType();
    
    if(!array_key_exists($mime, $exts)) {
        error("Ogiltigt format ($mime). Tillåtna format är gif, jpg och png.");
        return;
    }

    $filename = date('ymd-His').'.'.$exts[$mime];
    $db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
    $esc_filename = $db->escape_string($filename);
    if(! $db->query("insert into `slide` set `name`='$esc_filename'")) {
        error('Databasfel: '.$db->error);
        $db->close();
        return;
    }
    
    $im->writeImage($uldir.$filename);
    $im->scaleImage($thumb_width, $thumb_height, true);
    $im->writeImage($uldir.'thumb_'.$filename);

    $db->commit();
}

function add_slide_to_show($slide, $show) {
    global $db;

    $esc_slide = $db->escape_string($slide);
    $esc_show = $db->escape_string($show);
    $db->query("insert into `show_image` set `image`='$esc_slide',`show`=$esc_show");
}

function error($message) {
    setcookie('error', $message);
}

?>
