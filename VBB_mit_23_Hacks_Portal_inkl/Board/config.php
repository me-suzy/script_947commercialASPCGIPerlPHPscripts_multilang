<?php
/************************************************************************/
/* vbPortal: a CMS add-on for vBulletin 2.x                             */
/* vBulletin is Copyright ©2000, 2001, Jelsoft Enterprises Limited.     */
/* ===========================                                          */
/* vbPortal                                                             */
/* Copyright (c) 2001 by William A. Jones                               */
/* http://www.phpportals.com                                            */
/* ===========================                                          */
/* Portions are Based on PHP-NUKE: Web Portal System                    */
/* Copyright (c) 2001 by Francisco Burzi (fbc@mandrakesoft.com)         */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* The portions of this program Based on PHP-NUKE are free software.    */
/* You can redistribute and/or modify those portions under the terms of */
/* the GNU General Public License as published by the Free Software     */
/* Foundation; either version 2 of the License.                         */
/************************************************************************/

######################################################################
# This module is to configure the main options for your site
#
# This module is free software. You can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License.
######################################################################

          
######################################################################
# General Site Configuration
#
# $sitename:      Your Site Name
# $nukeurl:       Complete URL for your site (Do not put / at end)
# $bburl:         Complete URL to vbulletin (Do not put / at end)
# $nukepath:      System path to your site (Do not put / at end)
# $vbpath:        System path to your vBulletin sub-directory (Do not put / at end)
# See the different conventions below for unix and NT for $nukepath and $vbpath
#
# $site_logo:     Logo for Printer Friendly Page (It's good to have a Black/White graphic)
# $slogan:        Your site's slogan
# $startdate:     Start Date to display in Statistic Page
######################################################################

$sitename = "Overnet-Community Portal";
$nukeurl = "http://www.Deine URL";
$bburl = "http://www.Deine URL/forums";

######################################################################
# Use for unix
######################################################################
 $nukepath ="/www/htdocs/Deine URL";
 $vbpath   ="/www/htdocs/Deine URL/forums";
#############s#########################################################
# Use for NT
######################################################################
# $nukepath ="/htdocs"; 
# $vbpath ="/htdocs/forums";
#####################################################################

$slogan = "Das etwas andere Board";
$startdate = "09 Mai 2002";

######################################################################
# Database & System Config
# Uses vBulletin config
# $prefix:  Database Table prefix, Do not touch, there are still a few hard coded table names
######################################################################

include($vbpath ."/admin/config.php");
$dbhost = $servername;
$dbuname = $dbusername;
$dbpass = $dbpassword;
$dbname = $dbname;
$system = 0;
$prefix = "nuke";  

######################################################################
# Choose Forum left column display defaults 
# $Allow_Forum_Leftcolumn:    1 = allow show  0 = do not allow left column in forums 
# $Forum_Default_Leftcolumn:  0 = default off 1 = default on // if allow show is 1 
######################################################################

$Allow_Forum_Leftcolumn = 0;          
$Forum_Default_Leftcolumn = 0;    

######################################################################
# The News Config
# $newsforum:      The forumid that will be your news forum.
# $newslimit:      Number of news items you want to display on the frontpage.
# $num_chars:      Number of characters you want to display in each article on the frontpage.
# $hastopics:      0 = Topic Images option not installed, 1 = Installed
# $oldnewslimit:   The number of past news items to display, set to 0 for none.
######################################################################

$newsforum=2;               
$newslimit=2;               
$num_chars=1000;           
$hastopics=1;             
$oldnewslimit=3;

######################################################################
# Frontpage polls config
# $pollsforum:     The forumid that will be used for your polls forum.
# $listforums:     Forums listed when you click on the more polls link, separated by commas.
# $listmaxresults: Maximum polls to show in the more polls page. Users can get any more by 
#                  clicking the more link that appears if there more than this many poll threads.
######################################################################

$pollsforum=3;             
$listforums="1,2,3,4"; 
$listmaxresults="1"; 


######################################################################
# Centerpage activetopics vars
# $showtopicdesc:       0 = don't use 1 = show the activetopics descreption
# $num_active:          Number of active topics to list 
# $num_topicchars:      Number of active topic characters to display 
# $ftitle1en:           Length of Forum Title
# $ttitle1en:           Length of Thread Title
# $vbp_atbc1:           First alt color
# $vbp_atbc2:           Second alt color
# $dtopic:              Select default topic to display here, In most cases doesn't need changed.
######################################################################

