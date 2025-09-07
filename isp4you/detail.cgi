#!/usr/bin/perl
#    isp4you
#
#    (C) 2001-2003 by NH (Dipl. Wirt.-Ing. Nick Herrmann) - nh@isp4you.com
#
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#
#    dutch translation by Philip HW Schroth - philip@schroth.nl and Frans Ronner - webmaster@fransonline.nl
#    spanish translation by Matias Carrasco - matias.carrasco@silice.biz
#    turkish translation by Tanju Ergan - admin@ergan.net
#    french tarnslation by Pierre Gulliver - gulliverpierre@hotmail.com
#
#    All rights reserved worldwide

#######################
local $smart_ip = $ENV{'REMOTE_ADDR'};
local $smart_user = $ENV{'REMOTE_USER'};
######################

do '../web-lib.pl';
do 'isp4you.pl';
$|=1;
&init_config("isp4you");
%access=&get_module_acl();
&ReadParse();

$domainname = $in{'domainname'};

### COUNT FROM MYSQL DATABASE
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to mysql database isp4you');

isp4you_dom_counter();




if ($in{'next'} ne 1) {
### Mysql select fuer domainname um die id zu ermitteln
my (@id, @reihe);
$SELECTSQL =  "SELECT id FROM domainen WHERE domainname = '$in{'domainname'}'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@reihe = $sth->fetchrow) {
push(@id, $reihe[0]);
$id = $id[0];
}
}

### Mysql select um die Infos aus der Tabelle domaininfos zu bekommen
my (@data, @row);
$SELECTSQL =  "SELECT * FROM domaininfos WHERE info_id='$id' AND smart_user='$smart_user'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@row = $sth->fetchrow) {   
$id =$row[1];
$info_id = $row[1];
$smart_user_2 = $row[2];
$apache  = $row[3];
$ssl =  $row[4];
$bind = $row[5];
$mysql = $row[6];
$webalizer = $row[7];
$webmin = $row[8];
$quota = $row[9];
$mail = $row[10];
$frontpage = $row[11];
$proftp = $row[12];
$alias = $row[13];
$error = $row[14];
$dummy = $row[15];
}
$dbh->disconnect();

### checked vorbereiten
if ($mysql eq 1) { $checked_mysql ="checked"; }
if ($webalizer eq 1) { $checked_webalizer ="checked"; }
if ($error eq 1) { $checked_error ="checked"; }
if ($proftp eq 1) { $checked_proftp ="checked"; }
if ($frontpage eq 1) { $checked_indexes ="checked"; }

isp4you_header();

# Redirect ermitteln
open(LIST, "$config{'httpd_2'}$domainname.conf");
while(<LIST>) {
$ThisLine = $_;
chomp ($ThisLine);
@Words = split(/ /, $ThisLine);
if ('Redirect' eq $Words[0]) {
$red = "$Words[2]";
}
}

if ($in{'manual'} eq 1) { $x ="3"; } else { $x ="2"; }
print <<EOM;

<script language = "javascript">
var ie = document.all ? 1 : 0
var ns = document.layers ? 1 : 0
if(ns){doc = "document."; sty = ""}
if(ie){doc = "document.all."; sty = ".style"}
var initialize = 0
var Ex, Ey, topColor, subColor, ContentInfo
if(ie){
Ex = "event.x"
Ey = "event.y"
topColor = "#7979CB"
subColor = "#C0C0C0"
}

if(ns){
Ex = "e.pageX"
Ey = "e.pageY"
window.captureEvents(Event.MOUSEMOVE)
window.onmousemove=overhere
topColor = "#7979CB"
subColor = "#C0C0C0"
}

function MoveToolTip(layerName, FromTop, FromLeft, e){
if(ie){eval(doc + layerName + sty + ".top = "  + (eval(FromTop) + document.body.scrollTop))}
if(ns){eval(doc + layerName + sty + ".top = "  +  eval(FromTop))}
eval(doc + layerName + sty + ".left = " + (eval(FromLeft) + 15))
}

function ReplaceContent(layerName){
if(ie){document.all[layerName].innerHTML = ContentInfo}
if(ns){
with(document.layers[layerName].document)
{
   open();
   write(ContentInfo);
   close();
}
}
}

function Activate(){initialize=1}
function deActivate(){initialize=0}
function overhere(e){
if(initialize){

MoveToolTip("ToolTip", Ey, Ex, e)
eval(doc + "ToolTip" + sty + ".visibility = 'visible'")
}
else{
MoveToolTip("ToolTip", 0, 0)
eval(doc + "ToolTip" + sty + ".visibility = 'hidden'")
}
}

