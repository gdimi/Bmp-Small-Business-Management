		<span class="cl-b" onclick="$(this).parent().toggle();">Close me</span>
		<h2>Έσοδα</h2>
        <span class="filters" id="income_filters">
            <form>
                year <select id="income_year">
                    <option value="2014" selected>2014</option>
                </select> 
                month <select id="income_month">
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
/*$("#income").click(function() {
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
});*/
</script>
