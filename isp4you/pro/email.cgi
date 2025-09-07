#!/usr/bin/perl
#    isp4you
#
#    (C) 2001-2003 by NH (Dipl. Wirt.-Ing. Nick Herrmann) - nh@isp4you.com
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

######################
local $smart_user = $ENV{'REMOTE_USER'};
local $smart_ip = $ENV{'REMOTE_ADDR'};
######################

$no ="1";   ### fuer die Numerierung

do '../../web-lib.pl';
do '../isp4you.pl';
$|=1;
&init_config("isp4you");
%access=&get_module_acl();
&ReadParse();


#### Hier muss mein Mailmanagement kram hin, damit ich redirecten kann (vor den Header) !!!

#########################################
### Hier wird das Mailpassword geaendert
if ($in{'userpass'} eq 1) {
@ajump = split(/\./, $in{'user'});
system ("rm -rf mailpass");
#system ("touch mailpass");
open (MAILPASS,">>mailpass");
print MAILPASS "$in{'user'}:$in{'pass'}\n";
close (MAILPASS);
system ("chpasswd<mailpass");
system ("rm -rf mailpass");
&redirect("email.cgi?mail=$ajump[1].$ajump[2]&change=0");
}
########################################



if ($in{'changemail'} eq 1) {

####################################################################################
if (($config{'mxer'} eq 1) and ($config{'sendpost'} eq 1)) { isp4you_mxer_change(); }
####################################################################################

## Splitte in-mail damit ich die Domain bekomme wo spaeter wieder zur Liste gesprungen werden kann

@jump = split(/\./, $in{'mail'});
# Hier wird eine E-Mail Adresse geaendert
$lref = &read_file_lines("$config{'virtuser'}");
for (@$lref)
{
if (/^$in{'mail_old'} $in{'mail'}/) {
splice(@$lref, $Pos, 1, ("$in{'virt'}\@$in{'mail_domain'} $in{'mail'}"));
$Pos ="99999999999";
}
$Pos ++;
}
&flush_file_lines();
$Pos = 0;

isp4you_mapping();

&redirect("email.cgi?mail=$jump[1].$jump[2]&change=0");
}

#### Delete mail and user
if ($in{'change'} eq 3) {
@jump = split(/\./, $in{'mail'});
$lref = &read_file_lines("$config{'virtuser'}");
for (@$lref)
{
if (/$in{'mail'}/) {
splice(@$lref, $Pos, 1, ());
$Pos ="99999999999";
}
$Pos ++;
}
&flush_file_lines();
$Pos =0;
## Delete from access file if define in the module config
if (($config{'mxer'} eq 1) and ($config{'sendpost'} eq 1)) {
$mxer_conf =$config{'virtuser'};
@mxer_conf = split(/\//, $mxer_conf);
$mxer_conf_2 ="\/$mxer_conf[1]\/$mxer_conf[2]";   # /etc/mail/ for example


$lref = &read_file_lines("$mxer_conf_2/access");
for (@$lref)
{
if (/^From:$in{'virt'} RELAY/) {
splice(@$lref, $Pos, 1, ());
$Pos ="99999999999";
}
$Pos ++;
}
&flush_file_lines();
$Pos =0;
}

system ("userdel -r $in{'mail'}");

isp4you_mapping();

### Delete from table domaininfos
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to mysql database isp4you');

### Mysql select fuer domainname um die id zu ermitteln
my (@id, @reihe);
$SELECTSQL =  "SELECT id FROM domainen WHERE domainname = '$jump[1].$jump[2]'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@reihe = $sth->fetchrow) {
push(@id, $reihe[0]);
$id = $id[0];
}

### Mysql select um die Infos aus der Tabelle domaininfos zu bekommen
my (@data, @row);
$SELECTSQL =  "SELECT * FROM domaininfos WHERE info_id='$id' AND smart_user='$smart_user'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@row = $sth->fetchrow) {             # Query-results
push(@data, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10] , $row[11] , $row[12] , $row[13] , $row[14] , $row[15]);
$id = $data[0];
$info_id = $data[1];
$smart_user_2 = $data[2];
$apache  = $data[3];
$ssl =  $data[4];
$bind = $data[5];
$mysql_check = $data[6];
$webalizer = $data[7];
$webmin = $data[8];
$quota = $data[9];
$mail = $data[10];
$frontpage = $data[11];
$proftp = $data[12];
$alias = $data[13];
$error = $data[14];
$dummy = $data[15];
}