function EnterContent(layerName, TTitle, TContent){

ContentInfo = '<table border="0" width="150" cellspacing="0" cellpadding="0">'+
'<tr><td width="100%" bgcolor="#000000">'+
'<table border="0" width="100%" cellspacing="1" cellpadding="0">'+
'<tr><td width="100%" bgcolor='+topColor+'>'+
'<table border="0" width="90%" cellspacing="0" cellpadding="0" align="center">'+
'<tr><td width="100%">'+
'<font class="tooltiptitle"> '+TTitle+'</font>'+
'</td></tr>'+
'</table>'+
'</td></tr>'+
'<tr><td width="100%" bgcolor='+subColor+'>'+
'<table border="0" width="90%" cellpadding="0" cellspacing="1" align="center">'+
'<tr><td width="100%">'+
'<font class="tooltipcontent">'+TContent+'</font>'+
'</td></tr>'+
'</table>'+
'</td></tr>'+
'</table>'+
'</td></tr>'+
'</table>';
ReplaceContent(layerName)
}
</script>


<body onmousemove="overhere()">
<div id="ToolTip"></div>
<link rel="stylesheet" type="text/css" href="style.css">

<p>
<form ACTION="detail$x.cgi?domainname=$domainname&mail=$mail&www=www." METHOD=post>
<table width="650" border="0" align="center" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      <table width="650" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td bgcolor="#000000"> <table width="650" border="0" cellpadding="2" cellspacing="1">
              <tr>
                  <td height="50" valign="top" bgcolor="#C0BEFF" class="back_top">
<table width="100%" height="64" border="0" cellspacing="3" cellpadding="0">
                      <tr>
                        <td width="50%" height="50" rowspan="2" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="1%">&nbsp;</td>
                              <td width="99%"><font color="#00000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>domain details:</b> $domainname</font></td>
                            </tr>
                          </table>
                          <br>

                         &nbsp;<input name="submit" type="submit" class="input" value="$text{'detail_changes'}">

                          </td>
                        <td width="50%" height="25">
<div align="right"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'index_user'}
                            $smart_user ($LineCount /$left $text{'index_domains'})</font></div></td>
                      </tr>
                      <tr>
                        <td width="50%" height="25">
<div align="right"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                            $text{'index_ip'} $smart_ip </font></div></td>
                      </tr>
                    </table>
                  </td>
              </tr>
            </table></td>
        </tr>
      </table>
      <table width="650" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td bgcolor="#000000" width="1"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
            <td height="350" valign="top">
              <table width="100%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td width="50%" height="200" valign="top"> <div align="center"><font size="1">
                  <img src="images/spacer.gif" width="1" height="24"><br>


                      </font></div>
                    <table width="100%" border="0" cellspacing="5" cellpadding="0">
                      <tr>
                        <td width="50%" valign="top"><font size="1"><a class="NArial" href="javascript:void(0)" onMouseover="EnterContent('ToolTip','Server Alias','Aliases are domains which your webserver is also listening to. Use just one domainname per line!'); Activate();" onMouseout="deActivate()">Server Alias</a></font></td>
                        <td width="50%"><font size="1">
                    &nbsp;<textarea name="alias" wrap="OFF" cols="20" rows="4" class="input2">$alias</textarea>
                          </font></td>
                      </tr>
                      <tr>
                        <td width="50%"><font size="1"><a class="NArial" href="javascript:void(0)" onMouseover="EnterContent('ToolTip','Server Redirect','When you want to redirect you Server to another Webserver, you can insert the domainname here. You must type http:// in front of the domainname.'); Activate();" onMouseout="deActivate()">Server Redirect</a></font></td>
                        <td width="50%"><font size="1">
                    &nbsp;<input name="redirect" type="text" class="input2" size="21" value="$red">
                          </font></td>
                      </tr>
                      <tr>
                        <td width="50%"><font size="1"><a class="NArial" href="javascript:void(0)" onMouseover="EnterContent('ToolTip','401','When you mark this option, your server will point to the root of your webserver, if a page has not been found.'); Activate();" onMouseout="deActivate()">401/402/403/404</a></font></td>
                        <td width="50%"><font size="1">
                          <input name="401" type="checkbox" value="1" $checked_error>
                          </font></td>
                      </tr>

                       <tr>
                        <td width="50%"><font size="1"><a class="NArial" href="javascript:void(0)" onMouseover="EnterContent('ToolTip','Option Indexes','Option Indexes will show a folder when no index.html is present. This can be helpful when you wanna create a download directory.'); Activate();" onMouseout="deActivate()">Option Indexes</a></font></td>
                        <td width="50%"><font size="1">
                          <input name="indexes" type="checkbox" value="1" $checked_indexes>
                          </font></td>
                      </tr>

                      <tr>
                        <td width="50%"><font size="1"><a class="NArial" href="javascript:void(0)" onMouseover="EnterContent('ToolTip','MySQL','Mark this to create or unmark it to delete a MySQL database. The password will be shown after the creation.'); Activate();" onMouseout="deActivate()">$text{'index_mysql'}</a></font></td>
                        <td width="50%"><font size="1">
                          <input name="mysql" type="checkbox" value="1" $checked_mysql>
                          </font></td>
                      </tr>
                      <tr>
                        <td width="50%"><font size="1"><a class="NArial" href="javascript:void(0)" onMouseover="EnterContent('ToolTip','Webalizer','When marking this, your statistics will be shown on your webbrowser. The stats will be found on www.domain.de/WEBSTATS'); Activate();" onMouseout="deActivate()">$text{'index_webalizer'}</a></font></td>
                        <td width="50%"><font size="1">
                          <input name="webalizer" type="checkbox" value="1" $checked_webalizer>
                          </font></td>
                      </tr>
                      <tr>
                        <td width="50%"><font size="1"><a class="NArial" href="javascript:void(0)" onMouseover="EnterContent('ToolTip','proFTP User','Just mark this when you are running the proFTPd server on your system. If not, just leave it blank.'); Activate();" onMouseout="deActivate()">proFTP User</a></font></td>
                        <td width="50%"><font size="1">
                        <input name="proftp" type="checkbox" value="1" $checked_proftp>
                         </font></td>
                      </tr>
                      <tr>
                        <td width="50%"><font size="1"><a class="NArial" href="javascript:void(0)" onMouseover="EnterContent('ToolTip','Quota','Type in your quotas here. This is just helpful, when your kernel is supporting Quotas !!!'); Activate();" onMouseout="deActivate()">$text{'index_quota'}</a></font></td>
                        <td width="50%"><font size="1">
                          &nbsp;<input type="text" name="quota_index" class="input2" size="5" value="$quota">
                    </font></td>
                      </tr>
