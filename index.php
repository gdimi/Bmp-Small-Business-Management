<?php
define('_w00t_frm',true);
require_once('sources/init.php');
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $dss->sitename; ?></title>
	<meta charset="utf-8" />
	<meta generator="FOSS" />
	<meta name="Googlebot" content="no-index,no-follow" />
	<meta name="robots" content="no-index,no-follow" />
	<meta name="Reply-to" content="info@hybridwebs.gr" />
	<meta name="Author" content="George Dimitrakopoulos" />
    <meta name="referrer" content="no-referrer-when-downgrade">
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=5.0" />
	<meta name="theme-color" content="#577C0E"/>
	<link rel="shortcut icon" href="favicon.ico" />
	<script type="text/javascript"> var activeLanguage = "<?php echo $activeLanguage; ?>"; </script>
    <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>-->
    <script src="sources/js/jquery.min.js"></script>
    <!--<script src="sources/js/tinymce/tinymce.gzip.js"></script>
	<script>tinymce.init({selector:'textarea.rich'});</script>-->
    <!--<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.13.0/build/cssbase/cssbase-min.css">-->
	<script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>
    
	<link href="tpl/css/cssbase-min.css" rel="stylesheet" type="text/css" />
	<link href="tpl/css/style.css" rel="stylesheet" type="text/css" />
    <?php if ($dss->style['flat'] == 'yes'): ?>
	<link href="tpl/css/flat.css" rel="stylesheet" type="text/css" />
    <?php endif; ?>
  	<link href="tpl/css/responsive.css" rel="stylesheet" type="text/css" />

</head>
<body>
   <header id="top" <?php if ($dss->style['top_bar_bg']) { echo 'style="background-color: '.$dss->style['top_bar_bg'].';"';} ?>>
	<?php if ($dss->style['logo']) { ?>
	<img src="<?php echo $dss->style['logo'];?>" alt="<?php echo $dss->project_name; ?>" title="<?php echo $dss->project_name; ?>" style="width:130px">
	<?php } else { ?>
        <h1><?php echo $dss->project_name; ?></h1>
	<?php } ?>
        <?php if ($cms->motd) { echo '<span class="motd">'.$cms->motd.'</span>'; } ?>
    </header>
    <aside id="menu" <?php echo ($dss->style['left_bar_bg']) ? 'style="background-color: '.$dss->style['left_bar_bg'].';"' : '';?>>
		<?php include('tpl/html/left-menu.inc.php'); ?>
    </aside>
	<section id="status">
		<header>
			<span class="cisearch">
				<form>
					<input type="text" name="cisearch" id="cisearch" value="" />
					<span class="fake-button" id="cisubmit"><?php echo $lang['find']; ?></span>
				</form>
			</span>
			<?php echo $lang['case-tracker']; ?>
			<span class="filters"><?php include('tpl/html/caseFilter.inc.php'); ?></span>
		</header>
		<?php include_once('tpl/html/caseTracker.inc.php'); ?>
   </section>
   <div id="board">
		<?php echo $cms->board; ?>
   </div>
   <section id="history">
		<header><?php echo $lang['history']; ?></header>
        <div>
		<?php if ($dev_history) {
			echo $dev_history;
		  } ?>
        </div>
   </section>
   <section id="trash">
        <?php include('tpl/html/trashMain.inc.php'); ?>
   </section>
   <footer>Version <?php include('VERSION'); ?></footer>
   <div id="new_ticket" class="elevate menu-dialog draggable">
		<?php include('tpl/html/frmAddTicket.inc.php'); ?>
   </div>
   <div id="new_client" class="elevate menu-dialog draggable" data-cardinality="">
		<?php include('tpl/html/frmAddClient.inc.php'); ?>
   </div>
   <div id="settings" class="elevate menu-dialog draggable" data-cardinality="">
		<?php include('tpl/html/settings.inc.php'); ?>
   </div>
   <div id="client" class="elevate menu-dialog draggable" data-cardinality="">
		<?php include('tpl/html/client.inc.php'); ?>
   </div>
   <div id="allclients" class="elevate menu-dialog draggable data-cardinality="">
		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['close-me']; ?></span>
		<h2 class="drag-head" id="allclients-handle"><?php echo $lang['all-clients']; ?></h2>
		<div></div>
   </div>
   <div id="esoda" class="elevate menu-dialog draggable" data-cardinality="">
<?php include('tpl/html/income.inc.php'); ?>
   </div>
   <div id="costs" class="elevate menu-dialog draggable" data-cardinality="">
<?php include('tpl/html/costs.inc.php'); ?>
   </div>
   <div id="gen_res" class="elevate menu-dialog">
		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['close-me']; ?></span>
		<div></div>
   </div>
   <div id="cis_res" class="elevate menu-dialog">
		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['close-me']; ?></span>
		<div></div>
   </div>
   <div id="stats_info" class="elevate menu-dialog draggable" data-cardinality="">
		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['close-me']; ?></span>
<?php include('tpl/html/stats.inc.php'); ?>
		<div></div>
   </div>
   <div id="various" class="elevate menu-dialog draggable" data-cardinality="">
<?php include('tpl/html/various.inc.php'); ?>
   </div>
   <div id="prices" class="elevate menu-dialog draggable" contenteditable="true" data-cardinality="">
   		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['close-me']; ?></span>
<?php include('tpl/html/prices.inc.php'); ?>
   </div>
   <div id="cms_info" class="elevate menu-dialog draggable" style="width: auto; max-width: 848px; display: none; left: 20%; top: 1%;" data-cardinality="">
   		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['close-me']; ?></span>
<?php @include('tpl/html/cms.inc.php'); ?>
   </div>
   <div id="trash_info" class="elevate menu-dialog draggable" style="width: auto; max-width: 848px; display: none; left: 20%; top: 30%;" data-cardinality="">
   		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['close-me']; ?></span>
<?php @include('tpl/html/trash.inc.php'); ?>
   </div> 
   <div id="this_todo" class="elevate menu-dialog" style="display:none;" data-cardinality="">
		<span class="cl-b" onclick="$(this).parent().toggle();"><?php echo $lang['close-me']; ?></span>
		<h2>Development</h2>
		<hr size="1" />
		<div><pre>
			<?php include('CHANGELOG'); ?>
		</div></pre>
   </div>
   <div class="api-msg gen-error" style="display:none;">
        <span>Warning:</span> Content has changed since last time! <br> Reload the page to see the latest changes! 
   </div>
<script>
<?php include('sources/js/main.js'); ?>
</script>
</body>
</html>
