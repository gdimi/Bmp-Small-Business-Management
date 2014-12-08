		<h3><?php echo $lang['lmenu']; ?></h3>
        <ul>
            <li><span class="mcap"><?php echo $lang['lmenu-add-case']; ?></span><a href="javascript:$(this).preventDefault();" id="add_tk" title="add a new case" alt="add a new case"><img src="images/add.png" width="24" /></a></li>
            <li><span class="mcap"><?php echo $lang['lmenu-add-client']; ?></span> <span id="add_client"></span></li>
            <li><span class="mcap"><?php echo $lang['lmenu-all-clients']; ?></span> <span id="all_clients"></span></li>
            <li><span class="mcap"><?php echo $lang['lmenu-income']; ?></span> <span id="income"></span></li>
            <li><span class="mcap"><?php echo $lang['lmenu-expenses']; ?></span> <span id="expenses"></span></li>
            <li><span class="mcap"><?php echo $lang['lmenu-events']; ?></span> <span id="events"></span></li>
            <li><span class="mcap" id="cms"><?php echo $lang['lmenu-cms']; ?></span> <span id="cms_img"></span></li>
            <li><span class="mcap"><?php echo $lang['lmenu-stats']; ?></span> <span id="stats"></span></li>
            <li><span class="mcap"><?php echo $lang['lmenu-settings']; ?></span> <span id="settings_tk"></span></li>
            <li><span class="mcap"><a href="javascript:void(0);" onclick="$('#various').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-various']; ?></a></span></li>           
            <li><span class="mcap"><a href="javascript:void(0);" onclick="$('#prices').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-prices']; ?></a></span></li>
            <li><span class="mcap"><a href="javascript:void(0);" onclick="$('#this_todo').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-todo']; ?></a></span></li>
        </ul>
