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

//chdir($DOCUMENT_ROOT . "/"); 
if (eregi("banners.php",$PHP_SELF)) {
include("config.php");
 if ($op==""){
	 $op=login;
 }
}

mysql_connect($servername, $dbusername, $dbpassword);
@mysql_select_db("$dbname") or die ("Unable to select database");
/********************************************/
/* Function to display banners in all pages */
/********************************************/
$prefix = "nuke";
function viewbanner() {
    global $prefix,$bid,$imageurl;
    $bresult = mysql_query("select * from $prefix"._banner."");
    $numrows = mysql_num_rows($bresult);

/* Get a random banner if exist any. */
/* More efficient random stuff, thanks to Cristian Arroyo from http://www.planetalinux.com.ar */

    if ($numrows>1) {
	$numrows = $numrows-1;
	mt_srand((double)microtime()*1000000);
	$bannum = mt_rand(0, $numrows);
    } else {
	$bannum = 0;
    }
    $bresult2 = mysql_query("select bid, imageurl from $prefix"._banner." limit $bannum,1");
    list($bid, $imageurl) = mysql_fetch_row($bresult2);
    
    global $myIP;
    $myhost = getenv("REMOTE_ADDR");
    if($myIP==$myhost) {
    } else {
	mysql_query("update $prefix"._banner." set impmade=impmade+1 where bid=$bid");
    }
    if($numrows>0) {
	$aborrar = mysql_query("select cid, imptotal, impmade, clicks, date from $prefix"._banner." where bid=$bid");
	list($cid, $imptotal, $impmade, $clicks, $date) = mysql_fetch_row($aborrar);

/* Check if this impression is the last one and print the banner */

	if($imptotal==$impmade) {
	    mysql_query("insert into $prefix"._banner."finish values (NULL, '$cid', '$impmade', '$clicks', '$date', now())");
	    mysql_query("delete from $prefix"._banner." where bid=$bid");
	}
   // echo"<center><a href=\"banners.php?op=click&amp;bid=$bid\" target=\"_blank\"><img src=\"$imageurl\" border=\"1\" alt=\"\"></a></center><br>";
    }
    mysql_free_result($bresult);
    mysql_free_result($bresult2);
    if($aborrar) {
	mysql_free_result($aborrar);
    }
    
}

/********************************************/
/* Function to redirect the clicks to the   */
/* correct url and add 1 click              */
/********************************************/

function clickbanner($bid) {
    global $prefix;
    $bresult = mysql_query("select clickurl from $prefix"._banner." where bid=$bid");
    list($clickurl) = mysql_fetch_row($bresult);
    mysql_query("update $prefix"._banner." set clicks=clicks+1 where bid=$bid");
    mysql_free_result($bresult);
    Header("Location: $clickurl");
}

/********************************************/
/* Function to let your client login to see */
/* the stats                                */
/********************************************/

function clientlogin() {
    echo"
    <html>
    <body bgcolor=\"#AA9985\" text=\"#000000\" link=\"#006666\" vlink=\"#006666\">
    <center><br><br><br><br>
    <table width=\"200\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#000000\"><tr><td>
    <table width=\"100%\" cellpadding=\"5\" cellspacing=\"1\" border=\"0\" bgcolor=\"#FFFFFF\"><tr><td bgcolor=\"#EECFA1\">
    <center><b>Werbestatistiken</b></center>
    </td></tr><tr><td bgcolor=\"#FFFACD\">
    <form action=\"banners.php\" method=\"post\">
    Login: <input type=\"text\" name=\"login\" size=\"12\" maxlength=\"10\"><br>
    Password: <input type=\"password\" name=\"pass\" size=\"12\" maxlength=\"10\"><br>
    <input type=\"hidden\" name=\"op\" value=\"Ok\">
    <input type=\"submit\" value=\"Login\">
    </td></tr><tr><td bgcolor=\"#EECFA1\">
    <font size=\"2\">
    <center>Bitte geben Sie ihre Kundeninformationen ein.</center>
    </font></form>
    </td></tr></table></td></tr></table>
    </body>
    </html>
    ";
}

/*********************************************/
/* Function to display the banners stats for */
/* each client                               */
/*********************************************/

