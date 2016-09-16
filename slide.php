<?php
require_once('./admin/config.php'); //provides $screen_*, $thumb_*

$uldir = './'.$uldir.'/';

$file = '';
if(isset($_GET['name'])) {
    $file = $_GET['name'];
}

$show = '';
if(isset($_GET['show'])) {
    $show = $_GET['show'];
}

$dim = get_dimensions($show);

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
return 0;

######## FUNCTIONS ########

function get_dimensions($show) {

    global $screen_width, $screen_height;
    $dim = array(
        'x' => $screen_width,
        'y' => $screen_height
    );
    
    if($show == 'thumb') {

        global $thumb_width, $thumb_height;
        
        $dim['x'] = $thumb_width;
        $dim['y'] = $thumb_height;

        return $dim;
    }

    global $db_host, $db_user, $db_pass, $db_name;
    $db = new mysqli($db_host, $db_user, $db_pass, $db_name);

    $esc_show = $db->escape_string($show);
    $result = $db->query("select `width`, `height` from `slide` where `id`='$esc_show'");

    if($result->num_rows == 1) {

        $show_dim = $result->fetch_assoc();

        if ($show_dim['width'] && $show_dim['height']) {
            
            $dim['x'] = $show_dim['width'];
            $dim['y'] = $show_dim['height'];
        }
    }
    
    return $dim;
}

function create_image($width, $height, $bgcolor, $textcolor, $text) {

    $draw = new ImagickDraw();
    $draw->setFontSize(min($width, $height)/5);
    $draw->setFillColor(new ImagickPixel($textcolor));
    $draw->setTextAntialias(true);
    $draw->setGravity(Imagick::GRAVITY_CENTER);
    
    $im = new Imagick();
    $im->newImage($width, $height, $bgcolor, 'png');
    $im->annotateImage($draw, 0, 0, 0, $text);

    return $im;
}

?>
