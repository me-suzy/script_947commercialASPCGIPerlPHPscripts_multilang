#!/usr/bin/perl
# isp4you
#
# (C) 2001-2003 by NH (Dipl. Wirt.-Ing. Nick Herrmann) - nh@isp4you.com
#
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# dutch translation by Philip HW Schroth - philip@schroth.nl and Frans Ronner - webmaster@fransonline.nl
# spanish translation by Matias Carrasco - matias.carrasco@silice.biz
# turkish translation by Tanju Ergan - admin@ergan.net
# french tarnslation by Pierre Gulliver - gulliverpierre@hotmail.com
#
# All rights reserved worldwide
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

$LineCount eq 0;
$domaincount ="/var/log/isp4you/$smart_user";
open (INFILE, $domaincount);
while(<INFILE>) {
$TheLine = $_;
chomp($TheLine);
$LineCount = $LineCount +1;
}
close(INFILE);
if ($LineCount eq "") {
$LineCount = "0";
}
$left = $access{'dom'} - $LineCount;

isp4you_header();
isp4you_tracker();

if ($config{'checked'} == 1) {
 $checked="checked";
 }

isp4you_list();

if ($config{'disk_usage'} eq 1) {

system ("uptime > up");
$lref = &read_file_lines("up"); $up = "@$lref";
system ("rm -rf up");
@uptime = split(/\ /, $up);
@up = split(/\,/, $uptime[5]);


#$index_kb=&disk_usage_kb("$config{'webpfad'}");
#$index_kb =$index_kb/1024;
#$index_kb =~ s/\.(\d{2})\d*/\.\1/g;

system ("du -s $config{'webpfad'} > du_html");
open (DUHTML, du_html);
while(<DUHTML>) {
$v = $_;
#chomp($v);
@b = split(/\//, $v);
$v = substr("$v", 0, 5);
}
close (DUHTML);
system ("rm -rf du_html");

$index_kb = $b[0]/1024;
$index_kb =~ s/\.(\d{2})\d*/\.\1/g;  ### round this stuff
}





### the html for domainname
###
if ($in{'error'} eq 1) {
print "<META HTTP-EQUIV=Refresh CONTENT=\"60; URL=index.cgi\">";
}
print <<EOM;
<link rel="stylesheet" type="text/css" href="style.css">
<script language="javascript" src="print.js" type="text/javascript"></script>
<p>
<form ACTION="make.cgi?subdomain=0" METHOD="post" name="formular" target="_self" onSubmit="return validate_form(this)">
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
<td width="20%">&nbsp;</td>
<td width="85%"><font color="#00000" size="2" face="Verdana, Arial, Helvetica, sans-serif">$text{'webname'}</font></td>
</tr>
</table>
<input name="www" type="text" class="input2" id="www" value="www." size="7">

<input name="domainname" type="text" class="input2" id="domainname" size="20" maxlength="70" value="$in{'domainname'}">
<input type="image" src="images/create.gif" border="0" align="top" width="67" height="19" name="submit">
</p>
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
<td width="50%" height="200" valign="top"> <div align="center"><font size="1"><br>
<br>
</font></div>
<table width="100%" border="0" cellspacing="5" cellpadding="0">
<tr>
<td width="50%"><font size="1">$text{'index_apache'}</font></td>
<td width="50%"><font size="1">
<input name="webserver" type="checkbox" value="1" $checked>
</font></td>
</tr>
<tr>
<td width="50%"><font size="1">$text{'index_ssl'}</font></td>
<td width="50%"><font size="1">
<input name="ssl" type="checkbox" value="1">
</font></td>
</tr>
<tr>
<td width="50%"><font size="1">$text{'index_nameserver'}</font></td>
<td width="50%"><font size="1">
<input name="bind8" type="checkbox" value="1" $checked>
</font></td>
</tr>
<tr>
<td width="50%"><font size="1">$text{'index_mysql'}</font></td>
<td width="50%"><font size="1">
<input name="mysql" type="checkbox" value="1" $checked>
</font></td>
</tr>
<tr>
<td width="50%"><font size="1">$text{'index_webalizer'}</font></td>
<td width="50%"><font size="1">
<input name="webalizer" type="checkbox" value="1" $checked>
</font></td>
</tr>
<tr>
<td width="50%"><font size="1">$text{'index_webmin'}</font></td>
<td width="50%"><font size="1">
<input name="webminuser" type="checkbox" value="1" $checked>
</font></td>
</tr>
<tr>
<td width="50%"><font size="1">$text{'index_quota'}</font></td>
<td width="50%"><font size="1">
<input type="text" name="quota_index" class="input2" size="5" maxlength="5" value="$config{'quota_default'}">
</font></td>
</tr>
<tr>
<td width="50%"><font size="1">$text{'index_mailaccount'}</font></td>
<td width="50%"><font size="1">
<input type="text" name="sendmail" class="input2" size="5" maxlength="3" value="$config{'mailvariable'}">
</font></td>
</tr>
<tr>
<td width="50%"><font size="1">&nbsp</font></td>
<td width="50%"><font size="1">&nbsp;</font></td>
</tr>
</table>
<p>&nbsp;</p> </td>
<td width="50%" align="middle" valign="top">
<br>
EOM
### The ERROR STUFF
if ($in{'error'} eq 1) {
print "<br><table class=\"bodybg\" align=\"middle\" width=\"90%\" height=\"50\" border=\"0\" cellpadding=\"0\" cellspacing=\"10\">";
print "<tr>";
print "<td valign=\"top\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><b>ERROR:</b></font><br>";
## Hier stehen die einzelnen Fehler
if ($in{'error_empty'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_domainname'}</font>"; }
if ($in{'error_less'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_1'}</font>"; }
if ($in{'error_stup'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_3'}</font>"; }
if ($in{'error_point'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_2'}</font>"; }
if ($in{'error_quota'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_5'}</font>"; }
if ($in{'error_user'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$in{'domainname'} $text{'check_4'}</font>"; }
if ($in{'error_namevirtual'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_6'}</font>"; }
if ($in{'error_what'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_7'}</font>"; }
if ($in{'error_postfix'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_8'}</font>"; }
if ($in{'error_whois'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_9'}</font>"; }

if ($in{'error_endpoint'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_10'}</font>"; }

if ($in{'error_sendmail'} eq 1){
print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'check_11'}</font>"; }




## Ende Fehlerbeschreibung
print "</td>";
print "</tr>";
print "</table>";
}

print "</td>";

if (($smart_user eq "root") or ($smart_user eq "admin")) {
$my_administrate ="[<a href=\"administrate.cgi\" target=\"_self\">my admin</a>]";
}

print<<BOT;
</tr>
</table>
<p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
[<a href="list.cgi" target="_self">$text{'index_list_domains'}</a>] $my_administrate [<a href="mass.cgi" target="_self">mass</a>]<br><br>

BOT

if ($config{'disk_usage'} eq 1) {

print "<font color=\"#999999\">";
print "$text{'index_uptime'} $uptime[4] $up[0] - usage of: $config{'webpfad'} = $index_kb MB";
print "</font></p>";
}
print<<DOW;
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
DOW


isp4you_version();


print "</form><br>";