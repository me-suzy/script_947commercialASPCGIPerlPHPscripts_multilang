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
/*                                                                      */
/* Include file required for use of the view() function, among others	*/
/* Your .php files should include this if you need to use the local 	*/
/* ad view functions.                                                   */
/*                                                                      */
/* Like this:															*/
/*   require("phpAdsNew/phpadsnew.inc.php");							*/
/*                                                                      */
/* Then you can call the view function later in your php code like so:	*/
/*   view("486x60");													*/
/*                                                                      */
/************************************************************************/



// Figure out our location
if (strlen(__FILE__) > strlen(basename(__FILE__)))
    $phpAds_path=substr(__FILE__, 0, strlen(__FILE__) - strlen(basename(__FILE__)) - 1);
if (empty($phpAds_path))
    $phpAds_path = ".";

// If this path doesn't work for you, customize it here like this
// Note: no trailing backslash
// $phpAds_path="/home/myname/www/phpAdsNew";       


// Include required files
require	("$phpAds_path/config.inc.php"); 
require	("$phpAds_path/view.inc.php"); 
require	("$phpAds_path/acl.inc.php"); 

?>