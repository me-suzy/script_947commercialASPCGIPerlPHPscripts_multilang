<?php
error_reporting(7);
$templatesused='stat,stats_head,stats_head1,stats_head2,stats_table_threads,stats_table_threads1,stat_daybit';
require('./global.php');

// Einstellungen für die Statistik START
// Wenn ihr wollt das zusätzliche Boards außer die die über die Forumpermissions geregelt sind nicht mit aufgeführt werden 
// sollen bei den Beiträgen mit den meisten Hits bzw Antworten dann schreibt für jedes Board zwischen den "" bei $dieseboardsnicht1
// AND forumid!='ID des Forums' also bei der ID 12 würde dann da AND forumid!='12' stehen, wollt ihr z.B. die Boards mit
// der ID 11,12 und 13 zusätzlich ausblenden dann muß die ganze Zeile so aussehen (ohne die // am Anfang):
// $dieseboardsnicht1 = "AND forumid!='11' AND forumid!='12' AND forumid!='13'";
// Dies ist die besagte Zeile von der geredet wurde:
$dieseboardsnicht1 = "";

// Wenn Ihr wollt das Gäste die Statistik nicht sehen dürfen dann ändert die 0 auf eine 1
// 0 = Gäste dürfen die Statistik sehen
// 1 = Gäste dürfen die Statistik NICHT sehen
$darfsehen = "0";

// Top10 Referals anzeigen
// 0 = Top10 Referals nicht anzeigen
// 1 = Top10 Referals anzeigen
$top10referalsan = "1";

// Top10 Poster anzeigen
// 0 = Top10 Poster nicht anzeigen
// 1 = Top10 Poster anzeigen
$top10posteran ="1";

// Top10 Poster der letzten 30 Tage anzeigen
// 0 = Top10 Poster der letzten 30 Tage nicht anzeigen
// 1 = Top10 Poster der letzten 30 Tage anzeigen
$top10poster30an ="1";

// Top10 Poster der letzten 24 Stunden anzeigen
// 0 = Top10 Poster letzten 24 Stunden nicht anzeigen
// 1 = Top10 Poster letzten 24 Stunden anzeigen
$top10poster24an ="1";

// Top10 Besuchte Threads anzeigen
// 0 = Top 10 Besuchte Threads nicht anzeigen
// 1 = Top 10 Besuchte Threads anzeigen
$top10viewsan ="1";

// Top 10 Beantwortete Threads anzeigen
// 0 = Top 10 Beantwortete Threads nicht anzeigen
// 1 = Top 10 Beantwortete Threads anzeigen
$top10antwortenan ="1";

// Heute waren schon folgende User im Board anzeigen
// 0 = Heute waren schon folgende User im Board nicht anzeigen
// 1 = Heute waren schon folgende User im Board anzeigen
$heuteonlinean ="1";

// Server-Statistik anzeigen
// 0 = Server-Statistik nicht anzeigen
// 1 = Server-Statistik anzeigen
$serverinfoan ="1";

// Letze 10 Suchwörter anzeigen
// 0 = Letze 10 Suchwörter nicht anzeigen
// 1 = Letze 10 Suchwörter anzeigen
$suchwoerteran ="1";

// Sollen die Wörter nach denen Admins S-Mods und Mods gesucht haben angezeigt werden ?
// 0 = nicht anzeigen
// 1 = anzeigen
$showsearchedby567="1";

// ab hier braucht nichts mehr geändert zu werden !!!!!!!!!!!!!!!!!!!! Es sei denn eure AdminID ist nicht 6 und ihr habt bei der
// Rekordzeit für die OnlineUser was geändert, dann findet ihr im Code eine Markierung wo ihr was ändern müßt.
// Einstellungen für die Statistik ENDE

// Liste für Gäste abschalten
if ($darfsehen==1) {
$permissions=getpermissions();
if ($bbuserinfo[usergroupid]==1) {
        show_nopermission();
}
}
// Ende

// Rechte auslesen von den Forumpermissions
$rechte = $DB_site->query("SELECT forumid FROM forumpermission WHERE canview='0' AND usergroupid=$bbuserinfo[usergroupid]");
while($forumnummer=$DB_site -> fetch_array($rechte)) {
$dieseboardsnicht .= "AND forumid!='$forumnummer[forumid]'";
}
// Ende

// Definierte Boards verbergen
if ($bbuserinfo[usergroupid]!=6){ // Wenn Deine AdminID nicht 6 ist bitte austauschen
$dieseboardsnicht .=" $dieseboardsnicht1";
}
// Ende

