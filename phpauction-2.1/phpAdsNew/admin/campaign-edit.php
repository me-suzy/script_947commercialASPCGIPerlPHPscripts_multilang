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


// Security check
phpAds_checkAccess(phpAds_Admin);



/*********************************************************/
/* Process submitted form                                */
/*********************************************************/

if (isset($submit))
{ 
	// If ID is not set, it should be a null-value for the auto_increment
	$message = $strClientModified;
		
	if (empty($campaignID))
	{
		$campaignID = "null";
		$message = $strClientAdded;
	}
		
	// set expired
	if ($views == '-')
		$views = 0;
	if ($clicks == '-')
		$clicks = 0;
		
	// set unlimited
	if (strtolower ($unlimitedviews) == "on")
		$views = -1;
	if (strtolower ($unlimitedclicks) == "on")
		$clicks = -1;
		
	if ($expireSet == 'true')
	{
		if ($expireDay != '-' && $expireMonth != '-' && $expireYear != '-')
		{
			$expire = $expireYear."-".$expireMonth."-".$expireDay;
		}
		else
			$expire = "0000-00-00";
	}
	else
		$expire = "0000-00-00";
	
	
	if ($activateSet == 'true')
	{
		if ($activateDay != '-' && $activateMonth != '-' && $activateYear != '-')
		{
			$activate = $activateYear."-".$activateMonth."-".$activateDay;
		}
		else
			$activate = "0000-00-00";
	}
	else
		$activate = "0000-00-00";
	
	
	$active = "true";
	
	if ($clicks == 0 || $views==0)
		$active = "false";
	
	if ($activateDay != '-' && $activateMonth != '-' && $activateYear != '-')
		if (time() < mktime(0, 0, 0, $activateMonth, $activateDay, $activateYear))
			$active = "false";
	
	if ($expireDay != '-' && $expireMonth != '-' && $expireYear != '-')
		if (time() > mktime(0, 0, 0, $expireMonth, $expireDay, $expireYear))
			$active = "false";
	
	
	$query = "
		REPLACE INTO
			$phpAds_tbl_clients(clientID,
			clientname,
			parent,
			views,
			clicks,
			expire,
			activate,
			active,
			weight)
		VALUES
			('$campaignID',
			'$clientname',
			'$clientID',
			'$views',
			'$clicks',
			'$expire',
			'$activate',
			'$active',
			'$weight')";
	
	
	$res = db_query($query) or mysql_die();  
	if (isset($move) && $move == 'true')
	{
		// We are moving a client to a campaign
		// Get ID of new campaign
		$campaignID = @mysql_insert_id($phpAds_db_link);		
		
		// Update banners
		$res = db_query("
			UPDATE
				$phpAds_tbl_banners
			SET
				clientID=$campaignID
			WHERE
				clientID=$clientID  
			") or mysql_die();
	}
	
	Header("Location: client-index.php?expand=$clientID&message=".urlencode($message));
	exit;
}




/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

if ($campaignID != "")
{
	// Edit and existing campaign
	
	$extra = '';
	
	$res = db_query("
		SELECT
			*
		FROM
			$phpAds_tbl_clients
		WHERE
			parent != 0  
		") or mysql_die();
		
	while ($row = mysql_fetch_array($res))
	{
		if ($campaignID == $row['clientID'])
			$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-1.gif'>&nbsp;";
		else
			$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-0.gif'>&nbsp;";
		
		$extra .= "<a href=campaign-edit.php?campaignID=". $row['clientID'].">".phpAds_buildClientName ($row['clientID'], $row['clientname'])."</a>";
		$extra .= "<br>"; 
	}
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	
	$extra .= "<br><br><br><br><br>";
	$extra .= "<b>$strShortcuts</b><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	$extra .= "<img src='images/icon-client.gif' align='absmiddle'>&nbsp;<a href=client-edit.php?clientID=".phpAds_getParentID ($campaignID).">$strModifyClient</a><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	$extra .= "<img src='images/icon-campaign.gif' align='absmiddle'>&nbsp;<a href=campaign-index.php?campaignID=$campaignID>$strBanners</a><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	$extra .= "<img src='images/icon-statistics.gif' align='absmiddle'>&nbsp;<a href=stats-campaign.php?campaignID=$campaignID>$strStats</a><br>";
	$extra .= "<img src='images/break-el.gif' height='1' width='160' vspace='4'><br>";
	$extra .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='images/icon-weekly.gif' align='absmiddle'>&nbsp;<a href=stats-weekly.php?campaignID=$campaignID>$strWeeklyStats</a><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	
	phpAds_PageHeader("4.3", $extra);
}
else
{
	if (isset($move) && $move == 'true')
	{
		// Convert client to campaign
		phpAds_PageHeader("4.3");
	}
	else
	{
		// New campaign
		phpAds_PageHeader("4.2");   
	}
}

if ($campaignID != "" || (isset($move) && $move == 'true'))
{
	// Edit or Convert
	// Fetch exisiting settings
	// Parent setting for converting, campaign settings for editing
	if ($campaignID != "") $ID = $campaignID;
	if (isset($clientID) && $clientID != "") $ID = $clientID;

	$res = db_query("
		SELECT
			*,
			to_days(expire) as expire_day,
			to_days(curdate()) as cur_date,
			UNIX_TIMESTAMP(expire) as timestamp,
			DATE_FORMAT(expire, '$date_format') as expire_f,
			dayofmonth(expire) as expire_dayofmonth,
			month(expire) as expire_month,
			year(expire) as expire_year,
			DATE_FORMAT(activate, '$date_format') as activate_f,
			dayofmonth(activate) as activate_dayofmonth,
			month(activate) as activate_month,
			year(activate) as activate_year
		FROM
			$phpAds_tbl_clients
		WHERE
			clientID = $ID
		") or mysql_die();
		
	$row = mysql_fetch_array($res);
	
	
	
	// Set parent when editing an campaign, don't set it
	// when moving an campaign.
	if ($campaignID != "" && isset($row["parent"]))
		$clientID = $row["parent"];
	
	// Set default activation settings
	if (!isset($row["activate_dayofmonth"]))
		$row["activate_dayofmonth"] = 0;
	if (!isset($row["activate_month"]))
		$row["activate_month"] = 0;
	if (!isset($row["activate_year"]))
		$row["activate_year"] = 0;
	if (!isset($row["activate_f"]))
		$row["activate_f"] = "-";
	
	// Set default expiration settings
	if (!isset($row["expire_dayofmonth"]))
		$row["expire_dayofmonth"] = 0;
	if (!isset($row["expire_month"]))
		$row["expire_month"] = 0;
	if (!isset($row["expire_year"]))
		$row["expire_year"] = 0;
	if (!isset($row["expire_f"]))
		$row["expire_f"] = "-";

	// Check if timestamp is in the past or future
	if ($row["timestamp"] < time())
	{
		if ($row["timestamp"] > 0)
			$days_left = "0";
		else
			$days_left = -1;
	}
	else
		$days_left=$row["expire_day"] - $row["cur_date"];
}
else
{
	// New

	$row["views"] = "";
	$row["clicks"] = "";
	$days_left = "";
}



/*********************************************************/
/* Main code                                             */
/*********************************************************/

if (!isset($row['views']) || (isset($row['views']) && $row['views'] == ""))
	$row["views"] = -1;
if (!isset($row['clicks']) || (isset($row['clicks']) && $row['clicks'] == ""))
	$row["clicks"] = -1;

if ($days_left == "")
	$days_left = -1;

function phpAds_showDateEdit($name, $day=0, $month=0, $year=0, $edit=true)
{
	global $strMonth, $strDontExpire, $strActivateNow;
	
	if ($day == 0 && $month == 0 && $year == 0)
	{
		$day = '-';
		$month = '-';
		$year = '-';
		$set = false;
	}
	else
	{
		$set = true;
	}
	
	if ($name == 'expire')
		$caption = $strDontExpire;
	elseif ($name == 'activate')
		$caption = $strActivateNow;
	
	if ($edit)
	{
		echo "<table><tr><td>";
		echo "<input type='radio' name='".$name."Set' value='false' onclick=\"disableradio('".$name."', false);\"".($set==false?' checked':'').">";
		echo "&nbsp;$caption";
		echo "</td></tr><tr><td>";
		echo "<input type='radio' name='".$name."Set' value='true' onclick=\"disableradio('".$name."', true);\"".($set==true?' checked':'').">";
		echo "&nbsp;";
		
		echo "<select name='".$name."Day' onchange=\"checkdate('".$name."');\">\n";
		echo "<option value='-'".($day=='-' ? ' selected' : '').">-</option>\n";
		for ($i=1;$i<=31;$i++)
			echo "<option value='$i'".($day==$i ? ' selected' : '').">$i</option>\n";
		echo "</select>&nbsp;\n";
		
		echo "<select name='".$name."Month' onchange=\"checkdate('".$name."');\">\n";
		echo "<option value='-'".($month=='-' ? ' selected' : '').">-</option>\n";
		for ($i=1;$i<=12;$i++)
			echo "<option value='$i'".($month==$i ? ' selected' : '').">".$strMonth[$i-1]."</option>\n";
		echo "</select>&nbsp;\n";
		
		if ($year != '-')
			$start = $year < date('Y') ? $year : date('Y');
		else
			$start = date('Y');
		
		echo "<select name='".$name."Year' onchange=\"checkdate('".$name."');\">\n";
		echo "<option value='-'".($year=='-' ? ' selected' : '').">-</option>\n";
		for ($i=$start;$i<=($start+4);$i++)
			echo "<option value='$i'".($year==$i ? ' selected' : '').">$i</option>\n";
		echo "</select>\n";
		
		echo "</td></tr></table>";
	}
	else
	{
		if ($set == true)
		{
			echo $day." ".$strMonth[$month-1]." ".$year;
		}
		else
		{
			echo $caption;
		}
	}
}
?>


<script language="JavaScript">
<!--
	function disableradio(o, value)
	{
		day = eval ("document.clientform." + o + "Day.value");
		month = eval ("document.clientform." + o + "Month.value");
		year = eval ("document.clientform." + o + "Year.value");

		if (value == false)
		{
			eval ("document.clientform." + o + "Day.selectedIndex = 0");
			eval ("document.clientform." + o + "Month.selectedIndex = 0");
			eval ("document.clientform." + o + "Year.selectedIndex = 0");
		}
		
		if (value == true && (day=='-' || month=='-' || year=='-'))
		{
			eval ("document.clientform." + o + "Set[0].checked = true");
		}
	}

	function checkdate(o)
	{
		day = eval ("document.clientform." + o + "Day.value");
		month = eval ("document.clientform." + o + "Month.value");
		year = eval ("document.clientform." + o + "Year.value");
		
		if (day=='-' || month=='-' || year=='-')
		{
			eval ("document.clientform." + o + "Set[0].checked = true");
		}
		else
		{
			eval ("document.clientform." + o + "Set[1].checked = true");
		}
	}
	
	function valid(form)
	{
		var views=form.views.value;
		var clicks=form.clicks.value;

		if (!parseInt(views))
		{
			if (eval(form.unlimitedviews.checked) == false && views != '-')
			{
				alert("<?print $GLOBALS['strErrorViews'];?>");
				return false;
			}
		} 
		else if (parseInt(views) < 0)
		{
			alert("<?print $GLOBALS['strErrorNegViews'];?>");
			return false;
		}
		
		if (!parseInt(clicks))
		{
			if (eval(form.unlimitedclicks.checked) == false && clicks != '-')
			{
				alert("<?print $GLOBALS['strErrorClicks'];?>");
				return false;
			}
		} 
		else if (parseInt(clicks) < 0)
		{
			alert("<?print $GLOBALS['strErrorNegClicks'];?>");
			return false;
		}
	}
//-->
</script>


<table width='100%' border="0" align="center" cellspacing="0" cellpadding="0">
	<? if (isset($campaignID) && $campaignID > 0) { ?>
	<tr><td height='25' colspan='4'><img src='images/icon-client.gif' align='absmiddle'>&nbsp;<?echo phpAds_getParentName($campaignID);?>
									&nbsp;<img src='images/caret-rs.gif'>&nbsp;
									<img src='images/icon-campaign.gif' align='absmiddle'>&nbsp;<b><?echo phpAds_getClientName($campaignID);?></b></td></tr>
	<tr><td height='1' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>
	<? } else { ?>
	<tr><td height='25' colspan='4'><img src='images/icon-client.gif' align='absmiddle'>&nbsp;<?echo phpAds_getClientName($clientID);?>
									&nbsp;<img src='images/caret-rs.gif'>&nbsp;
									<img src='images/icon-campaign.gif' align='absmiddle'>&nbsp;<b><?echo $strCreateNewCampaign; ?></b></td></tr>
	<tr><td height='1' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>
	<? } ?>
</table


<br><br>
<br><br>
  

<form name="clientform" method="post" action="<?echo basename($PHP_SELF);?>" onSubmit="return valid(this)">
<input type="hidden" name="campaignID" value="<?if(isset($campaignID)) echo $campaignID;?>">
<input type="hidden" name="clientID" value="<?if(isset($clientID)) echo $clientID;?>">
<input type="hidden" name="expire" value="<?if(isset($row["expire"])) echo $row["expire"];?>">
<input type="hidden" name="move" value="<?if(isset($move)) echo $move;?>">

<table border='0' width='100%' cellpadding='0' cellspacing='0'>
	<tr><td height='25' colspan='3'><b><?echo $strBasicInformation;?></b></td></tr>
	<tr height='1'><td colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>

	<tr><td height='10' colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='30'>&nbsp;</td>
		<td width='200'><?echo $strName;?></td>
		<td><input type="text" name="clientname" size='35' style="width:350px;" value="<?if(isset($row["clientname"])) echo $row["clientname"]; else echo $strDefault;?>"></td>
	</tr>
	<tr><td height='10' colspan='3'>&nbsp;</td></tr>

	<tr height='1'><td colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>
</table>

<br><br>
<br><br>
<table border='0' width='100%' cellpadding='0' cellspacing='0'>
	<tr><td height='25' colspan='3'><b><?echo $strContractInformation;?></b></td></tr>
	<tr height='1'><td colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>

	<tr><td height='10' colspan='3'>&nbsp;</td></tr>
	<?
		if (isset($row['active']) && $row['active'] == 'false') {
	?>
	<tr>
		<td width='30' valign='top'><img src='images/info.gif'></td>
		<td width='200' colspan='2'>
		<?
			echo $strClientDeactivated;
			
			$expire_ts = mktime(0, 0, 0, $row["expire_month"], $row["expire_dayofmonth"], $row["expire_year"]);
			
			if ($row['clicks'] == 0) echo ", $strNoMoreClicks";
			if ($row['views'] == 0) echo ", $strNoMoreViews";
			if (time() < mktime(0, 0, 0, $row["activate_month"], $row["activate_dayofmonth"], $row["activate_year"]))
				echo ", $strBeforeActivate";
			if (time() > $expire_ts && $expire_ts > 0)
				echo ", $strAfterExpire";
			
			echo ".<br><br>";
		?>
		</td>
	</tr>
	<tr>
		<td><img src='images/spacer.gif' height='1' width='100%'></td>
		<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td>
	</tr>
	<?
		}
	?>
	<tr>
		<td width='30'>&nbsp;</td>
		<td width='200'><?echo $strViewsPurchased;?></td>
		<?
		if (phpAds_isUser(phpAds_Admin))
		{
			?>
			<td>
				<input type="text" name="views" size='25' value="<?if($row["views"]>0)echo $row["views"];else echo '-';?>" onKeyUp="disable_checkbox('unlimitedviews');">
				<input type="checkbox" name="unlimitedviews"<?if($row["views"]==-1)print " CHECKED";?> onClick="click_checkbox('unlimitedviews', 'views');">
				<? echo $GLOBALS['strUnlimited']; ?>
			</td>
			<?
		}
		else {
			?>
			<td><?if($row["views"]!=-1)echo $row["views"];else echo $GLOBALS['strUnlimited'];?></td>
			<?
		}
		?>
	</tr>
	<tr>
		<td><img src='images/spacer.gif' height='1' width='100%'></td>
		<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td>
	</tr>
	<tr>
		<td width='30'>&nbsp;</td>
		<td width='200'><?echo $strClicksPurchased;?></td>
		<?
		if (phpAds_isUser(phpAds_Admin))
		{
			?>
			<td>
				<input type="text" name="clicks" size='25' value="<?if($row["clicks"]>0)echo $row["clicks"];else echo '-';?>" onKeyUp="disable_checkbox('unlimitedclicks');">
				<input type="checkbox" name="unlimitedclicks"<?if($row["clicks"]==-1)print " CHECKED";?> onClick="click_checkbox('unlimitedclicks', 'clicks');">
				<? echo $GLOBALS['strUnlimited']; ?>
			</td>
			<?
		}
		else {
			?>
			<td><?if($row["clicks"]!=-1)echo $row["clicks"];else echo $GLOBALS['strUnlimited'];?></td>
			<?
		}
		?>
	</tr>
	<tr>
		<td><img src='images/spacer.gif' height='1' width='100%'></td>
		<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td>
	</tr>
	<tr>
		<td width='30'>&nbsp;</td>
		<td width='200'><? echo $GLOBALS['strActivationDate']; ?></td>
		<?
		if (phpAds_isUser(phpAds_Admin))
		{
			?>
			<td>
				<? phpAds_showDateEdit('activate', isset($row["activate_dayofmonth"]) ? $row["activate_dayofmonth"] : 0, 
												   isset($row["activate_month"]) ? $row["activate_month"] : 0, 
												   isset($row["activate_year"]) ? $row["activate_year"] : 0); ?>
			</td>
			<?
		}
		else 
		{
			?>
			<td>
				<? phpAds_showDateEdit('activate', isset($row["activate_dayofmonth"]) ? $row["activate_dayofmonth"] : 0, 
												   isset($row["activate_month"]) ? $row["activate_month"] : 0, 
												   isset($row["activate_year"]) ? $row["activate_year"] : 0, false); ?>
			</td>
			<?
		}
		?>
	</tr>
	<tr>
		<td><img src='images/spacer.gif' height='1' width='100%'></td>
		<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td>
	</tr>
	<tr>
		<td width='30'>&nbsp;</td>
		<td width='200'><? echo $strExpirationDate; ?></td>
		<?
		if (phpAds_isUser(phpAds_Admin))
		{
			?>
			<td>
				<? phpAds_showDateEdit('expire', isset($row["expire_dayofmonth"]) ? $row["expire_dayofmonth"] : 0, 
												 isset($row["expire_month"]) ? $row["expire_month"] : 0, 
												 isset($row["expire_year"]) ? $row["expire_year"] : 0); ?>
			</td>
			<?
		}
		else 
		{
			?>
			<td>
				<? phpAds_showDateEdit('expire', isset($row["expire_dayofmonth"]) ? $row["expire_dayofmonth"] : 0, 
												 isset($row["expire_month"]) ? $row["expire_month"] : 0, 
												 isset($row["expire_year"]) ? $row["expire_year"] : 0, false); ?>
			</td>

			<?
		}
		?>
	</tr>
	<tr>
		<td><img src='images/spacer.gif' height='1' width='100%'></td>
		<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td>
	</tr>
	<tr>
		<td width='30'>&nbsp;</td>
		<td width='200'><?echo $strWeight;?></td>
		<?
		if (phpAds_isUser(phpAds_Admin))
		{
			?>
			<td>
				<input type="text" name="weight" size='25' value="<?echo isset($row["weight"]) ? $row["weight"] : 1;?>">
			</td>
			<?
		}
		else {
			?>
			<td><?echo $row["weight"];?></td>
			<?
		}
		?>
	</tr>

	<tr><td height='10' colspan='3'>&nbsp;</td></tr>

	<tr height='1'><td colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>
</table>

<br><br>
		
<input type="submit" name="submit" value="<?echo $strSaveChanges;?>">
</form>



<?

/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

phpAds_PageFooter();

?>
