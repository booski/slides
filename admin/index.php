<?php
require_once('./auth.php'); // provides $db, $title, $thumb_width

header('Content-Type: text/html; charset=UTF-8');

$htmlfile = './admin.html';

$html = explode('¤¤', file_get_contents($htmlfile));
$html_body = $html[0];
$html_slide = $html[1];
$html_show = $html[2];

$error = '';
if(isset($_COOKIE['error'])) {
    $error = $_COOKIE['error'];
}

$visibility = 'hidden';
if($error) {
    $visibility = 'visible';
    setcookie('error', '', time() - 3600);
}

$keys = array('¤title', '¤slides', '¤shows', '¤error', '¤visibility');
$values = array($title, build_slidelist(), build_showlist(), $error, $visibility);

print str_replace($keys, $values, $html_body);


##### FUNCTIONS #####

function build_slidelist() {
    global $db, $html_slide;
    
    $slideresult = $db->query('select `name` from `slide`');
    
    $slides = '';
    while($slide = $slideresult->fetch_assoc()) {
        
        $keys = array('¤slide', '¤group');
        $values = array($slide['name'], 'slides');
        $slides .= str_replace($keys, $values, $html_slide);
    }
    
    return $slides;
}

function build_showlist() {
    global $db, $html_show, $thumb_width;

    $showresult = $db->query('select `id`,`name` from `show`');

    $shows = '';
    while($show = $showresult->fetch_assoc()) {
        $id = $show['id'];
        
        $keys = array('¤showid', '¤name', '¤slides', '¤width');
        $values = array($id, $show['name'], build_show($id), $thumb_width+64);
        
        $shows .= str_replace($keys, $values, $html_show);
    }

    return $shows;
}

function build_show($id) {
    global $db, $html_slide;

    $slideresult = $db->query("select `image` from `show_image` where `show`='$id' order by `seq`");
    
    $show_slides = '';
    while($show_slide = $slideresult->fetch_assoc()) {

        $keys = array('¤slide', '¤group');
        $values = array($show_slide['image'], $id);

        $show_slides .= str_replace($keys, $values, $html_slide);
    }

    return $show_slides;
}

?>
