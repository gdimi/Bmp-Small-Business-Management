<?php
if (!defined('_w00t_frm')) die('har har har');
?>		<span id="add_help" class="help"></span>
		<form id="new_tk_frm" name="new_tk_frm" action="index.php?task=atk&pos=before">
			<div id="add_help_data">
			<span style="    display: block;font-size: 1.2em;margin-bottom: 4px;text-align: center;">Heeeelp...</span>
			<span>Title:</span> A title we all can make sense of. 255 chars max<br /><br />
			<span>model:</span> To model i Serial Number i service tag 255 chars max. An den exei i den to 3eroume vazoume mia "-"<br /><br />
			<span>Additional info:</span> Any other info about the error goes here. Dont write the story of your life, 4096chars max<br /><br />
			<span>Client:</span> Auto complete field<br /><br />
			<span>Category:</span> Use your imagination (eg LAB meaning ..LAB! onsite etc). There are no standar categories here, categories work as a tagging system.<br /><br />
			<span>Priority:</span> High,Medium,Low pretty self explaining.<br /><br />
			<span>Type:</span> The usual..ta gnosta..<br /><br />
			<span>Status:</span> Open,Close,In progress,Frozen you can figure it out. Unfixable means either cannot be fixed or client doesnt want to fix it.<br /><br />
			<span>Your name:</span> Leave a nickname! Please dont write your full name or any other personal data, this is not a dating service xD <br />
			</div>
			<fieldset>
				<legend><?php echo $lang['case-add']; ?></legend>
				<label for="title"><?php echo $lang['case-add-title']; ?></label>
				<input type="text" name="title" id="ntk_title" value="" size="38" required  pattern=".{5,}" /><br /><br />
				<!--<label for="date">Date</label>
				<input type="text" name="date"  id="ntk_date" value="" />-->
				<label for="model"><?php echo $lang['case-add-model']; ?></label>
				<input type="text" name="model" id="ntk_model" value="" size="38" /><br /><br />
				<label for="info"><?php echo $lang['case-add-info']; ?></label>
				<textarea name="info" id="ntk_info" rows="10" cols="43" class="rich" required></textarea><br /><br />
				<!--<label for="fix">How to fix it</label>
				<textarea name="fix" id="ntk_fix"></textarea><br /><br />-->
				<label for="client"><?php echo $lang['case-add-client']; ?></label>
				<input type="text" name="client" id="ntk_client" value="" required /><br />
                <input type="hidden" name="cid" id="cid" value="" />
				<div id="ntk_client_res" style="display:none;"></div><br />
				<label for="cat"><?php echo $lang['case-add-tag']; ?></label>
				<input type="text" name="cat" id="ntk_cat" value="" /><br /><br />
				<label for="priority"><?php echo $lang['case-add-priority']; ?></label>
				<select name="priority" id="ntk_priority" required>
					<option></option>
					<option value="3">High</option>
					<option value="2">Medium</option>
					<option value="1">Low</option>
				</select><br /><br />
				<label for="type"><?php echo $lang['case-add-type']; ?></label>
				<select name="type" id="ntk_type" required>
					<option></option>
					<?php 
						foreach ($dss->caseType as $ctype=>$value) {
							echo '<option value="'.$ctype.'">'.$value.'</option>';
						}
					?>
				</select><br /><br />
				<label for="status"><?php echo $lang['case-add-status']; ?></label>
				<select name="status" id="ntk_status" required>
					<option></option>
					<?php 
						foreach ($dss->caseStatus as $cstat=>$value) {
							echo '<option value="'.$cstat.'">'.$value.'</option>';
						}
					?>
				</select><br /><br />
				<label for="price"><?php echo $lang['case-add-price']; ?></label>
				<input type="text" name="price" id="ntk_price" size="10" maxlength="10" value="" /><br /><br />
				<label for="follow"><?php echo $lang['case-add-follow']; ?></label>
				<input type="text" name="follow" id="ntk_follow" size="20" maxlength="255" value="" /><br /><br />
				<label for="your-name"><?php echo $lang['case-add-user']; ?></label>
				<select name="your-name" id="ntk_name">
					<option></option>
					<?php foreach ($dss->users as $oneuser) {
						echo '<option value="'.$oneuser.'">'.$oneuser.'</option>';
					} ?>
				</select><br />
			</fieldset><br />
			<!--<input type="hidden" name="id" id="id" value="<?php echo ($total+1); ?>" />-->
			<input type="hidden" name="pos" id="pos" value="before" />
			<span class="fake-button" id="addtckbtn"><?php echo $lang['case-add-submit']; ?></span><br />
		</form>
		<div id="new_tk_frm_error" style="color:red;border:medium solid red;padding:8px;display:none;"></div>
		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['controls-close']; ?></span>
<script>
$(document).ready(function() {
	$("#add_help").hover(function(){
		$("#add_help_data").slideToggle('fast');
	});
    //client autocomplete
	$("#ntk_client").keyup(function(){
		var cURL = "index.php?task=sclient&pos=before";
		var sdata = $(this).val();
		if (sdata.length > 1) {
			$.post(cURL,
			{term : sdata},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#ntk_client_res").show('fast').html(data.message);
				} else if(data.status === "error") {
					$("#ntk_client_res").show('fast').append(data.message).delay(2000).hide('slow');
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#ntk_client_res").show('fast').append(textStatus);
		});
		}
	});

	$("#ntk_client").click(function(){
		$("#ntk_client_res").hide('slow');
	});

	$("#ntk_client_res").on('click','>', function(){ //bind click event to direct children (the result divs)
        var cid = $(this).children('span').html(); // get the grandchild's html which is to client id
        var ct = $(this).text(); // get text of the whole div without the mark up
        $("#cid").val(cid); // update hidden input with client id
        $("#ntk_client").val(ct); // update value of the visible client input (user friendly bliah)
        $(this).parent().hide('fast'); // hide result list
	});

	$("#addtckbtn").click(function(){
		$(this).hide(); //hide submit button so as to avoid double-clicks
		var formData = $("#new_tk_frm").serializeArray();
		var URL = $("#new_tk_frm").attr("action");
		$.post(URL,
			formData,
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#new_tk_frm_error").hide();
					$("#new_tk_frm").append(data.message).delay(2000).hide('slow');
					$("#new_ticket").delay(3000).hide('slow', function() {
						window.location = 'index.php?action=docache';
					});
				} else if(data.status === "error") {
					$("#new_tk_frm_error").show();
					$("#new_tk_frm_error").html(data.message);
					$("#addtckbtn").show(); //restore submit btn so to correct and resubmit
				}

			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#new_tk_frm").append(textStatus);
				$("#addtckbtn").show();
			});
	});
});
</script>
