<h2><?php echo $lang['cms']; ?></h2> <?php echo $lang['cms-msg']; ?>
<hr size="1" />
<div id="cms_intro">
<?php echo $lang['cms-intro']; ?>
</div>
<hr size="1" />
<div id="cms_data">
    <form>
        <label for="motd"><?php echo $lang['cms-motd']; ?></label><br />
        <textarea id="motd_txt" name="motd"></textarea><br />
        <span class="fake-button" id="cms_save_motd"><?php echo $lang['cms-motd-save']; ?></span><br /><br />
        <label for="board"><?php echo $lang['cms-board']; ?></label><br />
        <textarea id="board_txt" name="board"></textarea><br />
        <span class="fake-button" id="cms_save_board"><?php echo $lang['cms-board-save']; ?></span><br />
    </form>
    <span class="fake-button" style="float:right;" id="cms_save"><?php echo $lang['cms-save-all']; ?></span>
    <div id="cms_res"></div>
</div>

<script>
$("#cms_save").click(function(){
	$("#csm_res").append('<img src="images/loader.gif" />');
    var motdtxt = $("#motd_txt").val();
    var boardtxt = $("#board_txt").val();
    var cURL = "index.php?task=cmsupd&pos=before";

    $.post(cURL,
    {motd: motdtxt, board: boardtxt, what: "all"},
    function(data, textStatus, jqXHR){
        if(data.status === "success") {
            $("#cms_res").show('fast').html("<?php echo $lang['cms-save-ok']; ?>").delay(2000).hide('slow');
        } else if(data.status === "error") {
            $("#cms_res").show('fast').html(data.message).delay(2000).hide('slow');
        }
    }, "json").fail(function(jqXHR, textStatus, errorThrown){
        $("#cms_res").show('fast').append(textStatus);
    });
});
</script>
