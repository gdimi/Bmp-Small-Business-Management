<?php
if (!defined('_w00t_frm')) die('har har har');

class DSConfig {
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
    
    public function __toString() {
        return '<br />require password: '.$this->require_pass.
        '<br />project name: '.$this->project_name.
        '<br />site name: '.$this->sitename.
        '<br />show history: '.$this->show_history.
        '<br />mailer: '.$this->mailer.
        '<br />mail to: '.$this->mailto.
        '<br />mail from: '.$this->mailfrom.
        '<br />from name: '.$this->fromname.
        '<br />take backup: '.$this->backup.
        '<br />store revisions: '.$this->revisions.
        '<br />revision number: '.$this->rev_num.
        '<br />valid users: '.implode(',',$this->users);
    }
}
?>
