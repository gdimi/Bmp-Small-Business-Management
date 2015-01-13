<?php
//update cms content
if (!defined('_w00t_frm')) die('har har har');

$scerr = ''; //var to hold error msg. Initialized here so functions below can access it by addr

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    if (isset($_POST['what'])) { $what = $_POST['what']; }
    if (isset($_POST['motd'])) { $motd = substr($_POST['motd'],0,128); } //motd will be 128 chars long
    if (isset($_POST['board'])) { $board = $_POST['board']; }
    if ($what == 'all') {
        $res = UpdateAll($motd,$board,$scerr);
        if ($res === false) { $scerr .= "updated all failed!"; } //because motd * board may return 0, we have to use === operator
    } elseif ($what == 'motd') {
        $res = UpdateMotd($motd,$scerr);
        if ($res === false) { $scerr .= "updated motd failed!"; }
    } elseif ($what == 'board') {
        $res = UpdateBoard($board,$scerr);
        if ($res === false) { $scerr .= "updated board failed!"; }
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

function UpdateMotd($motd,&$scerr) {
    if (file_exists("content/motd")) {
        return $motd = file_put_contents("content/motd",$motd);
    } else {
		$scerr .= "motd file not found!";
		return false;
	}
}

function UpdateBoard($board,&$scerr) {
    if (file_exists("content/board")) {
        return $board = file_put_contents("content/board",$board);
    } else {
		$scerr .= "board file not found!";
		return false;
	}
}

function UpdateAll($motd,$board,&$scerr) {
    $motd = UpdateMotd($motd,$scerr);
    $board = UpdateBoard($board,$scerr);
    return $motd * $board;
}
?>
