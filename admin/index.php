<?php
require_once('./auth.php'); // provides $db

header('Content-Type: text/html; charset=UTF-8');

$htmlfile = './admin.html';

$html = explode('¤¤', file_get_contents($htmlfile));
$html_body = $html[0];
$html_slide = $html[1];

$db->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
$slideresult = $db->query('select `name` from slide');
$db->commit();

$slides = '';

while($slide = $slideresult->fetch_assoc()) {
    
    $slides .= str_replace('¤slide', $slide['name'], $html_slide);
}


print str_replace('¤slides', $slides, $html_body);

?>