// Tagesstatistik
$max = $DB_site->query_first("SELECT MAX(memberson)AS memberson, MAX(newthreads)AS newthreads, MAX(newposts)AS newposts, MAX(maxuseron)AS maxuseron, MAX(newpms)AS newpms, MAX(newregs)AS newregs FROM afterburner_stat");
if(!$page) $page = 1;
$res = $DB_site->query_first("SELECT count(time) FROM afterburner_stat");
if(!$res[0]) {
	$nix = 1;
	$pagejump .= "<font color=\"{tableheadtextcolor}\">0</font> ";
}
$statperpage = 15;
$tabcount = ceil($res[0]/$statperpage);

for ($pagenr = 1; $pagenr < ($tabcount+1); $pagenr++){
	if($pagenr == $page) $pagejump .= "<font color=\"{tableheadtextcolor}\">$pagenr</font> ";
	else $pagejump .= "<a href=\"newstatistik.php?action=stat&page=$pagenr&s=$session[sessionhash]\">$pagenr</a> ";
}

$limita = ($page-1)*$statperpage;
$limitb = $limita+$statperpage;
$limit = "$limita, $limitb";
$result = $DB_site->query("SELECT * FROM afterburner_stat ORDER BY time DESC LIMIT $limit");
while ($row = $DB_site->fetch_array($result)) {
	$time = date("d.m.Y",$row[time]);
	$name_tag[0]  =   "<font color=\"red\">So</font>";
	$name_tag[1]  =   "Mo";
	$name_tag[2]  =   "Di";
	$name_tag[3]  =   "Mi";
	$name_tag[4]  =   "Do";
	$name_tag[5]  =   "Fr";
	$name_tag[6]  =   "<font color=\"#EF8F07\">Sa</font>";
	$num_tag  =  date("w",$row[time]);
	$tag  =  $name_tag[$num_tag];
	$time = "$tag, $time";
	$newthreads = $row[newthreads];
	if($newthreads) {
		$newthreads_percent_float = $newthreads*98/$max[newthreads];
		$newthreads_percent_int = floor($newthreads_percent_float);
	}
	else $newthreads_percent_int = 0;

	$newposts = $row[newposts];
	if($newposts) {
		$newposts_percent_float = $newposts*98/$max[newposts];
		$newposts_percent_int = floor($newposts_percent_float);
	}
	else $newposts_percent_int = 0;

	$newpms = $row[newpms];
	if($newpms) {
		$newpms_percent_float = $newpms*98/$max[newpms];
		$newpms_percent_int = floor($newpms_percent_float);
	}
	else $newpms_percent_int = 0;

	$maxuserontime = date( "H:i", $row[maxuserontime]);

	$maxuseron = $row[maxuseron];
	if($maxuseron) {
		$maxuseron_percent_float = $maxuseron*98/$max[maxuseron];
		$maxuseron_percent_int = floor($maxuseron_percent_float);
	}
	else $maxuseron_percent_int = 0;

	$memberson = $row[memberson];
	if($memberson) {
		$memberson_percent_float = $memberson*98/$max[memberson];
		$memberson_percent_int = floor($memberson_percent_float);
	}
	else $memberson_percent_int = 0;

	$newregs = $row[newregs];
	if($newregs) {
		$newregs_percent_float = $newregs*98/$max[newregs];
		$newregs_percent_int = floor($newregs_percent_float);
	}
	else $newregs_percent_int = 0;
eval("\$stat_daybit .= \"".gettemplate('stat_daybit')."\";");

}
if ($nix) $stat_daybit .= "<tr bgcolor=\"{firstaltcolor}\"><td colspan=7 align=center><smallfont>- keine Daten vorhanden -</font></td></tr>";
// Ende

// Heute online
if ($heuteonlinean=="1") {
$dateheute=date("d-m-Y");
$usersheute=$DB_site->query("SELECT username, userid FROM user WHERE FROM_UNIXTIME(lastactivity,'%d-%m-%Y') = '$dateheute' ORDER BY username");
while ($userheute=$DB_site->fetch_array($usersheute)) {
if ($heuteonline) {
   $heuteonline.=", ";
}
$heuteonline.="<a href=\"member.php?s=$session[sessionhash]&action=getinfo&userid=$userheute[userid]\">$userheute[username]</a>";
}
} else {
	$heuteonline = "Diese Anzeige wurde durch den Admin deaktiviert.";
}
// Ende


// Statistik
// Aktive Mitglieder
$aktiveuser = $DB_site->query_first("SELECT COUNT(userid)as anzahl FROM user");
$aktiveuser = $aktiveuser[anzahl];
$aktiveuserbl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Aktive Mitglieder:</font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$aktiveuser</b></font></td></tr>";
// Ende

// Aktive Teilnahme in %
$nullposter=$DB_site->query_first('SELECT COUNT(*) AS users,MAX(userid) AS max FROM user WHERE posts=0'); 
$nullposter=$nullposter['users']; 
$aktivemember=$aktiveuser-$nullposter; 
$aktivuserpro=sprintf("%.2f",(100*$aktivemember/$aktiveuser)); 
$aktiveuserprol = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Aktive Teilnahme in %:</font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$aktivuserpro</b></font></td></tr>";
// Ende

