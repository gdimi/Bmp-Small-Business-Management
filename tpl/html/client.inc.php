		<span class="cl-b" onclick="$(this).parent().toggle();">Close me</span>
		<h2>Καρτέλα Πελάτη</h2>
		<div></div>
<span class="fake-button red-btn cldel" style="display:inline-block">Delete Client</span>

<span class="elevate menu-dialog" style="display:none;" id="dt_cl">
    <h2>DELETE CLIENT!</h2>
    <div id="dt_cl_msg">Sigoura i tha kaneis xazomara? Delete <strong></strong>-<strong></strong> ?</div><br><br>
    <span id="" class="cl-b b-sblue del-cl-b">YES</span>
    <span onclick="document.getElementById('dt_cl').style.display = 'none';" class="cl-b b-sblue" style="font-size:24px;">NO</span>
</span>
<script>
$(document).ready(function() {
	$(".cldel").click(function(){
		$("#dt_cl").show();
	});
});
</script>
