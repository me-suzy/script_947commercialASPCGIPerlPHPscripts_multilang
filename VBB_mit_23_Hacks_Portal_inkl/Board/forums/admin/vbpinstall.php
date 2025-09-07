<?php
error_reporting(7);

function gotonext($extra="") {
	global $step,$thisscript;
	$nextstep = $step+1;
	echo "<p>$extra</p>\n";
	echo("<p><a href=\"$thisscript?step=$nextstep\"><b>Klicke hier um Fortzufahren --&gt;</b></a></p>\n");
}

require ("./global.php");

?>
<HTML><HEAD>
<META content="text/html; charset=windows-1252" http-equiv=Content-Type>
<META content="MSHTML 5.00.3018.900" name=GENERATOR></HEAD>
<link rel="stylesheet" href="../cp.css">
<title>vbPortal Installations Script ..::[Captain Kirk] ::..</title>
</HEAD>
<BODY>
<table width="100%" bgcolor="#3F3849" cellpadding="2" cellspacing="0" border="0"><tr><td>
      <table width="100%" bgcolor="#524A5A" cellpadding="3" cellspacing="0" border="0">
        <tr> 
          <td width="17%"><a href="http://www.phpportals.com/" target="_blank"><img src="cp_logo.gif" width="160" height="49" border="0" alt="Click here to visit the support forums."></a></td>
          <td width="60%" align="center"> 
            <p><font size="2" color="#F7DE00"><b>vbPortal 3.0 pr 8.1 -Deutsch by Captain Kirk-</b></font></p>
            <p><font size="1" color="#F7DE00"><b>Die Installation ben&ouml;tigt 
              nur wenige minuten...</b></font></p>
          </td>
          <td width="23%" align="center"><font size="2" color="#F7DE00"><b>..::[Captain Kirk] 
            ::..<br>
<br>
            http://www.Overnet-Community.org</b></font></td>
        </tr>
      </table>
    </td></tr></table>
<div align="center">
  <p><br>
<?php

if (!$step) {
  $step = 1;
}

// ******************* STEP 1 *******************
if ($step==1) {
  ?>
    <br>
 <br> 
    Deutsche Installationsanleitung: <a href="install.htm" target="_blank"><b>Klicke 
    HIER --&gt;</b></a><br>
    <br>
  </p>
  <p align="left">Wenn Du eine frühere Version des vbPortals installiert hast 
    (2.xx) dann deinstalliere diese vorher.<br>
    Natürlich werden dabei keine älteren Links&Downlods deinstalliert.<br>
    Wenn Du eine Version2.xx deinstallieren möchstest, dann <a href=./vbpinstall.php?step=7 target=_self><b>Klicke 
    HIER --&gt;</b></a></p>
  </div>
	  </p>
 <br> 
<p align="center"><i><b>Installation Abbrechen</b> <a href=./vbpinstall.php?step=6 target=_self><b>Klicke 
  HIER --&gt;</B></a></i></b> <br>
  <br>
  ------------------------------Installations Optionen-------------------------------<br>
  <br>
<p><b>Option 1: vbPortal Lite...</b> Diese Option installiert vbPortal ohne die 
  Topics(Themen) funktion.<br>
  Diese Themen funktion kann nat&uuml;rlich auch seperat zu einen sp&auml;teren 
  Zeitpunkt installiert werden.<br>
  Mehr dazu aber in der Option 3.<br>
  vbPortal Lite installation <b>ohne</b> Topics(Themen) funktion <a href=./vbpinstall.php?step=3 target=_self><b>Klicke 
  HIER --&gt;</B></a></b></p>
 
 
<p><b>Option 2: Vollinstallation</b> Diese Option installiert vbPortal uind enth&auml;lt 
  die Topics(Themen) funktion.<br>
  Diese Installation ben&ouml;tigt einige Modifikationen des vBulletin(Board) 
  scripts.<br>
  vbPortal mit der Themen(Topics) funktion installieren <a href=./vbpinstall.php?step=2 target=_self><b>Klicke 
  HIER --&gt;</B></a></b></p>
<p align="left"><b>Option 3: Installiere Topics(Themen) funktion </b> Diese Option 
  installiert die Topics(Themen) funktion.<br>
  Diese Installation ben&ouml;tigt einige Modifikationen des vBulletin(Board) 
  scripts.<br>
  <i>HINWEIS: Die VOLLINSTALLATION (Option 2) enth&auml;lt schon die Topics(Themen) 
  funktion. </i><br>
  Installation der Topics(Themen) funktion <a href=./vbpinstall.php?step=9 target=_self><b>Klicke 
  HIER --&gt;</B></a></b><br>
  <br>
  <br>
<center>  --------------------------------UN-INSTALL Optionen--------------------------------<br></center>
</p>
 
 
<p><b>Option 4: Un-Install Topics(Themen)</b> Dies ist eine deinstallation der 
  Topics(Themen) funktion.<br>
  Deinstallieren der Topics(Themen) funktion <a href=./vbpinstall.php?step=13 target=_self><b>Klicke 
  HIER --&gt;</B></a></b></p>
 
 
<p><b>Option 5: Un-Install vbPortal</b> Diese Option deinstalliert vbPortal von 
  deiner Page.<br>
  Deinstallieren des vbPortals <a href=./vbpinstall.php?step=10 target=_self><b>Klicke 
  HIER --&gt;</B></a></b></p>
 <?php
 }

