<?php
$statsselopt = '';

$oldYear = $dss->startYear;
 for($i = $oldYear; $i <= $thisYear; $i++) {
    $statsselopt .= '<option value="'.strtotime($i.'-01-01-00:00').'">'.$i.'</option>';
 }
?>

<h2 style="display: inline-block;padding-right: 12px;" class="drag-head" id="stats-handle"><?php echo $lang['stats']; ?></h2>
<form name="stats-time" id="stats_time_frm" style="display:inline">
	Χρονολογία <select id="stats_time">
	<option>all</option>
	<?php echo $statsselopt; ?>
	</select>
</form>
<span id="stats_last_month" class="bmp-label bmp-label-grey bmp-middle"><?php echo $lang['stats_last_month']; ?></span>
<span id="stats_last_3" class="bmp-label bmp-label-grey bmp-middle"><?php echo $lang['stats_last_3']; ?></span>
<span id="stats_last_6" class="bmp-label bmp-label-grey bmp-middle"><?php echo $lang['stats_last_6']; ?></span>
<span id="stats_last_9" class="bmp-label bmp-label-grey bmp-middle"><?php echo $lang['stats_last_9']; ?></span>
<span id="stats_trends" class="bmp-label bmp-label-grey bmp-middle">Trends</span>
<hr size="1" />
<div id="stats_data">

</div>
<div id="statres" class="btn-red"></div>
<div id="chart_container" style="position: relative; width:40%;clear:both">
    <canvas id="trendsChart" width="auto" height="400" style="display:none;width:auto;height:400px;" aria-label="Trends Chart" role="img">
        <p>Plotted line chart</p>
    </canvas>
