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
require ("config.php");
require ("lib-statistics.inc.php");
require ("lib-expiration.inc.php");
require ("lib-gd.inc.php");


// Security check
phpAds_checkAccess(phpAds_Admin+phpAds_Client);



/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

if (phpAds_isUser(phpAds_Client))
{
	if (phpAds_clientID() == phpAds_getParentID ($campaignID))
	{
		$extra = '';
		
		$res = db_query("
		SELECT
			*
		FROM
			$phpAds_tbl_clients
		WHERE
			parent = ".$Session["clientID"]."
		") or mysql_die();
		
		while ($row = mysql_fetch_array($res))
		{
			if ($campaignID == $row['clientID'])
				$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-1.gif'>&nbsp;";
			else
				$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-0.gif'>&nbsp;";
			
			$extra .= "<a href=stats-campaign.php?campaignID=".$row['clientID'].">".phpAds_buildClientName ($row['clientID'], $row['clientname'])."</a>";
			$extra .= "<br>"; 
		}
		$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
		
		phpAds_PageHeader("1.1", $extra);
	}
	else
	{
		phpAds_PageHeader("1");
		php_die ($strAccessDenied, $strNotAdmin);	
	}
}

if (phpAds_isUser(phpAds_Admin))
{
	$extra = '';
	
	$res = db_query("
	SELECT
		*
	FROM
		$phpAds_tbl_clients
	WHERE
		parent > 0
	") or mysql_die();
	
	while ($row = mysql_fetch_array($res))
	{
		if ($clientID == $row['clientID'])
			$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-1.gif'>&nbsp;";
		else
			$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-0.gif'>&nbsp;";
		
		$extra .= "<a href=stats-campaign.php?campaignID=".$row['clientID'].">".phpAds_buildClientName ($row['clientID'], $row['clientname'])."</a>";
		$extra .= "<br>"; 
	}
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	
	$extra .= "<br><br><br><br><br>";
	$extra .= "<b>$strShortcuts</b><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	$extra .= "<img src='images/icon-client.gif' align='absmiddle'>&nbsp;<a href=client-edit.php?clientID=".phpAds_getParentID ($campaignID).">$strModifyClient</a><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	$extra .= "<img src='images/icon-edit.gif' align='absmiddle'>&nbsp;<a href=campaign-edit.php?campaignID=$campaignID>$strModifyCampaign</a><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	$extra .= "<img src='images/icon-campaign.gif' align='absmiddle'>&nbsp;<a href=campaign-index.php?campaignID=$campaignID>$strBanners</a><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	
	phpAds_PageHeader("2.1", $extra);
}



/*********************************************************/
/* Store sorting order and view                          */
/*********************************************************/

$UpdateSession = false;

if (empty($Session["stats_compact"]))
	$Session["stats_compact"] = "";
if (!isset($compact))
	$compact = $Session["stats_compact"];
elseif ($compact != $Session["stats_compact"])
{
	$Session["stats_compact"] = $compact;
	$UpdateSession = true;
}

if (empty($Session["stats_view"]))
	$Session["stats_view"] = "";
if (!isset($view))
	$view = $Session["stats_view"];
elseif ($view != $Session["stats_view"])
{
	$Session["stats_view"] = $view;
	$UpdateSession = true;
}

if (empty($Session["stats_order"]))
	$Session["stats_order"] = "";
if (!isset($order))
	$order = $Session["stats_order"];
elseif ($order != $Session["stats_order"])
{
	$Session["stats_order"] = $order;
	$UpdateSession = true;
}

if ($UpdateSession == true)
	phpAds_SessionDataStore();


require("./stats-campaign.inc.php");



/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

phpAds_PageFooter();

?>