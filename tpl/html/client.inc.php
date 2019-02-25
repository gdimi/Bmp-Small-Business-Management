		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['controls-close']; ?></span>
		<h2><?php echo $lang['client-card']; ?></h2>
		<div>
			<div>
				<form id="ecl_frm" name="ecl-frm" action="index.php?task=eclient&pos=before">
				</form>
			</div>
			<div class="cl-cases"></div>
            <span class="fake-button hl-cl-b" style="margin-bottom:20px;display:inline-block"><?php echo $lang['client-highlight-btn']; ?></span>
		</div>
		<span class="ecl_res"></span>
		<span class="ecl_frm_error"></span>
	<span class="fake-button red-btn cldel" style="display:inline-block"><?php echo $lang['client-delete-btn']; ?></span>
	<span class="fake-button blue-btn cledit" style="display:inline-block"><?php echo $lang['client-edit-btn']; ?></span>
	<span class="elevate menu-dialog" style="display:none;" id="dt_cl">
    <h2><?php echo $lang['client-delete-title']; ?></h2>
    <div id="dt_cl_msg"><?php echo $lang['client-delete-msg']; ?> <strong></strong>-<strong></strong> ?</div><br><br>
    <span id="" class="cl-b b-sblue del-cl-b"><?php echo $lang['client-delete-yes']; ?></span>
    <span onclick="document.getElementById('dt_cl').style.display = 'none';" class="cl-b b-sblue" style="font-size:24px;"><?php echo $lang['client-delete-no']; ?></span>
</span>
<script>
$(document).ready(function() {
	if ($("#client .cldel").css('display') == 'none') {
		$("#client .cldel").show(); //show delete button in case it got hidden during a delete
	}
    //highlight
    $(".hl-cl-b").click(function() {
		var clid = $("#ecl_frm #eclid").val();
		if (clid > 0) {
            var clclass = 'cl-'+clid;
            var clelem = '#ct_table tr.'+clclass;

            $(clelem).css('background-color','#FFFF88');
        }
    });
	//show delete dialog
    $(".cldel").click(function(){
		$("#dt_cl").show();
	});
	//delete client
    $(".del-cl-b").click(function(){
		var clid = returnEndId(this);
		if (clid > 0) {
			$("#client > .ecl_res").append('<img src="images/loader.gif" />');
			$.get("index.php",
			{clid : clid, task: "dclient",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#dt_cl").hide();
					$("#client .cldel").hide();
					$("#client .cledit").hide();
					$("#client #ecl_frm").hide();
					$("#client > .ecl_res").html(data.message);
				} else if(data.status === "error") {
					$("#dt_cl").hide();
					$("#client > .ecl_frm_error").html(data.message);
					$("#client").delay(5000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#client").append(textStatus);
			});
		}
	});
    //edit client
	$("#client .cledit").click(function(){
		var clid = $("#ecl_frm #eclid").val();
		if (clid > 0) {
			var URL = $("#ecl_frm").attr("action");
			var formData = $("#ecl_frm").serializeArray();
			$.post(URL,
			formData,
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#client > .ecl_frm_error").hide();
					$("#client > .ecl_res").html(data.message).delay(2000);
				} else if(data.status === "error") {
					$("#client > .ecl_frm_error").show().html(data.message);
				}

			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#ecl_frm").append(textStatus);
			});
		} else {
			alert('<?php echo $lang['client-edit-error']; ?>');
		}
	});

});
</script>
