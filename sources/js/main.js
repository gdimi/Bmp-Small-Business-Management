$(document).ready(function() {
    $("#add_tk").click(function() {
		$("#new_ticket").toggle('fast');
	});
    $("#add_client").click(function() {
		$("#new_client").toggle('fast');
	});
    $("#stats").click(function() {
		$("#stats_info").toggle('fast');
	});
    $("#settings_tk").click(function() {
		$("#settings").toggle('fast');
	});
    $("#cms").click(function(){ $("#cms_info").toggle('fast'); });
	$("#all_clients").click(function() {
		$.get("index.php",
		{task: "acl",pos: "before"},
		function(data, textStatus, jqXHR){
			if(data.status === "success") {
				$("#allclients div").html(data.message);
				$("#allclients").show("fast");
			} else if(data.status === "error") {
				$("#allclients div").append(data.message);
				$("#allclients").show("fast").delay(2000).hide("slow");
			}
		}, "json").fail(function(jqXHR, textStatus, errorThrown){
			$("#allclients").show("fast").append(textStatus);
		});
	});
	//catch all client links
	$("#allclients").on('click','.show-client',function() {
		var clid = parseInt(this.id.match(/(\d+)$/)[0], 10); //we want the integer at the end from the id string
		if (clid > 0) {
			$.get("index.php",
			{cid : clid, task: "client",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
                    $("#dt_cl span.del-cl-b").attr( 'id', 'dlc_' + clid );  //set id with current client's id in delete button
					$("#client #ecl_frm").html(data.message);
					$("#client > div > .cl-cases").html(data.cases);
					$("#client").show("fast");
				} else if(data.status === "error") {
					$("#client > div").append(data.message);
					$("#client").show("fast").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#client").show("fast").append(textStatus);
			});
		}
	});
    //catch income clicks
	$("#income").click(function() {
			$.get("index.php",
			{task: "esoda",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#esoda div").html(data.message);
					$("#esoda").show("fast");
				} else if(data.status === "error") {
					$("#esoda div").append(data.message);
					$("#esoda").show("fast").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#esoda").show("fast").append(textStatus);
			});
	});
    //catch costs clicks
	$("#expenses").click(function() {
			$.get("index.php",
			{task: "costs",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#costs div#cres").html(data.message);
					$("#costs").show("fast");
				} else if(data.status === "error") {
					$("#costs div#cres").append(data.message);
					$("#costs").show("fast").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#costs").show("fast").append(textStatus);
			});
	});
    //catch stats clicks
	$("#stats").click(function() {
			$.get("index.php",
			{task: "stats",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#stats_info > #stats_data").html(data.message);
					$("#stats_info").show("fast");
				} else if(data.status === "error") {
					$("#stats_info > div").append(data.message);
					$("#stats_info").show("fast").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#stats_info").show("fast").append(textStatus);
			});
	});
    //catch cms clicks
	$("#cms").click(function() {
			$.get("index.php",
			{task: "cms",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.motd) {
					$("#cms_data > form > #motd_txt").html(data.motd);
				}
                if(data.board) {
					$("#cms_data > form > #board_txt").append(data.board);
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#cms_data").append(textStatus);
			});
	});
	//case id search
	$("#cisubmit").click(function() {
		var csid = document.getElementById('cisearch').value;
		if (csid) {
			$.get("index.php",
			{ci : csid, task: "cis",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#cis_res div").html(data.message);
					$("#cis_res").show("fast");
				} else if(data.status === "error") {
					$("#cis_res div").append(data.message);
					$("#cis_res").show("fast").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#cis_res").show("fast").append(textStatus);
			});
		}
	});
	//catch delete case click events
	$("#dt_tk").on('click', '.del-tk-b' ,function(){
		var dtkid = parseInt(this.id.match(/(\d+)$/)[0], 10); //we want the integer at the end from the id string
		$("#dt_tk").hide('fast');
		if (dtkid > 0) {
			$.get("index.php",
			{tid : dtkid, task: "dtk",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "error") {
					$("#gen_res div").html(data.message);
					$("#gen_res").show("fast");
				} else if(data.status === "success") {
					$("#gen_res div").append(data.message);
					$("#gen_res").show("fast").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#gen_res").show("fast").append(textStatus);
			});
		}
	});
	//client object
	var ClientObj = new Object();
	ClientObj.id = 0;
	ClientObj.strid = '';
	//autocomplete client in edit case
	$(".etcl").keyup(function(){
		var etclid = $(this).attr("id");
		var etid = parseInt(etclid.match(/(\d+)$/)[0], 10);
		ClientObj.id = etid; //store integer id
		etid = etid.toString();
		ClientObj.strid = etid; //store string id
		var cURL = "index.php?task=sclient&pos=before";
		var sdata = $(this).val();
		if (sdata.length > 1) {
			$.post(cURL,
			{term : sdata},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#etk_cl_res_"+etid).show('fast').html(data.message);
				} else if(data.status === "error") {
					$("#etk_cl_res_"+etid).show('fast').append(data.message).delay(2000).hide('slow');
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#etk_cl_res_"+etid).show('fast').append(textStatus);
		});
		}
	});

	$(".etcl").click(function(){
		$("#etk_cl_res_"+ClientObj.strid).hide('slow');
	});

	$('.autocomplete').on('click','>', function(){ //bind click event to direct children (the result divs)
        var etcid = $(this).children('span').html(); // get the grandchild's html which is to client id
        var etct = $(this).text(); // get text of the whole div without the mark up
        $("#et_client_"+ClientObj.strid).val(etcid); // update hidden input with client id
        $(".etcl").val(etct); // update value of the visible client input (user friendly bliah)
        $(this).parent().hide('fast'); // hide result list
	});

	//show client details in case tracker
	$(".cclient").click(function(){
		var cid = parseInt(this.id.match(/(\d+)$/)[0], 10);
		if (cid > 0) {
			$.get("index.php",
			{cid : cid, task: "client",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
                    $("#dt_cl span.del-cl-b").attr( 'id', 'dlc_' + cid ); //set id with current client's id
					$("#client #ecl_frm").html(data.message);
					$("#client > div > .cl-cases").html(data.cases);
					$("#client").show("fast");
				} else if(data.status === "error") {
					$("#client > div").append(data.message);
					$("#client").show("fast").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#client").show("fast").append(textStatus);
		});
		}
	});

	//catch edit case click events
	$(".frm-actual-data").on('click', '.edtckbtn' ,function(){
		var edtkid = parseInt(this.id.match(/(\d+)$/)[0], 10); //we want the integer at the end from the id string
		var frmedtkid = $(this).parent().attr("id");
		var edtkcl = "#et_client_"+edtkid.toString();
		if (edtkid > 0) {
			var edformData = $("#"+frmedtkid).serializeArray();
			var URL = 'index.php?task=etk&pos=before';
			$.post(URL,
				edformData,
				function(data, textStatus, jqXHR){
					if(data.status === "success") {
						$("#ed_tk_frm_err_"+edtkid).hide();
						$("#"+frmedtkid).append(data.message).delay(2000).hide('slow');
						$("#cur_ticket_"+edtkid).delay(3000).hide('slow', function() {
							window.location = 'index.php?action=docache';
							//window.location.reload(true);
						});
						//$("#tkt_success").hide();
					} else if(data.status === "error") {
						$("#ed_tk_frm_err_"+edtkid).show();
						$("#ed_tk_frm_err_"+edtkid).html(data.message);
					}

				}, "json").fail(function(jqXHR, textStatus, errorThrown){
					$("#"+frmedtkid).append(textStatus);
				});
		}
	});
});

    //functions
    
function returnEndId(elem) {
   //return the numeric id from the end of a string (eg a class or id of an element)
   return parseInt(elem.id.match(/(\d+)$/)[0], 10); //we want the integer at the end from the id string
}
