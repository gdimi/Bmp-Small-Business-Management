<?php
/*
 * main trash task
 * only views number of files
 * reports size
 * shows list of objects in trash
 */
if (!defined('_w00t_frm')) die('har har har');

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    if (is_dir("content/trashed")) {
        $Trash = new Trash;
        $Trash->initTrash();
        if (!$Trash->trashErr) {
            echo $Trash->trashSize;
            echo $Trash->trashFiles;
        } else {
            echo $Trash->trashErr;
        }
    } else {
        $scerr = 'Trash folder not found';
    }

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
