<?php
if (!defined('_w00t_frm')) die('har har har');

require_once('sources/preprocessor/settings.php'); //require preprocessor 

?>
<script src='sources/third_party/spectrum.min.js'></script>
<link rel='stylesheet' href='sources/third_party/spectrum.min.css' />

<span id="settings_help" class="help"></span>
<div id="settings_data">
	<form id="settings_form" name="settings_form" action="index.php?task=sets&pos=before">
		<div id="sets_help_data">
		<span style="display: block;font-size: 1.2em;margin-bottom: 4px;text-align: center;">Heeeelp...</span>
		<span>Types / Categories:</span> You cannot directly edit types/categories from this form. In the case of an already active database filled with data, this could prove catastrophic because it could alter what category/type cases have. If you must do it, edit the config file. If you just want to change the displayed order you can move the lines but be sure you preserve its indexes. If you want to add something, just add it at the end with the appropriate index. If you want to delete something you can delete it but then you would have to update the cases db table.<br><br>
		<span>Statuses :</span> The same problem as with categories arises here. Follow se same guidelines.<br><br>
		<span>Priority levels:</span> The same problem as with categories arises here. Follow se same guidelines. <br><br>
		<span>Excluded customer from stats:</span> From here in a future update you will be able to control which customers are excluded from statistics. Till then, edit the config file, add the ID of the customer you want to exclude.<br><br>
		<span>Mail related settings:</span> There are three settings for mails: <br>
		- Notification mail is the mail you want notifications to go to<br>
		- Email address "from" when sending email, is the email address you wish to be used for notifications to appear coming from<br>
		- "From" name for emails, is the name for the address "from".<br>
		For example you may wish for emails to go to operations@yourcompany.com, appear to come from tracker@yourcompany.com and the email client to display it with a name "Our Ops Tracker".
		<br>
		<span> App and Site name:</span> App name is displayed when no logo is provided. If a logo exists, it becomes the image title.<br>Site name is the html title that the browser displays as tab title.<br><br>
		</div>
		<fieldset class="bmp-fieldset">
			<legend class="bmp-legend">Settings</legend>
			<label class="bmp-frm-label" for="timezone"> Timezone </label>
			<select name="timezone">
				<?php echo $tz_sel; ?>
			</select><br>
			<label class="bmp-frm-label" for="project_name"> App Name </label>
			<input class="bmp-input" type="text" name="project_name" value="<?php echo $dss->project_name; ?>"><br>
			<label class="bmp-frm-label" for="sitename"> Site Name </label>
			<input class="bmp-input" type="text" name="sitename" value="<?php echo $dss->sitename; ?>"><br>
			<label class="bmp-frm-label" for="show_history"> <?php echo $lang['settings-history']; ?> </label>
			<select name="show_history">
				<?php echo $show_history_sel; ?>
			</select><br><br>
			<label class="bmp-frm-label" for="mailto"> <?php echo $lang['settings-notmail']; ?> </label>
			<input class="bmp-input" type="text" name="mailto" value="<?php echo $dss->mailto; ?>"><br> <!--where to send mails-->
			<label class="bmp-frm-label" for="mailfrom"> <?php echo $lang['settings-mailaddr']; ?> </label>
			<input class="bmp-input" type="text" name="mailfrom" value="<?php echo $dss->mailfrom; ?>"><br> <!--from email for mails-->
			<label class="bmp-frm-label" for="fromname"> <?php echo $lang['settings-mailfrom']; ?> </label>
			<input class="bmp-input" type="text" name="fromname" value="<?php echo $dss->fromname; ?>"><br> <!--from name for mails-->
			<label class="bmp-frm-label" for="startYear"> <?php echo $lang['settings-startyear']; ?> </label>
			<input class="bmp-input" type="text" name="startYear" value="<?php echo $dss->startYear; ?>"><br <!-- year start for statistics, expenses and income-->
			<label class="bmp-frm-label" for="lang"> <?php echo $lang['settings-lang']; ?> </label>
			<input class="bmp-input" type="text" name="lang" value="<?php echo $dss->lang; ?>"><br>
			<label class="bmp-frm-label" for="show_closed"> <?php echo $lang['settings-closed']; ?> </label>
			<select name="show_closed">
				<?php echo $show_closed_sel; ?>
			</select><br><br>
			<label class="bmp-frm-label" for="caseType"> <?php echo $lang['settings-ctypes']; ?> </label>
			<textarea class="bmp-textarea" name="caseType" rows="4" cols="50" disabled><?php echo implode(",",$dss->caseType); ?></textarea><br>
			<label class="bmp-frm-label" for="casePriority"> <?php echo $lang['settings-cprior']; ?> </label>
			<input class="bmp-input" type="text" name="casePriority" value="<?php echo implode(",",$dss->casePriority); ?>" disabled><br>
			<label class="bmp-frm-label" for="caseStatus"> <?php echo $lang['settings-cstatus']; ?> </label>
			<input class="bmp-input" type="text" name="caseStatus" value="<?php echo implode(",",$dss->caseStatus); ?>" disabled><br>
			<label class="bmp-frm-label" for="users"> <?php echo $lang['settings-users']; ?> </label>
			<input class="bmp-input" type="text" name="users" value="<?php echo implode(",",$dss->users); ?>"><br>
			<label class="bmp-frm-label" for="exclude_from_stats"> <?php echo $lang['settings-xcusstats']; ?> </label>
			<div><?php echo $exclude_from_stats_labels; ?></div>
			<input class="bmp-input" type="hidden" name="exclude_from_stats" value="<?php echo $exclude_from_stats; ?>"><br>
			<label class="bmp-frm-label" for="mylogo"> <?php echo $lang['settings-logo']; ?> </label>
			<input class="bmp-input" type="text" name="mylogo" id="mylogo" value="<?php echo $dss->style['logo']; ?>">
			<input class="bmp-input" type="hidden" name="orig_logo" id="orig_logo" value="<?php echo $dss->style['logo']; ?>">
			<br> <!--path to logo file-->
			<?php if ($dss->style['logo']): ?>
			<img id="mylogoimg" src="<?php echo $dss->style['logo']; ?>" alt="logo" height="40px"><br>
			<?php endif; ?>
			<fieldset id="sfupload">
				<?php echo $lang['upload-file']; ?> (Max: <?php echo $dss->maxUploadSize; ?> Kbytes)<br>
				<span class="removeAt" style="display:none"><?php echo $lang['delete']; ?></span>
				<input type="file" name="sfileToUpload" class="fileToUpload">
				<input type="hidden" name="sfileUploaded" id="sfileUploaded">
				<div class="" style="display:none;"></div>
			</fieldset>
			<label class="bmp-frm-label" for="top_bar_bg"> <?php echo $lang['settings-topbarbg']; ?> </label>
			<input id="cp_tbg" class="bmp-input" type="text" name="top_bar_bg" value="<?php echo $dss->style['top_bar_bg']; ?>"><br>
			<label class="bmp-frm-label" for="left_bar_bg"> <?php echo $lang['settings-lbarbg']; ?> </label>
			<input id="cp_lbg" class="bmp-input" type="text" name="left_bar_bg" value="<?php echo $dss->style['left_bar_bg']; ?>"><br>
		</fieldset>
		<input type="hidden" name="pos" id="pos" value="before" />
		<span class="fake-button" id="savesetbtn"><?php echo $lang['settings-save']; ?></span><br>
		<div id="settings_form_error"></div>
	</form>