function bannerstats($login, $pass) {
    global $prefix;
    $result = mysql_query("select cid, name, passwd from $prefix"._banner."client where login='$login'");
    list($cid, $name, $passwd) = mysql_fetch_row($result);
    
    if($login=="" AND $pass=="" OR $pass=="") {
	echo "<center><br>Login Fehlerhaft!!!<br><br><a href=\"javascript:history.go(-1)\">Zurück zur Anmeldung</a></center>";
    } else {
    
    if ($pass==$passwd) {
    
    echo"
    <html>
    <body bgcolor=\"#AA9985\" text=\"#000000\" link=\"#006666\" vlink=\"#006666\">
    <center>
    <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" bgcolor=\"#000000\"><tr><td>
    <table border=\"0\" width=\"100%\" cellpadding=\"8\" cellspacing=\"1\" bgcolor=\"#FFFACD\"><tr><td>
    <font size=\"3\">
    <center><b>Current Active Banners for $name</b></center><br>
    </font>
    <table width=\"100%\" border=\"0\"><tr>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>ID</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>Gemachter Eindruck</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>Gesamteindruck</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>Liegengelassener Eindruck</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>Klicks</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>% Klicks</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>Funktionen</b></td><tr>";
    $result = mysql_query("select bid, imptotal, impmade, clicks, date from $prefix"._banner." where cid='$cid'");
    while(list($bid, $imptotal, $impmade, $clicks, $date) = mysql_fetch_row($result)) {
        if($impmade==0) {
    	    $percent = 0;
        } else {
    	    $percent = substr(100 * $clicks / $impmade, 0, 5);
        }

        if($imptotal==0) {
    	    $left = "Unlimited";
        } else {
    	    $left = $imptotal-$impmade;
        }
        echo "
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$bid</td>
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$impmade</td>
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$imptotal</td>
	<td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$left</td>
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$clicks</td>
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$percent%</td>
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\"><a href=\"banners.php?op=EmailStats&login=$login&cid=$cid&bid=$bid\">E-mail Stats</a></td><tr>";
    }
    echo "
    </table>
    <center><br><br>
    Fortlaufende Banner in $sitename<br><br>";

    $result = mysql_query("select bid, imageurl, clickurl from $prefix"._banner." where cid='$cid'");
    while(list($bid, $imageurl, $clickurl) = mysql_fetch_row($result)) {

	$numrows = mysql_num_rows($result);
	if ($numrows>1) {
	    echo "<hr noshade width=\"80%\"><br>";
	}

	echo "<img src=\"$imageurl\" border=\"1\"><br>
	<font size=\"2\">Banner ID: $bid<br>
	Sende <a href=\"banners.php?op=EmailStats&login=$login&cid=$cid&bid=$bid\">E-Mail Statistik</a> für diesen Banner<br>
	Dieser Banner zeigt auf <a href=\"$clickurl\">diese URL</a><br>
	<form action=\"banners.php\" method=\"submit\">
	Ändere URL: <input type=\"text\" name=\"url\" size=\"50\" maxlength=\"200\" value=\"$clickurl\">
	<input type=\"hidden\" name=\"login\" value=\"$login\">
	<input type=\"hidden\" name=\"bid\" value=\"$bid\">
	<input type=\"hidden\" name=\"pass\" value=\"$pass\">
	<input type=\"hidden\" name=\"cid\" value=\"$cid\">
	<input type=\"submit\" name=\"op\" value=\"Ändern\"></form></font>";
    }
    echo "
    </td></tr></table></td></tr></table>
    ";


/* Finnished Banners */
    
    echo "
    <center><br>
    <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" bgcolor=\"000000\"><tr><td>
    <table border=\"0\" width=\"100%\" cellpadding=\"8\" cellspacing=\"1\" bgcolor=\"#FFFACD\"><tr><td>
    <font size=\"3\">
    <center><b>Beendete Banner für $name</b></center><br>
    </font>
    <table width=\"100%\" border=\"0\"><tr>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>ID</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>Eindrücke</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>Klicks</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>% Klicks</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>Start</b></td>
    <td bgcolor=\"#887765\"><font color=\"Black\"><center><b>Ende</b></td></tr>";
    $result = mysql_query("select bid, impressions, clicks, datestart, dateend from $prefix"._banner."finish where cid='$cid'");
    while(list($bid, $impressions, $clicks, $datestart, $dateend) = mysql_fetch_row($result)) {
        $percent = substr(100 * $clicks / $impressions, 0, 5);
	echo "
        <tr><td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$bid</td>
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$impressions</td>
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$clicks</td>
	<td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$percent%</td>
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$datestart</td>
        <td bgcolor=\"#AA9985\" align=\"center\"><font color=\"White\">$dateend</td></tr>";
    }
echo "
</table></td></tr></table></td></tr></table>
<br>
</body>
</html>
";
    
    } else {
	echo "<center><br>Login FEHLERHAFT!!!<br><br><a href=\"javascript:history.go(-1)\">Back to Login Screen</a></center>";
    }
}
}

