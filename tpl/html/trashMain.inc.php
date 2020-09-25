<div><?php echo $lang['trash-size']; ?>: <?php echo $trashSize; ?> Kbytes <?php if (isset($trasWarn)) echo $trashWarn; ?></div>
<div><a href="javascript:void(0);" id="trash_show"><?php echo $lang['trash-objects']; ?></a> <?php echo $lang['in-trash']; ?>: <?php echo $trashFiles; ?> <?php if (isset($trasWarn)) echo $trashWarn; ?></div>