</div>
<script>
$(document).ready(function() {
    
    var global_iy = 0;
    
	$('#stats_time_frm').on('change','select',function(){
		//var im = $('#cost_month').val();
		let iy = $('#stats_time').val();
		if (iy != NaN) {
			$("#stats_info > div#statres").append('<img src="images/loader.gif" />');
			$.get("index.php",
			{iy:iy,task: "stats",pos: "before",lang: "<?php echo $activeLanguage; ?>"},
			function(data, textStatus, jqXHR){
				$("#stats_info > div#statres").html('&nbsp;');
				if(data.status === "success") {
					$("#stats_data").html(data.message);
					$("#stats_info > div#statres").hide("fast");
				} else if(data.status === "error") {
					$("#stats_info > div#statres").append(data.message);
					$("#stats_info > div#statres").delay(2000).hide("slow");
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#stats_info").show("fast").append(textStatus);
			});
            $("#stats_last_month").removeClass('bmp-label-blue').addClass('bmp-label-grey');
            $("#stats_last_3").removeClass('bmp-label-blue').addClass('bmp-label-grey');
            $("#stats_last_6").removeClass('bmp-label-blue').addClass('bmp-label-grey');
            $("#stats_last_9").removeClass('bmp-label-blue').addClass('bmp-label-grey');
		}
	});
    
    $("#stats_last_month").click(function(){
        get_the_stats(1);
        $(this).removeClass('bmp-label-grey').addClass('bmp-label-blue');
        $("#stats_last_3").removeClass('bmp-label-blue').addClass('bmp-label-grey');
        $("#stats_last_6").removeClass('bmp-label-blue').addClass('bmp-label-grey');
        $("#stats_last_9").removeClass('bmp-label-blue').addClass('bmp-label-grey');
    });
    
    $("#stats_last_3").click(function(){
        get_the_stats(3);
        global_iy = 3;
        $(this).removeClass('bmp-label-grey').addClass('bmp-label-blue');
        $("#stats_last_month").removeClass('bmp-label-blue').addClass('bmp-label-grey');
        $("#stats_last_6").removeClass('bmp-label-blue').addClass('bmp-label-grey');
        $("#stats_last_9").removeClass('bmp-label-blue').addClass('bmp-label-grey');
    });

    $("#stats_last_6").click(function(){
        get_the_stats(6);
        global_iy = 6;
        $(this).removeClass('bmp-label-grey').addClass('bmp-label-blue');
        $("#stats_last_month").removeClass('bmp-label-blue').addClass('bmp-label-grey');
        $("#stats_last_3").removeClass('bmp-label-blue').addClass('bmp-label-grey');
        $("#stats_last_9").removeClass('bmp-label-blue').addClass('bmp-label-grey');
    });

    $("#stats_last_9").click(function(){
        get_the_stats(9);
        global_iy = 9;
        $(this).removeClass('bmp-label-grey').addClass('bmp-label-blue');
        $("#stats_last_month").removeClass('bmp-label-blue').addClass('bmp-label-grey');
        $("#stats_last_6").removeClass('bmp-label-blue').addClass('bmp-label-grey');
        $("#stats_last_3").removeClass('bmp-label-blue').addClass('bmp-label-grey');        
    });
    
    function get_the_stats(iy) {
        $("#stats_info > div#statres").append('<img src="images/loader.gif" />');
        $.get("index.php",
        {iy:iy,task: "stats",pos: "before",lang: "<?php echo $activeLanguage; ?>"},
        function(data, textStatus, jqXHR){
            $("#stats_info > div#statres").html('&nbsp;');
            if(data.status === "success") {
                $("#stats_data").html(data.message);
                $("#stats_info > div#statres").hide("fast");
            } else if(data.status === "error") {
                $("#stats_info > div#statres").append(data.message);
                $("#stats_info > div#statres").delay(2000).hide("slow");
            }
        }, "json").fail(function(jqXHR, textStatus, errorThrown){
            $("#stats_info").show("fast").append(textStatus);
        });        
    }
    
    //trends
    $("#stats_trends").click(function(){
        $.get("index.php",
        {iy:global_iy,task: "trends",pos: "before",lang: "<?php echo $activeLanguage; ?>"},
        function(data, textStatus, jqXHR){
            $("#stats_info > div#statres").html('&nbsp;');
            if(data.status === "success") {
                $("#trendsChart").toggle();
                $("#stats_info > div#statres").hide("fast");
                
                const sourceData = data.data;

                const Years = [];
                const graphData = [];

                for (const key of Object.keys(sourceData)) {
                   Years.push(key);
                   graphData.push(sourceData[key]);
                }
                /*const ctx = document.getElementById('trendsChart').getContext('2d');
                
                const myLineChart = new Chart(ctx, {
                  type: 'line',
                  data: {
                    labels: Years,
                    datasets: [
                    {
                      label: 'Planet Incoming',
                      data: graphData,
                      borderColor: 'green',
                      backgroundColor: 'rgba(0, 0, 0, 0.1)',
                      fill: true,
                      animations: false 
                    },
                    ]
                  }
                });*/           

        } else if(data.status === "error") {
                $("#stats_info > div#statres").append(data.message);
                $("#stats_info > div#statres").delay(2000).hide("slow");
            }
        }, "json").fail(function(jqXHR, textStatus, errorThrown){
            $("#stats_info").show("fast").append(textStatus);
        });
    });

    let Income = {
      "Years" : [
        "2015",
        "2016",
        "2017",
        "2018",
        "2019",
        "2020",
        "2021",
        "2022",
      ],
      "amount": [
        2954.5,
        4410.0,
        3716.4,
        4071.4,
        1818.2,
        4444.53,
        5576.7,
        1893
      ]
    };
    
    let Cases = {
      "Years" : [
        "2015",
        "2016",
        "2017",
        "2018",
        "2019",
        "2020",
        "2021",
        "2022",
      ],
      "num": [
        1880,
        1450,
        1550,
        1430,
        650,
        1050,
        1140,
        580
      ]      
    }
    
    let IncomePerCase = {
       "Years" : [
        "2015",
        "2016",
        "2017",
        "2018",
        "2019",
        "2020",
        "2021",
        "2022",
      ],
      "IPC": [
        157,
        304,
        239,
        284,
        279,
        423,
        488,
        326
      ]  
    }

    const ctx = document.getElementById('trendsChart').getContext('2d');
    
    const myLineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: Income.Years,
        datasets: [
        {
          label: 'Income per Year',
          data: Income.amount,
          borderColor: 'green',
          backgroundColor: 'rgba(0, 0, 0, 0.1)',
          fill: true,
          animations: false,
        },
        {
          label: '# of Cases (x10)',
          data: Cases.num,
          borderColor: 'blue',
          backgroundColor: 'rgba(0, 0, 0, 0.1)',
          fill: true,
          animations: false             
        },
        {
          label: 'Income Per Case (x10)',
          data: IncomePerCase.IPC,
          borderColor: 'black',
          backgroundColor: 'rgba(0, 0, 0, 0.1)',
          fill: true,
          animations: false             
        }
        ]
      },
      options: {
        responsive:true,
      }
    });
});
</script>
