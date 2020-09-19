<?php
/**
 * class to handle trash
 *
 * this file contains the class that handles trashed files
 * 
 * PHP version 5.6+
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
 *
 * @category   bmp\sources\classes
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2020 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      0.4
 * @deprecated -
 */

if (!defined('_w00t_frm')) die('har har har');

class Trash {

    private $trashFolder;
    private $trashFilesArr;
    public $trashFiles;
    public $trashSize;
    public $trashErr;
    
	function __construct() {
        $this->trashFolder = 'content/trashed/';
		$this->trashSize = 0;
		$this->trashFiles = 0; 
		$this->trashErr = '';
	}

    protected function countFilesInTrash() {
        $files = glob($this->trashFolder . '*');

        if ( $files !== false ) {
            foreach ($files as $file) {
                if (strpos($file,'html') === false) {
                    $this->trashFilesArr[] = $file;
                }
            }
            $this->trashFiles = count( $files ) - 1;
        } else {
            $this->trashErr = 'No files fould in trash!';
        }
    }

    protected function countTrashSize() {
        foreach ($this->trashFilesArr as $num=>$actualFile) {
            if (strpos($actualFile,'html') === false) {
                $this->trashSize += filesize($actualFile);
            }
        }
    }

    public function showObjectList() {
        return $this->trashFilesArr;
    }

    public function showObjectDetails($obj) {
		if (is_file($obj)) {
			return file_get_contents($obj);
		} else {
			$this->trashErr = 'Object not found!';
		}
    }

    public function restoreFromTrash($to) {
        $type = '';

        if (is_readable($file)) {
            //get - decode file
            $to_data = $this->showObjectDetails('content/trashed/'.$to);
                                
            //determine data type in trash file
            if (strpos($to,'case') !== false) { 
                $type = 'case';
            } elseif (strpos($to,'client') !== false) {
                $type = 'client';
            } 

            if ($type) {
                $to_data = json_decode($to_data,true); //return assoc array
                return $this->restoreObject($to_data,$type);
            }
        } else {
            $this->trashErr = 'Cannot find file';
            return false;
        }

    }

    private function restoreObject($to_data,$type) {
        if (is_array($to_data && count($to_data))) {
            require_once('config.php');
            $dss = new DSconfig;

            $ttype = $dss->caseType;
            $tstatus = $dss->caseStatus;
            $tpriority = $dss->casePriority;

            $trestored = date('Y-m-d H:i',time());//put human readable date instead of timestamp

            $newTicket = "\n
            [title: ${to_data['title']}]\n
            [date: ${to_data['created']}]\n
            [tag: ${to_data['category']}]\n
            [model: ${to_data['model']}]\n
            [info: ${to_data['info']}]\n
            [client: ${to_data['clientID']}]\n
            [FLAGS: ${tpriority[$ticket['priority']]},${ttype[$ticket['type']]},${tstatus[$ticket['status']]}]\n
            [price: ${ticket['price']}]\n
            [Follow: ${ticket['follow']}]\n
            [name: ${ticket['user']}]\n";
            //[FLAGS:${ticket['priority']},${ticket['type']},${ticket['status']}]\n
            
            $ahistory = "$tcreated ${ticket['user']} restored case <strong> ${ticket['title']} </strong> \n";

            //prepare mail
            $to      = $dss->mailto;
            $subject = 'new ticket for '.$dss->sitename;
            $headers = 'From: '.$dss->mailfrom . "\r\n" .
                'Reply-To: '.$dss->mailto . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            try {
                $sccon = new PDO('sqlite:pld/HyperLAB.db3');
                $sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                $insert ='INSERT INTO "Case" (id,title, model, info, clientID, category, priority, type, status, created, updated , user, price, follow, closed) VALUES (:id, :title, :model, :info, :clientID, :category, :priority, :type, :status, :created, :updated, :user, :price, :follow, :closed)';
                $sth = $sccon->prepare($insert);
                $scres = $sth->execute($ticket);
                if ($scres) {
                    $schtml = 'Case <strong>'.$ticket['title'].'</strong> added successfuly';
                    $tk_status = json_encode(array(
                        'status' => 'success',
                        'message'=> $schtml
                    ));
                    mail($to,$subject,$newTicket,$headers); //send notification mail
                    file_put_contents('content/action_history.txt',$ahistory,FILE_APPEND); //update history file
                    echo $tk_status;
                    exit(0);
                }
            } catch(PDOException $ex) {
                $tk_status = json_encode(array(
                'status' => 'error',
                'message'=> $ex->getMessage()
                ));
            }
            
            if (is_object($ex) && method_exists($ex,getMessage)) {
                mail($to, $subject, $ex->getMessage(), $headers);
            } else {
                mail($to, $subject, $tk_status, $headers);
            }

        }

    }

    protected function emptyTrash() {
        $trashErrors = '';
        foreach ($this->trashFilesArr as $trashfile) {
            if (!unlink($trashFile)) $trashErrors .= "Cannot delete ".$file."\n";
        }

        if ($trashErrors) {
            $this->trashErr = $trashErrors;
            return false;
        } else {
            return true;
        }
    }

    protected function deleteFromTrash($file) {
        if (is_readable($file)) {
            if (unlink($file)) {
                $this->TrashErr = '';
                return true;
            } else {
                $this->trashErr = 'Cannot delete file '.$file;
                return false;
            }
        } else {
            $this->trashErr = $file.' is not found or is not readable';
            return false;
        }
    }
    
    public function initTrash() {
        $this->countFilesInTrash();
        if (!$this->trashErr) {
            $this->countTrashSize();
        }
    }

}
?>
