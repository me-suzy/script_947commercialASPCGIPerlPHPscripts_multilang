#!/usr/bin/perl
#    isp4you
#
#    (C) 2001-2003 by NH (Nick Herrmann) - nh@isp4you.com 
#
#
#    This program is WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#
#    dutch translation by Philip HW Schroth - philip@schroth.nl and Frans Ronner - webmaster@fransonline.nl
#    spanish translation by Matias Carrasco - matias.carrasco@silice.biz
#    turkish translation by Tanju Ergan - admin@ergan.net
#    french translation by Pierre Gulliver - gulliverpierre@hotmail.com
#
#    All rights reserved worldwide

######################
local $smart_user = $ENV{'REMOTE_USER'};
local $smart_ip = $ENV{'REMOTE_ADDR'};
######################
$no = "1";  ### Nummerierung der Liste

do '../web-lib.pl';
do 'isp4you.pl';
$|=1;
&init_config("isp4you");
%access=&get_module_acl();
&ReadParse();

#### CHANGE THE PORT LIKE FOLLOWING: go to /etc/webmin/isp4you/config  --> add a line like:   mysql_port=:3306   ## Dont forget to set a : in front of the port !!!
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to isp4you database');

isp4you_dom_counter();
isp4you_header();
isp4you_tracker();

print <<EOM;
<link rel="stylesheet" type="text/css" href="style.css">
<script language="javascript" src="print.js" type="text/javascript"></script>

<p>
$in{'ap_nok'}
<form ACTION="list.cgi?search=1" METHOD="post" name="formular" target="_self">
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
                        <td width="60%" height="50" rowspan="2" valign="top">
<font color="#00000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font><br>

<img src="spacer.gif" width="9" height="1"><input name="domsearch" type="text" class="input2" size="20">
<input type="image" src="images/search.gif" border="0" align="top" width="67" height="19" name="submit">

EOM

print "<br><br>";
if ($in{'detail'} eq 1) {
if ($in{'mysql_test'} eq 1) {
print "<font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Mysql Password: $in{'pass'}";
$pass ="";
} # End mysql = 1
if ($in{'mail'} ne 0) {
print "<font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Mail Password(s): $in{'pass'}";
$pass ="";
} # End mail ungleich 0

} # End detail = 1

print<<MID;


                          </td>
                        <td width="40%" height="25">
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
            <td height="350" valign="top" align="center">

MID

print "<br>";

#######################################        
# LIST isp4you mysql
#######################################
   my (@domainname, @row);
   if ($in{'search'} eq 1) {
   $SELECTSQL =  "SELECT id,domainname,user FROM domainen WHERE user='$smart_user' AND domainname LIKE '%$in{'domsearch'}%' ORDER by domainname";
   } else {
   $SELECTSQL =  "SELECT id,domainname,user FROM domainen WHERE user='$smart_user' ORDER by domainname";
   }
   $msql =  sql($SELECTSQL);
   my $sth =  $msql;

print "<table width=\"96%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\" space=\"0\" hspace=\"0\">";
print "<font face=\"Arial, Helvetica, sans-serif\" size=\"3\" color=\"#ffffff\">";

$tdi="2";

   while(@row = $sth->fetchrow) {             # Query-results
      push(@domainname, $row[0]);

my (@data, @raw);
$SELECTSQL =  "SELECT * FROM domaininfos WHERE info_id='$row[0]' AND smart_user='$smart_user'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@raw = $sth->fetchrow) {   
$id2 =$raw[1];
$info_id = $raw[1];
$smart_user_2 = $raw[2];
$apache  = $raw[3];
$ssl =  $raw[4];
$bind = $raw[5];
$mysql = $raw[6];
$webalizer = $raw[7];
$webmin = $raw[8];
$quota = $raw[9];
$mail = $raw[10];
$frontpage = $raw[11];
$proftp = $raw[12];
$alias = $raw[13];
$error = $raw[14];
$dummy = $raw[15];
}




if ($tdi == 2){
print "<td class=\"bodybg\" width=\"3%\" align=\"middle\" valign=\"middle\" bgcolor=\"#999999\">";
} else {
print "<td class=\"bodybglight\" width=\"3%\" align=\"middle\" valign=\"middle\" bgcolor=\"#cccccc\">";
}
print "<font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">";
print "$no";
print "</div></td>";
$no++;


