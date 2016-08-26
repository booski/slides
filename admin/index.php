<?php
require_once('./auth.php'); // provides $db

header('Content-Type: text/html; charset=UTF-8');

$htmlfile = './admin.html';

$html = explode('¤¤', file_get_contents($htmlfile));
$html_body = $html[0];
$html_slide = $html[1];
$html_show = $html[2];

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
    global $db, $html_show;

    $showresult = $db->query('select `id`,`name` from `show`');

    $shows = '';
    while($show = $showresult->fetch_assoc()) {
        $id = $show['id'];
        
        $keys = array('¤showid', '¤name', '¤slides');
        $values = array($id, $show['name'], build_show($id));
        
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

$keys = array('¤slides', '¤shows', '¤error');
$values = array(build_slidelist(), build_showlist(), $_GET['error']);

print str_replace($keys, $values, $html_body);

?>
