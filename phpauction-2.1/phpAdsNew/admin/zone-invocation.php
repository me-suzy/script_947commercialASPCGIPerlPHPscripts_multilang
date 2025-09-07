<?php // $Revision: 1.1.1.1 $

/************************************************************************/
/* phpAdsNew 2                                                          */
/* ===========                                                          */
/*                                                                      */
/* Copyright (c) 2001 by Niels Leenheer <niels@creatype.nl              */
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
/* HTML framework                                        */
/*********************************************************/

phpAds_PageHeader("4.2.5", $extra);
phpAds_ShowSections(array("4.2.2", "4.2.3", "4.2.4", "4.2.5"));




/*********************************************************/
/* Generate bannercode                                   */
/*********************************************************/

function phpAds_GenerateInvocationCode($zoneid)
{
	global $codetype, $clientID, $source, $target;
	global $withText, $template, $refresh, $uniqueid;
	global $phpAds_url_prefix;
	global $phpAds_tbl_zones;
	
	
	// Get zone info
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
	
	
	// Set default values
	$what   = "zone:".$zoneid;
	$width  = $zone['width'];
	$height = $zone['height'];
	
	$buffer = '';
	$parameters = array();
	
	// Set parameters
	$parameters[] = "what=zone:".$zoneid;
	
	if (isset($source) && $source != '')
		$parameters[] = "source=".$source;
	
	if (isset($target) && $target != '')
		$parameters[] = "target=".$target;
	
	
	// Remote invocation
	if ($codetype=='adview')
	{
		if (isset($uniqueid) && $uniqueid != '')
			$parameters[] = "n=".$uniqueid;	
		
		$buffer .= "<a href='$phpAds_url_prefix/adclick.php";
		if (isset($uniqueid) & $uniqueid != '')
			$buffer .= "?n=".$uniqueid;
		$buffer .= "'";
		if (isset($target) && $target != '')
			$buffer .= " target='$target'";
		$buffer .= "><img src='$phpAds_url_prefix/adview.php";
		if (sizeof($parameters) > 0)
			$buffer .= "?".implode ("&", $parameters);
		$buffer .= "' border='0'></a>";
	}
	
	// Remote invocation with JavaScript
	if ($codetype=='adjs')
	{
		if (isset($withText) && $withText == '0')
			$parameters[] = "withText=0";
		
		$buffer .= "<script language='JavaScript' src='$phpAds_url_prefix/adjs.php";
		if (sizeof($parameters) > 0)
			$buffer .= "?".implode ("&", $parameters);
		$buffer .= "'></script>";
	}
	
	// Remote invocation for iframes
	if ($codetype=='adframe')
	{
		if (isset($refresh) && $refresh != '')
			$parameters[] = "refresh=".$refresh;
		
		$buffer .= "<iframe src='$phpAds_url_prefix/adframe.php";
		if (sizeof($parameters) > 0)
			$buffer .= "?".implode ("&", $parameters);
		$buffer .= "' framespacing='0' frameborder='no' scrolling='no'";
		if (isset($width) & $width != '')
			$buffer .= " width='".$width."'";
		if (isset($height) & $height != '')
			$buffer .= " height='".$height."'";
		$buffer .= "></iframe>";
	}
	
	// Combined remote invocation
	if ($codetype=='ad')
	{
		if (isset($refresh) && $refresh != '')
			$parameters['tmp'] = "refresh=".$refresh;
		
		$buffer .= "<iframe src='$phpAds_url_prefix/adframe.php";
		if (sizeof($parameters) > 0)
			$buffer .= "?".implode ("&", $parameters);
		$buffer .= "' framespacing='0' frameborder='no' scrolling='no'";
		if (isset($width) & $width != '')
			$buffer .= " width='".$width."'";
		if (isset($height) & $height != '')
			$buffer .= " height='".$height."'";
		$buffer .= ">";
		
		
		// Parameters for remote invocation for Javascript
		if (isset($withText) && $withText == '0')
			$parameters['tmp'] = "withText=0";
		else
			unset ($parameters['tmp']);
		
		$buffer .= "<script language='JavaScript' src='$phpAds_url_prefix/adjs.php";
		if (sizeof($parameters) > 0)
			$buffer .= "?".implode ("&", $parameters);
		$buffer .= "'></script>";
		
		
		// Parameters for remote invocation
		if (isset($uniqueid) && $uniqueid != '')
			$parameters['tmp'] = "n=".$uniqueid;	
		
		$buffer .= "<noscript>";
		$buffer .= "<a href='$phpAds_url_prefix/adclick.php";
		if (isset($uniqueid) & $uniqueid != '')
			$buffer .= "?n=".$uniqueid;
		$buffer .= "'";
		if (isset($target) && $target != '')
			$buffer .= " target='$target'";
		$buffer .= "><img src='$phpAds_url_prefix/adview.php";
		if (sizeof($parameters) > 0)
			$buffer .= "?".implode ("&", $parameters);
		$buffer .= "' border='0'></a>";		
		$buffer .= "</noscript>";
		
		$buffer .= "</iframe>";
	}
	
	if ($codetype=='local')
	{
		$path = phpAds_path;
		$path = str_replace ('\\', '/', $path);
		$root = getenv('DOCUMENT_ROOT');
		$pos  = strpos ($path, $root);
		
		if (is_int($pos) && $pos == 0)
			$path = "getenv('DOCUMENT_ROOT').'".substr ($path, $pos + strlen ($root))."/phpadsnew.inc.php'";
		else
			$path = "'".$path."/phpadsnew.inc.php'";
		
		$buffer .= "<"."?php\n";
		$buffer .= "    require($path);\n";
		$buffer .= "    view ('$what', 0, '$target', '$source', '$withText');\n";
		$buffer .= "?".">";
	
	}
	
	return $buffer;
}



