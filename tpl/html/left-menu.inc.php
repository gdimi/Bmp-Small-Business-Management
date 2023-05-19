		<h3><span><?php echo $lang['lmenu']; ?></span><a id="menuToggle" style="float:right;cursor:pointer;" title="shrink menu">&laquo;</a></h3>
        <ul>
            <li>
				<span class="mcap">
					<a href="javascript:void(0);" onclick="$('#add_tk').click();"><?php echo $lang['lmenu-add-case']; ?></a>
				
				<span" id="add_tk" title="add a new case" alt="add a new case"><img src="images/add.png" width="24" /></span></span>
			</li>
            <li>
				<span class="mcap">
					<a href="javascript:void(0);" onclick="$('#add_client').click();"><?php echo $lang['lmenu-add-client']; ?></a>
				<span id="add_client" title="<?php echo $lang['lmenu-add-client']; ?>"></span></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#all_clients').click();"><?php echo $lang['lmenu-all-clients']; ?></a>
				<span id="all_clients" title="<?php echo $lang['lmenu-all-clients']; ?>"></span></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#income').click();"><?php echo $lang['lmenu-income']; ?></a>
				<span id="income" title="<?php echo $lang['lmenu-income']; ?>"></span></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#expenses').click();"><?php echo $lang['lmenu-expenses']; ?></a>
				<span id="expenses" title="<?php echo $lang['lmenu-expenses']; ?>"></span></span>
			</li>
            <li style="display:none;">
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#events').toggle();" title="<?php echo $lang['lmenu-events']; ?>"><?php echo $lang['lmenu-events']; ?></a></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#cms').click();"><?php echo $lang['lmenu-cms']; ?></a>
				<span id="cms" title="<?php echo $lang['lmenu-cms']; ?>"></span></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#stats').click();"><?php echo $lang['lmenu-stats']; ?></a> 
				<span id="stats" title="<?php echo $lang['lmenu-stats']; ?>"></span></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#settings_tk').click();"><?php echo $lang['lmenu-settings']; ?></a>
				<span id="settings_tk" title="<?php echo $lang['lmenu-settings']; ?>"></span></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#various').toggle();"  title="<?php echo $lang['lmenu-various']; ?>"><?php echo $lang['lmenu-various']; ?></a></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#prices').toggle();"  title="<?php echo $lang['lmenu-prices']; ?>"><?php echo $lang['lmenu-prices']; ?></a></span>
			</li>
            <li style="display:none;">
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#this_todo').toggle();"><?php echo $lang['lmenu-todo']; ?></a></span>
			</li>
        </ul>
