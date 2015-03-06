		<?php require_once('sources/preprocessor/costs.php'); //require preprocessor ?>
		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['controls-close']; ?></span>
		<h2><?php echo $lang['costs']; ?></h2>
        <span class="filters" id="cost_filters">
            <form>
                <?php echo $lang['year']; ?> <select id="cost_year">
					<?php echo $CostsOptHTML; ?>
                </select> 
                <?php echo $lang['month']; ?> <select id="cost_month">
                    <option value="0" selected><?php echo $lang['month-all']; ?></option>
                    <option value="12">12 - <?php echo $lang['month-dec']; ?></option>                     
                    <option value="11">11 - <?php echo $lang['month-nov']; ?></option>                    
                    <option value="10">10 - <?php echo $lang['month-oct']; ?></option>                    
                    <option value="9">9 - <?php echo $lang['month-sep']; ?></option>                    
                    <option value="8">8 - <?php echo $lang['month-aug']; ?></option>
                    <option value="7">7 - <?php echo $lang['month-jul']; ?></option>
                    <option value="6">6 - <?php echo $lang['month-jun']; ?></option>
                    <option value="5">5 - <?php echo $lang['month-may']; ?></option>
                    <option value="4">4 - <?php echo $lang['month-apr']; ?></option>
                    <option value="3">3 - <?php echo $lang['month-mar']; ?></option>
                    <option value="2">2 - <?php echo $lang['month-feb']; ?></option>
                    <option value="1">1 - <?php echo $lang['month-jan']; ?></option>
                </select>
            </form>
        </span>
        <span class="add" id="add_cost">+ <?php echo $lang['costs-add']; ?></span>
        <div id="cres"></div>
		<span id="add_cost_frm" style="display:none;">
			<form name="ac_frm" id="ac_frm" action="index.php?task=acost&pos=before">
				<fieldset>
				<legend><?php echo $lang['costs-new']; ?></legend>
					<label for="cDesc"><?php echo $lang['costs-descr']; ?></label>
					<textarea name="cDesc" id="cDesc" required  pattern=".{4,}"></textarea><br />
					<label for="cAmount"><?php echo $lang['costs-amount']; ?></label>
					<input type="text" name="cAmount" id="cAmount" value="" size="4" required /><br />
					<label for="cDate"><?php echo $lang['costs-date']; ?></label>
					<input type="text" name="cDate" id="cDate" value="" size="12" required placeholder="DD/MM/YYYY" /><br />
				</fieldset>
				<span class="fake-button" id="addcsbtn"><?php echo $lang['costs-add-msg']; ?></span><br /><br />
				<span class="cl-b" onclick="$(this).parent().parent().toggle();"><?php echo $lang['controls-close']; ?></span>
			</form>
			<div id="new_cs_frm_error" style="color:red;border:medium solid red;padding:8px;display:none;"></div>
		</span>
		<span id="edit_cost_frm" style="display:none;">
			<form name="ec_frm" id="ec_frm" action="index.php?task=ecost&pos=before">
				<fieldset>
				<legend><?php echo $lang['costs-edit']; ?></legend>
					<label for="ecDesc"><?php echo $lang['costs-descr']; ?></label>
					<textarea name="ecDesc" id="ecDesc" required  pattern=".{4,}"></textarea><br />
					<label for="ecAmount"><?php echo $lang['costs-amount']; ?></label>
					<input type="text" name="ecAmount" id="ecAmount" value="" size="4" required /><br />
					<label for="ecDate"><?php echo $lang['costs-date']; ?></label>
					<input type="text" name="ecDate" id="ecDate" value="" size="12" required placeholder="DD/MM/YYYY"/><br />
                    <input type="hidden" name="ecId" id="ecId" value="" />
				</fieldset>
				<span class="fake-button" id="editcsbtn"><?php echo $lang['costs-save']; ?></span><br /><br />
				<span class="cl-b" onclick="$(this).parent().parent().toggle();"><?php echo $lang['controls-close']; ?></span>
			</form>
			<div id="edit_cs_frm_error" style="color:red;border:medium solid red;padding:8px;display:none;"></div>
		</span>
        <div class="elevate menu-dialog" style="display:none;" id="dcost_dialog">
            <h2><?php echo $lang['costs-delete']; ?></h2>
            <div id="dcs_cl_msg"><?php echo $lang['costs-delete-msg']; ?> <strong></strong>-<strong></strong> ?</div><br><br>
            <span id="dcs_yes" class="cl-b b-sblue"><?php echo $lang['costs-delete-yes']; ?></span>
            <span onclick="document.getElementById('dcost_dialog').style.display = 'none';" class="cl-b b-sblue" style="font-size:24px;"><?php echo $lang['costs-delete-no']; ?></span>
        </div>
        <div id="cres_act" class="elevate menu-dialog" style="display:none;"></div>