/*********************************************************/
/* Main code                                             */
/*********************************************************/

echo "<img src='images/icon-zone.gif' align='absmiddle'>&nbsp;<b>".phpAds_getZoneName($zoneid)."</b><br>";

echo "<br><br>";
echo "<br><br>";
echo "<br><br>";
echo "<form name='availability' action='zone-invocation.php' method='POST'>\n";
echo "<input type='hidden' name='zoneid' value='".$zoneid."'>";


if (!isset($codetype)) $codetype = 'local';

// Invocation type selection
echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
echo "<tr><td height='25' colspan='3'><img src='images/icon-generatecode.gif' align='absmiddle'>&nbsp;<b>$strChooseInvocationType</b></td></tr>";
echo "<tr><td height='1' colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
echo "<tr><td height='35'>";
	echo "<select name='codetype'>";
	echo "<option value='adview'".($codetype == 'adview' ? ' selected' : '').">Remote Invocation</option>";
	echo "<option value='adjs'".($codetype == 'adjs' ? ' selected' : '').">Remote Invocation with JavaScript</option>";
	echo "<option value='adframe'".($codetype == 'adframe' ? ' selected' : '').">Remote Invocation for iframes</option>";
	echo "<option value='ad'".($codetype == 'ad' ? ' selected' : '').">Combined Remote Invocation</option>";
	echo "<option value='local'".($codetype == 'local' ? ' selected' : '').">Local mode</option>";
	echo "</select>&nbsp;";
echo "</td></tr></table>";
echo "<br><br>";


// Header
echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
echo "<tr><td height='25' colspan='3'><b>$strParameters</b></td></tr>";
echo "<tr height='1'><td colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";
echo "<tr><td height='10' colspan='3'>&nbsp;</td></tr>";






if ($codetype == 'adview')
	$show = array ('target' => true, 'source' => true, 'uniqueid' => true);

if ($codetype == 'adjs')
	$show = array ('target' => true, 'source' => true, 'withText' => true);

if ($codetype == 'adframe')
	$show = array ('target' => true, 'source' => true, 'refresh' => true);

