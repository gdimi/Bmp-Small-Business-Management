<?php
/**
 * filesystem class
 *
 * this file contains the main filesystem class
 * 
 * PHP version 5+
 *
 * LICENCE: This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
 *
 * @category   bmp\sources\classes
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2023 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Since 0.658-dev
 * @deprecated -
 */
namespace BMP\Core;

if (!defined('_w00t_frm')) die('har har har');

Class Settings extends Filesystem 
{
	
	var $validate;
	var $validate_errors;
	var $available_languages;
	var $dss;
	var $thisYear;
	private $config_file;
	private $config_vars;
	
	function __construct($available_languages=array(),$thisYear) {
		$this->validate = false;
		$this->validate_errors = array();
		$this->config_file = 'sources/config.php';
		$this->config_vars = array();
		if (is_array($available_languages) && count($available_languages)) {
			$this->available_languages = $available_languages;
		} else {
			$this->available_languages = array('gr','en');
		}
		$this->thisYear = $thisYear;
	}
	
	private function checkConfigFile() {
		if ($this->config_file) {
			if (is_file($this->config_file)) {
				if (is_writable($this->config_file)) {
					return true;
				} else {
					$this->fsErr = 'Configuration file not writable!';
				}
			} else {
				$this->fsErr = 'Cannot find configuration file!';
			}
		} else {
			$this->fsErr = 'Configuration file not set!';
		}
		
		return false;
	}
	
	private function saveSettings() {
		$config_vars = $this->config_vars;
		//var_dump($config_vars);die();
		$caseType = '';
		foreach ($config_vars['caseType'] as $k => $ctype) {
			$key = $k++;
			$caseType .= "    '$key' => '$ctype', \n";
		}

		$casePriority = '';
		foreach ($config_vars['casePriority'] as $k => $cprior) {
			$key = $k++;
			$casePriority .= "    '$key' => '$cprior', \n";
		}		
		
		$caseStatus = '';
		foreach ($config_vars['caseStatus'] as $k => $cstat) {
			$key = $k++;
			$caseStatus .= "    '$key' => '$cstat', \n";
		}

		$users = explode(',',$config_vars['users']);
		$users_txt = '';
		foreach ($users as $k => $user) {
			$key = $k++;
			$users_txt .= "'$user', \n";
		}
		//var_dump($caseStatus);die();
		$config_data = "<?php".PHP_EOL.
        "namespace BMP\Core;".PHP_EOL.
        "
if (!defined('_w00t_frm')) die('har har har');".PHP_EOL."

class DSConfig {".PHP_EOL."
	var \$timezone = '{$config_vars['timezone']}';
	var \$require_pass = false; //not working
	var \$project_name = '{$config_vars['project_name']}';
	var \$sitename = '{$config_vars['sitename']}';
	var \$motd = ''; //deprecated use cms instead
	var \$show_history = '{$config_vars['show_history']}'; //show history table beneath case tracker
	var \$mailer = 'php mail'; //only valid option is php mail
	var \$mailto = '{$config_vars['mailto']}'; //where to send mails
	var \$mailfrom = '{$config_vars['mailfrom']}'; //from email for mails
	var \$fromname = '{$config_vars['fromname']}'; //from name for mails
	//general smtp settings , not working for now
	var \$sendmail = '/usr/sbin/sendmail'; 
	var \$smtpauth = '0'; 
	var \$smtpsecure = 'none';
	var \$smtpport = '25';
	var \$smtpuser = '';
	var \$smtppass = '';
	var \$smtphost = 'localhost';
	// backup policy for deleted items (not working)
	var \$backup = 'on delete'; //on delete, always, never
	// revisions held for cases (not working)
	var \$revisions = true;
	var \$rev_num = 2;

	var \$debug = false;	 //show debug info
	var \$startYear = {$config_vars['startYear']}; // year start for statistics, expenses and income
	var \$lang = '{$config_vars['lang']}'; //language
    var \$trashWarn = 100; //trash warning size in MB
    var \$trashOWarn = 500; //trash object number warning
	var \$maxUploadSize = 8192; // max upload size in KB
	var \$uploadTypes = array('jpg','jpeg','png','bmp','tiff','gif','docx','doc','pdf','txt','zip','rar');
	var \$show_closed = {$config_vars['show_closed']}; //1 = include closed cases , 0 = only open
	//case types list
	var \$caseType = array(
		{$caseType});
	//priority
	var \$casePriority = array(
		{$casePriority});
	//case statuses
	var \$caseStatus = array(
		{$caseStatus});
	//valid users for user list
    var \$users = array(
        {$users_txt});
	// clients to exclude from stats page (ids)
	var \$exclude_from_stats = array(
		{$config_vars['exclude_from_stats']});
	//general style settings
	var \$style = array(
		'logo'=>'{$config_vars['logo']}', //path to logo file
		'top_bar_bg'=>'{$config_vars['top_bar_bg']}', //top bar bg color
		'left_bar_bg'=>'{$config_vars['left_bar_bg']}', //left bar bg color
                'flat'=>'yes', //new flat style css
	);
".PHP_EOL."
    public function __toString() {
        return '<br />Require password: '.\$this->require_pass.
        '<br />Mailer: '.\$this->mailer.
        '<br />Take backup: '.\$this->backup.
        '<br />Store revisions: '.\$this->revisions.
        '<br />Revision number: '.\$this->rev_num.
        '<br />Trash size warning: '.\$this->trashWarn.
        '<br />Trash objects warning: '.\$this->trashOWarn.
        '<br />Maximum upload size: '.\$this->maxUploadSize.' Kbytes';
    }".PHP_EOL."
}".PHP_EOL;

		$chkcfgfile = $this->checkConfigFile();
		
		if ($chkcfgfile) {
			$wd = $this->safeFileWrite($this->config_file,$config_data);
			
			return $wd;
		} else {
			return false;
		}
			
	}
	
	private function validateSettings($post_vars) {
		//var_dump($this->dss);die();
		$val_vars = array();
		
		$val_vars['timezone'] = (isset($post_vars['timezone']) && !empty($post_vars['timezone'])) ? trim($post_vars['timezone']) : false;
		$val_vars['project_name'] = (isset($post_vars['project_name']) && !empty($post_vars['project_name'])) ? trim($post_vars['project_name']) : false;
		$val_vars['sitename'] = (isset($post_vars['sitename']) && !empty($post_vars['sitename'])) ? trim($post_vars['sitename']) : false;
		$val_vars['show_history'] = (isset($post_vars['show_history']) && !empty($post_vars['show_history'])) ? trim($post_vars['show_history']) : false; //show history table beneath case tracker
		$val_vars['mailto'] = (isset($post_vars['mailto']) && !empty($post_vars['mailto'])) ? trim($post_vars['mailto']) : false; //where to send mails
		$val_vars['mailfrom'] = (isset($post_vars['mailfrom']) && !empty($post_vars['mailfrom'])) ? trim($post_vars['mailfrom']) : false; //from email for mails
		$val_vars['fromname'] = (isset($post_vars['fromname']) && !empty($post_vars['fromname'])) ? trim($post_vars['fromname']) : false; //from name for mails
		$val_vars['startYear'] = (isset($post_vars['startYear']) && !empty($post_vars['startYear'])) ? trim($post_vars['startYear']) : false; // year start for statistics, expenses and income
		$val_vars['lang'] = (isset($post_vars['lang']) && !empty($post_vars['lang'])) ? trim($post_vars['lang']) : false; //language
		$val_vars['show_closed'] = (isset($post_vars['show_closed']) && !empty($post_vars['show_closed'])) ? trim($post_vars['show_closed']) : false; //1 = include closed cases , 0 = only open
		$val_vars['caseType'] = $this->dss->caseType; //trim($post_vars['caseType']);
		$val_vars['casePriority'] = $this->dss->casePriority; //trim($post_vars['casePriority']);
		$val_vars['caseStatus'] = $this->dss->caseStatus; //trim($post_vars['caseStatus']);
		$val_vars['users'] = (isset($post_vars['users']) && !empty($post_vars['users'])) ? trim($post_vars['users']) : false;
		$val_vars['exclude_from_stats'] = trim($post_vars['exclude_from_stats']);
		$val_vars['logo'] = (isset($post_vars['mylogo']) && !empty($post_vars['mylogo'])) ? $post_vars['logo'] = trim($post_vars['mylogo']) : false; //path to logo file
		$val_vars['top_bar_bg'] = trim($post_vars['top_bar_bg']);//top bar bg color
		$val_vars['left_bar_bg'] = trim($post_vars['left_bar_bg']); //left bar bg color
		
		foreach ($val_vars as $k=>$v) {
			if ($v === false) {
				$this->validate_errors[] = "$k cannot be empty";
				$val_vars[$k] = $this->dss->$k; //restore original setting
			}
			
			if ($k == 'mailto' || $k == 'mailfrom') {
				//valid email?
				$regexp='/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
				if(!preg_match($regexp, trim($v))) {
					$this->validate_errors[] = "Invalid email for $k";
					$val_vars[$k] = $this->dss->$k;
				} else {
					$val_vars[$k] = $v;
				}
			}
			
			if ($k == 'startYear') {
				if ((int)$v < 2000 || (int)$v > $this->thisYear) {
					$this->validate_errors[] = 'Start Year ('.$v.') must be between 2000 and current year ('.$thisYear.').';
					$val_vars[$k] = $this->dss->$k;
				}
			}
			
			if ($k == 'lang') {
				if (!in_array($v, $this->available_languages)) {
					$this->validate_errors[] = 'Language ('.$v.') does not exist!';
					$val_vars[$k] = $this->dss->$k;
				}
			}
		}
		
		$this->config_vars = $val_vars;
		
		if (count($this->validate_errors)) {
			return false;
		} else {
			return true;
		}
		
	}
	
	public function processSettings() {
	//var_dump($_POST);
		$res = '';
		$save = '';
		$error = false;
		$post_vars = $_POST;
		$res = $this->validateSettings($post_vars);
		
		//var_dump($this->config_vars);
		
		if ($res === true) {
			$save = $this->saveSettings();
			if ($save) {
				$message = 'Settings saved!';
			} else {
				$error = true;
				$message = $this->fsErr;
			}
		} else {
			$error = $res;
			$message = implode('<br>',$this->validate_errors);
		}
		
		$p_status = array(
			'error' => $error,
			'message'=> $message,
		);
		
		return $p_status;
	}
	
}

?>
