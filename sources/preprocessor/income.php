<?php
/* preprocess some html for select option year list in income tpl*/
if (!defined('_w00t_frm')) die('har har har');

if ($dss->startYear && $thisYear) {
	$tmpYear = $thisYear;
	while ($tmpYear >= $dss->startYear) {
		if ($tmpYear == $thisYear) {
			$sel = 'selected="selected"';
		}
		$IncOptHTML .= '<option value="'.$tmpYear.'" '.$sel.'>'.$tmpYear.'</option>';
		$tmpYear--;
		$sel = '';
	}
} else {
	echo "startYear or thisYear not defined: ";
	echo $dss->startYear,$thisYear;
}
?> 
