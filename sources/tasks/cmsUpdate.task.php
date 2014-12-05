<?php
//read cms content
if (!defined('_w00t_frm')) die('har har har');
 
if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    if (isset($_POST['what'])) { $what = $_POST['what']; }
    if (isset($_POST['motd'])) { $motd = substr($_POST['motd'],0,128); }
    if (isset($_POST['board'])) { $board = $_POST['board']; }
    if ($what == 'all') {
        UpdateAll($motd,$board);
    } elseif ($what == 'motd') {
        UpdateMotd($motd);
    } elseif ($what == 'board') {
        UpdateBoard($board);
    } else {
        $scerr = 'Uknown directive: '.$what;
    }
}


if ($scerr) {
    $tk_status = json_encode(array(
     'status' => 'error',
     'message'=> $scerr.'<br />'
    ));
    echo $tk_status;
    exit(1);
} else {
    $tk_status = json_encode(array(
    'status'=>'success',
    'message'=>'ok'
    ));
    echo $tk_status;
    exit(0);
}

function UpdateMotd($motd) {
    if (file_exists("content/motd")) {
        $motd = file_put_contents("content/motd",$motd);
    }
}

function UpdateBoard($board) {
    if (file_exists("content/board")) {
        $board = file_put_contents("content/board",$board);
    }
}

function UpdateAll($motd,$board) {
    UpdateMotd($motd);
    UpdateBoard($board);
}
?>
