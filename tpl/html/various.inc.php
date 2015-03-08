		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['controls-close']; ?></span>
        <h2><?php echo $lang['various']; ?></h2>
		<div class="variousContent">
			<?php include('content/various.html'); ?>

        </div>
		<div class="toolbar">
			<span id="evarious" class="fake-button">Edit page</span>
			<span id="svarious" class="fake-button" style="display:none;">Save page</span>
		</div>
		<div class="vmsg" style="display:none"></div>
		<script>
			$(document).ready(function() {
				$("#various").on('click', '#evarious' ,function(){
					$("#various > .variousContent").attr("contenteditable","true").css("border","thin solid lightblue");
					$("#various > div.toolbar > #svarious").show();
				});
				$("#various").on('click', '#svarious', function(){
					$("#various > .vmsg").show();
					$("#various > div.toolbar > #svarious").hide();
					$("#various > .variousContent").attr("contenteditable","false").css("border","medium none");
					var VariousData = $("#various > .variousContent").html();
					var cURL = "index.php?task=evar&pos=before";

					$.post(cURL,
					{varData:VariousData},
					function(data, textStatus, jqXHR){
						if(data.status === "success") {
							$("#various > .vmsg").html(data.message).delay(2000).hide("slow");
							updateVarious();
						} else if(data.status === "error") {
							$("#various > .vmsg").append(data.message).delay(2000).hide("slow");
						}
					}, "json").fail(function(jqXHR, textStatus, errorThrown){
						$("#various > div.vmsg").show("fast").append(textStatus);
					});
					
					function updateVarious() {
						$.get("index.php",
						{task: "uvar",pos: "before"},
						function(data, textStatus, jqXHR){
							if(data.status === "success") {
								$("#various > .variousContent").html(data.message)
							} else if(data.status === "error") {
								$("#various > .vmsg").append(data.message).delay(2000).hide("slow");
							}
						}, "json").fail(function(jqXHR, textStatus, errorThrown){
							$("#various > div.vmsg").show("fast").append(textStatus);
						});
					}
				});
			});
		</script>