<script>
$(document).ready(function() {
	$('#costs').on('change','select',function(){
		var im = $('#cost_month').val();
		var iy = $('#cost_year').val();
		if (im != NaN && iy != NaN) {
			//$("#costs > div#cres").append('<img src="images/loader.gif" />');
			$.get("index.php",
			{iy:iy,im:im,task: "costs",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#costs > div#cres").html(data.message);
					$("#costs").show("fast");
				} else if(data.status === "error") {
					$("#costs > div#cres").append(data.message);
					$("#costs").show("fast").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#costs").show("fast").append(textStatus);
			});
		}
	});
	$('#add_cost').click(function(){
		$('#add_cost_frm').slideToggle();
	});
	$("#addcsbtn").click(function(){
		var URL = $("#ac_frm").attr("action");
		var formData = $("#ac_frm").serializeArray();
		var csdescr = $("#cDesc").val();
		var csam = $("#cAmount").val();
		var csdate = $("#cDate").val();
		if (csdescr != '' && csam != '' && csdate != '') {
			$.post(URL,
			formData,
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#new_cs_frm_error").hide();
					$("#ac_frm").append(data.message).delay(2000);
					$("#add_cost_frm").delay(1000).hide('slow', function() {
                        var delfrm = document.getElementById('ac_frm');
                        delfrm.reset();
                        refreshCostsTable();
					});
				} else if(data.status === "error") {
					$("#new_cs_frm_error").show();
					$("#new_cs_frm_error").html(data.message);
				}

			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#cs_frm").append(textStatus);
			});
		} else {
			alert('<?php echo $lang['costs-error1']; ?>');
		}
	});
    //edit current cost
    $("#cres").on('click', '.cost-edit' ,function(){
	  //console.log('edit');
      var cid = returnEndId(this);
        if (cid > 0) { //must be bigger than zero
            $.get("index.php",
            {cid : cid, task: "onecs",pos: "before"},
            function(data, textStatus, jqXHR){
                if(data.status === "error") {
                    $("#cres_act").html(data.message);
                    $("#cres_act").show("fast");
                } else if(data.status === "success") {
                    $("#ecDesc").val(data.desc);
                    $("#ecAmount").val(data.amount);
                    $("#ecDate").val(data.cdate);
                    $("#ecId").val(cid);
                    $("#edit_cost_frm").show();
                }
            }, "json").fail(function(jqXHR, textStatus, errorThrown){
                $("#gen_res").show("fast").append(textStatus).delay(2000).hide("fast");
            });
        } else {
            alert('<?php echo $lang['costs-error2']; ?>');
        }
    });
    //store edited cost
    $("#editcsbtn").click(function(){
		var URL = $("#ec_frm").attr("action");
		var formData = $("#ec_frm").serializeArray();
		var csdescr = $("#ecDesc").val();
		var csam = $("#ecAmount").val();
		var csdate = $("#ecDate").val();
		if (csdescr != '' && csam != '' && csdate != '') {
			$.post(URL,
			formData,
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#edit_cs_frm_error").hide();
					$("#ec_frm").html(data.message).delay(2000);
                    $("#edit_cs_frm_error").hide();
					$("#edit_cost_frm").delay(1000).hide('slow', function() {
						refreshCostsTable();
					});
				} else if(data.status === "error") {
					$("#edit_cs_frm_error").show();
					$("#edit_cs_frm_error").html(data.message);
				}

			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#ecs_frm").append(textStatus);
			});
		} else {
			alert('<?php echo $lang['costs-error1']; ?>');
		}
    });
    //delete cost
    $("#cres").on('click', '.cost-delete' ,function(){
		//console.log(this);
        var cid = returnEndId(this); //get cost id
        
        $("#dcost_dialog").show();//show dialog
        
        $("#dcs_yes").click({cid:cid},function(evt) {
            $("#dcs_yes").parent().hide();//hide dialog
            var csid = evt.data.cid; //get cost id again from data passed to function

            if (csid > 0) { //must be bigger than zero
                $.get("index.php",
                {cid : csid, task: "csd",pos: "before"},
                function(data, textStatus, jqXHR){
                    if(data.status === "error") {
                        $("#cres_act").html(data.message);
                        $("#cres_act").show("fast");
                    } else if(data.status === "success") {
                        $("#cres_act").html(data.message);
                        $("#cres_act").show("fast").delay(2000).hide("slow");
                        refreshCostsTable();
                    }
                }, "json").fail(function(jqXHR, textStatus, errorThrown){
                    $("#gen_res").show("fast").append(textStatus);
                });
            }
        });
    });
    
    function refreshCostsTable() {
		var im = 0;
		var iy = 2015;
		//$("#costs > div#cres").append('<img src="images/loader.gif" />');
		$.get("index.php",
		{iy:iy,im:im,task: "costs",pos: "before"},
		function(data, textStatus, jqXHR){
			if(data.status === "success") {
				$("#costs > div#cres").html(data.message);
				$("#costs").show("fast");
			} else if(data.status === "error") {
				$("#costs > div#cres").append(data.message);
				$("#costs").show("fast").delay(2000).hide("slow");
			}
		}, "json").fail(function(jqXHR, textStatus, errorThrown){
			$("#costs").show("fast").append(textStatus);
		});
	}
});
</script>
