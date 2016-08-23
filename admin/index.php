<?php
require_once('./auth.php'); // provides $db

header('Content-Type: text/html; charset=UTF-8');

$htmlfile = './admin.html';

$html = explode('¤¤', file_get_contents($htmlfile));
$html_body = $html[0];
$html_slide = $html[1];
$html_show = $html[2];

$db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
$slideresult = $db->query('select `name` from `slide`');
$db->commit();

$slides = '';
while($slide = $slideresult->fetch_assoc()) {
    
    $slides .= str_replace('¤slide', $slide['name'], $html_slide);
}

$db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
$showresult = $db->query('select `id`,`name` from `show`');
$db->commit();

$shows = '';
while($show = $showresult->fetch_assoc()) {

    $db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
    $id = $show['id'];
    $slideresult = $db->query("select `image` from `show_image` where `show`='$id'");
    $db->commit();

    $slides = '';
    while($slide = $slideresult->fetch_assoc()) {
        $slides .= str_replace('¤slide', $slide['image'], $html_slide);
    }

    $keys = array('¤name', '¤slides');
    $values = array($show['name'], $slides);
    $shows .= str_replace('¤name', $show['name'], $html_show);
}

$keys = array('¤slides', '¤shows');
$values = array($slides, $shows);

print str_replace($keys, $values, $html_body);

?>
