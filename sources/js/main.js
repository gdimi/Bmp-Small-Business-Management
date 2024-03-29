var curState = '<?php echo $curState; ?>';
var cookieState = '';

$(document).ready(function() {
	liveTime();
    
    // Return today's date and time
    var currentTime = new Date()
    var thisYear = currentTime.getFullYear();
    var thisMonth = currentTime.getMonth()+1;
    
    $("#add_tk").click(function() {
		$("#new_ticket").toggle('fast');
	});
    $("#add_client").click(function() {
		$("#new_client").toggle('fast');
		$("#new_cl_frm").show('fast');
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
		$("#client > .ecl_res").html('&nbsp;');		//reset result span
		$("#client > .ecl_frm_error").html('&nbsp;');	//reset error span
		var clid = parseInt(this.id.match(/(\d+)$/)[0], 10); //we want the integer at the end from the id string
		if (clid > 0) {
			$.get("index.php",
			{cid : clid, task: "client",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
                    $("#dt_cl span.del-cl-b").attr( 'id', 'dlc_' + clid );  //set id with current client's id in delete button
					$("#client #ecl_frm").show().html(data.message);
					$("#client > div > .cl-cases").html(data.cases);
					$("#client .cldel").show();
					$("#client .cledit").show();
					$("#client").show("fast");
				} else if(data.status === "error") {
					$("#client .ecl_frm_error").html(data.message);
					$("#client #ecl_frm").hide();
					$("#client .cldel").hide();
					$("#client .cledit").hide();
					$("#client").show("fast").delay(5000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#client").show("fast").append(textStatus);
			});
		}
	});
    //catch income clicks
	$("#income").click(function() {
			$.get("index.php",
			{iy:thisYear,task: "esoda",pos: "before"},
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
			{iy:thisYear, task: "costs",pos: "before"},
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
			$("#stats_info").show();
			$("#stats_info > #stats_data").html('<img src="images/loader.gif" />');
			$.get("index.php",
			{task: "stats",pos: "before",lang: activeLanguage},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#stats_info > #stats_data").html(data.message);
                    //highlight client in each stats block on hover 
                    document.querySelectorAll(".clname").forEach(el => {
                       let clid = el.dataset.cid;
                       let clQuery = '.clname.cl-'+clid;
                       el.addEventListener("mouseover", () => {
                           document.querySelectorAll(clQuery).forEach(el => {
                               el.style.backgroundColor = 'lightgreen';
                           });
                       });
                       el.addEventListener("mouseout", () => {
                           document.querySelectorAll(clQuery).forEach(el => {
                               el.style.backgroundColor = '#F5F5F5';
                           });                           
                       });
                    });                    
                    const clients = document.getElementsByClassName('clname');
                    console.log(clients);
				} else if(data.status === "error") {
					$("#stats_info > div").append(data.message);
					$("#stats_info").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#stats_info").append(textStatus);
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
    //catch trash clicks
	$("#trash_show").click(function() {
			$.get("index.php",
			{task: "trash",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
					$("#trash_info div#trash_data").html(data.files);
					$("#trash_info").show("fast");
				} else if(data.status === "error") {
					$("#trash_info div#trash_data").append(data.message);
					$("#trash_info").show("fast").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#trash_info").show("fast").append(textStatus);
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
    
    // cases links in client details
	//case id search
	$("#client").on('click', "a" ,function() {
        var cclass = $(this).attr('class');
        var cclid = cclass.slice(3); //parseInt(cclass.match(/(\d+)$/)[0], 10); //we want the integer at the end from the id string;
 
 		if (cclid) {
			$.get("index.php",
			{ci : cclid, task: "cis",pos: "before"},
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
					$("#gen_res").show("fast").delay(2000).hide("slow").html("&nbsp;");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#gen_res").show("fast").append(textStatus);
			});
		}
	});
	//catch close case click events
	$("#ct_tk").on('click', '.close-tk-b' ,function(){
		var ctkid = parseInt(this.id.match(/(\d+)$/)[0], 10); //we want the integer at the end from the id string
		$("#ct_tk").hide('fast');
		if (ctkid > 0) {
			$("#gen_res div").html('&nbsp;'); //erase possibly previous messages in result div
			$.get("index.php",
			{tid : ctkid, ttime : "1", task: "ctk",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "error") {
					$("#gen_res div").html(data.message);
					$("#gen_res").show("fast");
				} else if(data.status === "success") {
					$("#gen_res div").append(data.message);
					$("#gen_res").show("fast").delay(2000).hide("slow");
                    var spanct = '#ct_s'+ctkid;
                    $(spanct).parent().parent().hide('slow'); //hide this case's tr from table
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
					$("#client .remainder").html('Remainder: '+data.remainder+' &euro;');
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
    
    $("#menuToggle").click(function() {
       if ($("aside#menu").hasClass("shrunk")) {
           $("aside#menu").removeClass("shrunk");
           $("aside#menu > h3 span").html("<?php echo $lang['lmenu']; ?>");
           $("section").css("margin-left","10%");
           $(this).html("&laquo;");
       } else {
           $("aside#menu").addClass("shrunk");
           $("aside#menu > h3 span").html("M");
           $("section").css("margin-left","48px");
           $(this).html("&raquo;");
       }
       $("aside#menu ul > li > span > a").toggle(); 
    });

	//Check state through API for changes since last accessed
	function checkState(){
		$.ajax({
			type: "get",
			url: "index.php?task=api&pos=before&comm=getstate",
			success:function(data)
			{
				let cookieState = getCookie("BMPstate");
				console.log((data.message == curState));
				console.log('state received '+data.message);
				console.log('current state '+curState);
				console.log('cookie state '+cookieState);

				if (cookieState != data.message) {
					$('.api-msg').show();
				} else {
					$('.api-msg').hide();
				}
				setTimeout(function(){
					checkState();
				}, 60000);
			}
		});
	}

	checkState();
    
    makeDragable('client','client-handle');
    makeDragable('allclients','allclients-handle');
    makeDragable('new_ticket','new_ticket-handle');
    makeDragable('new_client','new_client-handle');
    makeDragable('settings','settings-handle');
    makeDragable('esoda','income-handle');
    makeDragable('costs','costs-handle');
    makeDragable('stats_info','stats-handle');
    makeDragable('various','various-handle');
    makeDragable('prices','prices-handle');
    makeDragable('cms_info','cms-handle');
    makeDragable('trash_info','trash-handle');

});

//hide elevated items when click outside
/*$(document).mouseup(function(e) 
{
    var container = $(".elevate");
	console.log(e.target.parentNode);
    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0 && !e.target.parentNode.classList.contains("mcap") ) //do not do this for the left menu 
    {
        container.hide();
    }
});*/

//functions

function returnEndId(elem) {
   //return the numeric id from the end of a string (eg a class or id of an element)
   return parseInt(elem.id.match(/(\d+)$/)[0], 10); //we want the integer at the end from the id string
}



function getCookie(cname) {
	let name = cname + "=";
	let decodedCookie = decodeURIComponent(document.cookie);
	let ca = decodedCookie.split(';');
	for(let i = 0; i <ca.length; i++) {
	  let c = ca[i];
	  while (c.charAt(0) == ' ') {
		c = c.substring(1);
	  }
	  if (c.indexOf(name) == 0) {
		return c.substring(name.length, c.length);
	  }
	}
	return "";
}


/*old style draggable element taken from https://stackoverflow.com/questions/9334084/moveable-draggable-div/ and adapted */
function makeDragable(dragTarget,dragHandle) {
  // used to prevent dragged object jumping to mouse location
  let xOffset = 0;
  let yOffset = 0;

  //let handleFind = document.getElementById(dragTarget).querySelectorAll(':scope > .drag-head');
  
  //let handle = document.getElementById(dragTarget).querySelector(':scope > .drag-head');
  
  //Array.from(document.getElementById(dragTarget).querySelectorAll(':scope > .drag-head')).forEach(function(el) {
  //  var handle = el;
  //  console.log(el);
  //});
  

  let handle = document.querySelector('#'+dragHandle);

  console.log(handle); 
  
  if (handle !== null) {
      handle.addEventListener("mousedown", startDrag, true);
      handle.addEventListener("touchstart", startDrag, true);
  }

  /*sets offset parameters and starts listening for mouse-move*/
  function startDrag(e) {
    e.preventDefault();
    e.stopPropagation();
    let dragObj = document.querySelector('#'+dragTarget);
    
    /*// shadow element would take the original place of the dragged element, this is to make sure that every sibling will not reflow in the document
    let shadow = dragObj.cloneNode();
    shadow.id = ""
    // You can change the style of the shadow here
    shadow.style.opacity = 0.5
    dragObj.parentNode.insertBefore(shadow, dragObj.nextSibling);*/

    let rect = dragObj.getBoundingClientRect();
    dragObj.style.left = rect.left;
    dragObj.style.top = rect.top;
    dragObj.style.position = "absolute";
    dragObj.style.zIndex = 999999;

    /*Drag object*/
    function dragObject(e) {
      e.preventDefault();
      e.stopPropagation();
      if(e.type=="mousemove") {
        dragObj.style.left = e.clientX-xOffset + "px"; // adjust location of dragged object so doesn't jump to mouse position
        dragObj.style.top = e.clientY-yOffset + "px";
      } else if(e.type=="touchmove") {
        dragObj.style.left = e.targetTouches[0].clientX-xOffset +"px"; // adjust location of dragged object so doesn't jump to mouse position
        dragObj.style.top = e.targetTouches[0].clientY-yOffset +"px";
      }
    }
    /*End dragging*/
    document.addEventListener("mouseup", function() {
      // hide the shadow element, but still let it keep the room, you can delete the shadow element to let the siblings reflow if that is what you want
      /*shadow.style.opacity = 0
      shadow.style.zIndex = -999999*/
      window.removeEventListener('mousemove', dragObject, true);
      window.removeEventListener('touchmove', dragObject, true);
    }, true)

    if (e.type=="mousedown") {
      xOffset = e.clientX - rect.left; //clientX and getBoundingClientRect() both use viewable area adjusted when scrolling aka 'viewport'
      yOffset = e.clientY - rect.top;
      window.addEventListener('mousemove', dragObject, true);
    } else if(e.type=="touchstart") {
      xOffset = e.targetTouches[0].clientX - rect.left;
      yOffset = e.targetTouches[0].clientY - rect.top;
      window.addEventListener('touchmove', dragObject, true);
    }
  }
}

function liveTime() {
	let date = new Date(); 
	let hh = date.getHours();
	let mm = date.getMinutes();
	let ss = date.getSeconds();
	//let session = "AM";
  
/* 	if(hh == 0){
		hh = 12;
	}
	if(hh > 12){
		hh = hh - 12;
		//session = "PM";
	 } */
  
	 hh = (hh < 10) ? "0" + hh : hh;
	 mm = (mm < 10) ? "0" + mm : mm;
	 ss = (ss < 10) ? "0" + ss : ss;
	  
	 let time = hh + ":" + mm + ":" + ss; // + " " + session;
  
	document.getElementById("topClock").innerText = time; 
	let t = setTimeout(function(){ liveTime() }, 1000);
}
