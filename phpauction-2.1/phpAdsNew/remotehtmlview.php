<?php // $Revision: 1.1.1.1 $

/************************************************************************/
/* phpAdsNew 2                                                          */
/* ===========                                                          */
/*                                                                      */
/* Copyright (c) 2001 by the phpAdsNew developers                       */
/* http://sourceforge.net/projects/phpadsnew                            */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/



// Include required files
require("config.inc.php");
require("view.inc.php");
require("acl.inc.php");


// Set header information
header("Content-type: application/x-javascript");
require("nocache.inc.php");

if (!isset($what)) 		$what = '';
if (!isset($clientID)) 	$clientID = 0;
if (!isset($target)) 	$target = '';
if (!isset($source)) 	$source = '';
if (!isset($withText)) 	$withText = '';
if (!isset($context)) 	$context = '';

// Get the banner
view_js("$what",$clientID,"$target","$source","$withText","$context");

?>
