<h2><?php echo $lang['trash']; ?></h2>
<hr size="1" />
<div id="trash_data">

</div>
<div id="trash_obj_view">
    <h3 class="general-title">Object View</h3>
    <div id="transh_obj_view_data"></div>
    <div id="trash_actions"></div>
</div>

<script id="trash_object_tpl" type="text/x-handlebars-template">
    <ul>
    {{#each trashdata}}
        <li><span>{{@key}}</span> : {{this}}</li>
    {{/each}}
    <ul>
</script>

<script>
 $(document).ready(function(){
	//catch trashed items click
	$("#trash_data").on('click', '.to' ,function(){
		var tobjname = '';
        //Get list of CSS class names
        //var classNames = $(this).attr("class").toString().split(' '); //beware the only one space!!!
        var classNames = $(this).prop("classList"); //beware the only one space!!!
        $.each(classNames, function (i, className) {
            if (className !== 'to') {
				tobjname = className;
			}
        });
        if (tobjname !== '') {
			$.get("index.php",
			{to : tobjname, task: "trashObj",pos: "before"},
			function(data, textStatus, jqXHR){
				if(data.status === "success") {
                    var tobjdata = { "trashdata" : JSON.parse(data.data) };
                    
                    //console.log(tobjdata);

                    // Retrieve the template data from the HTML
                    var template = $('#trash_object_tpl').html();

                    // Compile the template data into a function
                    var templateScript = Handlebars.compile(template);

                    var html = templateScript(tobjdata);

                    // Insert the HTML code into the page
                    $('#transh_obj_view_data').html(html);

				} else if(data.status === "error") {
					$("#transh_obj_view_data").html('<div class="gen-error">'+data.message+'</div>');
				}
			}, "json").fail(function(jqXHR, textStatus, errorThrown){
				$("#trash_obj_view").html(textStatus);
			});	
		}
		console.log("a trash file is clicked!"+tobjname);
    });
 });
</script>