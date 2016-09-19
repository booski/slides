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

$replacements = array(
    '¤title' => $title,
    '¤slides' => build_slidelist(),
    '¤shows' => build_showlist(),
    '¤error' => $error,
    '¤visibility' => $visibility
);

print replace($replacements, $html_body);


##### FUNCTIONS #####

function build_slidelist() {
    global $db, $html_slide;
    
    $slideresult = $db->query('select `name` from `slide`');
    
    $slides = '';
    while($slide = $slideresult->fetch_assoc()) {
        
        $replacements = array(
            '¤slide' => $slide['name'],
            '¤group' => 'slides'
        );

        $slides .= replace($replacements, $html_slide);
    }
    
    return $slides;
}

function build_showlist() {
    global $db, $html_show, $thumb_width, $screen_width, $screen_height;

    $showresult = $db->query('select `id`,`name`,`width`,`height` from `show`');

    $shows = '';
    while($show = $showresult->fetch_assoc()) {
        $id = $show['id'];

        $replacements = array(
            '¤showid' => $id,
            '¤name' => $show['name'],
            '¤slides' => build_show($id),
            '¤bwidth' => $thumb_width + 64,
            '¤owidth' => $screen_width,
            '¤oheight' => $screen_height,
            '¤swidth' => $show['width'],
            '¤sheight' => $show['height']
        );
        
        $shows .= replace($replacements, $html_show);
    }

    return $shows;
}

function build_show($id) {
    global $db, $html_slide;

    $esc_id = $db->escape_string($id);
    $slideresult = $db->query("select `image` from `show_image` where `show`='$esc_id' order by `seq`");
    
    $show_slides = '';
    while($show_slide = $slideresult->fetch_assoc()) {

        $replacements = array(
            '¤slide' => $show_slide['image'],
            '¤group' => $id
        );

        $show_slides .= replace($replacements, $html_slide);
    }

    return $show_slides;
}

function replace($assoc_arr, $subject) {
    $keys = array();
    $values = array();

    foreach($assoc_arr as $key => $value) {
        $keys[] = $key;
        $values[] = $value;
    }

    return str_replace($keys, $values, $subject);
}

?>
