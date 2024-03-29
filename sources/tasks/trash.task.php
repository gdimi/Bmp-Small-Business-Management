<?php
/*
 * main trash task
 * only views number of files
 * reports size
 * shows list of objects in trash
 */
namespace BMP\Core; 

if (!defined('_w00t_frm')) die('har har har');

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    
    $tfData = '';
    $tfColor = '';
    
    include_once('sources/class.trash.php');
    if (is_dir("content/trashed")) {
        $Trash = new Trash;
        $Trash->initTrash();
        if (!$Trash->trashErr) {
            $trashFiles = $Trash->showObjectList();
            foreach ($trashFiles as $trashFile) {
				$to_class = str_replace("content/trashed/","",$trashFile);
                if (strpos($to_class,'case') === false) {
                    $tfColor = 'style="background-color: slategray"';
                }
                $tfData .= '<li><a href="javascript:void(0);" alt="View" title="View" class="to '.$to_class.'" '.$tfColor.'>'.$to_class.'</a></li>';
            }
            
            $tfData = '<ul id="trash_files">'.$tfData.'</ul>';
    
            $tk_status = json_encode(array(
             'status'=>'success',
             'files' => $tfData,
             'size'=> $Trash->trashSize,
             'nofiles'=>$Trash->trashFiles
            ));
            echo $tk_status;
            exit(0);
        } else {
            $scerr = $Trash->trashErr;
        }
    } else {
        $scerr = 'Trash folder not found';
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