### check if mail is in integrit� with the database wert
@xx = split(/\-/, $mail);                     ## splitte wert aus Datenbank     (z.B. 10-10)
@yy = split(/\./, $in{'mail'});               ## splitte wert aus Form (die zu loeschende Mail)  (mail10.domain.org)
$yy[0] =~tr/0-9//cd;                          ## alle Buchstaben lschen   ( es bleibt 10 brig)
$yy = $yy[0];

if ($yy eq $xx[1]) {   ## Nur diese Lschart, wenn die beide Werte $yy und $xx[1] gleich sind
$xx[1]--;
$xx[0]--;
$mail = "$xx[0]-$xx[1]";
} else {            ## Diese Lschart, wenn die obere NICHT zutrifft
$xx[0]--;
$mail = "$xx[0]-$xx[1]";
}
### altes System mit einbeziehen
if ($xx[1] eq "") {
$mail = "$xx[0]-$xx[0]";
}

### Update the stuff
$dbh->do("UPDATE domaininfos SET info_id='$id', smart_user='$smart_user', apache='$apache', s_s_l='$ssl', bind='$bind', mysql='$mysql_check', webalizer='$webalizer', webmin='$webmin', quota='$quota', mail='$mail' WHERE id='$info_id'");
$dbh->disconnect;

&redirect("email.cgi?mail=$jump[1].$jump[2]&change=0");

}

######################################5#####################################
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to mysql database isp4you');

isp4you_dom_counter();
$dbh->disconnect();

if ($LineCount eq "") {
print "<table width=\"96%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" vspace=\"0\" hspace=\"0\">";
print "<font face=\"Arial, Helvetica, sans-serif\" size=\"3\" color=\"#ffffff\">";

print "<center>$text{'list_nodomains_created'}</center>";
print "</table></table>";
&footer($return, isp4you);
exit;
}

isp4you_header();

print <<EOM;

<link rel="stylesheet" type="text/css" href="../style.css">
<script language="javascript" src="../print.js" type="text/javascript"></script>

<p>
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
                        <td width="70%" height="50" rowspan="2" valign="top">

                    <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>$text{'email_management'}</b> $in{'mail'}</font>
                          </p>
                          </td>
                        <td width="30%" height="25">
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
            <td height="350" valign="top" align="left">

EOM
$formail eq $in{'mail'};
print "<br>";
if($in{'change'} eq 0) {

open(LIST, "$config{'virtuser'}");

print "<table width=\"96%\" border=\"0\" align=\"center\" cellspacing=\"1\" cellpadding=\"3\" vspace=\"0\" hspace=\"0\">";

$tdi="2";
while(<LIST>) {
chomp ($_);
@splitter = split(/\@/, $_);
@mailsplit = split(/ /, $splitter[1]);


if ($in{'mail'} eq "$mailsplit[0]") {

if ($tdi == 2){
print "<td class=\"bodybg\" width=\"4%\" align=\"middle\" valign=\"middle\" bgcolor=\"#999999\">";
} else {
print "<td class=\"bodybglight\" width=\"4%\" align=\"middle\" valign=\"middle\" bgcolor=\"#cccccc\">";
}
print "<div align=\"middle\">";
print "<font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">";
print "$no";
print "</div></td>";
$no++;
$nomail =1;

if ($tdi == 2){
print "<td class=\"bodybg\" width=\"51%\" valign=\"middle\" bgcolor=\"#999999\">";
} else {
print "<td class=\"bodybglight\" width=\"51%\" valign=\"middle\" bgcolor=\"#cccccc\">";
}
print "<div align=\"left\">";
print "<font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">";
if ($splitter[2] ne "") {
print "&nbsp;$splitter[0]\@$mailsplit[0] <img src=\"../images/mail-arrow.gif\" width=\"13\" height=\"7\">  $mailsplit[1]\@$splitter[2]";
} else {
print "&nbsp;$splitter[0]\@$mailsplit[0] <img src=\"../images/mail-arrow.gif\" width=\"13\" height=\"7\">  $mailsplit[1]";
}
print "</div></td>";

if ($tdi == 2){
print "<td class=\"bodybg\" width=\"40%\" align=\"middle\" valign=\"middle\" bgcolor=\"#999999\">";
} else {
print "<td class=\"bodybglight\" width=\"40%\" align=\"middle\" valign=\"middle\" bgcolor=\"#cccccc\" >";
}
print "<font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">";
print "[<a  href=\"email.cgi?change=1&mail=$mailsplit[1]&virt=$splitter[0]\@$in{'mail'}\" target=\"_self\">$text{'email_change_mail'}</a>] - [<a href=\"email.cgi?change=2&user=$mailsplit[1]\" target=\"_self\">$text{'email_change_pass'}</a>]";
print "</td>";

if ($tdi == 2){
print "<td class=\"bodybg\" width=\"5%\" valign=\"middle\" align=\"middle\" bgcolor=\"#999999\">";
$tdi="1";
} else {
print "<td class=\"bodybglight\" width=\"5%\" valign=\"middle\" align=\"middle\" bgcolor=\"#cccccc\">";
$tdi="2";
}
print "<div align=\"middle\">";
print "<font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">";
print "<div align=\"center\"><a href=\"javascript:if (confirm('$text{'mail_delete'}')){window.location.href='email.cgi?mail=$mailsplit[1]&virt=$splitter[0]\@$in{'mail'}&change=3';}\" target=\"_self\"><img src=\"../images/del.gif\" alt=\"delete\" width=\"19\" height=\"15\" border=\"0\"></a></div>";

print "</div></td>";
print "</tr>";
}
}
print "</table>";
close(LIST);
}  ### Ende if change eq 0

