<h2>CMS</h2> (δλδ Content Management System)
<hr size="1" />
<div id="cms_intro">
Εδώ μπορείτε να κάνετε edit το message of the day (motd) και τον πίνακα που γράφουμε γενικώς διάφορα (board)
</div>
<hr size="1" />
<div id="cms_data">
    <form>
        <label for="motd">Message of the day</label><br />
        <textarea id="motd_txt" name="motd"></textarea><br />
        <span class="fake-button" id="cms_save_motd">Save motd</span><br /><br />
        <label for="board">Board</label><br />
        <textarea id="board_txt" name="board"></textarea><br />
        <span class="fake-button" id="cms_save_board">Save board</span><br />
    </form>
    <span class="fake-button" style="float:right;" id="cms_save">Save all</span>
    <div id="cms_res"></div>
</div>

<script>
$("#cms_save").click(function(){
    var motdtxt = $("#motd_txt").val();
    var boardtxt = $("#board_txt").val();
    var cURL = "index.php?task=cmsupd&pos=before";

    $.post(cURL,
    {motd: motdtxt, board: boardtxt, what: "all"},
    function(data, textStatus, jqXHR){
        if(data.status === "success") {
            $("#cms_res").show('fast').html("Ok all updated!").delay(2000).hide('slow');
        } else if(data.status === "error") {
            $("#cms_res").show('fast').html(data.message).delay(2000).hide('slow');
        }
    }, "json").fail(function(jqXHR, textStatus, errorThrown){
        $("#cms_res").show('fast').append(textStatus);
    });
});
</script>