$showtopicdesc = 1;       
$num_active = 10;         
$num_topicchars  = 100;   
$ftitle1en = 0;          
$ttitle1en = 45;          
$vbp_atbc1 = "{firstaltcolor}";  
$vbp_atbc2 = "{secondaltcolor}"; 
$dtopic=1;

######################################################################
# Sidebox activetopics vars
# $sdtopicdesc:           0 = don't use 1 = show the activetopics descreption
# $sbnum_active:          Number of active topics to list 
# $sbnum_topicchars:      Number of active topic characters to display 
# $sbftitle1en:           Length of Forum Title
# $sbttitle1en:           Length of Thread Title
######################################################################

$sdtopicdesc = 1;           
$sbnum_active = 5;          
$sbnum_topicchars  = 25;    
$sbftitle1en = 25;          
$sbttitle1en = 40;  

######################################################################
# Active Topics General
# $excatforums:    If you want to exclude forum's, put ID's here. Separate with commas, NO SPACES! e.g. 1,2,3,4
#                  e.g. 8 is my news forum... 36 is my polls forum.
######################################################################
$excatforums = "1,2,3"; 

######################################################################
# $use_templates: When using the Default Style, or a reasonable copy of it
# you may wabt to set $use_templates to 0 to save on a few queries.
# If you look at the header.php and footer.php you will see how this is handled.
# Default is 0, not using templates, be sure to set this to 1 if you design your 
# own style and want to make use of the left and right column templates.
######################################################################
$use_templates=0;  

######################################################################
# Set $TopMenu for the small top of page menu on all screens
# You can find it in the header template if you want to remove.
######################################################################

$TopMenu = "";

######################################################################
# These four footer lines are seen at the bootom of all screens
# You must leave the linkk back to phpPortals and the copyrite notice in place
# Contributing Members are free to remove, however I would appreciate a link
# back to phpPortals.
######################################################################
$foot1 = "<br><a href=\"http://www.phpportals.com\" target=\"blank\"><img src=\"/images/vbp.gif\" border=\"0\" Alt=\"Web site powered by vbPortal\" hspace=\"10\"></a><br>";
$foot2 = "<a href=\"http://phpportals.com\" target=\"blank\">This Web site is powered by vbPortal© 3.0b</a><br>";
$foot3 = "All logos and trademarks in this site are property of their respective owner. The comments are property of their posters, all the rest © 2000 by phpPortals";
$foot4 = "<a href=\"http://phpportals.com\">vbPortal©</a> is Free Software, portions are released under the <a href=\"http://www.gnu.org\">GNU/GPL license</a>.<br>";

######################################################################
# Banners/Advertising Configuration
#
# $banners: Activate Banners Ads for your site? (1=Yes 0=No) 0 is the default
# $myIP:    Write your IP number to not count impressions, be fair about this!
######################################################################

$banners = 0;
$myIP = "150.10.10.10";

######################################################################
# Site Language Preferences
#
# $language: Language of your site (You need to have lang-xxxxxx.php file for your selected language in the /language directory of your site)
# $locale:   Locale configuration to correctly display date with your country format. (See /usr/share/locale)
######################################################################

$language = "english";
$locale = "en_US";

######################################################################
# Web Links Preferences (Some variables are valid also for Downloads)
#
# $linksperpage:   	    	How many links to show on each page?
# $popular:      	    	How many hits need a link to be listed as popular?
# $newlinks:     	    	How many links to display in the New Links Page?
# $toplinks:     	    	How many links to display in The Best Links Page? (Most Popular)
# $linksresults: 	    	How many links to display on each search result page?
# $links_anonaddlinklock:   	Lock Unregistered users from Suggesting New Links? (1=Yes 0=No)
# $anonwaitdays:        	Number of days anonymous users need to wait to vote on a link
# $outsidewaitdays:     	Number of days outside users need to wait to vote on a link (checks IP)
# $useoutsidevoting:        	Allow Webmasters to put vote links on their site (1=Yes 0=No)
# $anonweight:          	How many Unregistered User vote per 1 Registered User Vote?
# $outsideweight:       	How many Outside User vote per 1 Registered User Vote?
# $detailvotedecimal:       	Let Detailed Vote Summary Decimal out to N places. (no max)
# $mainvotedecimal:     	Let Main Vote Summary Decimal show out to N places. (max 4)
# $toplinkspercentrigger:   	1 to Show Top Links as a Percentage (else # of links)
# $toplinks:            	Either # of links OR percentage to show (percentage as whole number. #/100)
# $mostpoplinkspercentrigger:	1 to Show Most Popular Links as a Percentage (else # of links)
# $mostpoplinks:        	Either # of links OR percentage to show (percentage as whole number. #/100)
# $featurebox:          	1 to Show Feature Link Box on links Main Page? (1=Yes 0=No)
# $linkvotemin:         	Number votes needed to make the 'top 10' list
# $blockunregmodify:        	Block unregistered users from suggesting links changes? (1=Yes 0=No)
######################################################################

