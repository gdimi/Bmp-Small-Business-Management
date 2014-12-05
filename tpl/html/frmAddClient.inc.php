<?php
if (!defined('_w00t_frm')) die('har har har');
?>
		<span id="add_help_cl" class="help"></span>
		<form id="new_cl_frm" name="new_cl_frm" action="index.php?task=aclient&pos=before">
			<div id="add_help_data_cl" style="display:none;">
			<span style="display: block;font-size: 1.2em;margin-bottom: 4px;text-align: center;">Heeeelp...</span>
			<span>Name:</span> Everyone has a name..<br /><br />
			<span>Tel1-Tel2:</span> Write down the CORRECT phone number<br /><br />
			<span>email:</span> you know...<br /><br />
			<span>Address:</span> His/her address if needed<br /><br />
			<span>Other Info:</span> Any other stuff like what music he/she likes or if he/she has a pet :P goes here<br /><br />
			<span>Your name:</span> Leave a nickname! Please dont write your full name or any other personal data, this is not a dating service xD <br />
			</div>
			<fieldset>
				<legend>Add a new Client</legend>
				<label for="clName">Name</label>
				<input type="text" name="clName" id="ncl_name" value="" /><br /><br />
				<label for="clTel1">Telephone 1</label>
				<input type="text" name="clTel1" id="ncl_tel1" value="" /><br /><br />
				<label for="clTel2">Telephone 2</label>
				<input type="text" name="clTel2" id="ncl_tel2" value="" /><br /><br />
				<label for="clemail">Email</label>
				<input type="text" name="clemail" id="ncl_email" value="" /><br /><br />
				<label for="clAddress">Address</label>
				<textarea name="clAddress" id="ncl_address" rows="7" cols="35"></textarea><br /><br />
				<label for="clOinfo">Other info</label>
				<textarea name="clOinfo" id="ncl_oinfo" rows="7" cols="35" class="rich"></textarea><br /><br />
			</fieldset><br /><br />
			<input type="hidden" name="pos" id="pos" value="before" />
			<span class="fake-button" id="addclbtn">submit Client!</span><br /><br />
		</form>
		<div id="new_cl_frm_error" style="color:red;border:medium solid red;padding:8px;display:none;"></div>
		<span class="cl-b" onclick="$(this).parent().toggle();">Close me</span>
<script>
$(document).ready(function() {
	$("#add_help_cl").hover(function(){
		$("#add_help_data_cl").slideToggle('fast');
	});
	$("#addclbtn").click(function(){
		var URL = $("#new_cl_frm").attr("action");
		var formData = $("#new_cl_frm").serializeArray();
		var clname = $("#ncl_name").val()
		if (clname != '') {
			$.post(URL,
			formData,
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#new_cl_frm_error").hide();
					$("#new_cl_frm").append(data.message).delay(2000).hide('slow');
					$("#new_client").delay(3000).hide('slow', function() {
						//window.location = 'index.php?action=docache';
					});
					//$("#tkt_success").hide();
				} else if(data.status === "error") {
					$("#new_cl_frm_error").show();
					$("#new_cl_frm_error").html(data.message);
				}

			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#new_cl_frm").append(textStatus);
			});
		} else {
			alert('Re vale onoma pelath!');
		}
	});
});
</script>