</div>
<span class="cl-b" onclick="$(this).parent().toggle();">Close me</span>
<script>
$(document).ready(function() {
	$("#settings_help").hover(function(){
		$("#sets_help_data").slideToggle('fast');
	});
	
    $("#cp_tbg").spectrum({
        color: "<?php echo $dss->style['top_bar_bg']; ?>"
    });

    $("#cp_lbg").spectrum({
        color: "<?php echo $dss->style['left_bar_bg']; ?>"
    });
	
	//logo change
	$('#mylogo').on('change keyup mousedown click mouseup blur mouseout', function() {
		let newlogo = $(this).val();
		$('#mylogoimg').attr('src',newlogo);
	});
	
	//file upload stuff
	$('#sfupload .fileToUpload').on('change', function() {
		var file_data = this.files[0];
		var filename = this.value;
		var form_data = new FormData();                  
		form_data.append('file', file_data);
		$("#settings_form #sfupload div").addClass("loader").show();
                             
		var request = $.ajax({
			url: 'index.php?task=upload&pos=before&type=logo', 
			dataType: 'json',  
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post'
		 });

		request.done(function( response ) {
			$("#settings_form #sfupload div").removeClass("loader");
			//alert(response);
			if (response.status === "success") {
				$("#settings_form #sfupload div").removeClass("gen-error").addClass("gen-success").html(response.message).show();
				$("#settings_form #sfupload #sfileUploaded").val(filename); //store filename so to move it to cid folder in uploads
				$("#settings_form #sfupload .fileToUpload").val(null).hide(); //remove file input
				$("#settings_form #sfupload .removeAt").show('fast'); //show delete file label

				let sfilename = filename.replace(/C:\\fakepath\\/i, '');
				sfilename = 'content/uploads/logo/'+sfilename;
				$("#mylogo").val(sfilename).trigger("click"); //update input and trigger change
			} else if(response.status === "error") {
				$("#settings_form #sfupload div").addClass("gen-error").html(response.message).show();
			}
		});

		 request.fail(function( jqXHR, textStatus ) {
			alert( "<?php echo $lang['ajax-fail']; ?> " + textStatus );
		});
	});
	
	$("#sfupload .removeAt").on('click',function() {
		var removeAt = confirm('<?php echo $lang['remove-attachment']; ?>');
		if (removeAt) {
			let whichAtt = $("#mylogo").val(); //store uploaded file's name
			var remove_data = new FormData();                  
			remove_data.append('fln', whichAtt);
			//now physically remove previously uploaded file to tmp folder
			var request = $.ajax({
				url: 'index.php?task=unlink&pos=before', 
				dataType: 'json',  
				cache: false,
				contentType: false,
				processData: false,
				data: remove_data,                         
				type: 'POST'
			 });

			request.done(function( response ) {
				if (response.status === "success") {
					$("#settings_form #sfupload div").removeClass("gen-error").addClass("gen-success").html(response.message).show();
					let orig_logo = $("#orig_logo").val();
					$("#mylogo").val(orig_logo).trigger("click");
					$("#sfupload .removeAt").hide('fast'); //hide remove button
					$("#sfupload #sfileUploaded").val(null); //set hidden value of what we uploaded to nothing
					$("#sfupload div").remove(); //remove result div
					$("#sfupload .fileToUpload").show();
					$("#sfupload").append('<div style="display:none"></div>'); //re-add result div
			} else if(response.status === "error") {
					$("#settings_form #sfupload div").addClass("gen-error").html(response.message).show();
				}
			});

			request.fail(function() {
					alert( 'SYSTEM: '+'<?php echo $lang['ajax-fail']; ?>' );
			})
		}
	});
	
	// save settings
	$("#savesetbtn").click(function(){
		$(this).hide(); //hide submit button so as to avoid double-clicks
		$("#settings_form").append('<span class="loader"><img src="images/loader.gif" /></span>');
		var formData = $("#settings_form").serializeArray();
		var URL = $("#settings_form").attr("action");
		$.post(URL,
			formData,
			function(data, textStatus, jqXHR){
				if(data.status == 'success') {
					$("#settings_form_error").hide();
					$("#settings_form").remove(".loader");
					$("#settings_form").append(data.message);
				} else if(data.status == 'error') {
					$("#settings_form_error").show();
					$("#settings_form_error").html(data.message);
					$("#savesetbtn").show(); //restore submit btn so to correct and resubmit
				}

			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#settings_form").append(textStatus);
				$("#savesetbtn").show();
			});
	});
});
</script>
