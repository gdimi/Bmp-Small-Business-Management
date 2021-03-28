<?php
/* preprocess some html for select options for settings tpl*/
if (!defined('_w00t_frm')) die('har har har');

$exclude_from_stats = '';
$show_closed_sel = '<option></option>';
$show_history_sel = '<option></option>';

$yesno = array(
	1 => 'yes',
	0 => 'no',
);

$yesno_truefalse = array(
	'true' => 'yes',
	'false' => 'no',
);

if ($dss->exclude_from_stats ) {
	foreach ($dss->exclude_from_stats as $cid) {
		$cname = $modelHelper->getContactName($cid);
		$exclude_from_stats_labels .= '<span class="bmp-label bmp-label-grey">'.$cid.' ('.$cname.')</span> ';
		$exclude_from_stats .= $cid.',';
	}
}

$selected = '';
foreach ($yesno as $k => $v) {
	$selected=($dss->show_closed == $k) ? ' selected'  : '';
	$show_closed_sel .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
}

$selected = '';
foreach ($yesno_truefalse as $k => $v) {
	$selected=($dss->show_history == $k) ? ' selected'  : '';
	$show_history_sel .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
}

$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
$tz_sel = '<option></option>';
foreach ($tzlist as $k => $v) {
	($v == $dss->timezone) ? $sel = 'selected' : $sel = '';
	$tz_sel .= '<option value="'.$v.'" '.$sel.'>'.$v.'</option>';
}

?>
