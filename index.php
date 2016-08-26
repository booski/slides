<?php
require_once('./admin/config.php'); //provides $screen_*

$html = file_get_contents('./picture.html');

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

$show = $_GET['id'];
$index = $_COOKIE['index'];

if(!$show) {
    echo "No show specified.";
    exit(0);
}

if(!$index) {
    $index = 0;
}

$esc_show = $db->escape_string($show);
$result = $db->query("select `image` from `show_image` where `show`=$esc_show order by `seq`");
    
$lines = $result->num_rows;
if($lines == 0) {
    
    $picture = '';
    
} else {

    if($index >= $lines) {
        $index = 0;
    }
    $result->data_seek($index);
    $picture = $result->fetch_assoc()['image'];
    
}

setcookie('index', $index+1);

echo str_replace('¤picture', $picture, $html);

?>