### Form um Die Mail zu aendern
@mailform = split(/@/, $in{'virt'});
if ($in{'change'} eq 1) {
$nomail =1;
print "<table width=\"60%\" border=\"0\" cellpadding=\"10\" cellspacing=\"0\">";
print "<tr><td width=\"5%\"></td><td class=\"bodybglight\" width=\"95%\" bgcolor=\"#cccccc\">";
print "&nbsp;&nbsp;&nbsp;<font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b>$text{'email_change_mail'}</b>";
print "<br><br><form action=\"email.cgi?change=1&changemail=1&mail=$in{'mail'}&mail_old=$in{'virt'}\" method=\"post\" name=\"form\" target=\"_self\" >";
print "<font align=\"left\">";
print "&nbsp;&nbsp;&nbsp;<input type=\"text\" name=\"virt\" value=\"$mailform[0]\" class=\"input2\" maxlength=\"70\" size=17>";
print "\@$mailform[1]<br><br>";
print "&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"Submit\" class=\"input\" value=\"$text{'mail_save'}\">";
print "<input type=\"hidden\" name=\"mail_domain\" value=\"$mailform[1]\">";
print "</font></td></tr></table>";
print "<br><br><br><br><br><br><br><br><br><br><br>";
}

if ($in{'change'} eq 2) {
$nomail =1;
print "<table width=\"60%\" border=\"0\" cellpadding=\"10\" cellspacing=\"0\">";
print "<tr><td width=\"5%\"></td><td class=\"bodybglight\" width=\"95%\" bgcolor=\"#cccccc\">";
print "&nbsp;&nbsp;<font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b>$text{'email_change_pass'}</b>";
print "<font align=\"left\">";
print "<form action=\"email.cgi?userpass=1&change=2&user=$in{'user'}\" method=\"post\" name=\"form\" target=\"_self\" >";
print "&nbsp;&nbsp;&nbsp;Pop3 Konto: $in{'user'} <br><br>&nbsp;&nbsp;&nbsp;$text{'mail_newpass'} ";
print "<input type=\"password\" name=\"pass\" maxlength=\"10\" class=\"input2\" size=12>";
print "<br><br>";
print "&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"Submit\" class=\"input\" value=\"$text{'mail_save'}\">";
print "</font></td></tr></table>";
print "<br><br><br><br><br><br><br><br><br>";
}

### print if no mails in virtuser for the selectied domain
if ($nomail eq "") {
print"<table width=\"95%\" align=\"center\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">";
print"  <tr>";
print" <td class=\"bodybg\"><center><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\"><br>$text{'mail_nomail'}<p></font></center></td>";
print"</tr>";
print"</table>";
}

print <<BOTTOM;

              <p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif" color="#000000" >
                [<a href="../list.cgi" target="_self">$text{'index_list_domains'}</a>] [<a href="javascript:tmt_print()" target="_self">$text{'email_print'}</a>]</font></p>
              </td>
          <td bgcolor="#000000" width="1"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#000000"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
      </table>
<input name="mailchange" type="hidden" value="1">
<form>

BOTTOM

print"</table>";
