<?php
    /* ___           __ _           _ __    __     _     
      / __\ __ __ _ / _| |_ ___  __| / / /\ \ \___| |__
      / / | '__/ _` | |_| __/ _ \/ _` \ \/  \/ / _ \ '_ \
      / /__| | | (_| |  _| ||  __/ (_| |\  /\  /  __/ |_) |
      \____/_|  \__,_|_|  \__\___|\__,_| \/  \/ \___|_.__/

      -[ Created by �Nomsoft
      `-[ Original core by Anthony (Aka. CraftedDev)

      -CraftedWeb Generation II-
      __                           __ _
      /\ \ \___  _ __ ___  ___  ___  / _| |_
      /  \/ / _ \| '_ ` _ \/ __|/ _ \| |_| __|
      / /\  / (_) | | | | | \__ \ (_) |  _| |_
      \_\ \/ \___/|_| |_| |_|___/\___/|_|  \__|	- www.Nomsoftware.com -
      The policy of Nomsoftware states: Releasing our software
      or any other files are protected. You cannot re-release
      anywhere unless you were given permission.
      � Nomsoftware 'Nomsoft' 2011-2012. All rights reserved. */

    class Shop
    {
        public function search($value, $shop, $quality, $type, $ilevelfrom, $ilevelto, $results, $faction, $class, $subtype)
        {
            global $Database;
            $conn = $Database->database();
            $Database->selectDB("webdb", $conn);

            $value      = $Database->conn->escape_string($value);
            $shop       = $Database->conn->escape_string($shop);
            $quality    = $Database->conn->escape_string($quality);
            $ilevelfrom = $Database->conn->escape_string($ilevelfrom);
            $ilevelto   = $Database->conn->escape_string($ilevelto);
            $results    = $Database->conn->escape_string($results);
            $faction    = $Database->conn->escape_string($faction);
            $class      = $Database->conn->escape_string($class);
            $type       = $Database->conn->escape_string($type);
            $subtype    = $Database->conn->escape_string($subtype);

            if ($value == "Search for an item...")
            {
                $value = "";
            }

            $advanced = NULL;

            ####Advanced Search
            if ( DATA['website']['shop'][$shop]['enable_advanced_search'] == TRUE )
            {
                if ($quality != "--Quality--")
                {
                    $advanced .= " AND quality='" . $quality . "'";
                }

                if ($type != "--Type--")
                {
                    if ($type == "15-5" || $type == "15-5")
                    {
                        //Mount or pet
                        $type     = explode('-', $type);
                        $advanced .= " AND type='" . $type[0] . "' AND subtype='" . $type[1] . "'";
                    }
                    else
                    {
                        $advanced .= " AND type='" . $type . "'";
                    }
                }

                if ($faction != "--Faction--")
                {
                    $advanced .= " AND faction='" . $faction . "'";
                }

                if ($class != "--Class--")
                {
                    $advanced .= " AND class='" . $class . "'";
                }

                if ($ilevelfrom != "--Item level from--")
                {
                    $advanced .= " AND itemlevel>='" . $ilevelfrom . "'";
                }

                if ($ilevelto != "--Item level to--")
                {
                    $advanced .= " AND itemlevel<='" . $ilevelto . "'";
                }

                $count = $Database->select("shopitems", "COUNT(id) AS item", null, "name LIKE '%$value%' AND in_shop='$shop' $advanced")->get_result();

                if ($count->data_seek(0) == 0)
                {
                    $count = 0;
                }
                else
                {
                    $count = $count->fetch_assoc()['item'];
                }

                if ($results != "--Results--")
                {
                    $advanced .= " ORDER BY name ASC LIMIT " . $results;
                }
                else
                {
                    $advanced .= " ORDER BY name ASC LIMIT 250";
                }
            }

            $result = $Database->select("shopitems", null, null, "name LIKE '%$value%' AND in_shop='". $Database->conn->escape_string($shop) ."' $advanced")->get_result();

            if ($results != "--Results--")
            {
                $limited = $results;
            }
            else
            {
                $limited = $result->num_rows;
            }

            echo "<div class='shopBox'><b>" . $count . "</b> results found. (" . $limited . " displayed)</div>";

            if ($result->num_rows == 0)
            {
                echo '<b class="red_text">No results found!</b><br/>';
            }
            else
            {
                while ($row = $result->fetch_assoc())
                {
                    $entry = $row['entry'];

                    switch ($row['quality'])
                    {
                        default:
                            $class = "white";
                            break;

                        case(0):
                            $class = "gray";
                            break;

                        case(2):
                            $class = "green";
                            break;

                        case(3):
                            $class = "blue";
                            break;

                        case(4):
                            $class = "purple";
                            break;

                        case(5):
                            $class = "orange";
                            break;

                        case(6):
                            $class = "gold";
                            break;

                        case(7):
                            $class = "gold";
                            break;
                    }

                    $getIcon = $Database->select("item_icons", "icon", null, "displayid=". $row['displayid'])->get_result();
                    if ($getIcon->num_rows == 0)
                    {
                        //No icon found. Probably cataclysm item. Get the icon from wowhead instead.
                        $sxml = new SimpleXmlElement(file_get_contents('http://www.wowhead.com/item='. $entry .'&xml'));

                        $icon = $Database->conn->escape_string(strtolower($sxml->item->icon));
                        //Now that we have it loaded. Add it into database for future use.
                        //Note that WoWHead XML is extremely slow. This is the main reason why we're adding it into the db.
                        $Database->conn->query("INSERT INTO item_icons VALUES(". $row['displayid'] .", '". $icon ."');");
                    }
                    else
                    {
                        $iconrow = $getIcon->fetch_assoc();
                        $icon    = strtolower($iconrow['icon']);
                    }
                    ?>
                    <div class="shopBox" id="item-<?php echo $entry; ?>"> 
                        <table>
                            <tr> 
                                <td>
                                    <div class="iconmedium icon" rel="50818">
                                        <ins style="background-image: url('http://static.wowhead.com/images/wow/icons/medium/<?php echo $icon; ?>.jpg');">
                                        </ins>
                                        <del></del>
                                    </div>
                                </td>
                                <td width="380">
                                    <a href="http://<?php echo DATA['website']['tooltip_href']; ?>item=<?php echo $entry; ?>" 
                                       class="<?php echo $class; ?>_tooltip" target="_blank">
                                        <?php echo $row['name']; ?></a>
                                </td>
                                <td align="right" width="350">
                                    <?php
                                    if ($row['faction'] == 2)
                                    {
                                        echo "<span class='blue_text'>Alliance only </span>";
                                        if ($row['class'] != "-1")
                                        {
                                            echo "<br/>";
                                        }
                                    }
                                    elseif ($row['faction'] == 1)
                                    {
                                        echo "<span class='red_text'>Horde only </span>";
                                        if ($row['class'] != "-1")
                                        {
                                            echo "<br/>";
                                        }
                                    }

                                    if ($row['class'] != "-1")
                                    {
                                        echo $this->getClassMask($row['class']);
                                    }


                                    if (isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel'] >= DATA['website']['admin']['minlvl'] ||
                                        isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel'] >= DATA['website']['staff']['minlvl'] && DATA['website']['staff']['permissions']['editShopItems'] == TRUE)
                                    {
                                        ?>
                                        <font size="-2">( 
                                        <a onclick="editShopItem('<?php echo $entry; ?>', '<?php echo $shop; ?>', '<?php echo $row['price']; ?>')">Edit</a> | 
                                        <a onclick="removeShopItem('<?php echo $entry; ?>', '<?php echo $shop; ?>')">Remove</a> 
                                        )</font>
                                        &nbsp; &nbsp; &nbsp; &nbsp;
                                        <?php
                                    }
                                    ?>
                                    <font class="shopItemPrice"><?php echo $row["price"]; ?> 
                                    <?php
                                    if ($shop == "donate")
                                    {
                                        echo DATA['website']['donation']['coins_name'];
                                    }
                                    else
                                    {
                                        echo 'Vote Points';
                                    }
                                    ?>
                                    </font>

                                    <div style="display:none;" id="status-<?php echo $entry; ?>" class="green_text">
                                        The item was added to your cart
                                    </div>
                                </td>
                                <td>
                                    <input type="button" value="Add to cart" onclick="addCartItem(<?php echo $entry; ?>, '<?php echo $shop; ?>Cart',
                                                    '<?php echo $shop; ?>', this)"> 
                                </td> 
                            </tr> 
                        </table> 
                    </div>
                    <?php
                }
            }
        }

        public function listAll($shop)
        {
            global $Database;
            $conn = $Database->database();
            $Database->selectDB("webdb", $conn);

            $shop = $Database->conn->escape_string($shop);

            $result = $Database->select("shopitems", null, null, "in_shop='$shop'")->get_result();

            if ($result->num_rows == 0)
            {
                echo 'No items was found in the shop.';
            }
            else
            {
                while ($row = $result->fetch_assoc())
                {
                    $entry   = $row['entry'];
                    $getIcon = $Database->select("item_icons", "icon", null, "displayid=". $row['displayid'])->get_result();
                    if ($getIcon->num_rows == 0)
                    {
                        //No icon found. Probably cataclysm item. Get the icon from wowhead instead.
                        $sxml = new SimpleXmlElement(file_get_contents('http://www.wowhead.com/item=' . $entry . '&xml'));

                        $icon = $Database->conn->escape_string(strtolower($sxml->item->icon));
                        //Now that we have it loaded. Add it into database for future use.
                        //Note that WoWHead XML is extremely slow. This is the main reason why we're adding it into the db.
                        $Database->conn->query("INSERT INTO item_icons VALUES(". $row['displayid'] .", '". $icon ."');");
                    }
                    else
                    {
                        $iconrow = $getIcon->fetch_assoc();
                        $icon    = strtolower($iconrow['icon']);
                    }
                    ?>
                    <div class="shopBox" id="item-<?php echo $entry; ?>"> 
                        <table>
                            <tr> 
                                <td>
                                    <div class="iconmedium icon" rel="50818">
                                        <ins style="background-image: url('http://static.wowhead.com/images/wow/icons/medium/<?php echo $icon; ?>.jpg');"></ins>
                                        <del></del>
                                    </div>
                                </td>
                                <td width="380">
                                    <a href="http://<?php echo DATA['website']['tooltip_href']; ?>item=<?php echo $entry; ?>" class="<?php echo $class; ?>_tooltip" target="_blank">
                                        <?php echo $row['name']; ?>
                                    </a>
                                </td>
                                <td align="right" width="350">
                                    <?php
                                    if ($row['faction'] == 2)
                                    {
                                        echo "<span class='blue_text'>Alliance only </span>";
                                        if ($row['class'] != "-1")
                                        {
                                            echo "<br/>";
                                        }
                                    }
                                    elseif ($row['faction'] == 1)
                                    {
                                        echo "<span class='red_text'>Horde only </span>";
                                        if ($row['class'] != "-1")
                                        {
                                            echo "<br/>";
                                        }
                                    }

                                    if ($row['class'] != "-1")
                                    {
                                        echo $Shop->getClassMask($row['class']);
                                    }

                                    if (isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel'] >= 5)
                                    {
                                        ?>
                                        <font size="-2">( 
                                        <a onclick="editShopItem('<?php echo $entry; ?>', '<?php echo $shop; ?>', '<?php echo $row['price']; ?>')">Edit</a> | 
                                        <a onclick="removeShopItem('<?php echo $entry; ?>', '<?php echo $shop; ?>')">Remove</a>
                                        )</font>
                                        &nbsp; &nbsp; &nbsp; &nbsp;   
                                        <?php
                                    }
                                    ?>
                                    <font class="shopItemPrice"><?php echo $row["price"]; ?> 
                                    <?php
                                    if ($shop == "donate")
                                    {
                                        echo DATA['website']['donation']['coins_name'];
                                    }
                                    else
                                    {
                                        echo 'Vote Points';
                                    }
                                    ?>
                                    </font>

                                    <div style="display:none;" id="status-<?php echo $entry; ?>" class="green_text">
                                        The item was added to your cart
                                    </div>
                                </td>
                                <td>
                                    <input type="button" value="Add to cart" 
                                           onclick="addCartItem(<?php echo $entry; ?>, '<?php echo $shop; ?>Cart',
                                                           '<?php echo $shop; ?>', this)"> 
                                </td> 
                            </tr> 
                        </table> 
                    </div>
                    <?php
                }
            }
        }

        public function logItem($shop, $entry, $char_id, $account, $realm_id, $amount)
        {
            global $Database;
            $conn = $Database->database();;
            $Database->selectDB("webdb", $conn);

            date_default_timezone_set(DATA['website']['timezone']);

            $entry      = $Database->conn->escape_string($entry);
            $char_id    = $Database->conn->escape_string($char_id);
            $shop       = $Database->conn->escape_string($shop);
            $account    = $Database->conn->escape_string($account);
            $realm_id   = $Database->conn->escape_string($realm_id);
            $amount     = $Database->conn->escape_string($amount);



            $Database->conn->query("INSERT INTO shoplog (`entry`, `char_id`, `date`, `ip`, `shop`, `account`, `realm_id`, `amount`) VALUES 
                (". $entry .", '". $char_id ."', '". date("Y-m-d H:i:s") ."', '". $_SERVER['REMOTE_ADDR'] ."', '". $shop ."', '". $account ."', ". $realm_id .", '". $amount ."')");
        }

        public function getClassMask($classID)
        {
            switch ($classID)
            {

                case(1):
                    return "<span class='warrior_color'>Warrior only</span> <br/>";
                    break;

                case(2):
                    return "<span class='paladin_color'>Paladin only</span> <br/>";
                    break;

                case(4):
                    return "<span class='hunter_color'>Hunter only</span> <br/>";
                    break;

                case(8):
                    return "<span class='rogue_color'>Rogue only</span> <br/>";
                    break;

                case(16):
                    return "<span class='priest_color'>Priest only</span> <br/>";
                    break;

                case(32):
                    return "<span class='dk_color'>Death Knight only</span> <br/>";
                    break;

                case(64):
                    return "<span class='shaman_color'>Shaman only</span> <br/>";
                    break;

                case(128):
                    return "<span class='mage_color'>Mage only</span> <br/>";
                    break;

                case(256):
                    return "<span class='warlock_color'>Warlock only</span> <br/>";
                    break;

                case(1024):
                    return "<span class='druid_color'>Druid only</span> <br/>";
                    break;
            }
        }

    }

    $Shop = new Shop();
    