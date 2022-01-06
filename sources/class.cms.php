<?php
namespace BMP\Core;

if (!defined('_w00t_frm')) die('har har har');

class Cms {
    public $motd;
    public $board;
    
    function getMotd() {
        if (file_exists("content/motd")) {
            $this->motd = trim(file_get_contents("content/motd"));
        } else {
            $this->motd = 'no file found';
        }
    }
    
    function setMotd($motd) {
        if ($motd) {
            file_put_contents("content/motd",$motd);
            $this->motd = trim($motd);
        }
    }
    
    function readBoard() {
    /* # = comment do not show
     * ---other directives---
     * postit: makes a postit
     * sos: makes an SOS message
     * ------styling------
     * new: adds a new sign to a postit
     * bg: sets background color (any css color will do)
     * fg: sets foreground color (any css color will do)
     * -------instructions-------
     * place directive and styling in []
     * works only in single lines
     * examples: 
     * [postit] test postit #this will show a postit with "test postit as content"
     * [postit bg=pink fg=lightgreen] mpitzis einai flwraki kai o fanis xameno kormi
     * [postit new]auto einai ena neo postit
     * [sos] check this asap!! # this will make an sos sign in the middle of the screen (aproximately)
     * */
     $boardFile = "content/board";
     $boardContents = file($boardFile);
     $boardHTML = '';
     $css = '';
     $dcvExploded = array();
	 $closeJS = '<span class="cl-b" onclick="$(this).parent().toggle();">Close me</span>'; 
     foreach ($boardContents as $line) {
		 if ($line[0] == '[') { //check if this line has a directive
			$directiva = substr($line,1,strpos($line,']')); //isolate that directive
			$directiva = str_replace(array('[',']'),'',$directiva); //remove [] from it
			$dcvTXT = str_replace(array('[',']',$directiva),'',$line); //get content
			$directiva = trim($directiva); //trim possible spaces
			$directiva = str_replace(" ",",",$directiva); // prepare for explode
			$dcvExploded = explode(",",$directiva);
			foreach ($dcvExploded as $dcv) {
				if ($dcv == 'sos') {
					$boardHTML .= '<div class="sos">'.$dcvTXT.' '.$closeJS.'</div>';
				} elseif ($dcv == 'postit') {
					if (count($dcvExploded) > 1) { //if there are params, # of array elems is > 1
						foreach ($dcvExploded as $param) { // loop through array to find them
							$tstyle = '';
							$pstyle = '';
							if ($param[0] == 'f') {
								$tstyle = str_replace("=",":",$param); // so that fg=black becomes fg:black
								$tstyle = str_replace("fg","color",$tstyle); // fg:black -> color:black
								$tstyle = $tstyle.';'; //color:black -> color:black; valid css rule
								$css .= $tstyle; 
							} elseif ($param[0] == 'b') {
								$tstyle = str_replace("=",":",$param); 
								$tstyle = str_replace("bg","background",$tstyle);
								$tstyle = $tstyle.';';
								$css .= $tstyle; 
							} elseif ($param[0] == 'n') {
								$pstyle .= ' new ';
							}
						}
						$boardHTML .= '<span class="postit'.$pstyle.'" style="'.$css.'">'.$dcvTXT.'</span>';
						$css = '';
						$pstyle = '';
					} else {
						$boardHTML .= '<span class="postit">'.$dcvTXT.'</span>';
					}
				}
			}
		 }
	 }
	 $this->board = $boardHTML;
    }
    
    function writeBoard() {
        
    }
}

?>
