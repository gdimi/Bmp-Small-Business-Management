		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['controls-close']; ?></span>
		<h2><?php echo $lang['client-card']; ?></h2>
		<div></div>
<span class="fake-button red-btn cldel" style="display:inline-block"><?php echo $lang['client-delete-btn']; ?></span>

<span class="elevate menu-dialog" style="display:none;" id="dt_cl">
    <h2><?php echo $lang['client-delete-title']; ?></h2>
    <div id="dt_cl_msg"><?php echo $lang['client-delete-msg']; ?> <strong></strong>-<strong></strong> ?</div><br><br>
    <span id="" class="cl-b b-sblue del-cl-b"><?php echo $lang['client-delete-yes']; ?></span>
    <span onclick="document.getElementById('dt_cl').style.display = 'none';" class="cl-b b-sblue" style="font-size:24px;"><?php echo $lang['client-delete-no']; ?></span>
</span>
<script>
$(document).ready(function() {
	$(".cldel").click(function(){
		$("#dt_cl").show();
	});
	$(".del-cl-b").click(function(){
		var clid = returnEndId(this);
		if (clid > 0) {
			$.get("index.php",
			{clid : clid, task: "dclient",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#client > div").html(data.message);
				} else if(data.status === "error") {
					$("#client > div").append(data.message);
					$("#client").delay(5000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#client").append(textStatus);
		});
		}
	});
});
</script>
