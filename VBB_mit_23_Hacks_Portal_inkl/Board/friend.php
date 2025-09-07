<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2001 by Francisco Burzi (fbc@mandrakesoft.com)         */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!isset($mainfile)) { include("mainfile.php"); }
global  $Pmenu,$breadcrumb;
$Pmenu="";
$breadcrumb="Empfehle uns";
getvbpvars();
function FriendSend($sid) {
    global $user, $cookie, $prefix;
    if(!isset($sid)) { exit(); }
    include ("header.php");
    $result=mysql_query("select title from $prefix"._stories." where sid=$sid");
    list($title) = mysql_fetch_row($result);
    OpenTable();
    echo "<font class=title><b>"._FRIEND."</b></font><br><br>"
	.""._YOUSENDSTORY." <b>$title</b> "._TOAFRIEND."<br><br>"
	."<form action=\"friend.php\" method=\"post\">"
	."<input type=\"hidden\" name=\"sid\" value=\"$sid\">";
    if (is_user($user)) {
	$result=mysql_query("select name, email from $prefix"._users." where uname='$cookie[1]'");
	list($yn, $ye) = mysql_fetch_row($result);
    }
    echo "<b>"._FYOURNAME." </b> <input type=\"text\" name=\"yname\" value=\"$yn\"><br>\n"
	."<b>"._FYOUREMAIL." </b> <input type=\"text\" name=\"ymail\" value=\"$ye\"><br><br>\n"
	."<b>"._FFRIENDNAME." </b> <input type=\"text\" name=\"fname\"><br>\n"
	."<b>"._FFRIENDEMAIL." </b> <input type=\"text\" name=\"fmail\"><br><br>\n"
	."<input type=\"hidden\" name=\"op\" value=\"SendStory\">\n"
	."<input type=\"submit\" value="._SEND.">\n"
	."</form>\n";
    CloseTable();
    include ('footer.php');
}

function SendStory($sid, $yname, $ymail, $fname, $fmail) {
    global $sitename, $nukeurl, $prefix;

    $result2=mysql_query("select title, time, topic from $prefix"._stories." where sid=$sid");
    list($title, $time, $topic) = mysql_fetch_row($result2);

    $result3=mysql_query("select topictext from $prefix"._topics." where topicid=$topic");
    list($topictext) = mysql_fetch_row($result3);

    $subject = ""._INTERESTING." $sitename";
    $message = ""._HELLO." $fname:\n\n"._YOURFRIEND." $yname "._CONSIDERED."\n\n\n$title\n("._FDATE." $time)\n"._FTOPIC." $topictext\n\n"._URL.": $nukeurl/article.php?sid=$sid\n\n"._YOUCANREAD." $sitename\n$nukeurl";
    mail($fmail, $subject, $message, "From: \"$yname\" <$ymail>\nX-Mailer: PHP/" . phpversion());
    $title = urlencode($title);
    $fname = urlencode($fname);
    Header("Location: friend.php?op=StorySent&title=$title&fname=$fname");
}

function StorySent($title, $fname) {
    include ("header.php");
    $title = urldecode($title);
    $fname = urldecode($fname);
    OpenTable();
    echo "<center><font class=\"content\">"._FSTORY." <b>$title</b> "._HASSENT." $fname... "._THANKS."</font></center>";
    CloseTable();
    include ("footer.php");
}

function RecommendSite() {
    global $user, $bbuserinfo, $prefix;
    include ("header.php");
    OpenTable();
    echo "<font class=\"title\"><b>"._RECOMMEND."</b></font><br><br>"
	."<form action=\"friend.php\" method=\"post\">"
	."<input type=\"hidden\" name=\"op\" value=\"SendSite\">";
    if (is_user($user)) {
        $yn=$bbuserinfo['username'];
        $ye=$bbuserinfo['email'];
	}
    echo "<b>"._FYOURNAME." </b> <input type=\"text\" name=\"yname\" value=\"$yn\"><br>\n"
	."<b>"._FYOUREMAIL." </b> <input type=\"text\" name=\"ymail\" value=\"$ye\"><br><br>\n"
	."<b>"._FFRIENDNAME." </b> <input type=\"text\" name=\"fname\"><br>\n"
	."<b>"._FFRIENDEMAIL." </b> <input type=\"text\" name=\"fmail\"><br><br>\n"
	."<input type=submit value="._SEND.">\n"
	."</form>\n";
    CloseTable();
    include ('footer.php');
}


function SendSite($yname, $ymail, $fname, $fmail) {
    global $sitename, $slogan, $nukeurl;
    $subject = ""._INTSITE." $sitename";
    $message = ""._HELLO." $fname:\n\n"._YOURFRIEND." $yname "._OURSITE." $sitename "._INTSENT."\n\n\n"._FSITENAME." $sitename\n$slogan\n"._FSITEURL." $nukeurl\n";
    mail($fmail, $subject, $message, "From: \"$yname\" <$ymail>\nX-Mailer: PHP/" . phpversion());
    Header("Location: friend.php?op=SiteSent&fname=$fname");
}

function SiteSent($fname) {
    include ('header.php');
    OpenTable();
    echo "<center><font class=\"content\">"._FREFERENCE." $fname...<br><br>"._THANKSREC."</font></center>";
    CloseTable();
    include ('footer.php');
}


switch($op) {

    case "SendStory":
    SendStory($sid, $yname, $ymail, $fname, $fmail);
    break;
	
    case "StorySent":
    StorySent($title, $fname);
    break;

    case "SendSite":
    SendSite($yname, $ymail, $fname, $fmail);
    break;
	
    case "SiteSent":
    SiteSent($fname);
    break;

    case "FriendSend":
    FriendSend($sid);
    break;

    default:
    RecommendSite();
    break;

}

?>
