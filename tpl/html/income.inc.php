		<?php require_once('sources/preprocessor/income.php'); //require preprocessor ?>
		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['controls-close']; ?></span>
		<h2><?php echo $lang['costs']; ?></h2>
        <span class="filters" id="income_filters">
            <form>
                <?php echo $lang['year']; ?> <select id="income_year">
					<?php echo $IncOptHTML; ?>
                </select> 
                <?php echo $lang['month']; ?> <select id="income_month">
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
		<div></div>
<script>
$('#esoda').on('change','select',function(){
	var im = $('#income_month').val();
	var iy = $('#income_year').val();
	if (im != NaN && iy != NaN) {
		$.get("index.php",
		{iy:iy,im:im,task: "esoda",pos: "before"},
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
	}
});
</script>
