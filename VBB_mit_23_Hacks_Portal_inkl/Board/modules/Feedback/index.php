<?php

/*  
    Feedback 1.0
    Internet International http://www.INTERNETintl.com

    PHPNuke 5, Author Francisco Burzi (fburzi@ncc.org.ve)
    http://www.phpnuke.org/

    php Addon Feedback 1.0 - Copyright (c) 2001 by Jack Kozbial
    http://www.InternetIntl.com
    jack@internetintl.com

    Adapted to vbPortal by wajones
    http://www.phpportals.com
    wajones@vbportals.com

    This program is free software. You can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License.
*/

if (!eregi("modules.php", $PHP_SELF)) {
    die ("You can't access this file directly...");
}

global  $Pmenu,$breadcrumb;
$Pmenu="";
$breadcrumb="Feedback";
getvbpvars();
global $bbuserinfo,$user;
if ($bbuserinfo['userid']!=0) {
    $sender_name=$bbuserinfo['username'];
    $sender_email=$bbuserinfo['email'];
}

/**********************************/
/* Configuration                  */
/*                                */
/* You can change this:           */
/* $index = 0; (right side off)   */
/**********************************/

$index = 1;
$subject = "$sitename Feedback";
$formname = "Feedback";

/***********************************************************************************/

include("header.php");

$form_block = "
    <center><font class=\"title\"><b>$sitename: $formname</b></font>
    <br><br><font class=\"content\">Alle Anmerkungen und Vorschläge<br>
    über <b>$sitename</b><br>
    sind sehr Willkommen und ein Wertvolle<br>
    Quelle an Informationen für uns. Danke!</font>
    <FORM METHOD=\"post\" ACTION=\"modules.php?op=modload&amp;name=Feedback&amp;file=index\">
    <P><strong>Dein Name:</strong><br>
    <INPUT type=\"text\" NAME=\"sender_name\" VALUE=\"$sender_name\" SIZE=30></p>
    <P><strong>Deine e-Mail Addresse:</strong><br>
    <INPUT type=\"text\" NAME=\"sender_email\"  VALUE=\"$sender_email\" SIZE=30></p>
    <P><strong>Nachricht:</strong><br>
    <TEXTAREA NAME=\"message\" COLS=30 ROWS=5 WRAP=virtual>$message</TEXTAREA></p>
    <INPUT type=\"hidden\" name=\"opi\" value=\"ds\">
    <P><INPUT TYPE=\"submit\" NAME=\"submit\" VALUE=\"Abschicken\"></p>
    </FORM></center>
";

OpenTable();
if ($opi != "ds") {
    echo "$form_block";
} else if ($opi == "ds") {
    if ($sender_name == "") {
	$name_err = "<center><font color=red>Bitte Deinen Namen eintragen!</font></center><br>";
	$send = "no";
    } 
    if ($sender_email == "") {
	$email_err = "<center><font color=red>Bitte Deine e-Mail Addresse eintragen!</font></center><br>";
	$send = "no";
    } 
    if ($message == "") {
    	$message_err = "<center><font color=red>Bitte das Nachrichtenfeld ausfüllen!</font></center><br>";
	$send = "no";
    } 
    if ($send != "no") {
	$msg = "$sitename\n";
	$msg .= "Sender's Name:    $sender_name\n";
	$msg .= "Sender's E-Mail:  $sender_email\n";
	$msg .= "Message:          $message\n\n";
	$to = $webmasteremail;
	$subject = "My Nuke Site Feedback";
	$mailheaders = "From: $nukeurl <> \n";
	$mailheaders .= "Reply-To: $sender_email\n\n";
	mail($to, $subject, $msg, $mailheaders);
	echo "<P><center>Feedback ist gesendet worden!</center></p>";
	echo "<P><center>Danke für das In Verbindung treten mit uns</center></p>";
        // echo "<P><center>you can add more text here</center></p>";
    } else if ($send == "no") {
	echo "$name_err";
	echo "$email_err";
	echo "$message_err";
	echo "$form_block";  
    } 
}
CloseTable();   
include("footer.php");

?>