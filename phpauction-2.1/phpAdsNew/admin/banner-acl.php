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
require ("banner-acl.inc.php");


// Security check
phpAds_checkAccess(phpAds_Admin);



/*********************************************************/
/* Process submitted form                                */
/*********************************************************/

if (isset($btndel_x))
{
	if (!isset($acl_order)) 
		php_die("hu?", "Where is my acl_order? I've lost my acl_orde! Moooommmeee... I want my acl_order back!");
	$res = db_query("
      			DELETE FROM $phpAds_tbl_acls WHERE
        		bannerID = $bannerID AND acl_order = $acl_order ") or
		mysql_die();
	
	// get banner-acl after the deleted one
	$res = db_query("
		SELECT * FROM $phpAds_tbl_acls WHERE
		bannerID = $bannerID AND acl_order > $acl_order") or
		mysql_die();
    
	// decrement every following acl
	while ($row = mysql_fetch_array($res)) 
	{
		$old_order = $row['acl_order'];
		$res1 = db_query("
			UPDATE $phpAds_tbl_acls SET
			acl_order = acl_order - 1 WHERE
			acl_order = $old_order
               AND bannerID = $bannerID
               ") or mysql_die();
	}
	
	phpAds_ShowMessage("$strACL $strDeleted");
}

if (isset($btnsave_x))
{
	if ($update)
	{
		$res = db_query("
			UPDATE $phpAds_tbl_acls SET
			acl_type = '$acl_type', acl_data = '$acl_data',
			acl_ad = '$acl_ad' where bannerID = $bannerID 
			AND acl_order = $acl_order") or mysql_die();
		
		phpAds_ShowMessage("$strACL $strUpdated");
	} 
	else
	{
		$res = db_query("
			INSERT into $phpAds_tbl_acls SET
			acl_order = $acl_order, bannerID = $bannerID,
			acl_type = '$acl_type', acl_data = '$acl_data',
			acl_ad = '$acl_ad'") or mysql_die();
		phpAds_ShowMessage("$strACL $strSaved");
	}
}

if (isset($btnup_x))
{
	if ($acl_order < 1)
		 php_die("oops", $strNoMoveUp);
	
       // delete current acl
	$res = db_query("
		DELETE FROM $phpAds_tbl_acls WHERE
		bannerID = $bannerID AND acl_order = $acl_order ") or
		mysql_die();		
	
	// increment previous acl
	$new_acl_order = $acl_order - 1;
	$res = db_query("
		UPDATE $phpAds_tbl_acls SET
		acl_order = acl_order + 1 WHERE 
		acl_order = $new_acl_order 
		AND bannerID = $bannerID
		") or mysql_die();	 
	
	// insert actual acl with decremented order
	$res = db_query("
		INSERT into $phpAds_tbl_acls SET
		acl_order = $new_acl_order, bannerID = $bannerID,
		acl_type = '$acl_type', acl_data = '$acl_data',
		acl_ad = '$acl_ad'") or mysql_die();
	
	phpAds_ShowMessage("$strACL $strMovedUp");
}

if (isset($btndown_x))
{
	$res = db_query("
		DELETE FROM $phpAds_tbl_acls WHERE
		bannerID = $bannerID AND acl_order = $acl_order ") or
	mysql_die();
	
	$new_acl_order = $acl_order + 1;
	$res = db_query("
		UPDATE $phpAds_tbl_acls SET
		acl_order = acl_order - 1 WHERE 
		acl_order = $new_acl_order
		AND bannerID = $bannerID
		") or mysql_die();
	
	$res = db_query("
		INSERT into $phpAds_tbl_acls SET
		acl_order = $new_acl_order, bannerID = $bannerID,
		acl_type = '$acl_type', acl_data = '$acl_data',
		acl_ad = '$acl_ad'") or mysql_die();
	
	phpAds_ShowMessage("$strACL $strMovedDown");
}



/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

if (!isset($bannerID)) 
	php_die("This page can't be displayed",	"There was no bannerID suppied");

$extra = '';

$res = db_query("
	SELECT
		*
	FROM
		$phpAds_tbl_banners
	WHERE
		clientID = $campaignID
") or mysql_die();

while ($row = mysql_fetch_array($res))
{
	if ($bannerID == $row['bannerID'])
		$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-1.gif'>&nbsp;";
	else
		$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-0.gif'>&nbsp;";
	$extra .= "<a href='banner-acl.php?campaignID=$campaignID&bannerID=".$row['bannerID']."'>";
	$extra .= phpAds_buildBannerName ($row['bannerID'], $row['description'], $row['alt']);
	$extra .= "</a>";
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
$extra .= "<img src='images/icon-statistics.gif' align='absmiddle'>&nbsp;<a href=stats-campaign.php?campaignID=$campaignID>$strStats</a><br>";
$extra .= "<img src='images/break-el.gif' height='1' width='160' vspace='4'><br>";
$extra .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='images/icon-weekly.gif' align='absmiddle'>&nbsp;<a href=stats-weekly.php?campaignID=$campaignID>$strWeeklyStats</a><br>";
$extra .= "<img src='images/break-el.gif' height='1' width='160' vspace='4'><br>";
$extra .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='images/icon-zoom.gif' align='absmiddle'>&nbsp;<a href=stats-details.php?campaignID=$campaignID&bannerID=$bannerID>$strDetailStats</a><br>";
$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";

phpAds_PageHeader("4.1.3", $extra);



/*********************************************************/
/* Main code                                             */
/*********************************************************/

?>


<table width='100%' border="0" align="center" cellspacing="0" cellpadding="0">
	<tr><td height='25' colspan='4'><img src='images/icon-client.gif' align='absmiddle'>&nbsp;<?echo phpAds_getParentName($campaignID);?>
									&nbsp;<img src='images/caret-rs.gif'>&nbsp;
									<img src='images/icon-campaign.gif' align='absmiddle'>&nbsp;<?echo phpAds_getClientName($campaignID);?>
									&nbsp;<img src='images/caret-rs.gif'>&nbsp;
									<? if ($bannerID != '') { ?>
									<img src='images/icon-banner-stored.gif' align='absmiddle'>&nbsp;<b><?echo phpAds_getBannerName($bannerID);?></b></td></tr>
									<? } else { ?>
									<img src='images/icon-banner-stored.gif' align='absmiddle'>&nbsp;<?echo $strUntitled; ?></td></tr>
									<? } ?>
  <tr><td height='1' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>
  <?
	if ($bannerID != '')
		echo "<tr><td align='left'><br>".phpAds_getBannerCode($bannerID)."<br><br></td></tr>";
  ?>
</table>

<br><br>

<?
echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";


$res = db_query("
	SELECT
		*
	FROM
		$phpAds_tbl_acls
	WHERE
		bannerID = $bannerID ORDER by acl_order
	") or mysql_die();

$count = mysql_num_rows($res);
if ($count > 0)
{
	echo "<tr><td height='25' colspan='7'><b>$strACLExist</b></td></tr>";
	echo "<tr><td height='1' colspan='7' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
	
	$i = 0;
	while ($row = mysql_fetch_array($res))	// acl array already sorted by select
	{
		showaclrow($row, $count, 1, $i);
		$i++;
	}
	
}

?>

	<tr><td height='35' colspan='7'>&nbsp;</td></tr>
	<tr><td height='25' colspan='7'><b><? echo $strACLAdd; ?></b></td></tr>
	<tr><td height='1' colspan='7' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>
	<?
	$newdata['acl_order'] = $count;
	$newdata['bannerID'] = $bannerID;
	showaclrow($newdata, 0, 0, 0);
?>
</table>

<br><br>


<?	

// Show acl help file
include("../language/banneracl.".$phpAds_language.".inc.php");



/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

phpAds_PageFooter();

?>
