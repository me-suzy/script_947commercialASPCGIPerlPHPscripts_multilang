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
require ("lib-gd.inc.php");

// Security check
phpAds_checkAccess(phpAds_Admin+phpAds_Client);



/*********************************************************/
/* Client interface security                             */
/*********************************************************/

if (phpAds_isUser(phpAds_Client))
{
	$clientID = phpAds_clientID();
}



/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

$extra = '';

if (phpAds_isUser(phpAds_Admin))
{
	$res = db_query("
	SELECT
		*
	FROM
		$phpAds_tbl_clients
	WHERE
		parent = 0
	") or mysql_die();
	
	while ($row = mysql_fetch_array($res))
	{
		if ($clientID == $row['clientID'])
			$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-1.gif'>&nbsp;";
		else
			$extra .= "&nbsp;&nbsp;&nbsp;<img src='images/box-0.gif'>&nbsp;";
		
		$extra .= "<a href='stats-client.php?clientID=".$row['clientID']."'>";
		$extra .= phpAds_buildClientName ($row['clientID'], $row['clientname']);
		$extra .= "</a>";
		$extra .= "<br>"; 
	}
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	
	$extra .= "<br><br><br><br><br>";
	$extra .= "<b>$strShortcuts</b><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	$extra .= "<img src='images/icon-client.gif' align='absmiddle'>&nbsp;<a href=client-edit.php?clientID=$clientID>$strModifyClient</a><br>";
	$extra .= "<img src='images/break.gif' height='1' width='160' vspace='4'><br>";
	
	phpAds_PageHeader("2.1.1", $extra);
	phpAds_ShowSections(array("2.1.1"));
	
	echo "<img src='images/icon-client.gif' align='absmiddle'>&nbsp;<b>".phpAds_getClientName($clientID)."</b>";
}

if (phpAds_isUser(phpAds_Client))
{
	phpAds_PageHeader("1.2", $extra);
	phpAds_ShowSections(array("1.1", "1.2"));
}

echo "<br><br>";
echo "<br><br>";
echo "<br><br>";


/*********************************************************/
/* Main code                                             */
/*********************************************************/

if (!isset($limit) || $limit=='') $limit = '7';


// Get bannerID's for this client
$idresult = db_query (" SELECT
						$phpAds_tbl_banners.bannerID
					  FROM
					  	$phpAds_tbl_banners, $phpAds_tbl_clients
					  WHERE
					  	$phpAds_tbl_clients.parent = $clientID AND
						$phpAds_tbl_clients.clientID = $phpAds_tbl_banners.clientID
					");


if (@mysql_num_rows($idresult) > 0)
{
	while ($row = mysql_fetch_array($idresult))
	{
		$bannerIDs[] = "bannerID = ".$row['bannerID'];
	}
	
	if ($phpAds_compact_stats) 
	{
		$result = db_query(" SELECT
								*,
								sum(views) as sum_views,
								sum(clicks) as sum_clicks,
								DATE_FORMAT(day, '$date_format') as t_stamp_f
					 		 FROM
								$phpAds_tbl_adstats
							 WHERE
								(".implode(' OR ', $bannerIDs).")
							 GROUP BY
							 	day
							 ORDER BY
								day DESC
							 LIMIT $limit 
				  ") or mysql_die();
		
		//mysql_die();
		while ($row = mysql_fetch_array($result))
		{
			$stats[$row['day']] = $row;
		}
	}
	else
	{
		$result = db_query(" SELECT
								count(*) as views,
								DATE_FORMAT(t_stamp, '$date_format') as t_stamp_f,
								DATE_FORMAT(t_stamp, '%Y-%m-%d') as day
					 		 FROM
								$phpAds_tbl_adviews
							 WHERE
								(".implode(' OR ', $bannerIDs).")
							 GROUP BY
							    day
							 ORDER BY
								day DESC
							 LIMIT $limit 
				  ");
		
		while ($row = mysql_fetch_array($result))
		{
			$stats[$row['day']]['sum_views'] = $row['views'];
			$stats[$row['day']]['sum_clicks'] = '0';
			$stats[$row['day']]['t_stamp_f'] = $row['t_stamp_f'];
		}
		
		
		$result = db_query(" SELECT
								count(*) as clicks,
								DATE_FORMAT(t_stamp, '$date_format') as t_stamp_f,
								DATE_FORMAT(t_stamp, '%Y-%m-%d') as day
					 		 FROM
								$phpAds_tbl_adclicks
							 WHERE
								(".implode(' OR ', $bannerIDs).")
							 GROUP BY
							    day
							 ORDER BY
								day DESC
							 LIMIT $limit 
				  ");
		
		while ($row = mysql_fetch_array($result))
		{
			$stats[$row['day']]['sum_clicks'] = $row['clicks'];
			$stats[$row['day']]['t_stamp_f'] = $row['t_stamp_f'];
		}
	}
}

echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

echo "<tr bgcolor='#FFFFFF' height='25'>";
echo "<td align='left' nowrap height='25'><b>$strDays</b></td>";
echo "<td align='left' nowrap height='25'><b>$strViews</b></td>";
echo "<td align='left' nowrap height='25'><b>$strClicks</b></td>";
echo "<td align='left' nowrap height='25'><b>$strCTRShort</b>&nbsp;&nbsp;</td>";
echo "</tr>";

echo "<tr><td height='1' colspan='4' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";


$totalviews  = 0;
$totalclicks = 0;

// for (reset($stats);$key=key($stats);next($stats))	{

$today = time();

for ($d=0;$d<$limit;$d++)
{
	$key = date ("Y-m-d", $today - ((60 * 60 * 24) * $d));
	$text = date (str_replace ("%", "", $date_format), $today - ((60 * 60 * 24) * $d));
	
	if (isset($stats[$key]))
	{
		$views  = $stats[$key]['sum_views'];
		$clicks = $stats[$key]['sum_clicks'];
		$text   = $stats[$key]['t_stamp_f'];
		$ctr	= phpAds_buildCTR($views, $clicks);
		
		$totalviews  += $views;
		$totalclicks += $clicks;
		
		$available = true;
	}
	else
	{
		$views  = '-';
		$clicks = '-';
		$ctr	= '-';
		$available = false;
	}
	
	$bgcolor="#FFFFFF";
	$d % 2 ? 0: $bgcolor= "#F6F6F6";
	
	echo "<tr>";
	
	echo "<td height='25' bgcolor='$bgcolor'>&nbsp;".$text."</td>";
	
	echo "<td height='25' bgcolor='$bgcolor'>".$views."</td>";
	echo "<td height='25' bgcolor='$bgcolor'>".$clicks."</td>";
	echo "<td height='25' bgcolor='$bgcolor'>".$ctr."</td>";
	echo "</tr>";
	
	echo "<tr><td height='1' colspan='4' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
}

if ($totalviews > 0 || $totalclicks > 0)
{
	echo "<tr>";
	echo "<td height='25'>&nbsp;</td>";
	echo "<td height='25'>&nbsp;</td>";
	echo "<td height='25'>&nbsp;</td>";
	echo "<td height='25'>&nbsp;</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td height='25'>&nbsp;<b>$strTotal</b></td>";
	echo "<td height='25'>".$totalviews."</td>";
	echo "<td height='25'>".$totalclicks."</td>";
	echo "<td height='25'>".phpAds_buildCTR($totalviews, $totalclicks)."</td>";
	echo "</tr>";
	
	echo "<tr><td height='1' colspan='4' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
	
	echo "<tr>";
	echo "<td height='25'>&nbsp;<b>$strAverage</b></td>";
	echo "<td height='25'>".number_format (($totalviews / $d), $phpAds_percentage_decimals)."</td>";
	echo "<td height='25'>".number_format (($totalclicks / $d), $phpAds_percentage_decimals)."</td>";
	echo "<td height='25'>&nbsp;</td>";
	echo "</tr>";
	
	echo "<tr><td height='1' colspan='4' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
}

echo "<tr>";
echo "<form action='".$GLOBALS['PHP_SELF']."'>";
echo "<td height='35' colspan='4' align='right'>";
	echo $strHistory.":&nbsp;&nbsp;";
	echo "<input type='hidden' name='clientID' value='$clientID'>";
	echo "<select name='limit' onChange=\"this.form.submit();\">";
	echo "<option value='7' ".($limit==7?'selected':'').">7 ".$strDays."</option>";
	echo "<option value='14' ".($limit==14?'selected':'').">14 ".$strDays."</option>";
	echo "<option value='21' ".($limit==21?'selected':'').">21 ".$strDays."</option>";
	echo "<option value='28' ".($limit==28?'selected':'').">28 ".$strDays."</option>";
	echo "</select>&nbsp;";
	echo "<input type='image' src='images/go_blue.gif' border='0' name='submit'>";
echo "</td>";
echo "</form>";
echo "</tr>";

if ($totalviews > 0 || $totalclicks > 0)
{
	if (phpAds_GDImageFormat() != "none") 
	{
		//echo "<tr><td colspan='4' align='left' bgcolor='#FFFFFF'>";
		//echo "<br><br><img src='graph-details.php?bannerID=$bannerID&campaignID=$campaignID&limit=$limit'><br><br><br>";
		//echo "</td></tr>";
	}
	
	//echo "<tr><td height='1' colspan='4' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
}

echo "</table>";

if (phpAds_isUser(phpAds_Admin))
{
	echo "<br><br>";
	
	echo "<table width='100%' border='0' align='center' cellspacing='0' cellpadding='0'>";
	echo "<tr><td height='25'><b>$strMaintenance</b></td></tr>";
  	echo "<tr><td height='1' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
	
	echo "<tr><td height='35'>";
	echo "<img src='images/icon-mail.gif' align='absmiddle'>&nbsp;";
	echo $strSendAdvertisingReport;
	echo "</td></tr>";
	
	echo "<tr><td height='25'>";
	echo "<form method='get' action='client-mailreport.php'>";
	echo "<input type='hidden' name='clientID' value='$clientID'>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$strFrom&nbsp;&nbsp;";
	
	// Starting date
	echo "<select name='startday'>\n";
	echo "<option value='' selected>-</option>\n";
	for ($i=1;$i<=31;$i++)
		echo "<option value='$i'>$i</option>\n";
	echo "</select>&nbsp;\n";
	
	echo "<select name='startmonth'>\n";
	echo "<option value='' selected>-</option>\n";
	for ($i=1;$i<=12;$i++)
		echo "<option value='$i'>".$strMonth[$i-1]."</option>\n";
	echo "</select>&nbsp;\n";
	
	$end = date('Y');
	
	echo "<select name='startyear'>\n";
	echo "<option value='' selected>-</option>\n";
	for ($i=date('Y')-4;$i<=date('Y');$i++)
		echo "<option value='$i'>$i</option>\n";
	echo "</select>\n";	
	
	// To
	echo "&nbsp;$strTo&nbsp;&nbsp;";
	
	// End date
	echo "<select name='endday'>\n";
	for ($i=1;$i<=31;$i++)
		echo "<option value='$i'".($i == date('d') ? ' selected' : '').">$i</option>\n";
	echo "</select>&nbsp;\n";
	
	echo "<select name='endmonth'>\n";
	for ($i=1;$i<=12;$i++)
		echo "<option value='$i'".($i == date('m') ? ' selected' : '').">".$strMonth[$i-1]."</option>\n";
	echo "</select>&nbsp;\n";
	
	$end = date('Y');
	
	echo "<select name='endyear'>\n";
	for ($i=date('Y')-4;$i<=date('Y');$i++)
		echo "<option value='$i'".($i == date('Y') ? ' selected' : '').">$i</option>\n";
	echo "</select>\n";	
	
	echo "&nbsp;";
	echo "<input type='image' src='images/go_blue.gif'>";
	
	echo "</form>";
	
	echo "</td></tr>";
  	echo "<tr><td height='1' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
	
	
	// Reset statistics
	echo "<tr><td height='25'>";
	echo "<a href='stats-reset.php?clientID=$clientID'".phpAds_DelConfirm($strConfirmResetClientStats).">";
	echo "<img src='images/icon-undo.gif' align='absmiddle' border='0'>&nbsp;$strResetStats</a>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "</td></tr>";
	
	echo "</table>";
}




/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

phpAds_PageFooter();

?>
