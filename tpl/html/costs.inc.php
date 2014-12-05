		<span class="cl-b" onclick="$(this).parent().toggle();">Close me</span>
		<h2>Έξοδα</h2>
        <span class="filters" id="cost_filters">
            <form>
                year <select id="cost_year">
                    <option value="2014" selected>2014</option>
                </select> 
                month <select id="cost_month">
                    <option value="0" selected>All</option>
                    <option value="12">12 - Δεκέμβρης</option>                     
                    <option value="11">11 - Νοέμβρης</option>                    
                    <option value="10">10 - Οκτώβρης</option>                    
                    <option value="9">9 - Σεπτέμβρης</option>                    
                    <option value="8">8 - Αύγουστος</option>
                    <option value="7">7 - Ιούλης</option>
                    <option value="6">6 - Ιούνης</option>
                    <option value="5">5 - Μάης</option>
                    <option value="4">4 - Απρίλης</option>
                    <option value="3">3 - Μάρτης</option>
                </select>
            </form>
        </span>
        <span class="add" id="add_cost">+ προσθήκη εξόδου</span>
        <div id="cres"></div>
		<span id="add_cost_frm" style="display:none;">
			<form name="ac_frm" id="ac_frm" action="index.php?task=acost&pos=before">
				<fieldset>
				<legend>Νέο έξοδο</legend>
					<label for="cDesc">Περιγραφή</label>
					<textarea name="cDesc" id="cDesc"></textarea><br />
					<label for="cAmount">Ποσό</label>
					<input type="text" name="cAmount" id="cAmount" value="" size="4" /><br />
					<label for="cDate">Ημ/νια</label>
					<input type="text" name="cDate" id="cDate" value="" size="12" /><br />
				</fieldset>
				<span class="fake-button" id="addcsbtn">Προσθήκη λυπητερής</span><br /><br />
				<span class="cl-b" onclick="$(this).parent().parent().toggle();">Close me</span>
			</form>
			<div id="new_cs_frm_error" style="color:red;border:medium solid red;padding:8px;display:none;"></div>
		</span>
		<span id="edit_cost_frm" style="display:none;">
			<form name="ec_frm" id="ec_frm" action="index.php?task=ecost&pos=before">
				<fieldset>
				<legend>Επεξεργασία εξόδου</legend>
					<label for="ecDesc">Περιγραφή</label>
					<textarea name="ecDesc" id="ecDesc"></textarea><br />
					<label for="ecAmount">Ποσό</label>
					<input type="text" name="ecAmount" id="ecAmount" value="" size="4" /><br />
					<label for="ecDate">Ημ/νια</label>
					<input type="text" name="ecDate" id="ecDate" value="" size="12" /><br />
                    <input type="hidden" name="ecId" id="ecId" value="" />
				</fieldset>
				<span class="fake-button" id="editcsbtn">Αποθήκευση λυπητερής</span><br /><br />
				<span class="cl-b" onclick="$(this).parent().parent().toggle();">Close me</span>
			</form>
			<div id="edit_cs_frm_error" style="color:red;border:medium solid red;padding:8px;display:none;"></div>
		</span>
        <div class="elevate menu-dialog" style="display:none;" id="dcost_dialog">
            <h2>ΔΙΑΓΡΑΦΗ ΛΥΠΗΤΕΡΗΣ!</h2>
            <div id="dcs_cl_msg">Sigoura i tha kaneis xazomara? Delete <strong></strong>-<strong></strong> ?</div><br><br>
            <span id="dcs_yes" class="cl-b b-sblue del-cl-b">YES</span>
            <span onclick="document.getElementById('dcost_dialog').style.display = 'none';" class="cl-b b-sblue" style="font-size:24px;">NO</span>
        </div>
        <div id="cres_act" class="elevate menu-dialog" style="display:none;"></div>
<script>
$(document).ready(function() {
	$('#costs').on('change','select',function(){
		var im = $('#cost_month').val();
		var iy = $('#cost_year').val();
		if (im != NaN && iy != NaN) {
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
					});
				} else if(data.status === "error") {
					$("#new_cs_frm_error").show();
					$("#new_cs_frm_error").html(data.message);
				}

			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#cs_frm").append(textStatus);
			});
		} else {
			alert('Re vale perigrafh, poso kai imerominia!');
		}
	});
    //edit current cost
    $("#cres").on('click', '.cost-edit' ,function(){
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
            alert('zero id is not possible');
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
					$("#ec_frm").append(data.message).delay(2000);
                    $("#edit_cs_frm_error").hide();
					$("#edit_cost_frm").delay(1000).hide('slow', function() {
					});
				} else if(data.status === "error") {
					$("#edit_cs_frm_error").show();
					$("#edit_cs_frm_error").html(data.message);
				}

			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#ecs_frm").append(textStatus);
			});
		} else {
			alert('Re vale perigrafh, poso kai imerominia!');
		}
    });
    //delete cost
    $("#cres").on('click', '.cost-delete' ,function(){
        $("#dcost_dialog").show();//show dialog
        var cid = returnEndId(this); //get cost id
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
                        $("#cres_act").append(data.message);
                        $("#cres_act").show("fast").delay(2000).hide("slow");
                    }
                }, "json").fail(function(jqXHR, textStatus, errorThrown){
                    $("#gen_res").show("fast").append(textStatus);
                });
            }
        });
    });
});
</script>
