<?php
    foreach ($dss->users as $oneuser) {
        $userHTML .= '<option value="'.$oneuser.'">'.$oneuser.'</option>';
    }
?>
<form>
    <strong><?php echo $lang['case-filter-filter']; ?></strong>&nbsp;
	<label for="toggle_closed"><?php echo $lang['case-filter-Closed']; ?></label>
	<input type="checkbox" name="toggle_closed" id="toggle_closed" checked/>
	<label for="toggle_open"><?php echo $lang['case-filter-Open']; ?></label>
	<input type="checkbox" name="toggle_open" id="toggle_open" />
	<label for="toggle_high"><?php echo $lang['case-filter-High']; ?></label>
	<input type="checkbox" name="toggle_high" id="toggle_high" />
	<label for="toggle_user"><?php echo $lang['case-filter-User']; ?></label>
	<select name="toggle_user" id="toggle_user">
		<option></option>
        <?php echo $userHTML; ?>
	</select>
</form>
<script>
$(document).ready(function() {
	//close closed & unfixable cases by default
	$("#ct_table .Closed").toggle('fast');
	$("#ct_table .Unfixable").toggle('fast');
	$("#toggle_closed").change(function(){
		$("#ct_table .Closed").toggle('fast');
		$("#ct_table .Unfixable").toggle('fast');
	});
	$("#toggle_open").change(function(){
		$("#ct_table tr.tbody").filter(':not(.Open)').toggle('fast');
	});
	$("#toggle_high").change(function(){
		$("#ct_table tr.tbody").filter(':not(.High)').toggle('fast');
	});
	$("#toggle_user").change(function(){
		var user = $(this).val();
		$("#ct_table tr.tbody").show(); //IMPROVEME stupid way of resetting display before toggling...
		$("#ct_table tr.tbody").filter(':not(.'+user+')').toggle('fast');
	});
});
</script>