$linksperpage = 10;
$popular = 500;
$newlinks = 10;
$toplinks = 25;
$linksresults = 10;
$links_anonaddlinklock = 1;
$anonwaitdays = 1;
$outsidewaitdays = 1;
$useoutsidevoting = 1;
$anonweight = 10;
$outsideweight = 20;
$detailvotedecimal = 2;
$mainvotedecimal = 1;
$toplinkspercentrigger = 0;
$toplinks = 25;
$mostpoplinkspercentrigger = 0;
$mostpoplinks = 25;
$featurebox = 1;
$linkvotemin = 5;
$blockunregmodify = 0;

######################################################################
# Downloads Preferences
#
# $newdownloads:     	    	  How many downloads to display in the New downloads Page?
# $topdownloads:     	    	  How many downloads to display in The Best downloads Page? (Most Popular)
# $downloadsresults: 	    	  How many downloads to display on each search result page?
# $downloads_anonadddownloadlock: Lock Unregistered users from Suggesting New downloads? (1=Yes 0=No)
# $user_adddownload:		  Let users to add new downloads? (1=Yes 0=No)
# $topdownloadspercentrigger:     1 to Show Top downloads as a Percentage (else # of downloads)
# $topdownloads:            	  Either # of downloads OR percentage to show (percentage as whole number. #/100)
# $mostpopdownloadspercentrigger: 1 to Show Most Popular downloads as a Percentage (else # of downloads)
# $mostpopdownloads:        	  Either # of downloads OR percentage to show (percentage as whole number. #/100)
# $downloadvotemin:         	  Number votes needed to make the 'top 10' list
######################################################################

$newdownloads = 10;
$topdownloads = 25;
$downloadsresults = 10;
$downloads_anonadddownloadlock = 0;
$user_adddownload = 1;
$topdownloadspercentrigger = 0;
$topdownloads = 25;
$mostpopdownloadspercentrigger = 0;
$mostpopdownloads = 25;
$downloadvotemin = 5;

######################################################################
# Some Graphics Options
#
# $tipath:       Topics images path (put / only at the end, not at the begining)
# $userimg:      User images path (No / at begining and at the end)
# $adminimg:     Administration system images path (put / only at the end, not at the begining)
# $admingraphic: Activate graphic menu for Administration Menu? (1=Yes 0=No)
# $admart:       How many articles to show in the admin section?
######################################################################

$tipath = "$nukeurl/images/topics/";
$userimg = "$nukeurl/images/menu";
$adminimg = "$nukeurl/images/admin/";
$admingraphic = 1;
$admart = 20;

######################################################################
# HTTP Referers Options
#
# $httpref:    Activate HTTP referer logs to know who is linking to our site? (1=Yes 0=No)# $httprefmax: Maximum number of HTTP referers to store in the Database (Try to not set this to a high number, 500 ~ 1000 is Ok)
######################################################################

$httpref = 1;
$httprefmax = 1000;

######################################################################
# Allowable HTML tags
#
# $AllowableHTML: HTML command to allow in the comments
#                  =>2 means accept all qualifiers: <foo bar>
#                  =>1 means accept the tag only: <foo>
######################################################################

$AllowableHTML = array("p"=>2,
		    "b"=>1,
		    "i"=>1,
		    "a"=>2,
		    "em"=>1,
		    "br"=>1,
		    "strong"=>1,
		    "blockquote"=>1,
                    "tt"=>1,
                    "li"=>1,
                    "ol"=>1,
                    "ul"=>1);

######################################################################
# Do not touch the following options!
######################################################################

$Version_Num = "3.0 pr 8.1 German by Captain Kirk";
?>