<?php
/************************************************************************/
/* vbPortal: CMS mod for vBulletin                                      */
/* vBulletin is Copyright ©2000, 2001, Jelsoft Enterprises Limited.     */
/* ===========================                                          */
/* vbPortal by wajones                                                  */
/* Copyright (c) 2001 by William A. Jones                               */
/* http://www.phpportals.com                                            */
/* ===========================                                          */
/* Based on PHP-NUKE: Web Portal System                                 */
/* Copyright (c) 2001 by Francisco Burzi (fbc@mandrakesoft.com)         */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
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
$vbpvars=1;
if(!isset($mainfile)) { include("mainfile.php"); }
global  $Pmenu,$breadcrumb;
$Pmenu="";
$breadcrumb="Top 10";
getvbpvars();
include("header.php");
OpenTable();
echo "<center><font size=\"4\"><b>"._TOPWELCOME." $sitename!</b></font></center>";
CloseTable();
echo "<br>\n\n";
OpenTable();

/* Top 10 read stories */
/*
$top=10;
$result = mysql_query("select * from thread order by views DESC limit 0,$top");
if (mysql_num_rows($result)>0) {
    echo "<table border=\"0\" cellpadding=\"10\" width=\"100%\"><tr><td align=\"left\">\n"
        ."<font size=\"3\"><b>$top "._READSTORIES."</b></font><br><br><font size=\"2\">\n";
    $lugar=1;
    while(list($threadid, $title, $timeline, $views) = mysql_fetch_row($result)) {
        if($counter>0) {
    	    echo "<strong><big>&middot;</big></strong>&nbsp;$lugar: <a href=\"article.php?sid=$sid\">$title</a> - ($counter "._READS.")<br>\n";
	    $lugar++;
	}
    }
    echo "</font></td></tr></table><br>\n";
}
mysql_free_result($result);
*/
/* Top 10 commented stories */
/*
$result = mysql_query("select sid, title, comments from $prefix"._stories." order by comments DESC limit 0,$top");
if (mysql_num_rows($result)>0) {
    echo "<table border=\"0\" cellpadding=\"10\" width=\"100%\"><tr><td align=\"left\">\n"
	."<font size=\"3\"><b>$top "._COMMENTEDSTORIES."</b></font><br><br><font size=\"2\">\n";
    $lugar=1;
    while(list($sid, $title, $comments) = mysql_fetch_row($result)) {
	if($comments>0) {
	    echo "<strong><big>&middot;</big></strong>&nbsp;$lugar: <a href=\"article.php?sid=$sid\">$title</a> - ($comments "._COMMENTS.")<br>\n";
	    $lugar++;
	}
    }
    echo "</font></td></tr></table><br>\n";
}
mysql_free_result($result);
*/
/* Top 10 categories */
/*
$result = mysql_query("select catid, title, counter from $prefix"._stories."_cat order by counter DESC limit 0,$top");
if (mysql_num_rows($result)>0) {
    echo "<table border=\"0\" cellpadding=\"10\" width=\"100%\"><tr><td align=\"left\">\n"
	."<font size=\"3\"><b>$top "._ACTIVECAT."</b></font><br><br><font size=\"2\">\n";
    $lugar=1;
    while(list($catid, $title, $counter) = mysql_fetch_row($result)) {
	if($counter>0) {
	    echo "<strong><big>&middot;</big></strong>&nbsp;$lugar: <a href=\"categories.php?op=newindex&amp;catid=$catid\">$title</a> - ($counter "._HITS.")<br>\n";
	    $lugar++;
	}
    }
    echo "</font></td></tr></table><br>\n";
}
mysql_free_result($result);
*/

/* Top 10 users submitters */
// Set this to the max number top posters to display 
$top = 10; 
global $bburl;
$query = "SELECT * FROM user ORDER BY posts DESC LIMIT $top"; 
$result = mysql_query($query); 
if (mysql_num_rows($result)>0) {
echo "<table border=\"0\" cellpadding=\"10\" width=\"100%\"><tr><td align=\"left\">\n"
	."<font size=\"3\"><b>Die Top $top der Benutzerbeiträge</b></font><br><br><font size=\"2\">\n";
$lugar=1;
while ($latest_array = mysql_fetch_array($result)) { 
echo "<font size = 2><strong><big>&middot;</big></strong>&nbsp;$lugar: <A HREF='$bburl/member.php?action=getinfo&userid=$latest_array[userid]'>$latest_array[username]</A>&nbsp;mit&nbsp; $latest_array[posts]&nbsp;Beiträgen.</FONT><BR>";
$lugar++;
}
 echo "</font></td></tr></table><br>\n";
}
mysql_free_result($result);