// 0-Poster
$nullposterl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>0-Poster:</font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$nullposter</b></font></td></tr>";
// Ende

// Registrierungen seit Start
$registers = $DB_site->query_first("SELECT userid, username FROM user ORDER BY userid DESC LIMIT 1");
$registers = $registers[userid];
$registersbl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Registrierungen seit Start:</font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$registers</b></font></td></tr>";
// Ende

// Die letzte Registrierung
$newestuser = $DB_site->query_first("SELECT userid,username FROM user ORDER by joindate DESC LIMIT 1");
$newestuserbl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Die letzte Registrierung:</font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b><a href=\"member.php?s=$session[sessionhash]&action=getinfo&userid=$newestuser[userid]\">$newestuser[username]</a></b></font></td></tr>";
// Ende

// Besucherrekord / Rekorddatum / Rekordzeit
$recordusers = $DB_site->query_first("select template from template where title='maxloggedin'");
list ($meisteuser, $umdiesezeit) = split ('[ ]', $recordusers[0]);
$recorddate = vbdate($dateformat,$umdiesezeit);
$recordtime = vbdate($timeformat,$umdiesezeit);
$rekordbl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Besucherrekord: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$meisteuser</b></font></td></tr>";

$rekorddatebl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Rekorddatum: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$recorddate</b></font></td></tr>";

$rekordtimebl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Rekordzeit: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$recordtime Uhr</b></font></td></tr>";
// Ende
// Ende der Tabelle Statistik

