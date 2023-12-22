<?php
/*
 * view object trash task
 * views details of an object
 */
namespace BMP\Core;

if (!defined('_w00t_frm')) die('har har har');

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	$to = $_GET['to'];
	if ($to) {	
		include_once('sources/class.trash.php');
		if (is_dir("content/trashed")) {
			$Trash = new Trash;
			$Trash->initTrash();
			if (!$Trash->trashErr) {
				$to_data = $Trash->showObjectDetails('content/trashed/'.$to);
  
                //determine data type in trash file
                if (strpos($to,'case') !== false) { 
                    $type = 'case';
                } elseif (strpos($to,'client') !== false) {
                    $type = 'client';
                } 
                
				if (!$Trash->trashErr) {
					$tk_status = json_encode(array(
					 'status'=>'success',
                     'type'=>$type,
					 'data' => $to_data
					));
					echo $tk_status;
					exit(0);
				} else {
					$scerr = $Trash->trashErr;
				}
			} else {
				$scerr = $Trash->trashErr;
			}
		} else {
			$scerr = 'Trash folder not found';
		}
	}
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
