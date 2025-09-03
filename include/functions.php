<?php

$basedir = dirname(__FILE__);
require_once $basedir.'/config.php';
require_once $basedir.'/translations.php';

$html_admin  = get_fragments("$basedir/$language/admin.html");
$html_public = get_fragments("$basedir/$language/list.html");
$html_slide  = get_fragments("$basedir/$language/slide.html");

$uldir = $basedir.'/../uploads/';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
if($db->connect_errno) {
    echo i18n('Failed to connect to db. The error was: {error}', $db->connect_error);
    exit(1);
}

$add_slide       = prepare('insert into `slide`(`name`, `type`) values (?, ?)');
$get_slide       = prepare('select * from `slide` where `id`=?');

$add_show                  = prepare('insert into `show`(`name`) values (?)');
$get_shows                 = prepare('select * from `show`');
$get_show                  = prepare('select * from `show` where `id`=?');
$get_show_slides           = prepare('select * from `show_slide`
                                      where `show`=? order by `seq`');

$get_allowed_users = prepare('select * from `allowed_users`');

if(!do_autoremoval()) {
    echo i18n('Autoremoval failed.');
    exit(2);
}

########## UTILITIES ##########

function prepare($statement) {
    global $db;

    if(!($s = $db->prepare($statement))) {
        echo i18n("Failed to prepare statement: {statement} {errorcode} {error}",
                  $statement,
                  $db->errno,
                  $db->error);
        exit(1);
    }

    return $s;
}

function execute($statement) {
    if(!$statement->execute()) {
        return error(i18n("Database error: {error}", $statement->error));
    }
    return true;
}

function result($statement) {
    return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
}

function begin_trans() {
    global $db;

    $db->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);
}

function commit_trans() {
    global $db;

    $db->commit();
    return true;
}

