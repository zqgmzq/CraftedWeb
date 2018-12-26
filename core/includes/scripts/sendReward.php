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


    require "../ext_scripts_class_loader.php";

    global $Database, $Account, $Shop, $Character;

    if ( isset($_POST['item_entry']) )
    {
        $entry           = $Database->conn->escape_string($_POST['item_entry']);
        $character_realm = $Database->conn->escape_string($_POST['character_realm']);
        $type            = $Database->conn->escape_string($_POST['send_mode']);

        if ( empty($entry) || empty($character_realm) || empty($type) )
        {
            echo '<b class="red_text">Please specify a character.</b>';
        }
        else
        {
            $Database->selectDB("webdb");

            $realm = explode("*", $character_realm);

            $statement       = $Database->select("shopitems", "price", null, "entry=$entry");
            $result       = $statement->get_result(); 
            $row          = $result->fetch_assoc();
            $account_id   = $Account->getAccountIDFromCharId($realm[0], $realm[1]);
            $account_name = $Account->getAccountName($account_id);

            if ( $type == 'vote' )
            {
                if ( $Account->hasVP($account_name, $row['price']) == FALSE )
                {
                    die('<b class="red_text">You do not have enough Vote Points</b>');
                }

                $Account->deductVP($account_id, $row['price']);
            }
            elseif ( $type == 'donate' )
            {
                if ( $Account->hasDP($account_name, $row['price']) == FALSE )
                {
                    die('<b class="red_text">You do not have enough ' . DATA['website']['donation']['coins_name'] . '</b>');
                }

                $Account->deductDP($account_id, $row['price']);
            }
            $statement->close();


            $Shop->logItem($type, $entry, $realm[0], $account_id, $realm[1], 1);
            $statement = $Database->select("realms", null, null, "id=". $realm[1]);
            $result = $statement->get_result();
            $row    = $result->fetch_assoc();

            if ( $row['sendType'] == 'ra' )
            {
                require "../misc/ra.php";
                require "../classes/character.php";

                sendRa("send items " . $Character->getCharname($realm[0]) . " \"Your requested item\" \"Thanks for supporting us!\" " . $entry . " ", $row['rank_user'], $row['rank_pass'], $row['host'], $row['ra_port']);
                $statement->close();
            }
            elseif ( $row['sendType'] == 'soap' )
            {
                require "../misc/soap.php";
                require "../classes/character.php";

                sendSoap("send items " . $Character->getCharname($realm[0]) . " \"Your requested item\" \"Thanks for supporting us!\" " . $entry . " ", $row['rank_user'], $row['rank_pass'], $row['host'], $row['soap_port']);
                $statement->close();
            }
        }
    }
