		<h3><?php echo $lang['lmenu']; ?></h3>
        <ul>
            <li>
				<span class="mcap">
					<a href="javascript:void(0);" onclick="$('#new_ticket').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-add-case']; ?></a>
				</span>
				<a href="javascript:$(this).preventDefault();" id="add_tk" title="add a new case" alt="add a new case"><img src="images/add.png" width="24" /></a>
			</li>
            <li>
				<span class="mcap">
					<a href="javascript:void(0);" onclick="$('#new_client').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-add-client']; ?></a>
				</span>
				<span id="add_client"></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#allclients').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-all-clients']; ?></a></span>
				<span id="all_clients"></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#esoda').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-income']; ?></a></span>
				<span id="income"></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#costs').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-expenses']; ?></a></span>
				<span id="expenses"></span>
			</li>
            <li style="display:none;">
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#events').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-events']; ?></a></span>
				<span id="events"></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#cms_info').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-cms']; ?></a></span>
				<span id="cms"></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#stats_info').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-stats']; ?></a></span> 
				<span id="stats"></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#settings').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-settings']; ?></a></span>
				<span id="settings_tk"></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#various').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-various']; ?></a></span>
			</li>
            <li>
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#prices').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-prices']; ?></a></span>
			</li>
            <li style="display:none;">
				<span class="mcap"><a href="javascript:void(0);" onclick="$('#this_todo').toggle();" style="color:white;text-decoration:none;"><?php echo $lang['lmenu-todo']; ?></a></span>
			</li>
        </ul>
