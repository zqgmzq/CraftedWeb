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

require "core/includes/classes/template_parse.php";

global $Database, $Plugins;
$Database->selectDB("webdb");

if ( $getTemplate = $Database->select("template", "path", null, "applied='1'")->get_result() )
{
	$template = $getTemplate->fetch_assoc();

	if ( !file_exists("core/styles/". $template['path'] ."/style.css") || 
		!file_exists("core/styles/" . $template['path'] . "/template.html") )
	{
		buildError("<b>Template Error: </b>The active template does not exist or missing files. (". $template['path'].")", NULL);
		exit_page();
	}?>
	<!-- Boostrap Styling -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- CraftedCMS Styling -->
	<link rel="stylesheet" href="core/styles/<?php echo $template['path']; ?>/style.css" />
	<link rel="stylesheet" href="core/styles/global_bootstrap/style.css" /><?php
	$Plugins->load('styles');
}
else
{
	buildError("<b>Error getting the template's path, see logs for more info.</b>", NULL, $Database->conn->error);
}