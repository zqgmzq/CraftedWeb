<?php
#   ___           __ _           _ __    __     _     
#  / __\ __ __ _ / _| |_ ___  __| / / /\ \ \___| |__  
# / / | '__/ _` | |_| __/ _ \/ _` \ \/  \/ / _ \ '_ \ 
#/ /__| | | (_| |  _| ||  __/ (_| |\  /\  /  __/ |_) |
#\____/_|  \__,_|_|  \__\___|\__,_| \/  \/ \___|_.__/ 
#
#		-[ Created by �Nomsoft
#		  `-[ Original core by Anthony (Aka. CraftedDev)
#
#				-CraftedWeb Generation II-                  
#			 __                           __ _   							   
#		  /\ \ \___  _ __ ___  ___  ___  / _| |_ 							   
#		 /  \/ / _ \| '_ ` _ \/ __|/ _ \| |_| __|							   
#		/ /\  / (_) | | | | | \__ \ (_) |  _| |_ 							   
#		\_\ \/ \___/|_| |_| |_|___/\___/|_|  \__|	- www.Nomsoftware.com -	   
#                  The policy of Nomsoftware states: Releasing our software   
#                  or any other files are protected. You cannot re-release    
#                  anywhere unless you were given permission.                 
#                  � Nomsoftware 'Nomsoft' 2011-2012. All rights reserved.    
    if (isset($_SESSION['cw_user']))
    { ?>
        <div class="box_one">
            <div class="box_one_title">Account Management</div>
            <span style="z-index: 99;">Welcome back <?php echo $_SESSION['cw_user']; ?>
                <?php
                if (isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel'] >= DATA['website']['admin']['minlvl'] && DATA['admin']['enable'] == TRUE)
                {
                    echo ' <a href="admin/">(Admin Panel)</a>';
                }

                if (isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel'] >= DATA['website']['staff']['minlvl'] && DATA['staff']['enable'] == TRUE)
                {
                    echo ' <a href="staff/">(Staff Panel)</a>';
                }
                ?>
            </span>
            <hr/>
            <input type='button' value='Account Panel'      onclick='window.location = "?page=account"'    class="leftbtn">
            <input type='button' value='Change Password'    onclick='window.location = "?page=changepass"' class="leftbtn">
            <input type='button' value='Vote Shop'          onclick='window.location = "?page=voteshop"'   class="leftbtn">  
            <input type='button' value='Donation Shop'      onclick='window.location = "?page=donateshop"' class="leftbtn">
            <input type='button' value='Refer-A-Friend'     onclick='window.location = "?page=raf"'        class="leftbtn">
            <input type='button' value='Log Out'            onclick='window.location = "?page=logout&last_page=<?php echo $_SERVER["REQUEST_URI"]; ?>"' class="leftbtn">
        </div>
    <?php }