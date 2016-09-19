<?php
require_once('./auth.php'); // provides $db, $uldir, $thumb_*
$uldir = '../'.$uldir.'/';

header('Content-Type: text/html; charset=UTF-8');

if(isset($_POST['new_show']) && $_POST['name']) {

    $esc_name = $db->escape_string($_POST['name']);
    $db->query("insert into `show` set `name`='$esc_name'");

} else if(isset($_POST['size'])) {

    set_size($_POST['id'], $_POST['width'], $_POST['height']);
    
} else if(isset($_POST['remove'])) {
    
    $item = $_POST['remove'];
    $from = $_POST['from'];

    if($from === 'slides') {
        delete_slide($item);
        
    } else if($from === 'shows') {
        delete_show($item);
        
    } else {
        delete_from_show($from, $item);
    }

} else if(isset($_FILES['uploadfile'])) {

    save_upload($_FILES['uploadfile']);

} else if(isset($_POST['add'])) {

    add_slide_to_show($_POST['add'], $_POST['to']);
    
}

header('Location: '.$_SERVER['HTTP_REFERER']);



####### FUNCTIONS #######

function set_size($show, $width, $height) {
    global $db;

    $esc_show = $db->escape_string($show);
    if($width xor $height) {
        error('Både bredd och höjd måste anges');
        return;
    }

    $width = ltrim($width, '0');
    $height = ltrim($height, '0');
        
    $esc_width = null;
    $esc_height = null;
    if($width && $height) {

        if(!ctype_digit($width)) {
            error('Ogiltig bredd.');
            return;
        }
        
        if(!ctype_digit($height)) {
            error('Ogiltig höjd.');
            return;
        }

        $esc_width = $db->escape_string($width);
        $esc_height = $db->escape_string($height);
        $db->query("update `show` set `width`=$esc_width, `height`=$esc_height where `id`=$esc_show");

    } else {
        $db->query("update `show` set `width`=NULL, `height`=NULL where `id`=$esc_show");
    }
}

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
        array_map('unlink', glob($uldir.'*_'.$slide));

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
