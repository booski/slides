<?php

require_once '../include/auth.php';

header('Content-Type: text/html; charset=UTF-8');

if(isset($_POST['action'])) {
    switch($_POST['action']) {

        case 'upload_file':
            save_upload($_FILES['uploadfile']);
            break;

        case 'create_show':
            create_show($_POST['name']);
            break;

        case 'add_slide_to_show':
            add_slide_to_show($_POST['add'],
                              $_POST['to']);
            break;

        case 'remove':
            $item = $_POST['remove'];
            $from = $_POST['from'];

            if($from === 'slides') {
                delete_slide($item);

            } else if($from === 'shows') {
                delete_show($item);

            } else {
                delete_from_show($from, $item);
            }
            break;

        case 'configure_slide':
            $showid = $_POST['showid'];
            $slideid = $_POST['slideid'];

            $starttime_date = $_POST['starttime'];
            $starttime_ts = NULL;
            if($starttime_date) {
                $starttime_ts = date_format(
                    date_create_from_format("Y-m-d H:i",
                                            "$starttime_date 00:00"), 'U');
                if(!$starttime_ts) {
                    error("Ogiltigt startdatum.");
                    break;
                }
            }

            $endtime_date = $_POST['endtime'];
            $endtime_ts = NULL;
            if($endtime_date) {
                $endtime_ts = date_format(
                    date_create_from_format("Y-m-d H:i",
                                            "$endtime_date 23:59"), 'U');
                if(!$endtime_ts) {
                    error("Ogiltigt slutdatum.");
                    break;
                }
            }

            $autodelete = false;
            if(isset($_POST['autodelete'])) {
                $autodelete = true;
            }

            set_starttime($showid, $slideid, $starttime_ts);
            set_autoremoval($showid, $slideid, $endtime_ts, $autodelete);
            break;

        case 'configure_show':
            $id = $_POST['showid'];

            set_size($id,
                     $_POST['width'],
                     $_POST['height']);

            set_timeout($id, $_POST['timeout']);

            $copy = trim($_POST['copy']);
            if($copy) {
                copy_show($_POST['showid'],
                          $_POST['copy']);
            }
            break;

        case 'configure_security':
            set_allowed_users($_POST['userlist']);
            break;

        default:
            break;
    }
}

header('Location: '.$_SERVER['HTTP_REFERER']);

?>