/* Top 10 Polls */
/*
$result = mysql_query("select pollID, pollTitle, voters from $prefix"._poll_desc." order by voters DESC limit 0,$top");
if (mysql_num_rows($result)>0) {
    echo "<table border=\"0\" cellpadding=\"10\" width=\"100%\"><tr><td align=\"left\">\n"
	."<font size=\"3\"><b>$top "._VOTEDPOLLS."</b></font><br><br><font size=\"2\">\n";
    $lugar=1;
    while(list($pollID, $pollTitle, $voters) = mysql_fetch_row($result)) {
	$result2 = mysql_query("SELECT SUM(optionCount) AS SUM FROM $prefix"._poll_data." WHERE pollID=$pollID");
	$sum = (int)mysql_result($result2, 0, "SUM");
	if($sum>0) {
	    echo "<strong><big>&middot;</big></strong>&nbsp;$lugar: <a href=\"pollBooth.php?op=results&amp;pollID=$pollID\">$pollTitle</a> - ("._VOTES." $voters)<br>\n";
	    $lugar++;
	}
    }
    echo "</font></td></tr></table><br>\n";
}
mysql_free_result($result);
*/
/* Top 10 authors */
/*
$result = mysql_query("select aid, counter from $prefix"._authors." order by counter DESC limit 0,$top");
if (mysql_num_rows($result)>0) {
    echo "<table border=\"0\" cellpadding=\"10\" width=\"100%\"><tr><td align=\"left\">\n"
	."<font size=\"3\"><b>$top "._MOSTACTIVEAUTHORS."</b></font><br><br><font size=\"2\">\n";
    $lugar=1;
    while(list($aid, $counter) = mysql_fetch_row($result)) {
	if($counter>0) {
	    echo "<strong><big>&middot;</big></strong>&nbsp;$lugar: <a href=\"search.php?query=&amp;author=$aid\">$aid</a> - ($counter "._NEWSPUBLISHED.")<br>\n";
	    $lugar++;
	}
    }
    echo "</font></td></tr></table><br>\n";
}
mysql_free_result($result);
*/
/* Top 10 reviews */

$result = mysql_query("select id, title, hits from $prefix"._reviews." order by hits DESC limit 0,$top");
if (mysql_num_rows($result)>0) {
    echo "<table border=\"0\" cellpadding=\"10\" width=\"100%\"><tr><td align=\"left\">\n"
	."<font size=\"3\"><b>$top "._READREVIEWS."</b></font><br><br><font size=\"2\">\n";
    $lugar=1;
    while(list($id, $title, $hits) = mysql_fetch_row($result)) {
	if($hits>0) {
	    echo "<strong><big>&middot;</big></strong>&nbsp;$lugar: <a href=\"reviews.php?op=showcontent&amp;id=$id\">$title</a> - ($hits "._READS.")<br>\n";
	    $lugar++;
	}
    }
    echo "</font></td></tr></table><br>\n";
}
mysql_free_result($result);

/* Top 10 downloads */

$result = mysql_query("select lid, cid, title, hits from $prefix"._downloads_downloads." order by hits DESC limit 0,$top");
if (mysql_num_rows($result)>0) {
    echo "<table border=\"0\" cellpadding=\"10\" width=\"100%\"><tr><td align=\"left\">\n"
	."<font size=\"3\"><b>$top "._DOWNLOADEDFILES."</b></font><br><br><font size=\"2\">\n";
    $lugar=1;
    while(list($lid, $cid, $title, $hits) = mysql_fetch_row($result)) {
	if($hits>0) {
	    $res = mysql_query("select title from $prefix"._downloads_categories." where cid='$cid'");
	    list($ctitle) = mysql_fetch_row($res);
	    $utitle = ereg_replace(" ", "_", $title);
	    echo "<strong><big>&middot;</big></strong>&nbsp;$lugar: <a href=\"download.php?op=viewdownloaddetails&amp;lid=$lid&amp;ttitle=$utitle\">$title</a> ("._CATEGORY.": $ctitle) - ($hits "._DOWNLOADS.")<br>\n";
	    $lugar++;
	}
    }
    echo "</font></td></tr></table>\n\n";
}
mysql_free_result($result);

CloseTable();
include("footer.php");

?>
