<?php
if (!defined('_w00t_frm')) die('har har har');

class DSConfig {
    var $timezone = 'Europe/Athens';
	var $require_pass = false;
	var $project_name = 'Business management pro';
	var $sitename = 'BMP';
	var $motd = ''; //deprecated use cms instead
	var $show_history = 'true';
	var $mailer = 'php mail';
	var $mailto = 'gdimi@hyperworks.gr';
	var $mailfrom = 'bmp@hyperworks.gr';
	var $fromname = 'BMP';
	var $sendmail = '/usr/sbin/sendmail';
	var $smtpauth = '0';
	var $smtpsecure = 'none';
	var $smtpport = '25';
	var $smtpuser = '';
	var $smtppass = '';
	var $smtphost = 'localhost';
	var $backup = 'on delete'; //on delete, always, never
	var $revisions = true;
	var $rev_num = 2;
	var $debug = false;
	var $startYear = 2014;
	var $lang = 'gr';
    var $trashWarn = 100; //trash warning size in MB
    var $trashOWarn = 500; //trash object number warning
    var $maxUploadSize = 8192; // max upload size in KB
    var $uploadTypes = array('jpg','jpeg','png','bmp','tiff','gif','docx','doc','pdf','txt','zip','rar');
    var $show_closed = 1; //1 = include closed cases , 0 = only open
	var $caseType = array(
		'1'=>'case type 1',
		'2'=>'case type 2',
		'3'=>'case type 3'
	);
	var $casePriority = array(
		'1'=>'Low',
		'2'=>'Medium',
		'3'=>'High'
	);
	var $caseStatus = array(
		'1'=>'Open',
		'2'=>'In Progress',
		'3'=>'Frozen',
		'4'=>'Closed',
		'5'=>'Unfixable'
	);
    var $users = array(
        'gdimi',
        'vader',
        'spok'
    );

    var $style = array(
        'logo'=>'', //path to logo file
        'top_bar_bg'=>'yellow', //top bar bg color
        'left_bar_bg'=>'pink' //left bar bg color
    );

    public function __toString() {
        return '<br />Require password: '.$this->require_pass.
        '<br />Timezone: '.$this->timezone.
        '<br />Project name: '.$this->project_name.
        '<br />Site name: '.$this->sitename.
        '<br />Show history: '.$this->show_history.
        '<br />Mailer: '.$this->mailer.
        '<br />Mail to: '.$this->mailto.
        '<br />Mail from: '.$this->mailfrom.
        '<br />From name: '.$this->fromname.
        '<br />Take backup: '.$this->backup.
        '<br />Store revisions: '.$this->revisions.
        '<br />Revision number: '.$this->rev_num.
        '<br />Valid users: '.implode(',',$this->users).
        '<br />Trash size warning: '.$this->trashWarn.
        '<br />Trash objects warning: '.$this->trashOWarn.
        '<br />Maximum upload size: '.$this->maxUploadSize.' Kbytes'.
        '<br />Logo: '.$this->style['logo'].
        '<br />Top bar bg color: '.$this->style['top_bar_bg'].
        '<br />Left bar bg color: '.$this->style['left_bar_bg'];
    }
}
?>