EOM

@sub_detail = split(/\./, $in{'domainname'});

if ($sub_detail[2] eq "") {
@mail = split(/\-/, $mail);
print"                      <tr>";
print"                        <td width=\"50%\"><font size=\"1\"><a class=\"NArial\" href=\"javascript:void(0)\" onMouseover=\"EnterContent('ToolTip','add Mailaccounts','Here you can add more mailaccounts to your domain. The password will be shown after the creation.'); Activate();\" onMouseout=\"deActivate()\">add $text{'index_mailaccount'}</a> ($mail[0])</font></td>";
print"                        <td width=\"50%\"><font size=\"1\">";
print"                          &nbsp;<input type=\"text\" name=\"sendmail\" class=\"input2\" size=\"5\">";
print"                          </font></td>";
print"                      </tr>";
print "<input name=\"mail1\" type=\"hidden\" value=\"$mail[0]\">";
print "<input name=\"mail2\" type=\"hidden\" value=\"$mail[1]\">";
} else {
print "<input name=\"sendmail\" type=\"hidden\" value=\"0\">";
}
print <<MID;
                      <tr>
                        <td width="50%"><font size="1">&nbsp;</font></td>
                        <td width="50%"><font size="1">&nbsp;</font></td>
                      </tr>
                    </table>
                    </td>
                  <td width="50%" valign="top">
<div align="center">

MID