if ($codetype == 'ad')
	$show = array ('target' => true, 'source' => true, 'withText' => true, 'uniqueid' => true, 'refresh' => true);

if ($codetype == 'local')
	$show = array ('target' => true, 'source' => true, 'withText' => true, 'template' => true);


// Target
if (isset($show['target']) && $show['target'] == true)
{
//	echo "<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td></tr>";
	echo "<tr><td width='30'>&nbsp;</td>";
	echo "<td width='200'>Target</td><td width='370'>";
		echo "<input type='text' name='target' size='' value='".(isset($target) ? $target : '')."' style='width:175px;'></td></tr>";
	echo "<tr><td width='30'><img src='images/spacer.gif' height='1' width='100%'></td>";
}


// Source
if (isset($show['source']) && $show['source'] == true)
{
	echo "<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td></tr>";
	echo "<tr><td width='30'>&nbsp;</td>";
	echo "<td width='200'>Source</td><td width='370'>";
		echo "<input type='text' name='source' size='' value='".(isset($source) ? $source : '')."' style='width:175px;'></td></tr>";
	echo "<tr><td width='30'><img src='images/spacer.gif' height='1' width='100%'></td>";
}


// WithText
if (isset($show['withText']) && $show['withText'] == true)
{
	echo "<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td></tr>";
	echo "<tr><td width='30'>&nbsp;</td>";
	echo "<td width='200'>WithText</td>";
	echo "<td width='370'><input type='radio' name='withText' value='1'".(isset($withText) && $withText == 1 ? ' checked' : '').">&nbsp;True<br>";
	echo "<input type='radio' name='withText' value='0'".(!isset($withText) || $withText == 0 ? ' checked' : '').">&nbsp;False</td>";
	echo "</tr>";
	echo "<tr><td width='30'><img src='images/spacer.gif' height='1' width='100%'></td>";
}


// refresh
if (isset($show['refresh']) && $show['refresh'] == true)
{
	echo "<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td></tr>";
	echo "<tr><td width='30'>&nbsp;</td>";
	echo "<td width='200'>Refresh</td><td width='370'>";
		echo "<input type='text' name='refresh' size='' value='".(isset($refresh) ? $refresh : '')."' style='width:175px;'></td></tr>";
	echo "<tr><td width='30'><img src='images/spacer.gif' height='1' width='100%'></td>";
}


// uniqueid
if (isset($show['uniqueid']) && $show['uniqueid'] == true)
{
	echo "<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td></tr>";
	echo "<tr><td width='30'>&nbsp;</td>";
	echo "<td width='200'>$strUniqueidentifier (n)</td><td width='370'>";
		echo "<input type='text' name='uniqueid' size='' value='".(isset($uniqueid) ? $uniqueid : '')."' style='width:175px;'></td></tr>";
	echo "<tr><td width='30'><img src='images/spacer.gif' height='1' width='100%'></td>";
}


if (isset($submit))
{
	echo "<td colspan='2'><img src='images/break-l.gif' height='1' width='200' vspace='6'></td></tr>";
	echo "<tr><td width='30'>&nbsp;</td>";
	echo "<td width='560' colspan='2'>";
	echo "<br><br><img src='images/icon-generatecode.gif' align='absmiddle'>&nbsp;<b>$strBannercode:</b><br><br>";
	
	echo "<textarea rows='8' cols='55' style='width:560px;'>".htmlentities(phpAds_GenerateInvocationCode($zoneid))."</textarea>";
	echo "</td></tr>";
}


echo "<tr><td height='10' colspan='3'>&nbsp;</td></tr>";
echo "<tr height='1'><td colspan='3' bgcolor='#888888'><img src='images/break.gif' height='1' width='100%'></td></tr>";

echo "</table>";
echo "<br><br>";
echo "<input type='submit' value='$strGenerate' name='submit'>";
echo "</form>";
echo "<br><br>";



/*********************************************************/
/* HTML framework                                        */
/*********************************************************/

phpAds_PageFooter();







?>