/*********************************************/
/* Function to let the client E-mail his     */
/* banner Stats                              */
/*********************************************/

function EmailStats($login, $cid, $bid, $pass) {
    global $prefix;
    $result2 = mysql_query("select name, email from $prefix"._banner."client where cid='$cid'");
    list($name, $email) = mysql_fetch_row($result2);
    if ($email=="") {
	echo "
	<html>
	<body bgcolor=\"#AA9985\" text=\"#000000\" link=\"#006666\" vlink=\"#006666\">
	<center><br><br><br>
	<b>Statistiken für den Banner Nr. $bid konnten nicht gesendet werden<br>
	da keine E-Mail verbindung mit den Kunden $name besteht.<br>
	Bitte kontaktieren Sie den Adminstrator.<br><br></b>
	<a href=\"javascript:history.go(-1)\">Zurück zu den Banner Statistiken</a>
	";
    } else {
	$result = mysql_query("select bid, imptotal, impmade, clicks, imageurl, clickurl, date from $prefix"._banner." where bid='$bid' and cid='$cid'");
	list($bid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date) = mysql_fetch_row($result);
        if($impmade==0) {
    	    $percent = 0;
        } else {
    	    $percent = substr(100 * $clicks / $impmade, 0, 5);
        }

        if($imptotal==0) {
    	    $left = "Unlimited";
	    $imptotal = "Unlimited";
        } else {
    	    $left = $imptotal-$impmade;
        }
	$fecha = date("F jS Y, h:iA.");
	$subject = "Ihre Banner Statistik für $sitename";
	$message = "Die vollständige Bannerstatistik für Ihre Werbung auf $sitename:\n\n\nKunden Name: $name\nBanner ID: $bid\nAnz. Bild: $imageurl\nBanner URL: $clickurl\n\nBezahlte Eindrücke: $imptotal\nGemachte Eindrücke: $impmade\nLiegengelassene Eindrücke: $left\nKlicks empfangen: $clicks\nKlicks in Prozent: $percent%\n\n\nReport: $fecha";
	$from = "$sitename";
	mail($email, $subject, $message, "From: $from\nX-Mailer: PHP/" . phpversion());

	echo "
	<html>
	<body bgcolor=\"#AA9985\" text=\"#000000\" link=\"#006666\" vlink=\"#006666\">
	<center><br><br><br>
	<b>Statistiken des Banner Nr. $bid wurden an folgende Adresse gesendet:<br>
	<i>$email</i> - $name<br><br></b>
	<a href=\"javascript:history.go(-1)\">Zurück zu den Banner Statistiken</a>
	";
    }
}

/*********************************************/
/* Function to let the client to change the  */
/* url for his banner                        */
/*********************************************/

function change_banner_url_by_client($login, $pass, $cid, $bid, $url) {
    global $prefix;
    $result = mysql_query("select passwd from $prefix"._banner."client where cid='$cid'");
    list($passwd) = mysql_fetch_row($result);
    if (!empty($pass) AND $pass==$passwd) {
	mysql_query("update $prefix"._banner." set clickurl='$url' where bid='$bid'");
	echo "<center><br>Sie haben die URL geändert<br><br><a href=\"javascript:history.go(-1)\">Zurück zu den Statistiken</a></center>";
    } else {
	echo "<center><br>Ihr Login/Password ist nicht korrekt.<br><br>Bitte versuchen Sie es <a href=\"banners.php?op=login\">erneut</a></center>";
    }
    
}

switch($op) {

    case "click":
	clickbanner($bid);
	break;

    case "login":
	clientlogin();
	break;

    case "Ok":
	bannerstats($login, $pass);
	break;

    case "Change":
	change_banner_url_by_client($login, $pass, $cid, $bid, $url);
	break;

    case "EmailStats":
	EmailStats($login, $cid, $bid, $pass);
	break;
	
    default:
	viewbanner();
	break;
}

?>
