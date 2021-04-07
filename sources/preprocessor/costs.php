<?php
/* preprocess some html for select option year list in costs tpl*/
if (!defined('_w00t_frm')) die('har har har');

$CostsOptHTML = '';

if ($dss->startYear && $thisYear) {
	$tmpYear = $thisYear;
	while ($tmpYear >= $dss->startYear) {
		if ($tmpYear == $thisYear) {
			$sel = 'selected="selected"';
		}
		$CostsOptHTML .= '<option value="'.$tmpYear.'" '.$sel.'>'.$tmpYear.'</option>';
		$tmpYear--;
		$sel = '';
	}
} else {
	echo "startYear or thisYear not defined: ";
	echo $dss->startYear,$thisYear;
}
?>
