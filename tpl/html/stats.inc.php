<?php
 $oldYear = $dss->startYear;
 for($i = $oldYear; $i <= $thisYear; $i++) {
	$statsselopt .= '<option value="'.strtotime($i.'-01-01-00:00').'">'.$i.'</option>';
 }
?>

<h2 style="display: inline-block;padding-right: 12px;"><?php echo $lang['stats']; ?></h2>
<form name="stats-time" id="stats_time_frm" style="display:inline">
	Χρονολογία <select id="stats_time">
	<option>all</option>
	<?php echo $statsselopt; ?>
	</select>
</form>
<hr size="1" />
<div id="stats_data">

</div>
<div id="statres"></div>
<script>
$(document).ready(function() {
	$('#stats_time_frm').on('change','select',function(){
		//var im = $('#cost_month').val();
		var iy = $('#stats_time').val();
		if (iy != NaN) {
			$("#stats_info > div#statres").append('<img src="images/loader.gif" />');
			$.get("index.php",
			{iy:iy,task: "stats",pos: "before",lang: "<?php echo $activeLanguage; ?>"},
			function(data, textStatus, jqXHR){
				$("#stats_info > div#statres").html('&nbsp;');
				if(data.status === "success") {
					$("#stats_data").html(data.message);
					$("#stats_info > div#statres").hide("fast");
				} else if(data.status === "error") {
					$("#stats_info > div#statres").append(data.message);
					$("#stats_info > div#statres").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#stats_info").show("fast").append(textStatus);
			});
		}
	});
});
</script>
