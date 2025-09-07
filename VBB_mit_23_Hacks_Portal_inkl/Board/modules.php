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
if (!isset($op)) { $op="modload"; }
if (!isset($file)) { $file="index"; }
switch($op) {

    case "modload":
	if (!isset($mainfile)) { include("mainfile.php"); }
	if (ereg("\.\.",$name) || ereg("\.\.",$file)) {
	    echo "You are so cool...";
	    break;
	} else {
	    include("modules/$name/$file.php");
	}
	break;

    default:
	die ("Sorry, you can't access this file directly...");
	break;
    
}

?>