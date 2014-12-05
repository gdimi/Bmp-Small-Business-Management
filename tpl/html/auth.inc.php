<h2>!!YOU SHALL NOT PASS!!</h2>
<form name="auth_frm" id="auth_frm" action="index.php?task=auth&pos=con" method="post">
	Username:<input type="text" name="un" id="un" value="" size="20" maxsize="20" /><br />
	Password:<input type="password" name="unp" id="unp" value="" size="20" maxsize="20" /><br />
	<input type="submit" id="uns" value="Μπες" />
</form>
<script>
  $('#uns').click(function(){
	  $(this).preventDefault();
	  var username = $("#un").val();
	  var password = $("#unp").val();
  });
</script>