// ******************* STEP 2 *******************
if ($step==2) {

  echo("<br><b>Modifiziere die 'Beitrags' und 'Such' Tabellen</b><br>");
// begin modification to the "post' table
  $DB_site->query("ALTER TABLE post ADD topic int(3) DEFAULT '0' NOT NULL AFTER editdate");
// end modification to "post"

// begin modification to the "search' table
  $DB_site->query("ALTER TABLE search ADD topic int(3) DEFAULT '0' NOT NULL AFTER ipaddress");
// end modification to "search"


echo ("<b>Hinzufügen der 'vbPortal' Topics(Themen) Tabellen</b><br>");

$DB_site->query("CREATE TABLE nuke_topics (
   topicid int(3) NOT NULL auto_increment,
   topicname varchar(20) NOT NULL,
   topicimage varchar(20) NOT NULL,
   topictext varchar(40) NOT NULL,
   counter int(11) NOT NULL,
   PRIMARY KEY (topicid)
)");

$DB_site->query("INSERT INTO nuke_topics VALUES (1, 'News', 'news.gif', 'News', 0)");
$DB_site->query("INSERT INTO nuke_topics VALUES (2, 'Info', 'info.gif', 'Information', 0)");
$DB_site->query("INSERT INTO nuke_topics VALUES (3, 'Misc', 'misc.gif', 'Miscellanious', 0)");

gotonext();
}
// ******************* STEP 3 *******************
if ($step==3) {
echo ("<b>Hinzufügen der  'vbPortal'  Tabellen</b><br>");

$DB_site->query("ALTER TABLE thread add index (pollid)");

$DB_site->query("CREATE TABLE nuke_advblocks (
   bid int(10) unsigned NOT NULL auto_increment,
   bkey varchar(255) NOT NULL,
   title varchar(255) NOT NULL,
   content text NOT NULL,
   url varchar(255) NOT NULL,
   position char(1) DEFAULT 'l' NOT NULL,
   weight decimal(10,1) DEFAULT '0.0' NOT NULL,
   active tinyint(3) unsigned DEFAULT '1' NOT NULL,
   refresh int(10) unsigned NOT NULL,
   last_update timestamp(14) NOT NULL,
   templates int(1) DEFAULT '1' NOT NULL,
   PRIMARY KEY (bid)
)");

$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','main','Hauptmenü','<strong><big>·</big></strong> <a href=\"/index.php\">Home</a><br>
<strong><big>·</big></strong> <a href=\"/forums/index.php\">Forum</a><br>
<strong><big>·</big></strong> <a href=\"/topics.php\">Themen</a><br>
<strong><big>·</big></strong> <a href=\"/friend.php\">Empfehle uns</a><br>
<strong><big>·</big></strong> <a href=\"/reviews.php\">Berichte</a><br>
<strong><big>·</big></strong> <a href=\"/links.php\">Links</a><br>
<strong><big>·</big></strong> <a href=\"/stats.php\">Statistiken</a><br>
<strong><big>·</big></strong> <a href=\"/top.php\">Top 10</a><br>
<strong><big>·</big></strong> <a href=\"/download.php\">Downloads</a><br>','','l','0.5','1','0','20010712164939','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','online','Wer ist Online','','','l','8.0','1','0','20010701164256','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','search','Such Box','','','r','1.0','1','0','20010701163405','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','ephem','Tagesmotto','','','l','10.0','1','0','20010712220956','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','category','News','','','r','2.0','1','0','00000000000000','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','calendar','Kalender','','','l','4.0','1','0','00000000000000','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','poll','Umfragen','','','r','4.0','1','0','00000000000000','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','login','Benutzer Login','','','r','2.0','1','0','20010701163405','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','past','Vorige Artikel','','','r','6.0','1','0','20010701115244','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','php','Beispiels PHP Block','print strftime(\'%A, %B %e, %Y %I:%M %p %Z\');','','r','8.0','1','0','00000000000000','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','showbirthdays','Heutige Geburtstage','','','l','7.0','1','0','20010701164256','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','activetopics','Aktive Themen','','','r','7.0','1','0','20010710070842','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','pminfo','PM\'s','','','l','9.0','1','0','20010701164240','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','showevents','Heutige Ereignisse','','','l','5.0','1','0','20010701163345','1')");
$DB_site->query("INSERT INTO nuke_advblocks VALUES('NULL','admin','Administration','<!-- This box will appear only if your
logged in as Admin. Use the Full URL for entering hyperlinks -->
<strong><big>·</big></strong> <a href=\"/admin.php\">Administration</a><br>
<strong><big>·</big></strong> <a href=\"/admin.php?op=logout\">Abmelden</a><br>
<strong><big>·</big></strong> <a href=\"/forums/admin/index.php\">VB Admin</a><br>
','','l','2.0','1','0','00000000000000','1')");

$DB_site->query("CREATE TABLE nuke_centerblocks (
   bid int(10) NOT NULL auto_increment,
   bkey varchar(15) NOT NULL,
   title varchar(60) NOT NULL,
   content text NOT NULL,
   url varchar(200) NOT NULL,
   position char(1) NOT NULL,
   weight int(10) DEFAULT '1' NOT NULL,
   active int(1) DEFAULT '1' NOT NULL,
   refresh int(10) NOT NULL,
   last_update timestamp(14) NOT NULL,
   templates int(1) DEFAULT '1' NOT NULL,
   PRIMARY KEY (bid)
)");

$DB_site->query("INSERT INTO nuke_centerblocks VALUES('NULL','news','News','','','l','3','1','0','20010712231207','1')");
$DB_site->query("INSERT INTO nuke_centerblocks VALUES('NULL','downloads','Downloads','','','l','4','0','0','20010712231202','1')");
$DB_site->query("INSERT INTO nuke_centerblocks VALUES('NULL','links','Links','','','l','6','0','0','20010708225027','1')");
$DB_site->query("INSERT INTO nuke_centerblocks VALUES('NULL','messagebox','PM\'s','','','l','1','1','0','20010712231218','1')");
$DB_site->query("INSERT INTO nuke_centerblocks VALUES('NULL','activetopics','Aktive Themen','','','l','5','1','0','20010712231202','1')");


$DB_site->query("CREATE TABLE nuke_forumblocks (
   bid int(10) unsigned NOT NULL auto_increment,
   bkey varchar(255) NOT NULL,
   title varchar(255) NOT NULL,
   content text NOT NULL,
   url varchar(255) NOT NULL,
   position char(1) DEFAULT 'l' NOT NULL,
   weight decimal(10,1) DEFAULT '0.0' NOT NULL,
   active tinyint(3) unsigned DEFAULT '1' NOT NULL,
   refresh int(10) unsigned NOT NULL,
   last_update timestamp(14) NOT NULL,
   templates int(1) DEFAULT '1' NOT NULL,
   PRIMARY KEY (bid)
)");

$DB_site->query("INSERT INTO nuke_forumblocks VALUES('NULL','forum','Foren Menü','<strong><big>·</big></strong> <a href=\"../index.php\">Home</a><br>
<strong><big>·</big></strong> <a href=\"../topics.php\">Themen</a><br>
<strong><big>·</big></strong> <a href=\"../sections.php\">Bereiche</a><br>
<strong><big>·</big></strong> <a href=\"../reviews.php\">Berichte</a><br>
<strong><big>·</big></strong> <a href=\"../stats.php\">Statistiken</a><br>
<strong><big>·</big></strong> <a href=\"../top.php\">Top 10</a><br>
<strong><big>·</big></strong> <a href=\"../links.php\">Links</a><br>
<strong><big>·</big></strong> <a href=\"../download.php\">Downloads</a><br>','','l','0.5','1','0','00000000000000','1')");
$DB_site->query("INSERT INTO nuke_forumblocks VALUES('NULL','search','Such Box','','','l','1.0','0','0','20010713084100','1')");
$DB_site->query("INSERT INTO nuke_forumblocks VALUES('NULL','ephem','Tagesmotto','','','l','10.0','0','0','20010713084132','1')");
$DB_site->query("INSERT INTO nuke_forumblocks VALUES('NULL','calendar','Kalender','','','l','4.0','1','0','20010713084303','1')");
$DB_site->query("INSERT INTO nuke_forumblocks VALUES('NULL','poll','Umfragen','','','l','4.0','0','0','20010713084110','1')");
$DB_site->query("INSERT INTO nuke_forumblocks VALUES('NULL','showbirthdays','Heutige Geburtstage','','','l','7.0','0','0','20010713084121','1')");
$DB_site->query("INSERT INTO nuke_forumblocks VALUES('NULL','activetopics','Aktive Themen','','','l','7.0','0','0','20010713084124','1')");
$DB_site->query("INSERT INTO nuke_forumblocks VALUES('NULL','showevents','Heutige Ereignisse','','','l','5.0','0','0','20010713084115','1')");
$DB_site->query("INSERT INTO nuke_forumblocks VALUES('NULL','admin','Administration','<!-- This box will appear only if your
logged in as Admin. Use the Full URL for entering hyperlinks -->
<strong><big>·</big></strong> <a href=\"../admin.php\">Administration</a><br>
<strong><big>·</big></strong> <a href=\"../admin.php?op=logout\">Abmelden</a><br>
<strong><big>·</big></strong> <a href=\"admin/index.php\">VB Admin</a><br>
','','l','2.0','1','0','00000000000000','1')");

$DB_site->query("CREATE TABLE nuke_advheadlines (
   id int(10) unsigned NOT NULL auto_increment,
   sitename varchar(255) NOT NULL,
   rssurl varchar(255) NOT NULL,
   siteurl varchar(255) NOT NULL,
   PRIMARY KEY (id)
)");

$DB_site->query("INSERT INTO nuke_advheadlines VALUES('1','AbsoluteGames','http://files.gameaholic.com/agfa.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('2','AppWatch','http://static.appwatch.com/appwatch.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('3','BrunchingShuttlecocks','http://www.brunching.com/brunching.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('4','BSDToday','http://www.bsdtoday.com/backend/bt.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('5','DailyDaemonNews','http://daily.daemonnews.org/ddn.rdf.php3','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('6','DigitalTheatre','http://www.dtheatre.com/backend.php3?xml=yes','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('7','DotKDE','http://dot.kde.org/rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('8','DrDobbsTechNetCast','http://www.technetcast.com/tnc_headlines.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('9','exoScience','http://www.exosci.com/exosci.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('10','FreakTech','http://sunsite.auc.dk/FreakTech/FreakTech.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('11','Freshmeat','http://freshmeat.net/backend/fm.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('12','GeekNik','http://www.geeknik.net/backend/weblog.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('13','Gnotices','http://news.gnome.org/gnome-news/rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('14','HappyPenguin','http://happypenguin.org/html/news.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('15','HollywoodBitchslap','http://hollywoodbitchslap.com/hbs.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('16','HotWired','http://www.hotwired.com/webmonkey/meta/headlines.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('17','JustLinux','http://www.justlinux.com/backend/features.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('18','KDE','http://www.kde.org/news/kdenews.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('19','LAN-Systems','http://www.lansystems.com/backend/gazette_news_backend.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('20','Lin-x-pert','http://www.lin-x-pert.com/linxpert_apps.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('21','Linux.com','http://linux.com/mrn/front_page.rss','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('22','Linux.nu','http://www.linux.nu/backend/lnu.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('23','LinuxCentral','http://linuxcentral.com/backend/lcnew.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('24','Linuxdev.net','http://linuxdev.net/archive/news.cdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('25','LinuxM68k','http://www.linux-m68k.org/linux-m68k.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('26','LinuxNewbie','http://www.linuxnewbie.org/news.cdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('27','Linuxpower','http://linuxpower.org/linuxpower.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('28','LinuxPreview','http://linuxpreview.org/backend.php3','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('29','LinuxWeelyNews','http://lwn.net/headlines/rss','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('30','Listology','http://listology.com/recent.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('31','MaximumBSD1','http://www.maximumbsd.com/backend/weblog.rdf1','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('32','MicroUnices','http://mu.current.nu/mu.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('33','MozillaNewsBot','http://www.mozilla.org/newsbot/newsbot.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('34','NewsForge','http://www.newsforge.com/newsforge.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('35','NewsTrolls','http://newstrolls.com/newstrolls.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('36','PBSOnline','http://cgi.pbs.org/cgi-registry/featuresrdf.pl','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('37','PDABuzz','http://www.pdabuzz.com/netscape.txt','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('38','Perl.com','http://www.perl.com/pace/perlnews.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('39','PerlMonks','http://www.perlmonks.org/headlines.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('40','PerlNews','http://news.perl.org/perl-news-short.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('41','PHP-Nuke','http://phpnuke.org/backend.php','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('42','PHPBuilder','http://phpbuilder.com/rss_feed.php','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('43','Protest.net','http://www.protest.net/netcenter_rdf.cgi','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('44','RivaExtreme','http://rivaextreme.com/ssi/rivaextreme.rdf.cdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('45','SciFi-News','http://www.technopagan.org/sf-news/rdf.php','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('46','Segfault','http://segfault.org/stories.xml','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('47','SisterMachineGun','http://www.smg.org/index/mynetscape.html','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('48','Slashdot','http://slashdot.org/slashdot.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('49','SolarisCentral','http://www.SolarisCentral.org/news/SolarisCentral.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('50','Technocrat','http://technocrat.net/rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('51','Themes.org','http://www.themes.org/news.rdf.phtml','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('52','TheNextLevel','http://www.the-nextlevel.com/rdf/tnl.rdf','')");
$DB_site->query("INSERT INTO nuke_advheadlines VALUES('53','DrDobbs','http://www.technetcast.com/tnc_headlines.rdf','')");


$DB_site->query("CREATE TABLE nuke_banner (
   bid int(11) NOT NULL auto_increment,
   cid int(11) NOT NULL,
   imptotal int(11) NOT NULL,
   impmade int(11) NOT NULL,
   clicks int(11) NOT NULL,
   imageurl varchar(100) NOT NULL,
   clickurl varchar(200) NOT NULL,
   date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   PRIMARY KEY (bid)
)");

$DB_site->query("INSERT INTO nuke_banner VALUES('2','1','0','11756','23','http://www.qksrv.net/image-694929-804494','http://www.qksrv.net/click-694929-804494','2001-06-29 17:01:12')");
$DB_site->query("INSERT INTO nuke_banner VALUES('3','2','0','141','2','http://www.qksrv.net/image-801566-5042834','http://www.qksrv.net/click-801566-5042834','2001-07-13 21:23:06')");


$DB_site->query("CREATE TABLE nuke_bannerclient (
   cid int(11) NOT NULL auto_increment,
   name varchar(60) NOT NULL,
   contact varchar(60) NOT NULL,
   email varchar(60) NOT NULL,
   login varchar(10) NOT NULL,
   passwd varchar(10) NOT NULL,
   extrainfo text NOT NULL,
   PRIMARY KEY (cid)
)");


$DB_site->query("CREATE TABLE nuke_bannerfinish (
   bid int(11) NOT NULL auto_increment,
   cid int(11) NOT NULL,
   impressions int(11) NOT NULL,
   clicks int(11) NOT NULL,
   datestart datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   dateend datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   PRIMARY KEY (bid)
)");


$DB_site->query("CREATE TABLE nuke_counter (
   type varchar(80) NOT NULL,
   var varchar(80) NOT NULL,
   count int(10) unsigned NOT NULL
)");

$DB_site->query("INSERT INTO nuke_counter VALUES('total','hits','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('browser','WebTV','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('browser','Lynx','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('browser','MSIE','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('browser','Opera','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('browser','Konqueror','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('browser','Netscape','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('browser','Bot','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('browser','Other','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','Windows','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','Linux','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','Mac','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','FreeBSD','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','SunOS','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','IRIX','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','BeOS','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','OS/2','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','AIX','0')");
$DB_site->query("INSERT INTO nuke_counter VALUES('os','Other','0')");


$DB_site->query("CREATE TABLE nuke_downloads_categories (
   cid int(11) NOT NULL auto_increment,
   title varchar(50) NOT NULL,
   cdescription text NOT NULL,
   PRIMARY KEY (cid)
)");


$DB_site->query("CREATE TABLE nuke_downloads_downloads (
   lid int(11) NOT NULL auto_increment,
   cid int(11) NOT NULL,
   sid int(11) NOT NULL,
   title varchar(100) NOT NULL,
   url varchar(100) NOT NULL,
   description text NOT NULL,
   date datetime NOT NULL,
   name varchar(100) NOT NULL,
   email varchar(100) NOT NULL,
   hits int(11) NOT NULL,
   submitter varchar(60) NOT NULL,
   downloadratingsummary double(6,4) DEFAULT '0.0000' NOT NULL,
   totalvotes int(11) NOT NULL,
   totalcomments int(11) NOT NULL,
   filesize int(11) NOT NULL,
   version varchar(10) NOT NULL,
   homepage varchar(200) NOT NULL,
   PRIMARY KEY (lid)
)");


$DB_site->query("CREATE TABLE nuke_downloads_editorials (
   downloadid int(11) NOT NULL,
   adminid varchar(60) NOT NULL,
   editorialtimestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   editorialtext text NOT NULL,
   editorialtitle varchar(100) NOT NULL,
   PRIMARY KEY (downloadid)
)");


$DB_site->query("CREATE TABLE nuke_downloads_modrequest (
   requestid int(11) NOT NULL auto_increment,
   lid int(11) NOT NULL,
   cid int(11) NOT NULL,
   sid int(11) NOT NULL,
   title varchar(100) NOT NULL,
   url varchar(100) NOT NULL,
   description text NOT NULL,
   modifysubmitter varchar(60) NOT NULL,
   brokendownload int(3) NOT NULL,
   name varchar(100) NOT NULL,
   email varchar(100) NOT NULL,
   filesize int(11) NOT NULL,
   version varchar(10) NOT NULL,
   homepage varchar(200) NOT NULL,
   PRIMARY KEY (requestid),
   KEY requestid (requestid)
)");


$DB_site->query("CREATE TABLE nuke_downloads_newdownload (
   lid int(11) NOT NULL auto_increment,
   cid int(11) NOT NULL,
   sid int(11) NOT NULL,
   title varchar(100) NOT NULL,
   url varchar(100) NOT NULL,
   description text NOT NULL,
   name varchar(100) NOT NULL,
   email varchar(100) NOT NULL,
   submitter varchar(60) NOT NULL,
   filesize int(11) NOT NULL,
   version varchar(10) NOT NULL,
   homepage varchar(200) NOT NULL,
   PRIMARY KEY (lid)
)");


$DB_site->query("CREATE TABLE nuke_downloads_subcategories (
   sid int(11) NOT NULL auto_increment,
   cid int(11) NOT NULL,
   title varchar(50) NOT NULL,
   PRIMARY KEY (sid)
)");


$DB_site->query("CREATE TABLE nuke_downloads_votedata (
   ratingdbid int(11) NOT NULL auto_increment,
   ratinglid int(11) NOT NULL,
   ratinguser varchar(60) NOT NULL,
   rating int(11) NOT NULL,
   ratinghostname varchar(60) NOT NULL,
   ratingcomments text NOT NULL,
   ratingtimestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   PRIMARY KEY (ratingdbid)
)");


$DB_site->query("CREATE TABLE nuke_ephem (
   eid int(11) NOT NULL auto_increment,
   did int(2) NOT NULL,
   mid int(2) NOT NULL,
   yid int(4) NOT NULL,
   content text NOT NULL,
   PRIMARY KEY (eid)
)");

$DB_site->query("INSERT INTO nuke_ephem VALUES('4','12','6','2001','Trauertag gewittmed den Opfern von Amerika')");


$DB_site->query("CREATE TABLE nuke_faqAnswer (
   id tinyint(4) NOT NULL auto_increment,
   id_cat tinyint(4) NOT NULL,
   question varchar(255) NOT NULL,
   answer text NOT NULL,
   PRIMARY KEY (id)
)");

$DB_site->query("INSERT INTO nuke_faqAnswer VALUES('1','1','Kannst Du das sehen','Ich hoffe es doch!')");


$DB_site->query("CREATE TABLE nuke_faqCategories (
   id_cat tinyint(3) NOT NULL auto_increment,
   categories varchar(255) NOT NULL,
   PRIMARY KEY (id_cat)
)");

$DB_site->query("INSERT INTO nuke_faqCategories VALUES('1','Test')");


$DB_site->query("CREATE TABLE nuke_links_categories (
   cid int(11) NOT NULL auto_increment,
   title varchar(50) NOT NULL,
   cdescription text NOT NULL,
   PRIMARY KEY (cid)
)");


$DB_site->query("CREATE TABLE nuke_links_editorials (
   linkid int(11) NOT NULL,
   adminid varchar(60) NOT NULL,
   editorialtimestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   editorialtext text NOT NULL,
   editorialtitle varchar(100) NOT NULL,
   PRIMARY KEY (linkid)
)");

$DB_site->query("CREATE TABLE nuke_links_links (
   lid int(11) NOT NULL auto_increment,
   cid int(11) NOT NULL,
   sid int(11) NOT NULL,
   title varchar(100) NOT NULL,
   url varchar(100) NOT NULL,
   description text NOT NULL,
   date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   name varchar(60) NOT NULL,
   email varchar(60) NOT NULL,
   hits int(11) NOT NULL,
   submitter varchar(60) NOT NULL,
   linkratingsummary double(6,4) DEFAULT '0.0000' NOT NULL,
   totalvotes int(11) NOT NULL,
   totalcomments int(11) NOT NULL,
   PRIMARY KEY (lid)
)");


$DB_site->query("CREATE TABLE nuke_links_modrequest (
   requestid int(11) NOT NULL auto_increment,
   lid int(11) NOT NULL,
   cid int(11) NOT NULL,
   sid int(11) NOT NULL,
   title varchar(100) NOT NULL,
   url varchar(100) NOT NULL,
   description text NOT NULL,
   modifysubmitter varchar(60) NOT NULL,
   brokenlink int(3) NOT NULL,
   PRIMARY KEY (requestid),
   KEY requestid (requestid)
)");


$DB_site->query("CREATE TABLE nuke_links_newlink (
   lid int(11) NOT NULL auto_increment,
   cid int(11) NOT NULL,
   sid int(11) NOT NULL,
   title varchar(100) NOT NULL,
   url varchar(100) NOT NULL,
   description text NOT NULL,
   name varchar(60) NOT NULL,
   email varchar(60) NOT NULL,
   submitter varchar(60) NOT NULL,
   PRIMARY KEY (lid)
)");


$DB_site->query("CREATE TABLE nuke_links_subcategories (
   sid int(11) NOT NULL auto_increment,
   cid int(11) NOT NULL,
   title varchar(50) NOT NULL,
   PRIMARY KEY (sid)
)");


$DB_site->query("CREATE TABLE nuke_links_votedata (
   ratingdbid int(11) NOT NULL auto_increment,
   ratinglid int(11) NOT NULL,
   ratinguser varchar(60) NOT NULL,
   rating int(11) NOT NULL,
   ratinghostname varchar(60) NOT NULL,
   ratingcomments text NOT NULL,
   ratingtimestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   PRIMARY KEY (ratingdbid)
)");


$DB_site->query("CREATE TABLE nuke_message (
   title varchar(100) NOT NULL,
   content text NOT NULL,
   date varchar(14) NOT NULL,
   expire int(7) NOT NULL,
   active int(1) DEFAULT '1' NOT NULL,
   view int(1) DEFAULT '1' NOT NULL
)");

$DB_site->query("INSERT INTO nuke_message VALUES('Willkommen zu vbPortal 3.0 pr 8.1 Deutsch by Captain Kirk','<font size = \"2\"><b>Diese Version wurde Übersetzt von <a href=\"http://www.Overnet-Community.org\">Captain Kirk</a></b></font>','993177178','2592000','1','1')");


$DB_site->query("CREATE TABLE nuke_referer (
   rid int(11) NOT NULL auto_increment,
   url varchar(100) NOT NULL,
   PRIMARY KEY (rid)
)");


$DB_site->query("CREATE TABLE nuke_reviews (
   id int(10) NOT NULL auto_increment,
   date date DEFAULT '0000-00-00' NOT NULL,
   title varchar(150) NOT NULL,
   text text NOT NULL,
   reviewer varchar(20) NOT NULL,
   email varchar(60) NOT NULL,
   score int(10) NOT NULL,
   cover varchar(100) NOT NULL,
   url varchar(100) NOT NULL,
   url_title varchar(50) NOT NULL,
   hits int(10) NOT NULL,
   PRIMARY KEY (id)
)");


$DB_site->query("CREATE TABLE nuke_reviews_add (
   id int(10) NOT NULL auto_increment,
   date date DEFAULT '0000-00-00' NOT NULL,
   title varchar(150) NOT NULL,
   text text NOT NULL,
   reviewer varchar(20) NOT NULL,
   email varchar(60) NOT NULL,
   score int(10) NOT NULL,
   url varchar(100) NOT NULL,
   url_title varchar(50) NOT NULL,
   PRIMARY KEY (id)
)");


$DB_site->query("CREATE TABLE nuke_reviews_comments (
   cid int(10) NOT NULL auto_increment,
   rid int(10) NOT NULL,
   userid varchar(25) NOT NULL,
   date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   comments text NOT NULL,
   score int(10) NOT NULL,
   PRIMARY KEY (cid)
)");


$DB_site->query("CREATE TABLE nuke_reviews_main (
   title varchar(100) NOT NULL,
   description text NOT NULL
)");

$DB_site->query("INSERT INTO nuke_reviews_main VALUES('Bericht-Abschnitt-Titel','Berichte Unterteilen bei langer Beschreibung')");

$DB_site->query("CREATE TABLE nuke_seccont (
  artid int(11) NOT NULL auto_increment,
  secid int(11) NOT NULL default '0',
  title text NOT NULL,
  content text NOT NULL,
  counter int(11) NOT NULL default '0',
  PRIMARY KEY  (artid)
)");



$DB_site->query("CREATE TABLE nuke_sections (
  secid int(11) NOT NULL auto_increment,
  secname varchar(40) NOT NULL default '',
  image varchar(50) NOT NULL default '',
  PRIMARY KEY  (secid)
)");


  gotonext();
}


if ($step==4) {
  ?>
<p>Die Installation der vbPortal Tabellen ist abgeschlossen.</p>
<p>Nun modifiziere das Script und das Style <a href=./vbpinstall.php?step=5 target=_self><b>Klicke 
  HIER --&gt;</B></a></b> <br>
 <?php
}

if ($step==5) {
  echo("<br>Klicke<a href=vbpscripts.html target=_blank><b> HIER</b></a> für die Modifikationen des VBulletin(Board).<br>");
  echo("<br>HINWEIS: Vergesse nicht den vbPortal Style mit dem Default zu überschreiben! Zum vBulletin CP <b><a href=index.php target=_blank>HIER</a></b>");
}
if ($step==6) {
 ?>
<p>Vielen Dank!</p>
 <br>
 <?php
}

if ($step==7) {
  ?>
<p>Dieser Script deinstalliert die vbPortal 2.xx Datenbank.<br>
 <br> 
  <b>Vorherige Versionen von den Downloads und WebLinks werden natürlich nicht 
  deinstalliert.</b><br>
<p><b>Dies ist allerdings ohne Garantie und Support.</b></p>
<br> 
 
<p>ABBRECHEN <a href=./vbpinstall.php?step=6 target=_self><b>Klicke HIER --&gt;</B></a></b> 
  <br>
  <br>
 
<p>Mit der Deinstallation fortfahren <a href=./vbpinstall.php?step=8 target=_self><b>Klicke 
  HIER --&gt;</B></a></b></p>
 <br><br>

 <?php

}

if ($step==8) {

  $DB_site->query("DROP TABLE addon");
  $DB_site->query("ALTER TABLE post DROP topic");
  $DB_site->query("ALTER TABLE user DROP showleftcolumn");
  $DB_site->query("DROP TABLE P_bblock");
  $DB_site->query("DROP TABLE P_lblocks");
  $DB_site->query("DROP TABLE P_mblock");
  $DB_site->query("DROP TABLE P_rblocks");
  $DB_site->query("DROP TABLE menu");
  $DB_site->query("DROP TABLE P_headlines");
  $DB_site->query("DROP TABLE topics");
  
?>
<br>
<b>Die deinstallation des vbPortals 2.xx ist abgeschlossen.</b><br>
 <br>
Zur&uuml;ck zur Startseite <a href=./vbpinstall.php?step=1 target=_self><b>Klicke 
HIER --&gt;</B></a> <br>
<?
}

// ******************* STEP 9 *******************
if ($step==9) {

// begin modification to the "post' table
  $DB_site->query("ALTER TABLE post ADD topic int(3) DEFAULT '0' NOT NULL AFTER editdate");
// end modification to "post"

// begin modification to the "search' table
  $DB_site->query("ALTER TABLE search ADD topic int(3) DEFAULT '0' NOT NULL AFTER ipaddress");
// end modification to "search"



$DB_site->query("CREATE TABLE nuke_topics (
   topicid int(3) NOT NULL auto_increment,
   topicname varchar(20) NOT NULL,
   topicimage varchar(20) NOT NULL,
   topictext varchar(40) NOT NULL,
   counter int(11) NOT NULL,
   PRIMARY KEY (topicid)
)");

$DB_site->query("INSERT INTO nuke_topics VALUES (1, 'News', 'news.gif', 'News', 0)");
$DB_site->query("INSERT INTO nuke_topics VALUES (2, 'Info', 'info.gif', 'Information', 0)");
$DB_site->query("INSERT INTO nuke_topics VALUES (3, 'Misc', 'misc.gif', 'Miscellanious', 0)");
?>
<br>
<p><b>Fettisch mit den änderungen der Themen(Topics) Funktion.</b><br>

<p>BEENDEN <a href=./vbpinstall.php?step=6 target=_self><b>Klicke HIER--&gt;</B></a></b> 
<br> 
  <br>
<p>oder zur&uuml;ck zur Startseite <a href=./vbpinstall.php?step=1 target=_self><b>Klicke 
  HIER --&gt;</B></a></b></p>
<?php
}


// ******************* STEP 10 *******************
if ($step==10) {
  ?>
<p>Diese Option deinstalliert vbPortal 3.0</p>
  <br> 
 
<p>ABBRECHEN <a href=./vbpinstall.php?step=6 target=_self><b>Klicke HIER --&gt;</B></a></b> 
 <br>
<p>Mit der Deinstallation fortfahren <a href=./vbpinstall.php?step=11 target=_self><b>Klicke 
  HIER --&gt;</B></a></b> <br>
 <?php
 }



// ******************* STEP 11 *******************
if ($step==11) {
    echo("<br><b>Modifizieren der Tabellen</b><br>");
  // modification to the "post' table
   $DB_site->query("ALTER TABLE post DROP topic");
  // modification to the "search' table
   $DB_site->query("ALTER TABLE search DROP topic");
  
// Dropping the 34 Nuke tables
$DB_site->query("DROP TABLE IF EXISTS nuke_advblocks");
$DB_site->query("DROP TABLE IF EXISTS nuke_centerblocks");
$DB_site->query("DROP TABLE IF EXISTS nuke_forumblocks");
$DB_site->query("DROP TABLE IF EXISTS nuke_advheadlines");
$DB_site->query("DROP TABLE IF EXISTS nuke_banner");
$DB_site->query("DROP TABLE IF EXISTS nuke_bannerclient");
$DB_site->query("DROP TABLE IF EXISTS nuke_bannerfinish");
$DB_site->query("DROP TABLE IF EXISTS nuke_counter");
$DB_site->query("DROP TABLE IF EXISTS nuke_downloads_categories");
$DB_site->query("DROP TABLE IF EXISTS nuke_downloads_downloads");
$DB_site->query("DROP TABLE IF EXISTS nuke_downloads_editorials");
$DB_site->query("DROP TABLE IF EXISTS nuke_downloads_modrequest");
$DB_site->query("DROP TABLE IF EXISTS nuke_downloads_newdownload");
$DB_site->query("DROP TABLE IF EXISTS nuke_downloads_subcategories");
$DB_site->query("DROP TABLE IF EXISTS nuke_downloads_votedata");
$DB_site->query("DROP TABLE IF EXISTS nuke_ephem");
$DB_site->query("DROP TABLE IF EXISTS nuke_faqAnswer");
$DB_site->query("DROP TABLE IF EXISTS nuke_faqCategories");
$DB_site->query("DROP TABLE IF EXISTS nuke_links_categories");
$DB_site->query("DROP TABLE IF EXISTS nuke_links_editorials");
$DB_site->query("DROP TABLE IF EXISTS nuke_links_links");
$DB_site->query("DROP TABLE IF EXISTS nuke_links_modrequest");
$DB_site->query("DROP TABLE IF EXISTS nuke_links_newlink");
$DB_site->query("DROP TABLE IF EXISTS nuke_links_subcategories");
$DB_site->query("DROP TABLE IF EXISTS nuke_links_votedata");
$DB_site->query("DROP TABLE IF EXISTS nuke_message");
$DB_site->query("DROP TABLE IF EXISTS nuke_referer");
$DB_site->query("DROP TABLE IF EXISTS nuke_reviews");
$DB_site->query("DROP TABLE IF EXISTS nuke_reviews_add");
$DB_site->query("DROP TABLE IF EXISTS nuke_reviews_comments");
$DB_site->query("DROP TABLE IF EXISTS nuke_reviews_main");
$DB_site->query("DROP TABLE IF EXISTS nuke_seccont");
$DB_site->query("DROP TABLE IF EXISTS nuke_sections");
$DB_site->query("DROP TABLE IF EXISTS nuke_topics");

gotonext();
}


if ($step==12) {
  
 echo "<br>Die deinstallation des vbPortals ist abgeschlossen.<br>";
 echo "Vergesse nicht den Style und die vBulletin(Board) Dateien wieder zu ändern.<br>";
 echo "Zum vBulletin Kontrollzentrum <b><a href=index.php target=_self>HIER</a></b><br>";

}

// ******************* STEP 13 *******************
if ($step==13) {
    echo("<br><b>Modifiziere die Tabellen</b><br>");
  // modification to the "post' table
   $DB_site->query("ALTER TABLE post DROP topic");
  // modification to the "search' table
   $DB_site->query("ALTER TABLE search DROP topic");
  
// Dropping the topics table
$DB_site->query("DROP TABLE IF EXISTS nuke_topics");

gotonext();
}


if ($step==14) {
  
 echo "<br>Die deinstallation der Topics(Themen) funktion ist abgeschlossen.<br>";
 echo "Vergesse nicht die modifikationen in den Dateien newthread.php, editpost.php und search.php wiederherzustellen.<br>";
 echo "BEENDEN <a href=./vbpinstall.php?step=6 target=_self><b>Klicke HIER --&gt;</B></a></b>";
 echo "<br><br>";
 echo "Zurück zur Startseite <a href=./vbpinstall.php?step=1 target=_self><b>Klicke HIER --&gt;</B></a></b>";

}

?>
</body>
</html>
