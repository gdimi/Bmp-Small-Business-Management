<?php
//read cms content
if (!defined('_w00t_frm')) die('har har har');

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    if (file_exists("content/motd")) {
        $motd = file_get_contents("content/motd");
    }
    if (file_exists("content/board")) {
        $board = file_get_contents("content/board");
    }
    $data = array("motd"=>$motd,"board"=>$board);
    $tk_status = json_encode($data);
    echo $tk_status;
    exit(0);
}

if ($scerr) {
	$tk_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $tk_status;
	exit(1);
}
?>
