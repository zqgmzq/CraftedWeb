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

    class Plugins
    {

        public static function globalInit()
        {
            if ( DATA['website']['enable_plugins'] == TRUE )
            {
                if ( !isset($_SESSION['loaded_plugins']) )
                {
                    global $Database;
                    $loaded_plugins = array();

                    $bad   = array('.', '..', 'index.html');
                    $count = 0;

                    $folder = scandir('core/plugins/');
                    if ( is_array($folder) || is_object($folder) )
                    {
                        foreach ($folder as $folderName)
                        {
                            if ( !in_array($folderName, $bad) )
                            {
                                $Database->selectDB("webdb");
                                if ( file_exists('core/plugins/'. $folderName .'/config.php') )
                                {
                                    include "core/plugins/". $folderName ."/config.php";
                                }

                                $loaded_plugins[] = $folderName;
                                $count++;
                            }
                        }
                    }

                    if ( $count == 0 )
                    {
                        $_SESSION['loaded_plugins'] = NULL;
                    }
                    else
                    {
                        $_SESSION['loaded_plugins'] = $loaded_plugins;
                    }
                }
            }
        }

        public static function init($type)
        {
            if ( DATA['website']['enable_plugins'] == TRUE )
            {
                if ( $_SESSION['loaded_plugins'] != NULL )
                {
                    global $Database;

                    $bad    = array('.', '..', 'index.html');
                    $loaded = array();

                    if ( is_array($_SESSION['loaded_plugins']) || is_object($_SESSION['loaded_plugins']) )
                    {
                        foreach ($_SESSION['loaded_plugins'] as $folderName)
                        {
                            $Database->selectDB("webdb");
                            $folderName = $Database->conn->escape_string($folderName);

                            $statement = $Database->select("disabled_plugins", "COUNT(*) AS plugins", null, "foldername=$folderName");
                            $check = $statement->get_result();
                            if ($check->fetch_assoc()['plugins'] == 0 && file_exists('plugins/'. $folderName .'/'. $type .'/'))
                            {
                                $folder = scandir('core/plugins/'. $folderName .'/'. $type .'/');

                                foreach ($folder as $fileName)
                                {
                                    if ( !in_array($fileName, $bad) )
                                    {
                                        $loaded[] = 'core/plugins/'. $folderName .'/'. $type .'/'. $fileName;
                                    }
                                }

                                $_SESSION['loaded_plugins_'. $type] = $loaded;
                            }
                        }
                    }
                }
            }
        }

        public static function load($type)
        {
            if ( DATA['website']['enable_plugins'] == TRUE )
            {
                ##########################
                if ( $type == "pages" )
                {
                    $count = 0;
                    if ( is_array($_SESSION['loaded_plugins_' . $type]) || is_object($_SESSION['loaded_plugins_' . $type]) )
                    {
                        foreach ($_SESSION['loaded_plugins_' . $type] as $filename)
                        {
                            $name = basename(substr($filename, 0, -4));
                            if ( $name == $_GET['page'] )
                            {
                                include "core/". $filename;
                                $count = 1;
                            }
                        }
                    }

                    if ( $count == 0 ) include "core/pages/404.php";
                }
                ###########################
                elseif ( $type == 'javascript' )
                {
                    if ( is_array($_SESSION['loaded_plugins_' . $type]) || is_object($_SESSION['loaded_plugins_' . $type]) )
                    {
                        foreach ($_SESSION['loaded_plugins_' . $type] as $filename)
                        {
                            echo '<script type="text/javascript" src="core/' . $filename . '"></script>';
                        }
                    }
                }
                ###########################
                elseif ( $type == 'styles' )
                {
                    if ( is_array($_SESSION['loaded_plugins_' . $type]) || is_object($_SESSION['loaded_plugins_' . $type]) )
                    {
                        foreach ($_SESSION['loaded_plugins_' . $type] as $filename)
                        {
                            echo '<link rel="stylesheet" href="core/' . $filename . '" />';
                        }
                    }
                }
                ###########################
                elseif ( $type == 'classes' )
                {
                    if ( is_array($_SESSION['loaded_plugins_' . $type]) || is_object($_SESSION['loaded_plugins_' . $type]) )
                    {
                        foreach ($_SESSION['loaded_plugins_' . $type] as $filename)
                        {
                            include "core/". $filename;
                        }
                    }
                }
            }
        }

    }

    $Plugins = new Plugins();
    