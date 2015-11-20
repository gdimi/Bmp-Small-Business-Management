<?php
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

    protected function showObjectDetails($obj) {
        
    }

    private function restoreFromTrash() {
        
    }

    protected function emptyTrash() {
        
    }
    
    public function initTrash() {
        $this->countFilesInTrash();
        if (!$this->trashErr) {
            $this->countTrashSize();
        }
    }

}
?>
