<?php
if (!defined('_w00t_frm')) die('har har har');

class DSConfig {

	var $timezone = 'Europe/Athens';
	var $require_pass = false; //not working
	var $project_name = 'Business management pro';
	var $sitename = 'BMP';
	var $motd = ''; //deprecated use cms instead
	var $show_history = 'true'; //show history table beneath case tracker
	var $mailer = 'php mail'; //only valid option is php mail
	var $mailto = 'your@mail.tld'; //where to send mails
	var $mailfrom = 'bmp@yourmail.tld'; //from email for mails
	var $fromname = 'BMP'; //from name for mails
	//general smtp settings , not working for now
	var $sendmail = '/usr/sbin/sendmail'; 
	var $smtpauth = '0'; 
	var $smtpsecure = 'none';
	var $smtpport = '25';
	var $smtpuser = '';
	var $smtppass = '';
	var $smtphost = 'localhost';
	// backup policy for deleted items (not working)
	var $backup = 'on delete'; //on delete, always, never
	// revisions held for cases (not working)
	var $revisions = true;
	var $rev_num = 2;


	var $debug = false;	 //show debug info
	var $startYear = 2014; // year start for statistics, expenses and income
	var $lang = 'gr'; //language
    var $trashWarn = 100; //trash warning size in MB
    var $trashOWarn = 500; //trash object number warning
	var $maxUploadSize = 8192; // max upload size in KB
	var $uploadTypes = array('jpg','jpeg','png','bmp','tiff','gif','docx','doc','pdf','txt','zip','rar');
	var $show_closed = 1; //1 = include closed cases , 0 = only open
	//case types list
	var $caseType = array(
		'1'=>'case type 1',
		'2'=>'case type 2',
		'3'=>'case type 3'
	);
       //priority
	var $casePriority = array(
		'1'=>'Low',
		'2'=>'Medium',
		'3'=>'High'
	);
        //case statuses list
	var $caseStatus = array(
		'1'=>'Open',
		'2'=>'In Progress',
		'3'=>'Frozen',
		'4'=>'Closed',
		'5'=>'Unfixable'
	);
    // users list
    var $users = array(
        'gdimi',
        'vader',
        'spok'
    );

        // clients to exclude from stats page (ids)
        var $exclude_from_stats = array(

        );

    // style options
    var $style = array(
        'logo'=>'', //path to logo file
        'top_bar_bg'=>'dimgrey', //top bar bg color
        'left_bar_bg'=>'brown' //left bar bg color
    );

    public function __toString() {
        return '<br />Require password: '.$this->require_pass.
        '<br />Mailer: '.$this->mailer.
        '<br />Take backup: '.$this->backup.
        '<br />Store revisions: '.$this->revisions.
        '<br />Revision number: '.$this->rev_num.
        '<br />Trash size warning: '.$this->trashWarn.
        '<br />Trash objects warning: '.$this->trashOWarn.
        '<br />Maximum upload size: '.$this->maxUploadSize.' Kbytes';
    }

}
?>