if ($in{'error'} eq 1) {
print "<br><br><table class=\"bodybg\" align=\"middle\" width=\"90%\" height=\"50\" border=\"0\" cellpadding=\"0\" cellspacing=\"10\">";
print "<tr>";
print "<td valign=\"top\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b>ERROR:</b></font><br>";
## Hier stehen die einzelnen Fehler
if ($in{'error_stup'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_3'}</font>"; }
if ($in{'error_point'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_2'}</font>"; }
if ($in{'error_quota'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_5'}</font>"; }
if ($in{'error_http'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'error_http'}</font>"; }

## Ende Fehlerbeschreibung
print "</td>";
print "</tr>";
print "</table>";
}


if ($in{'manual'} eq 1) {

open (FILE, "$config{'httpd_2'}$in{'domainname'}.conf");
@lines = <FILE>;
close(FILE);

print "<img src=\"images/spacer.gif\" width=\"1\" height=\"28\"><br>";
print "<font size=\"1\"><textarea name=\"manual\" wrap=\"OFF\" cols=\"45\" rows=\"20\" class=\"input2\">", join("", @lines),"</textarea>";

}
if ($in{'manual'} eq 2) {

### delete access_log or error_log if selected
if ($gconfig{'real_os_type'} eq "SuSE Linux") {$reloader = "apache"; } else { $reloader = "httpd"; }

if ($in{'ea'} eq 1) {
if ($webalizer eq 1) {
## mach nochmal nen Webalizer durchlauf für die Domain, bevor Du das Access File löscht
system ("/usr/bin/webalizer -c /var/log/isp4you/webalizer-data/$in{'domainname'} > /dev/null 2>&1");
}
system ("rm -rf $config{'webpfad'}\www.$in{'domainname'}/logs/access_log");
isp4you_apache_reload();
}
if ($in{'ee'} eq 1) {
system ("rm -rf $config{'webpfad'}\www.$in{'domainname'}/logs/error_log");
isp4you_apache_reload();
}
###############################################


if ($webalizer eq 1) {
$webalizer_pfad ="$config{'webpfad'}\www.$in{'domainname'}/html/WEBSTATS/webalizer.hist";
open (WEBAL, $webalizer_pfad);
while(<WEBAL>) {
$TheLine = $_;
chomp($TheLine);
@weba = split(/ /, $TheLine);
push(@webal, $weba[5]);

$weba2 = $webal[0]+$webal[1]+$webal[2]+$webal[3]+$webal[4]+$webal[5]+$webal[6]+$webal[7]+$webal[8]+$webal[9]+$webal[10]+$webal[11]+$webal[12]+$webal[13]+$webal[14]+$webal[15];
$weba = $weba +1;
}
close(WEBAL);
$v = $weba2/1024;
$v =~ s/\.(\d{2})\d*/\.\1/g;  ### round this stuff
} ## end webalizer eq 1

### the du check for grepping disk usage information
$du_error_pfad = "$config{'webpfad'}\www.$in{'domainname'}/logs/error_log";
$du_access_pfad ="$config{'webpfad'}\www.$in{'domainname'}/logs/access_log";
$du_html_pfad ="$config{'webpfad'}\www.$in{'domainname'}/html/";

system ("du -s $du_html_pfad > du_html");
system ("du -s $du_error_pfad > du_error");
system ("du -s $du_access_pfad > du_access");
####################################
open (DUHTML, du_html);
while(<DUHTML>) {
$h = $_;
@b = split(/\//, $h);
$h = $b[0]/1024;
$h =~ s/\.(\d{2})\d*/\.\1/g;  ### round this stuff
}
close (DUHTML);
####################################
open (DUERROR, du_error);
while(<DUERROR>) {
$y = $_;
@c= split(/\//, $y);
$y = $c[0]/1024;
$y =~ s/\.(\d{2})\d*/\.\1/g;
}
close (DUERROR);
####################################
open (DUACCESS, du_access);
while(<DUACCESS>) {
$x = $_;
@d = split(/\//, $x);
$x = $d[0]/1024;
$x =~ s/\.(\d{2})\d*/\.\1/g;  
}
close (DUACCESS);
####################################
system ("rm -rf du_html");
system ("rm -rf du_error");
system ("rm -rf du_access");

if ($in{'ea'} eq 1) {
$x ="0";
}
if ($in{'ee'} eq 1) {
$y ="0";
}
####################################


print "<img src=\"images/spacer.gif\" width=\"1\" height=\"28\"><br>";
print <<INF;
                            <table class="bodybg" width="96%" border="0" cellspacing="1" cellpadding="5">
                        <tr>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">total
                            webtraffic:</font></td>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;$v MB</font></td>
                        </tr>
                        <tr>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">owner:</font></td>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;$smart_user_2</font></td>
                        </tr>
                        <tr>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">mailaccounts:</font></td>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;$mail[0]</font></td>
                        </tr>
                        <tr>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
                            html:</font></td>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;$h MB</font></td>
                        </tr>
                        <tr>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">                      
                            access_log:</font></td>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;$x MB - <a href=\"javascript:if (confirm('Do you really want to delete the access file? Wenn no webalizer.hist is setted, and you use Webalizer stats you can loose the stats for the actual mounth!')){window.location.href='detail.cgi?manual=2&domainname=$in{'domainname'}&ea=1';}\" target=\"_self\">clear</a></font></td>
                        </tr>
                        <tr>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
                            error_log:</font></td>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;$y MB - <a href="detail.cgi?manual=2&domainname=$in{'domainname'}&ee=1">clear</a></font></td>
                        </tr>
                        <tr>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
                          <td width="50%" height="15"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
                        </tr>
                        <tr>
                          <td width="50%" height="105"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
                          <td width="50%" height="105"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
                        </tr>
                      </table>
INF



}




print <<BOT;

                </div></td>
                </tr>
              </table>
      <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
            [<a href="list.cgi" target="_self">$text{'index_list_domains'}</a>] [<a href=\"javascript:if (confirm('$text{'detail_manual'}')){window.location.href='detail.cgi?manual=1&domainname=$in{'domainname'}';}\" target=\"_self\">manual http entries</a>] [<a href="detail.cgi?manual=2&domainname=$in{'domainname'}" target="_self">details</a>]
          </div>
              </td>
          <td bgcolor="#000000" width="1"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#000000"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>


BOT
print "<br>";