if ($tdi == 2){
print "<td class=\"bodybg\" width=\"42%\" valign=\"middle\" bgcolor=\"#999999\">";
} else {
print "<td class=\"bodybglight\" width=\"42%\" valign=\"middle\" bgcolor=\"#cccccc\">";
}
print "<div align=\"left\">";
print "<font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">";
@subline = split(/\./, $row[1]);  ## This splits the domain for various reasons (.co.uk and disk_usage)
if ($subline[2] eq "") { $ro ="www.$row[1]"; } else { $ro ="$row[1]" };

if ($config{'disk_usage'} eq 1) {
#$usage_kb=&disk_usage_kb("$config{'webpfad'}$ro");
system ("du -s $config{'webpfad'}$ro/html > du_html");
open (DUHTML, du_html);
while(<DUHTML>) {
$v = $_;
#chomp($v);
@b = split(/\//, $v);
#$v = substr("$v", 0, 5);
}
close (DUHTML);
system ("rm -rf du_html");

$v = $b[0]/1024;
$v =~ s/\.(\d{2})\d*/\.\1/g;  ### round this stuff
}
if ($quota eq 0) { $color="#000000"; } else {

if ($quota < $v) { $color="#FF0000"; } else { $color="#000000"; }
}

if ($config{'disk_usage'} eq 1) {
$the_disk ="($v MB)";
}

if ($ssl eq 1) {
print "<font color=\"$color\"><a href=\"https://www.$row[1]\" target=\"_blank\">$row[1]</a> $the_disk</font>\n";
} else {
print "<font color=\"$color\"><a href=\"http://www.$row[1]\" target=\"_blank\">$row[1]</a> $the_disk</font>\n";
}

print "</div></td>";

if ($tdi == 2){
print "<td class=\"bodybg\" width=\"50%\" valign=\"middle\" bgcolor=\"#999999\">";
} else {
print "<td class=\"bodybglight\" width=\"50%\" valign=\"middle\" bgcolor=\"#cccccc\" >";
}
print "<font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">";


if (($subline[2] eq "") or (($subline[1] eq 'co') and ($subline[2] eq 'uk')) or (($subline[1] eq 'org') and ($subline[2] eq 'uk')) or (($subline[1] eq 'me') and ($subline[2] eq 'uk')) or (($subline[1] eq 'com') and ($subline[2] eq 'au')) or (($subline[1] eq 'com') and ($subline[2] eq 'ro'))      ) {
print "&nbsp; [<a href=\"detail.cgi?domainname=$row[1]\" target=\"_self\">Features</a>] - [<a href=\"pro/user.cgi?domain=$row[1]\" target=\"_self\">User Info</a>] - [<a href=\"pro/email.cgi?mail=$row[1]&change=0\" target=\"_self\">Mail</a>] - [<a href=\"pro/sub.cgi?domainname=$row[1]\" target=\"_self\">$text{'list_subdomain'}</a>]";
} else {
print "&nbsp; [<a href=\"detail.cgi?domainname=$row[1]\" target=\"_self\">Features</a>] - [<a href=\"pro/user.cgi?domain=$row[1]\" target=\"_self\">User Info</a>]";
}



print "</td>";

if ($tdi == 2){
print "<td class=\"bodybg\" width=\"5%\" valign=\"middle\" bgcolor=\"#999999\">";
$tdi="1";
} else {
print "<td class=\"bodybglight\" width=\"5%\" valign=\"middle\" bgcolor=\"#cccccc\">";
$tdi="2";
}
print "<div align=\"middle\">";
print "<div align=\"center\"><a href=\"javascript:if (confirm('$text{'list_delete_confirm'} $row[1] $text{'list_delete_confirm2'}')){window.location.href='del.cgi?domainen.selected=$row[1]';}\" target=\"_self\"><img src=\"images/del.gif\" alt=\"$text{'del_domain'} $row[1]\" width=\"19\" height=\"15\" border=\"0\"></a></div>";
print "</div></td>";
print "</tr>";
}
print "</table>";
$dbh->disconnect;

if ($id2 eq "") {
print"<table width=\"95%\" align=\"center\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">";
print"  <tr>";
if ($in{'search'} eq 1) {
print" <td class=\"bodybg\"><center><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\"><br>$text{'domain_not_found'}<p></font></center></td>";
} else {
print" <td class=\"bodybg\"><center><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\"><br>$text{'list_nodomain'}<p></font></center></td>";
}
print"</tr>";
print"</table>";

}
print <<BOTTOM;


              <p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif" color="#000000">
                [<a href="index.cgi" target="_self">Home</a>] [<a href="javascript:tmt_print()" target="_self">$text{'list_print_domainlist'}</a>]</font></p><br>
              </td>
          <td bgcolor="#000000" width="1"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#000000"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
      </table>


BOTTOM
print"</table><br>";
print "</form>";




