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
require ("lib-zones.inc.php");


// Security check
phpAds_checkAccess(phpAds_Admin);



/*********************************************************/
/* Process submitted form                                */
/*********************************************************/

if (isset($submit))
{
	// Edit
	if (isset($zoneid) && $zoneid != '')
	{
		if (isset($description)) $description = addslashes ($description);
		
		if ($zonetype == phpAds_ZoneBanners)
		{
			if (isset($bannerid) && is_array($bannerid))
			{
				for ($i=0;$i<sizeof($bannerid);$i++)
					$bannerid[$i] = 'bannerid:'.$bannerid[$i];
				
				$what = implode (',', $bannerid);
			}
		}
		
		$res = db_query("
			UPDATE
				$phpAds_tbl_zones
			SET
				what = '$what',
				zonetype = $zonetype
			WHERE
				zoneid=$zoneid
			") or mysql_die();
		
		// Rebuild Cache
		phpAds_RebuildZoneCache ($zoneid);
		
		header ("Location: zone-index.php");
		exit;
	}
}


/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

if (phpAds_isUser(phpAds_Admin))
{
	$extra = '';
	
	$res = db_query("
		SELECT
			*
		FROM
			$phpAds_tbl_zones
		") or mysql_die();
	
	$extra = "";
	while ($row = mysql_fetch_array($res))
	{
		if ($zoneid == $row['zoneid'])
			$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-1.gif'>&nbsp;";
		else
			$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-0.gif'>&nbsp;";
		
		$extra .= "<a href='zone-include.php?zoneid=". $row['zoneid']."'>".phpAds_buildZoneName ($row['zoneid'], $row['zonename'])."</a>";
		$extra .= "<br>"; 
	}
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	
	phpAds_PageHeader("4.2.3", $extra);
	phpAds_ShowSections(array("4.2.2", "4.2.3", "4.2.4", "4.2.5"));
}





/*********************************************************/
/* Main code                                             */
/*********************************************************/

function phpAds_showZoneBanners ($width, $height, $what)
{
	global $strName, $strID, $phpAds_percentage_decimals, $strUntitled;
	global $phpAds_tbl_banners, $strEdit;
	
	
	$what_array = explode(",",$what);
	for ($k=0; $k < count($what_array); $k++)
	{
		if (substr($what_array[$k],0,9)=="bannerid:")
		{
			$bannerID = substr($what_array[$k],9);
			$bannerIDs[$bannerID] = true;
		}
	}
	
	$query = "
		SELECT
			*
		FROM
			$phpAds_tbl_banners
		";
	
	if ($width != -1 && $height != -1)
		$query .= "WHERE width = $width AND height = $height";
	elseif ($width != -1)
		$query .= "WHERE width = $width";
	elseif ($height != -1)
		$query .= "WHERE height = $height";
	
	$query .= "
		ORDER BY
			bannerID";
		
	$res = db_query($query) or mysql_die();
	
	//echo "<span style='width:350; height:250; overflow: scroll; overflow-x:hidden;'>";
	
	
	// Header
	echo "<table width='100%' border='0' align='center' cellspacing='0' cellpadding='0'>";
	echo "<tr height='25'>";
	echo "<td height='25'><b>&nbsp;&nbsp;$strName</b></td>";
	echo "<td height='25'><b>$strID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>";
	echo "<td height='25'>&nbsp;</td>";
	echo "</tr>";
	
	echo "<tr height='1'><td colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
	
	$i = 0;
	$checkedall = true;
	
	while ($row = mysql_fetch_array($res))
	{
		$name = $strUntitled;
		if (isset($row['alt']) && $row['alt'] != '') $name = $row['alt'];
		if (isset($row['description']) && $row['description'] != '') $name = $row['description'];
			
		$name = phpAds_breakString ($name, '60');
		
		if ($i > 0) echo "<tr height='1'><td colspan='3' bgcolor='#888888'><img src='images/break-l.gif' height='1' width='100%'></td></tr>";
		
	    echo "<tr height='25' ".($i%2==0?"bgcolor='#F6F6F6'":"").">";
		
		// Begin row
		echo "<td height='25'>";
		echo "&nbsp;&nbsp;";
		
		// Show checkbox
		if ($bannerIDs[$row['bannerID']] == true)
			echo "<input type='checkbox' name='bannerid[]' value='".$row['bannerID']."' checked onclick='reviewall();'>"; 
		else
		{
			echo "<input type='checkbox' name='bannerid[]' value='".$row['bannerID']."' onclick='reviewall();'>"; 
			$checkedall = false;
		}
		
		// Space
		echo "&nbsp;&nbsp;";
		
		// Banner icon
		if ($row['active'] == 'true')
		{
			if ($row['format'] == 'html')
				echo "<img src='images/icon-banner-html.gif' align='absmiddle'>&nbsp;";
			elseif ($row['format'] == 'url')
				echo "<img src='images/icon-banner-url.gif' align='absmiddle'>&nbsp;";
			else
				echo "<img src='images/icon-banner-stored.gif' align='absmiddle'>&nbsp;";
		}
		else
		{
			if ($row['format'] == 'html')
				echo "<img src='images/icon-banner-html-d.gif' align='absmiddle'>&nbsp;";
			elseif ($row['format'] == 'url')
				echo "<img src='images/icon-banner-url-d.gif' align='absmiddle'>&nbsp;";
			else
				echo "<img src='images/icon-banner-stored-d.gif' align='absmiddle'>&nbsp;";
		}
		
		// Name
		echo $name;
		echo "</td>";
		
		// ID
		echo "<td height='25'>".$row['bannerID']."</td>";
		
		// Edit
		echo "<td height='25'>";
		echo "<a href='banner-edit.php?bannerID=".$row['bannerID']."&campaignID=".$row['clientID']."'><img src='images/icon-edit.gif' border='0' align='absmiddle' alt='$strEdit'>&nbsp;$strEdit</a>&nbsp;&nbsp;";
		echo "</td>";
		
		// End row
		echo "</tr>";
		$i++;
	}
	
	// Footer
	echo "<tr height='1'><td colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
	
	echo "<tr><td height='25'>";
	echo "&nbsp;&nbsp;<input type='checkbox' name='checkall' value=''".($checkedall == true ? ' checked' : '')." onclick='toggleall();'>";
	echo "</td></tr>";	
	
	echo "</table>";
	
	//echo "</span>";
}



/*********************************************************/
/* Main code                                             */
/*********************************************************/

?>

<script language='Javascript'>
<!--
	function toggleall()
	{
		allchecked = false;
		
		for (var i=0; i<document.zonetypeselection.elements.length; i++)
		{
			if (document.zonetypeselection.elements[i].name == 'bannerid[]')
			{
				if (document.zonetypeselection.elements[i].checked == false)
				{
					allchecked = true;
				}
			}
		}
		
		for (var i=0; i<document.zonetypeselection.elements.length; i++)
		{
			if (document.zonetypeselection.elements[i].name == 'bannerid[]')
			{
				document.zonetypeselection.elements[i].checked = allchecked;
			}
		}
	}
	
	function reviewall()
	{
		allchecked = true;
		
		for (var i=0; i<document.zonetypeselection.elements.length; i++)
		{
			if (document.zonetypeselection.elements[i].name == 'bannerid[]')
			{
				if (document.zonetypeselection.elements[i].checked == false)
				{
					allchecked = false;
				}
			}
		}
		
				
		document.zonetypeselection.checkall.checked = allchecked;
	}	
//-->
</script>

<?php

if (isset($zoneid) && $zoneid != '')
{
	$res = @db_query("
		SELECT
			*
		FROM
			$phpAds_tbl_zones
		WHERE
			zoneid = $zoneid
		") or mysql_die();
	
	if (@mysql_num_rows($res))
	{
		$zone = @mysql_fetch_array($res);
	}
}

// Set the default zonetype
if (!isset($zonetype) || $zonetype == '')
	$zonetype = $zone['zonetype'];


echo "<img src='images/icon-zone.gif' align='absmiddle'>&nbsp;<b>".phpAds_getZoneName($zoneid)."</b><br>";

echo "<br><br>";
echo "<br><br>";
echo "<br><br>";

echo "<form name='zonetypes' method='post' action='zone-include.php'>";
echo "<input type='hidden' name='zoneid' value='$zoneid'>";

echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
echo "<tr><td height='25' colspan='3'><b>".$strSelectZoneType."</b></td></tr>";
echo "<tr><td height='1' colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
echo "<tr><td height='35'>";

echo "<select name='zonetype' onChange='this.form.submit();'>";
	echo "<option value='".phpAds_ZoneBanners."'".(($zonetype == phpAds_ZoneBanners) ? " selected" : "").">".$strBannerSelection."</option>";
	//echo "<option value='".phpAds_ZoneInteractive."'".(($zonetype == phpAds_ZoneInteractive) ? " selected" : "").">".$strInteractive."</option>";
	echo "<option value='".phpAds_ZoneRaw."'".(($zonetype == phpAds_ZoneRaw) ? " selected" : "").">".$strRawQueryString."</option>";
echo "</select>";
echo "&nbsp;<a href='javascript:document.zonetypes.submit();'><img src='images/go_blue.gif' border='0'></a>";

echo "</td></tr>";
echo "</table>";
echo "<br><br>";

echo "</form>";



echo "<form name='zonetypeselection' method='post' action='zone-include.php'>";
echo "<input type='hidden' name='zoneid' value='$zoneid'>";
echo "<input type='hidden' name='zonetype' value='$zonetype'>";


if ($zonetype == phpAds_ZoneBanners)
{
	phpAds_showZoneBanners($zone["width"], $zone["height"], $zone["what"]);
}

if ($zonetype == phpAds_ZoneRaw)
{
	echo "<textarea cols='50' rows='16' name='what' style='width:600px;'>".(isset($zone['what']) ? $zone['what'] : '')."</textarea>";
}


echo "<br><br>";
echo "<br><br>";

echo "<input type='submit' name='submit' value='$strSaveChanges'>";
echo "</form>";



/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

phpAds_PageFooter();

?>