// Mitglieder-Statistik
// auslesen aus der DB
$datecut=time()-$cookietimeout;
$loggedins=$DB_site->query_first("SELECT COUNT(*) AS sessions FROM session WHERE userid=0 AND lastactivity>$datecut");
$numberguest=$loggedins['sessions'];
$loggedins=$DB_site->query("SELECT DISTINCT session.userid,username,invisible,usergroupid
                              FROM session
                              LEFT JOIN user ON (user.userid=session.userid)
                              WHERE session.userid>0 AND session.lastactivity>$datecut
                              ORDER BY invisible ASC, username ASC");
while ($loggedin=$DB_site->fetch_array($loggedins)) {
    $numberregistered++;
    if ($loggedin['invisible']==0 or $bbuserinfo['usergroupid']==6) {
      $numbervisible++;
	}
}
$totalonline=$numberregistered+$numberguest;
$numberinvisible=$numberregistered-$numbervisible;
// Ende

// Zur Zeit aktive Benutzer
$useronlinebl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Zur Zeit aktive Benutzer:</font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$totalonline</b></font></td></tr>";
// Ende

// Zur Zeit aktive Mitglieder
$userbl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Zur Zeit aktive Mitglieder: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$numbervisible</b></font></td></tr>";
// Ende

// Zur Zeit aktive Gäste
$guestsbl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Zur Zeit aktive Gäste: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$numberguest</b></font></td></tr>";
// Ende

// Zur Zeit unsichtbare Mitglieder
$ghostbl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Zur Zeit unsichtbare Mitglieder: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$numberinvisible</b></font></td></tr>";
// Ende
// Ende der Tabelle Mitglieder-Statistik

// Crew-Statistik
// Admins
$admins = $DB_site->query_first("SELECT COUNT(*) FROM user WHERE (usergroupid=6)"); // Admins 6
$admins = $admins[0];
$adminsgesamt = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Administratoren: </font></td><td bgcolor=\"{firstaltcolor}\" width=\"30%\" align=\"right\"><smallfont><b>$admins</b></font></td></tr>";
// Ende

// SuperModeratoren
$smod = $DB_site->query_first("SELECT COUNT(*) FROM user WHERE (usergroupid=5)"); // SMod 5
$smod = $smod[0];
$smodgesamt = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Super-Moderatoren: </font></td><td bgcolor=\"{secondaltcolor}\" width=\"30%\" align=\"right\"><smallfont><b>$smod</b></font></td></tr>";
// Ende

// Moderatoren
$mods = $DB_site->query_first("SELECT COUNT(*) FROM user WHERE (usergroupid=7)"); // Mods 7
$mods = $mods[0];
$modsgesamt = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Moderatoren: </font></td><td bgcolor=\"{firstaltcolor}\" width=\"30%\" align=\"right\"><smallfont><b>$mods</b></font></td></tr>";
// Ende
// Ende der Tabelle Crew-Statistik

// Board-Statistik
// Kategorien
$kategorie=$DB_site->query_first("SELECT COUNT(*) AS kategorie, MAX(parentid) AS parentid FROM forum WHERE parentid = -1");
$kategorie = $kategorie[0];
$kategoriebl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Kategorien: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$kategorie</b></font></td></tr>";
// Ende

// Foren
$foren=$DB_site->query_first("SELECT COUNT(*) AS foren, MAX(parentid) AS parentid FROM forum WHERE parentid <> -1");
$foren = $foren[0];
$forenbl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Foren: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$foren</b></font></td></tr>";
// Ende

// Benutzergruppen
$groups=$DB_site->query_first("SELECT COUNT(*) AS groups, MAX(usergroupid) AS usergroupid FROM usergroup");
$groups = $groups[0];
$groupsbl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Benutzergruppen: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$groups</b></font></td></tr>";
// Ende

// Benutzerränge
$rank=$DB_site->query_first("SELECT COUNT(*) AS rank, MAX(usertitleid) AS usertitleid FROM usertitle");
$rank = $rank[0];
$rankbl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Benutzerränge: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$rank</b></font></td></tr>";
// Ende

// Attachments
$attachments=$DB_site->query_first("SELECT COUNT(*) AS attachments, MAX(attachmentid) AS attachmentid FROM attachment");
$attachments = $attachments[0];
$attachmentsbl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Attachments: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$attachments</b></font></td></tr>";
// Ende

// Umfragen
$polls=$DB_site->query_first("SELECT COUNT(*) AS polls, MAX(pollid) AS pollid FROM poll");
$polls = $polls[0];
$pollsbl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Umfragen: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$polls</b></font></td></tr>";
// Ende

// Benutzung der Suche
$suche=$DB_site->query_first("SELECT COUNT(*) AS suche, MAX(searchid) AS searchid FROM search");
$suche=$suche[0];
$suchebl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Benutzung der Suche: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$suche</b></font></td></tr>";
// Ende
// Ende der Tabelle Board-Statistik

// Beitrags-Statistik
// Beiträge aktiv
$anzahlposts = $DB_site->query_first("SELECT COUNT(postid)as anzahl FROM post");
$anzahlposts = $anzahlposts[anzahl];
$anzahlpostsbl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Beiträge aktiv: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$anzahlposts</b></font></td></tr>";
// Ende

// Themen aktiv
$anzahlthreads = $DB_site->query_first("SELECT COUNT(threadid)as anzahl FROM thread");
$anzahlthreads = $anzahlthreads[anzahl];
$anzahlthreadsbl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Themen aktiv: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$anzahlthreads</b></font></td></tr>";
// Ende

// Beiträge insgesamt
$allposts = $DB_site->query_first("SELECT postid FROM post ORDER BY postid DESC LIMIT 1");
$allposts = $allposts[postid];
$allpostsbl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Beiträge insgesamt: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$allposts</b></font></td></tr>";
// Ende

// Themen insgesamt
$allthreads = $DB_site->query_first("SELECT threadid FROM thread ORDER BY threadid DESC LIMIT 1");
$allthreads = $allthreads[threadid];
$allthreadsbl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Themen insgesamt: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$allthreads</b></font></td></tr>";
// Ende

// Wichtige Themen
$getoppt=$DB_site->query_first("SELECT COUNT(*) AS sticky, MAX(threadid) AS threadid FROM thread WHERE sticky <> 0");
$getoppt=$getoppt[0];
$getopptbl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Wichtige Themen: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$getoppt</b></font></td></tr>";
// Ende

// Geschlossene Themen
$close=$DB_site->query_first("SELECT COUNT(*) AS open, MAX(threadid) AS threadid FROM thread WHERE open = 0");
$close = $close[0];
$closebl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Geschlossene Themen: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$close</b></font></td></tr>";
// Ende

// Verschobene Themen
$moved=$DB_site->query_first("SELECT COUNT(*) AS notes, MAX(threadid) AS threadid FROM thread WHERE notes like '%Moved%'");
$moved = $moved[0];
$movedl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Verschobene Themen: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$moved</b></font></td></tr>";
// Ende

// Gesamtviews der Beiträge
$postviews = $DB_site->query_first("SELECT SUM(views) AS threadviews FROM thread"); 
$postviews = $postviews[threadviews]; 
$postviewsl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Gesamtviews der Beiträge: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$postviews</b></font></td></tr>";
// Ende

// Themen ohne Antworten
$anzahlthreads0 = $DB_site->query_first("SELECT COUNT(threadid)as anzahl FROM thread WHERE replycount=0");
$anzahlthreads0 = $anzahlthreads0[anzahl];
$anzahlthreadsb0l = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Themen ohne Antworten: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$anzahlthreads0</b></font></td></tr>";
// Ende
// Ende der Tabelle Beitrags-Statistik

// Private Nachrichten
// Private Nachrichten aktiv
$gesamtpm = $DB_site->query_first("SELECT COUNT(privatemessageid)as anzahl FROM privatemessage");
$gesamtpm = $gesamtpm[anzahl];
$gesamtpml = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Private Nachrichten aktiv: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$gesamtpm</b></font></td></tr>";
// Ende

// Private Nachrichten insgesamt
$allpms = $DB_site->query_first("SELECT privatemessageid FROM privatemessage ORDER BY privatemessageid DESC LIMIT 1");
$allpms = $allpms[privatemessageid];
$allpmsl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Insgesamt verschickt: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$allpms</b></font></td></tr>";
// Ende

// Private Nachrichten ungelesen
$gesamtpmungelesen = $DB_site->query_first("SELECT COUNT(messageread)as anzahl FROM privatemessage WHERE messageread='0'");
$gesamtpmungelesen = $gesamtpmungelesen[anzahl];
$gesamtpmungelesenl = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Ungelesen: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$gesamtpmungelesen</b></font></td></tr>";
// Ende

// Private Nachrichten gelesen
$gesamtpmgelesen = $DB_site->query_first("SELECT COUNT(messageread)as anzahl FROM privatemessage WHERE messageread='1'");
$gesamtpmgelesen = $gesamtpmgelesen[anzahl];
$gesamtpmgelesenl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Nur gelesen: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$gesamtpmgelesen</b></font></td></tr>";
// Ende

// Private Nachrichten gelesen
$gesamtpmgelesen2 = $DB_site->query_first("SELECT COUNT(messageread)as anzahl FROM privatemessage WHERE messageread='2'");
$gesamtpmgelesen2 = $gesamtpmgelesen2[anzahl];
$gesamtpmgelesen2l = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Gelesen und geantwortet: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$gesamtpmgelesen2</b></font></td></tr>";
// Ende
// Ende der Tabelle Private Nachrichten

// Durchschnitts-Statistik
// DB auslesen und berechnen
$anzahluser = $DB_site->query_first("SELECT COUNT(userid) FROM user");
$anzahlthreads = $DB_site->query_first("SELECT COUNT(threadid) FROM thread");
$anzahlposts = $DB_site->query_first("SELECT COUNT(postid) FROM post");
$anzahluser = $anzahluser[0];
$anzahlthreads = $anzahlthreads[0];
$anzahlposts = $anzahlposts[0];
$durch01= $anzahlposts/$anzahluser;
$durch02= sprintf("%.2f",($durch01));
$durch03= $anzahlthreads/$anzahluser;
$durch04= sprintf("%.2f",($durch03));
// Ende

// Beiträge pro User
$postsprouser = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Beiträge pro User:</font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$durch02</b></font></td></tr>";
// Ende

// Themen pro User
$threadsprouser = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Themen pro User: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$durch04</b></font></td></tr>";
// Ende
// Ende der Tabelle Durchschnitts-Statistik

// Grafik-Statistik
// Smilies
$smilies = $DB_site->query_first("SELECT COUNT(*) FROM smilie");
$smilies = $smilies[0];
$smiliesgesamt = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>Smilies: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$smilies</b></font></td></tr>";
// Ende

// Board-Avatare
$avatars = $DB_site->query_first("SELECT COUNT(*) FROM avatar"); // Die Boardavatare
$avatars = $avatars[0];
$avatarsgesamt = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Board-Avatare: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$avatars</b></font></td></tr>";
// Ende

// User-Avatare
$avatars1 = $DB_site->query_first("SELECT COUNT(*) FROM customavatar"); // Die Useravatare
$avatars1 = $avatars1[0];
$avatarsgesamt1 = "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>User-Avatare: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$avatars1</b></font></td></tr>";
// Ende

// Styles
$style=$DB_site->query_first("SELECT COUNT(*) AS style, MAX(styleid) AS styleid FROM style");
$style = $style[0];
$stylebl = "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Styles: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$style</b></font></td></tr>";
// Ende
// Ende der Tabelle Grafik-Statistik

// Datenbank Statistik
// belegter Mysql Speicher
$table_data = 0;
$table_idx = 0;
$db_all = 0;
$result = $DB_site->query("SHOW TABLE STATUS");
while ($row = $DB_site->fetch_array($result)) {
	$table_data += $row['Data_length'];
	$table_idx  += $row['Index_length'];
}
$db_all = $table_data + $table_idx;
$mysqlmem = round($db_all/1048576,2);
$result = $DB_site->query_first("show variables like 'version'");
$mysqlver = $result[1];
// Ende

// MySQL Version
$mysqlverbl .= "<tr bgcolor=\"{firstaltcolor}\"><td width=\"70%\"><smallfont>MySQL Version: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{firstaltcolor}\"><smallfont><b>$mysqlver</b></font></td></tr>";
// Ende

// Grösse der Datenbank
$mysqlmembl .= "<tr bgcolor=\"{secondaltcolor}\"><td width=\"70%\"><smallfont>Grösse der Datenbank: </font></td><td align=\"right\" width=\"30%\" bgcolor=\"{secondaltcolor}\"><smallfont><b>$mysqlmem</b> MB</font></td></tr>";
// Ende
// Ende der Tabelle Datenbank Statistik

// Top10 Referals
if ($top10referalsan=="1") {
$top10referals="<br>
<table cellpadding=4 cellspacing=1 border=0 width=\"100%\" bgcolor=\"{tablebordercolor}\">
	<tr bgcolor=\"{tableheadbgcolor}\" id=\"tabletitle\"><td align=\"left\" colspan=3 ><smallfont color=\"{tableheadtextcolor}\"><b>Top10 Referals</b></font></td>
	</tr>
	<tr><td bgcolor=\"{tableheadbgcolor}\" width=\"10%\"><smallfont color=\"{tableheadtextcolor}\"><b>Rang</b></font></td><td bgcolor=\"{tableheadbgcolor}\" width=\"60%\"><smallfont color=\"{tableheadtextcolor}\"><b>Username</b></font></td><td bgcolor=\"{tableheadbgcolor}\" width=\"30%\"><smallfont color=\"{tableheadtextcolor}\"><b>Referals</b></font></td></tr>
";
$users = $DB_site->query("SELECT COUNT(*) AS count, user.joindate, user.lastvisit, user.posts, user.usertitle, user.username, user.userid FROM user AS users
                   LEFT JOIN user ON (users.referrerid = user.userid)
                   WHERE users.referrerid <> 0
                   GROUP BY users.referrerid
                   ORDER BY count DESC LIMIT 0,10");
$zaehlen=1;				   
if ($DB_site->num_rows($users)==0) {
      $top10referals .="<tr><td bgcolor=\"{firstaltcolor}\" colspan=\"3\" align=\"center\"><smallfont>Es sind keine Referals vorhanden oder das Referal System ist deaktiviert.</font></td></tr>";
  }
while ($user=$DB_site->fetch_array($users)) {
	$userdeleted="";
		if ($user[username]=="") {
			$userdeleted="gelöschter User";  
		}
	$top10referals .="<tr><td bgcolor=\"{firstaltcolor}\" align=\"center\"><smallfont>$zaehlen</font></td><td bgcolor=\"{secondaltcolor}\" align=\"center\"><smallfont><a href=\"member.php?s=$session[sessionhash]&action=getinfo&userid=$user[userid]\">$user[username]</a>$userdeleted</font></td><td bgcolor=\"{firstaltcolor}\" align=\"right\"><smallfont><b>$user[count]</b></font></td></tr>";
	$zaehlen++;
}
$top10referals .= "</table>";
}
// Ende der Tabelle Top10 Referals

// Letze 10 Suchwörter
if ($suchwoerteran=="1") {
$suchwoerter1 ="<br>
<table cellpadding=4 cellspacing=1 border=0 width=\"100%\" bgcolor=\"{tablebordercolor}\">
	<tr bgcolor=\"{tableheadbgcolor}\" id=\"tabletitle\"><td align=\"left\" colspan=\"2\"><smallfont color=\"{tableheadtextcolor}\"><b>Letze 10 Suchwörter</b></font></td>
	</tr><tr><td bgcolor=\"{tableheadbgcolor}\" align=\"center\"><smallfont color=\"{tableheadtextcolor}\"><b>Datum & Zeit</b></font></td><td bgcolor=\"{tableheadbgcolor}\" align=\"center\"><smallfont color=\"{tableheadtextcolor}\"><b>gesucht nach</b></font></td></tr>";
if ($showsearchedby567=="0") {
$suchwoerters = $DB_site->query("SELECT querystring,dateline,searchid FROM search LEFT JOIN user USING (userid) WHERE querystring!='' AND usergroupid NOT IN (5,6,7) ORDER BY searchid desc LIMIT 0,10");
} else {
$suchwoerters = $DB_site->query("SELECT querystring,dateline,searchid FROM search WHERE querystring !='' ORDER BY searchid DESC LIMIT 0,10");
}
while ($suchwoerter=$DB_site->fetch_array($suchwoerters)) {
	 $searchdate = vbdate($dateformat,$suchwoerter[dateline]);
	 $searchtime = vbdate($timeformat,$suchwoerter[dateline]);
$suchwoerter1 .= "<tr><td bgcolor=\"{firstaltcolor}\" align=\"center\"><smallfont>$searchdate<br><font color=\"{timecolor}\">[$searchtime]</font></font></td><td bgcolor=\"{secondaltcolor}\" align=\"center\"><smallfont>$suchwoerter[querystring]<br><a href=\"search.php?s=$session[sessionhash]&action=showresults&searchid=$suchwoerter[searchid]&sortby=lastpost&sortorder=descending\">suchen</a></font></td></tr>";
}
$suchwoerter1 .="</table>";
}
// Ende der Tabelle letzte 10 Suchwörter

// Serversoftware
if ($serverinfoan=="1") {
$serverinfo = getenv("SERVER_SOFTWARE");
} else {
	$serverinfo ="Diese Anzeige wurde vom Admin deaktiviert.";
}
// Ende

// TOP 10 Poster
if ($top10posteran=="1") {
eval("\$otherstats .= \"".gettemplate('stats_head')."\";");
$result = $DB_site -> query("SELECT posts,userid,username FROM user ORDER BY posts DESC LIMIT 0,10");
$i = 1;

while($num=$DB_site -> fetch_array($result)) {
	if($i=="1" && $num['posts'] > 0) {
		$multip = 95 / $num['posts'];
	};
	$grafik = "<img src=\"./images/statistik/vote_left" . $i . ".gif\" width=\"6\" height=\"9\" border=\"0\" alt=\"\"><img src=\"./images/statistik/vote_middle" . $i . ".gif\" width=\"" . round($num['posts']*$multip,0) . "%\" height=\"9\" border=\"0\" alt=\"$i\"><img src=\"./images/statistik/vote_right" . $i . ".gif\" width=\"6\" height=\"9\" alt=\"\" border=\"0\">";
	$otherstats .= "
	<tr>
		<td bgcolor=\"{firstaltcolor}\" width=\"5%\" align=\"center\"><smallfont>".$i."</font></td>
		<td bgcolor=\"{secondaltcolor}\" width=\"15%\" align=center><smallfont><a href=\"member.php?s=$session[sessionhash]&action=getinfo&userid=".$num['userid']."\">".$num['username']."</a></font></td>
		<td bgcolor=\"{firstaltcolor}\"><smallfont>$grafik<br>[".$num['posts']."]</font></td>
	</tr>
	";
	$i++;
};
$otherstats .="</table></td></tr></table></td></tr></table>";
}
// Ende

// TOP 10 Poster der letzten 30 Tage
if ($top10poster30an=="1") {
eval("\$otherstats .= \"".gettemplate('stats_head1')."\";");
$diezeit=time()-(30*86400);
$result = $DB_site -> query("SELECT DISTINCT(userid),COUNT(postid) AS posts FROM post WHERE userid > 0 AND post.dateline>=$diezeit GROUP BY userid ORDER BY posts DESC LIMIT 10");
$i = 1;

while($num=$DB_site -> fetch_array($result)) {
	$result1=$DB_site->query_first("SELECT username FROM user WHERE userid=$num[userid]"); 
	if($i=="1" && $num['posts'] > 0) {
		$multip = 95 / $num['posts'];
	};
	$grafik = "<img src=\"./images/statistik/vote_left" . $i . ".gif\" width=\"6\" height=\"9\" border=\"0\" alt=\"\"><img src=\"./images/statistik/vote_middle" . $i . ".gif\" width=\"" . round($num['posts']*$multip,0) . "%\" height=\"9\" border=\"0\" alt=\"$i\"><img src=\"./images/statistik/vote_right" . $i . ".gif\" width=\"6\" height=\"9\" alt=\"\" border=\"0\">";
	$otherstats .= "
	<tr>
		<td bgcolor=\"{firstaltcolor}\" width=\"5%\" align=\"center\"><smallfont>".$i."</font></td>
		<td bgcolor=\"{secondaltcolor}\" width=\"15%\" align=\"center\"><smallfont><a href=\"member.php?s=$session[sessionhash]&action=getinfo&userid=".$num['userid']."\">".$result1['username']."</a></font></td>
		<td bgcolor=\"{firstaltcolor}\"><smallfont>$grafik<br>[".$num['posts']."]</font></td>
	</tr>";
	$i++;
};
$otherstats .="</table></td></tr></table></td></tr></table>";
}
// Ende


// TOP 10 Poster der letzten 24 Stunden
if ($top10poster24an=="1") {
eval("\$otherstats .= \"".gettemplate('stats_head2')."\";");
$diezeit=time()-86400;
$result = $DB_site -> query("SELECT DISTINCT(userid),COUNT(postid) AS posts FROM post WHERE userid > 0 AND post.dateline>=$diezeit GROUP BY userid ORDER BY posts DESC LIMIT 10");
$i = 1;

while($num=$DB_site -> fetch_array($result)) {
	$result1=$DB_site->query_first("SELECT username FROM user WHERE userid=$num[userid]"); 
	if($i=="1" && $num['posts'] > 0) {
		$multip = 95 / $num['posts'];
	};
	$grafik = "<img src=\"./images/statistik/vote_left" . $i . ".gif\" width=\"6\" height=\"9\" border=\"0\" alt=\"\"><img src=\"./images/statistik/vote_middle" . $i . ".gif\" width=\"" . round($num['posts']*$multip,0) . "%\" height=\"9\" border=\"0\" alt=\"$i\"><img src=\"./images/statistik/vote_right" . $i . ".gif\" width=\"6\" height=\"9\" alt=\"\" border=\"0\">";
	$otherstats .= "
	<tr>
		<td bgcolor=\"{firstaltcolor}\" width=\"5%\" align=center><smallfont>".$i."</font></td>
		<td bgcolor=\"{secondaltcolor}\" width=\"15%\" align=center><smallfont><a href=\"member.php?s=$session[sessionhash]&action=getinfo&userid=".$num['userid']."\">".$result1['username']."</a></font></td>
		<td bgcolor=\"{firstaltcolor}\"><smallfont>$grafik<br>[".$num['posts']."]</font></td>
	</tr>";
	$i++;
};
$otherstats .="</table></td></tr></table></td></tr></table>";
}
// Ende


// TOP 10 Threads meiste Views
if ($top10viewsan=="1") {
$stats_thread = "Besuchte Threads";
$i = 1;
eval("\$otherstats .= \"".gettemplate('stats_table_threads1')."\";");
$result=$DB_site->query("SELECT thread.threadid,thread.title,thread.views FROM thread WHERE open!='0' AND visible='1' $dieseboardsnicht  ORDER BY thread.views DESC LIMIT 0,10");

while(($num=$DB_site -> fetch_array($result)) && ($i <= 10)) {

	if($i=="1" && $num['views'] > 0) {
		$multip = 95 / $num['views'];
	};

	$grafik = "<img src=\"./images/statistik/vote_left" . $i . ".gif\" width=\"6\" height=\"9\" border=\"0\" alt=\"\"><img src=\"./images/statistik/vote_middle" . $i . ".gif\" width=\"" . round($num['views']*$multip,0) . "%\" height=\"9\" border=\"0\" alt=\"$i\"><img src=\"./images/statistik/vote_right" . $i . ".gif\" width=\"6\" height=\"9\" alt=\"\" border=\"0\">";
		$otherstats .= "
		<tr>
			<td bgcolor=\"{firstaltcolor}\" width=\"5%\" align=center><smallfont>".$i."</font></td>
			<td bgcolor=\"{secondaltcolor}\" width=\"30%\"><smallfont><a href=\"showthread.php?threadid=".$num['threadid']."&s=$session[sessionhash]\">".$num['title']."</a></font></td>
			<td bgcolor=\"{firstaltcolor}\"><smallfont>$grafik<br>[".$num['views']."]</font></td>
		</tr>";
		$i++;
};
$otherstats .="</table></td></tr></table></td></tr></table>";
}
// Ende

// TOP 10 Threads meiste Antworten
if ($top10antwortenan=="1") {
$stats_thread = "Beantwortete Threads";
$i = 1;
eval("\$otherstats .= \"".gettemplate('stats_table_threads')."\";");
$result=$DB_site->query("SELECT thread.threadid,thread.title,thread.replycount FROM thread WHERE open!='0' AND visible='1' $dieseboardsnicht  ORDER BY thread.replycount DESC LIMIT 0,10");
while(($num=$DB_site -> fetch_array($result)) && ($i <= 10)) {

	if($i=="1" && $num['replycount'] > 0) {
		$multip = 95 / $num['replycount'];
	};
	$grafik = "<img src=\"./images/statistik/vote_left" . $i . ".gif\" width=\"6\" height=\"9\" border=\"0\" alt=\"\"><img src=\"./images/statistik/vote_middle" . $i . ".gif\" width=\"" . round($num['replycount']*$multip,0) . "%\" height=\"9\" border=\"0\" alt=\"$i\"><img src=\"./images/statistik/vote_right" . $i . ".gif\" width=\"6\" height=\"9\" alt=\"\" border=\"0\">";
	$otherstats .= "
	<tr>
		<td bgcolor=\"{firstaltcolor}\" width=\"5%\" align=center><smallfont>".$i."</font></td>
		<td bgcolor=\"{secondaltcolor}\" width=\"30%\"><smallfont><a href=\"showthread.php?threadid=".$num['threadid']."&s=$session[sessionhash]\">".$num['title']."</a></font></td>
		<td bgcolor=\"{firstaltcolor}\"><smallfont>$grafik<br>[".$num['replycount']."]</font></td>
	</tr>";
	$i++;
}
$otherstats .="</table></td></tr></table></td></tr></table>";
}
// Ende

$hackversion ="1.2.1";
makeforumjump(); 
eval("dooutput(\"".gettemplate("stat")."\");");
?>