function revert_trans() {
    global $db;

    $db->rollback();
    return false;
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

function error($message) {
    set_cookie('error', $message);
    return false;
}

function set_cookie($name, $value, $expires=null) {
    $cookieoptions='Secure; SameSite=None; Partitioned';
    $date = 0;
    if($expires !== null) {
        $date = date('D, d M Y H:i:s \G\M\T', $expires);
    }
    header("Set-Cookie: $name=$value; Expires=$date; $cookieoptions");
}

/*
   Takes an html file containing named fragments.
   Returns an associative array on the format array[name]=>fragment.

   Fragments are delimited like this:

   ¤¤ name 1
   fragment 1
   ¤¤ name 2
   fragment 2
   ¤¤ name 3
   fragment 3

   The first delimiter and name ('¤¤ name 1' in the aboce example) can
   be omitted, in which case the first fragment will be assigned the
   name 'base'. All other fragments must be named.

   Throws an exception if:
   - any fragment except the first is missing a name
   - two (or more) fragments share a name
 */
function get_fragments($infile) {
    $out = array();

    $name = 'base';
    $current_fragment = '';
    foreach(file($infile) as $line) {
        if(strpos($line, '¤¤') === 0) {
            $out = try_adding($name, $current_fragment, $out, $infile);
            $current_fragment = '';
            $name = trim($line, "\t\n\r ¤");
        } else {
            $current_fragment .= $line;
        }
    }

    return try_adding($name, $current_fragment, $out, $infile);
}

function try_adding($key, $value, $array, $filename) {
    if(array_key_exists($key, $array)) {
        $error = 'There is already a fragment with that name in '.$filename;
        throw new Exception($error);
    } else if($key === '') {
        throw new Exception('There is an unnamed fragment in '.$filename);
    }

    $array[$key] = $value;

    return $array;
}

function i18n($string, ...$args) {
    global $language, $i18n;
    return $i18n[$string][$language](...$args);
}

########## PRESENTATION ##########

##### PUBLIC #####

function build_public_showlist() {
    global $html_public, $get_shows, $get_show_slides;
    global $screen_width, $screen_height, $timeout;

    if(!execute($get_shows)) {
        return false;
    }

    $shows = '';
    foreach(result($get_shows) as $show) {
        $id = $show['id'];

        $get_show_slides->bind_param('i', $id);
        if(!execute($get_show_slides)) {
            return false;
        }

        $slides = result($get_show_slides);
        $lines = count($slides);
        $slideid = 0;
        if($lines != 0) {
            $slideid = $slides[0]['slide'];
        }

        $width = $screen_width;
        $height = $screen_height;
        if($show['width']) {
            $width = $show['width'];
            $height = $show['height'];
        }

        if($show['timeout']) {
            $timeout = $show['timeout'];
        }

        $replacements = array(
            '¤showid'  => $show['id'],
            '¤slide'   => $slideid,
            '¤name'    => $show['name'],
            '¤width'   => $width,
            '¤height'  => $height,
            '¤timeout' => $timeout,
        );

        $shows .= replace($replacements, $html_public['show']);
    }
    return $shows;
}

function build_public_page() {
    global $html_public, $title;

    $replacements = array(
        '¤title' => $title,
        '¤shows' => build_public_showlist(),
    );

    return replace($replacements, $html_public['base']);
}

function build_public_slide($showid) {
    global $html_slide;

    return replace(array(
        '¤content' => build_slide($showid)
    ), $html_slide['base']);

}

function build_slide($showid) {
    global $get_show, $get_slide, $timeout;
    $time = time();

    $get_show->bind_param('i', $showid);
    if(!execute($get_show)) {
        return false;
    }

    $show = result($get_show);

    if(count($show) != 1) {
        return false;
    }

    $show = $show[0];

    $index = 0;
    if(isset($_COOKIE['index'])) {
        $index = $_COOKIE['index'];
    }

    $timeout_temp = $show['timeout'];
    if($timeout_temp) {
        $timeout = $timeout_temp;
    }

    $get_active_slides = prepare(
        'select * from `show_slide` where `show`=? and
             (`starttime`<? or `starttime` is null)
             order by `seq`');
    $get_active_slides->bind_param('ii', $showid, $time);
    if(!execute($get_active_slides)) {
        return false;
    }

    $slides = result($get_active_slides);
    $lines = count($slides);

    if($lines == 0) {
        return build_image($showid, '', $timeout);
    } else {
        if($index >= $lines) {
            $index = 0;
        }

        set_cookie('index', $index+1);
    }

    $slideid = $slides[$index]['slide'];

    $get_slide->bind_param('i', $slideid);
    if(!execute($get_slide)) {
        return false;
    }

    $slide = result($get_slide);
    if(count($slide) != 1) {
        return false;
    }

    $slide = $slide[0];
    $type = $slide['type'];

    switch($type) {
        case 'video':
            return build_video($slide['name'], get_dimensions($showid));
            break;
        case 'image':
            return build_image($showid, $slide['id'], $timeout);
            break;
        default:
            return build_image($showid, 'invalid', $timeout);
            break;
    }
}

function build_image($showid, $slideid, $timeout) {
    global $html_slide;

    $replacements = array(
        '¤showid'  => $showid,
        '¤slideid' => $slideid,
        '¤timeout' => $timeout,
    );

    return replace($replacements, $html_slide['image']);
}

function build_video($videosrc, $showdim) {
    global $html_slide, $uldir;

    $replacements = array(
        '¤video'  => $videosrc,
        '¤thumb'  => $videosrc.'.png',
        '¤width'  => $showdim['x'],
        '¤height' => $showdim['y'],
    );

    return replace($replacements, $html_slide['video']);
}

function build_show_slide($showid, $slideid) {
    global $uldir, $get_slide;

    $dim = get_dimensions($showid);

    if(!$slideid) {
        return create_image($dim['x'],
                            $dim['y'],
                            'black',
                            'gray',
                            $dim['x'].' x '.$dim['y']);
    }

    $get_slide->bind_param('i', $slideid);
    if(!execute($get_slide)) {
        return create_image($dim['x'],
                            $dim['y'],
                            'darkred',
                            'white',
                            ":(\nDatabasfel");
    }

    $slide = result($get_slide);
    if(count($slide) != 1) {
        return create_image($dim['x'],
                            $dim['y'],
                            'darkred',
                            'white',
                            i18n(":(\nDatabase error"));
    }
    $slide = $slide[0];

    $type = $slide['type'];
    $file = $slide['name'];

    if($type == 'video') {
        $file = $file.'.png';
    }

    if(!file_exists($uldir.$file)) {
        return create_image($dim['x'],
                            $dim['y'],
                            'darkred',
                            'white',
                            i18n(":(\nNot found"));
    }

    $file_scaled = $uldir.$dim['x'].'_'.$dim['y'].'_'.$file;

    if(!file_exists($file_scaled)) {

        $im = new Imagick($uldir.$file);
        $im->scaleImage($dim['x'], $dim['y'], true);
        $im->writeImage($file_scaled);
        return $im;
    }

    return new Imagick($file_scaled);

}

function get_dimensions($showid) {
    global $screen_width, $screen_height;
    global $thumb_width, $thumb_height;
    global $get_show;

    $dim = array(
        'x' => $screen_width,
        'y' => $screen_height
    );

    if($showid == 'thumb') {

        $dim['x'] = $thumb_width;
        $dim['y'] = $thumb_height;
        return $dim;
    }

    $get_show->bind_param('i', $showid);
    if(!execute($get_show)) {
        return false;
    }

    $show = result($get_show);

    if(count($show) != 1) {
        return false;
    }

    $show = $show[0];

    if ($show['width'] && $show['height']) {

        $dim['x'] = $show['width'];
        $dim['y'] = $show['height'];
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
    $im->shaveImage(3, 3);
    $im->borderImage($textcolor, 3, 3);

    return $im;
}


##### ADMIN #####

function build_admin_page() {
    global $html_admin, $user, $title;

    $error = '';
    if(isset($_COOKIE['error'])) {
        $error = $_COOKIE['error'];
    }

    $visibility = 'hidden';
    if($error) {
        $visibility = 'visible';
        set_cookie('error', '', time() - 3600);
    }

    $replacements = array(
        '¤title'        => $title,
        '¤slides'       => build_slidelist(),
        '¤shows'        => build_showlist(),
        '¤username'     => $user,
        '¤allowedusers' => get_allowed_users(),
        '¤error'        => $error,
        '¤visibility'   => $visibility,
    );

    return replace($replacements, $html_admin['base']);
}

function build_slidelist() {
    global $html_admin;

    $get_slides = prepare('select * from `slide`');
    if(!execute($get_slides)) {
        return false;
    }

    $slides = '';
    foreach(result($get_slides) as $slide) {

        $slidepath = '../uploads/'.$slide['name'];
        $thumbpath = $slidepath;
        if($slide['type'] == 'video') {
            $thumbpath .= '.png';
        }

        $replacements = array(
            '¤slideid'  => $slide['id'],
            '¤thumbpath' => $thumbpath,
            '¤slidepath' => $slidepath,
            '¤hidden' => 'hidden',
        );

        $slides .= replace($replacements, $html_admin['slide']);
    }

    return $slides;
}

function build_showlist() {
    global $html_admin, $thumb_width, $screen_width, $screen_height, $timeout;
    global $get_shows;

    if(!execute($get_shows)) {
        return false;
    }

    $shows = '';
    foreach(result($get_shows) as $show) {
        $id = $show['id'];

        $swidth = $show['width'];
        $sheight = $show['height'];
        $stime = $show['timeout'];

        $image = 'settings_inactive.svg';
        if($swidth || $sheight || $stime) {
            $image = 'settings_active.svg';
        }

        $replacements = array(
            '¤showid'  => $id,
            '¤name'    => $show['name'],
            '¤slides'  => build_show($id),
            '¤bwidth'  => max($thumb_width + 50, 100),
            '¤owidth'  => $screen_width,
            '¤oheight' => $screen_height,
            '¤swidth'  => $swidth,
            '¤sheight' => $sheight,
            '¤otime'   => $timeout,
            '¤stime'   => $stime,
            '¤image'   => $image,
        );

        $shows .= replace($replacements, $html_admin['show']);
    }

    return $shows;
}

function build_show($id) {
    global $html_admin, $get_show_slides, $get_slide;

    $get_show_slides->bind_param('i', $id);
    if(!execute($get_show_slides)) {
        return false;
    }

    $show = '';
    foreach(result($get_show_slides) as $slide) {
        $active = 'hidden';

        $starttime = $slide['starttime'];
        if($starttime) {
            $starttime = date("Y-m-d", $starttime);
            $active = '';
        }
        $endtime = $slide['endtime'];
        if($endtime) {
            $endtime = date("Y-m-d", $endtime);
            $active = '';
        }
        $autodelete = $slide['autodelete'];
        if($autodelete) {
            $autodelete = 'checked';
            $active = '';
        } else {
            $autodelete = '';
        }

        $get_slide->bind_param('i', $slide['slide']);
        if(!execute($get_slide)) {
            return false;
        }

        $slide = result($get_slide);
        if(count($slide) != 1) {
            return false;
        }
        $slide = $slide[0];

        $slidetype = $slide['type'];
        $slidepath = '../uploads/'.$slide['name'];
        $thumbpath = $slidepath;
        if($slidetype == 'video') {
            $thumbpath .= '.png';
        }

        $replacements = array(
            '¤slideid'  => $slide['id'],
            '¤thumbpath' => $thumbpath,
            '¤slidepath' => $slidepath,
            '¤showid'   => $id,
            '¤sstarttime' => $starttime,
            '¤sendtime' => $endtime,
            '¤sautodelete_checked' => $autodelete,
            '¤active'   => $active,
            '¤type'     => $slidetype,
            '¤hidden'   => '',
        );

        $show .= replace($replacements, $html_admin['slide']);
    }

    return $show;
}

function get_allowed_users() {
    global $get_allowed_users;

    if(!execute($get_allowed_users)) {
        return false;
    }

    $userlist = '';
    foreach(result($get_allowed_users) as $line) {
        $userlist .= $line['user'] . "\n";
    }

    return $userlist;
}


########## ACTIONS ##########

function set_allowed_users($users) {
    global $user, $get_allowed_users;

    $newlist = preg_split('/[\s,;]+/', $users);

    if(!in_array($user, $newlist)) {
        $newlist[] = $user;
    }

    if(!execute($get_allowed_users)) {
        return false;
    }

    $oldlist = result($get_allowed_users);

    $dellist = array();
    foreach($oldlist as $line) {
        $u = $line['user'];
        if(!in_array($u, $newlist)) {
            $dellist[] = $u;
        }
    }

    $addlist = array();
    foreach($newlist as $u) {
        if(!in_array(array('user' => $u), $oldlist)) {
            $addlist[] = $u;
        }
    }

    begin_trans();
    $add_allowed_user = prepare('insert into `allowed_users`(`user`)
                                     values (?)');
    $add_allowed_user->bind_param('s', $adduser);
    foreach($addlist as $adduser) {

        if($adduser && !execute($add_allowed_user)) {
            return revert_trans();
        }
    }

    $del_allowed_user = prepare('delete from `allowed_users` where `user`=?');
    $del_allowed_user->bind_param('s', $deluser);
    foreach($dellist as $deluser) {
        if(!execute($del_allowed_user)) {
            return revert_trans();
        }
    }

    return commit_trans();
}

function create_show($showname) {
    global $add_show;

    if(!$showname) {
        error(i18n('The slideshow must have a name.'));
        return false;
    }

    $add_show->bind_param('s', $showname);
    return execute($add_show);
}

function set_size($showid, $width, $height) {
    if($width xor $height) {
        error(i18n('Both width and height are mandatory.'));
        return false;
    }

    $width = ltrim($width, '0');
    $height = ltrim($height, '0');

    if($width && $height) {

        if(!ctype_digit($width)) {
            error(i18n('Invalid width.'));
            return false;
        }

        if(!ctype_digit($height)) {
            error(i18n('Invalid height.'));
            return false;
        }
    } else {
        $width = NULL;
        $height = NULL;
    }

    $set_show_size = prepare('update `show` set `width`=?, `height`=?
                              where `id`=?');
    $set_show_size->bind_param('iii', $width, $height, $showid);
    return execute($set_show_size);
}

function set_timeout($showid, $timeout) {
    $timeout = strval($timeout);
    if($timeout === '') {
        $timeout = NULL;
    } else if(!ctype_digit($timeout)) {
        error(i18n('Invalid time.'));
        return false;
    }

    $set_show_timeout = prepare('update `show` set `timeout`=?
                                 where `id`=?');
    $set_show_timeout->bind_param('ii', $timeout, $showid);
    return execute($set_show_timeout);
}

function copy_show($oldshow_id, $newname) {
    global $add_show, $get_show, $get_show_slides, $get_slide;

    begin_trans();

    $add_show->bind_param('s', $newname);
    execute($add_show);
    $newshow_id = $add_show->insert_id;

    $get_show->bind_param('i', $oldshow_id);
    execute($get_show);
    $oldshow = result($get_show)[0];

    set_size($newshow_id, $oldshow['width'], $oldshow['height']);
    set_timeout($newshow_id, $oldshow['timeout']);

    $get_show_slides->bind_param('i', $oldshow_id);
    execute($get_show_slides);
    foreach(result($get_show_slides) as $show_slide) {
        $get_slide->bind_param('i', $show_slide['slide']);
        execute($get_slide);
        $slide = result($get_slide)[0];
        $id = $slide['id'];
        $endtime = $show_slide['endtime'];
        $autodelete = $show_slide['autodelete'];
        add_slide_to_show($id, $newshow_id);
        set_autoremoval($newshow_id, $id, $endtime, $autodelete);
    }
    return commit_trans();
}

function set_starttime($showid, $slideid, $starttime) {
    $set_show_slide_starttime = prepare('update `show_slide` set `starttime`=?
                                             where `show`=? and `slide`=?');
    $set_show_slide_starttime->bind_param('iii',
                                          $starttime,
                                          $showid,
                                          $slideid);
    return execute($set_show_slide_starttime);
}

function set_autoremoval($showid, $slideid, $endtime, $autodelete) {
    $set_show_slide_autoremove = prepare('update `show_slide`
                                              set `endtime`=?, `autodelete`=?
                                          where `show`=? and `slide`=?');
    $set_show_slide_autoremove->bind_param('iiii',
                                           $endtime,
                                           $autodelete,
                                           $showid,
                                           $slideid);
    return execute($set_show_slide_autoremove);
}

function do_autoremoval() {
    $time = time();
    $get_slides_to_delete = prepare('select `slide` from `show_slide`
                                     where `endtime`<?
                                         and `autodelete`=true');
    $get_slides_to_delete->bind_param('i', $time);
    execute($get_slides_to_delete);
    $autodelete_candidates = result($get_slides_to_delete);

    $do_show_slide_autoremove = prepare('delete from `show_slide`
                                         where `endtime`<?');
    $do_show_slide_autoremove->bind_param('i', $time);
    $result = execute($do_show_slide_autoremove);
    if(!$result) {
        return false;
    }

    foreach($autodelete_candidates as $candidate) {
        delete_slide($candidate['slide'], true);
    }

    return true;
}

function delete_slide($slideid, $quiet=false) {
    global $get_slide, $uldir;

    begin_trans();
    $get_slide_usage = prepare('select `show` from `show_slide`
                                where `slide`=?');
    $get_slide_usage->bind_param('i', $slideid);
    if(!execute($get_slide_usage)) {
        return revert_trans();
    }

    if(count(result($get_slide_usage)) != 0) {
        if(!$quiet) {
            return error(
                i18n("The picture is in use in one or more slideshows."));
        } else {
            return false;
        }
    }

    $get_slide->bind_param('i', $slideid);
    if(!execute($get_slide)) {
        return revert_trans();
    }

    $slide = result($get_slide);
    if(count($slide) != 1) {
        return revert_trans();
    }
    $slide = $slide[0];

    $slidename = $slide['name'];
    $slidetype = $slide['type'];

    $del_slide = prepare('delete from slide where `id`=?');
    $del_slide->bind_param('i', $slideid);
    if(!execute($del_slide)) {
        return revert_trans();
    }

    unlink($uldir.$slidename);
    if($slidetype == 'video') {
        $slidename .= '.png';
        unlink($uldir.$slidename);
    }

    array_map('unlink', glob($uldir.'*_'.$slidename));
    return commit_trans();
}

function delete_show($showid) {
    begin_trans();
    $del_show_slides = prepare('delete from `show_slide` where `show`=?');
    $del_show_slides->bind_param('i', $showid);
    if(!execute($del_show_slides)) {
        return revert_trans();
    }

    $del_show = prepare('delete from `show` where `id`=?');
    $del_show->bind_param('i', $showid);
    if(!execute($del_show)) {
        return revert_trans();
    }
    return commit_trans();
}

function add_slide_to_show($slideid, $showid) {
    $add_show_slide = prepare('insert into `show_slide`(`show`, `slide`)
                                   values (?, ?)');
    $add_show_slide->bind_param('ii', $showid, $slideid);
    return execute($add_show_slide);
}

function delete_from_show($showid, $slideid) {
    $del_show_slide = prepare('delete from `show_slide`
                               where `show`=? and `slide`=?');
    $del_show_slide->bind_param('ii', $showid, $slideid);
    return execute($del_show_slide);
}

function save_upload($file) {
    if($file['error'] != 0) {
        return error(
            i18n('The file could not be uploaded. (Error code: {error})',
                 $file['error']));
    }

    $filepath = $file['tmp_name'];
    $finfo = new finfo();
    $mime = $finfo->file($filepath, FILEINFO_MIME_TYPE);

    $halfmime = explode('/', $mime)[0];

    if($halfmime == 'image') {
        return save_image($filepath, $mime);
    }

    if($halfmime == 'video') {
        return save_video($filepath, $mime);
    }

    return error(i18n('Invalid file type {mime}', $mime));
}

function save_image($image, $mime) {
    global $uldir, $add_slide;

    $exts = array(
        'image/gif'  => 'gif',
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
    );

    if(!array_key_exists($mime, $exts)) {
        $out = join(', ', $exts);
        $out = preg_replace('/, ([^,]+)$/', ' och \1', $out);
        return error(
            i18n('Invalid format {mime}. Allowed formats are {formats}',
                 $mime,
                 $out));
    }

    try {
        $im = new Imagick($image);

    } catch(Exception $e) {
        return error(i18n('Image could not be read. (Error message: {error})',
                          $e->getMessage()));
    }

    $filename = date('ymd-His').'.'.$exts[$mime];

    $type = 'image';
    $add_slide->bind_param('ss', $filename, $type);
    begin_trans();
    if(!execute($add_slide)) {
        return revert_trans();
    }

    $im->writeImage($uldir.$filename);
    return commit_trans();
}

function save_video($video, $mime) {
    global $uldir, $add_slide;

    $time = date('ymd-His');

    $filename = $time.'.mp4';
    $filepath = $uldir.$filename;
    $cmdstring = 'ffmpeg -n -xerror -loglevel error -i '.$video
                .' -vcodec h264 -an '.$filepath;

    $out = array();
    $result = null;
    exec($cmdstring, $out, $result);

    if(count($out) != 0) {
        unlink($filepath);
        $out = join('<br/>', $out);
        return error(i18n('Could not save video. {error} {errorcode}',
                          $out,
                          $result));
    }

    $thumbname = $filename.'.png';
    $thumbpath = $uldir.$thumbname;
    $thumbstring = 'ffmpeg -n -xerror -loglevel error -i '.$filepath
                  .' -vframes 1 '.$thumbpath;

    $out = array();
    $result = null;
    exec($thumbstring, $out, $result);

    if(count($out) != 0) {
        unlink($filepath);
        unlink($thumbpath);
        $out = join('<br/>', $out);
        return error(i18n('File could not be saved. {error} {errorcode}',
                          $out,
                          $result));
    }

    $im = new Imagick($thumbpath);
    $width = $im->getImageWidth();
    $height = $im->getImageHeight();

    $draw = new ImagickDraw();
    $draw->setFontSize(min($width, $height)/5);
    $draw->setFillColor(new ImagickPixel('white'));
    $draw->setTextAntialias(true);
    $draw->setGravity(Imagick::GRAVITY_CENTER);

    $im->annotateImage($draw, 0, 0, 0, 'Video');
    $im->writeImage();

    $type = 'video';
    $add_slide->bind_param('ss', $filename, $type);
    begin_trans();
    if(!execute($add_slide)) {
        unlink($filepath);
        unlink($thumbpath);
        return revert_trans();
    }

    return commit_trans();
}
?>
