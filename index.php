<?php
require_once('./admin/config.php'); //provides $db_*, $screen_*, $title, $timeout

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

$show = '';
if (isset($_GET['id'])) {
    $show = $_GET['id'];
}

if(!$show) {

    $html = explode('¤¤', file_get_contents('./list.html'));
    $html_body = $html[0];
    $html_show = $html[1];

    $showresult = $db->query('select `id`,`name` from `show`');

    $shows = '';
    while($show = $showresult->fetch_assoc()) {
        $id = $show['id'];
        
        $keys = array('¤showid', '¤name');
        $values = array($id, $show['name']);
        
        $shows .= str_replace($keys, $values, $html_show);
    }

    $keys = array('¤title', '¤shows');
    $values = array($title, $shows);
    echo str_replace($keys, $values, $html_body);
    exit(0);
}

$html = file_get_contents('./picture.html');

$esc_show = $db->escape_string($show);

$result = $db->query("select `id`from `show` where `id`=$esc_show");

if($result->num_rows != 1) {
    
    $picture = 'invalid';

} else {

    $index = 0;
    if(isset($_COOKIE['index'])) {
        $index = $_COOKIE['index'];
    }
    
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

}

$keys = array('¤show', '¤picture', '¤timeout');
$values = array($show, $picture, $timeout);
echo str_replace($keys, $values, $html);

?>
