<?PHP

/************************************************************************/
/* vbPortal: CMS mod for vBulletin                                      */
/* vBulletin is Copyright ©2000, 2001, Jelsoft Enterprises Limited.     */
/* ===========================                                          */
/* vbPortal by wajones                                                  */
/* Copyright (c) 2001 by William A. Jones                               */
/* http://www.phpportals.com                                            */
/* ===========================                                          */
/* Based on PHP-NUKE: Web Portal System                                 */
/* Copyright (c) 2001 by Francisco Burzi (fbc@mandrakesoft.com)         */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!isset($mainfile)) { include("mainfile.php"); }

if ((isset($aid)) && (isset($pwd)) && ($op == "login")) {
    if($aid!="" AND $pwd!="") {
    $result=mysql_query("SELECT password FROM user WHERE username = '$aid'");
	list($pass)=mysql_fetch_row($result);
	$pwd = md5($pwd);
	if ($pass == $pwd){
	    $admin = base64_encode("$aid:$pwd");
	    setcookie("admin","$admin",time()+2592000);
	    unset($op);
	}
    }
}

$admintest = 0;

if(isset($admin)) {
  $admin = base64_decode($admin);
  $admin = explode(":", $admin);
  $aid = "$admin[0]";
  $pwd = "$admin[1]";
  if ($aid=="" || $pwd=="") {
    $admintest=0;
    echo "<html>\n";
    echo "<title>INTRUDER ALERT!!!</title>\n";
    echo "<body bgcolor=\"#FFFFFF\" text=\"#000000\">\n\n<br><br><br>\n\n";
    echo "<center><img src=\"images/eyes.gif\" border=\"0\"><br><br>\n";
    echo "<font face=\"Verdana\" size=\"+4\"><b>Verlassen!</b></font></center>\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;
  }
  $result=mysql_query("SELECT password as pwd FROM user WHERE username = '$aid'");
  // $result=mysql_query("select pwd from $prefix"._authors." where aid='$aid'");
  if(!$result) {
        echo "Auswahl aus der Datenbank fehlgeschlagen!";
        exit;
  } else {
    list($pass)=mysql_fetch_row($result);
    if($pass == $pwd && $pass != "") {
        $admintest = 1;
    }
  }
}
?>
