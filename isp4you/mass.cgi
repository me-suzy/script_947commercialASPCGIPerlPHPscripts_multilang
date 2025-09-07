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

#### COUNT FROM MYSQL DATABASE
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to isp4you database');

isp4you_dom_counter();
isp4you_header();

if ($config{'checked'} == 1) {
 $checked="checked";
 }


isp4you_uptime();
isp4you_disk_usage();

### the html for domainname
###

print <<EOM;
<link rel="stylesheet" type="text/css" href="style.css">
<p>
<form ACTION="mass2.cgi?subdomain=0" METHOD=post>
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
<td width="50%" height="50" rowspan="2" valign="top">

<input name="www" type="text" class="input2" value="www." size="7">
<textarea name="domainname" cols="25" rows="3" class="input2" id="domainname"></textarea>
<input type="image" src="images/create.gif" border="0" align="bottom" width="67" height="19" name="submit">
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
<input type="text" name="quota_index" class="input2" size="5" value="$config{'quota_default'}">
</font></td>
</tr>
<tr>
<td width="50%"><font size="1">$text{'index_mailaccount'}</font></td>   
<td width="50%"><font size="1">
<input type="text" name="sendmail" class="input2" size="5" value="$config{'mailvariable'}">

</font></td>
</tr>
<tr>
<td width="50%" height="36"><font size="1">&nbsp</font></td>
<td width="50%"><font size="1">&nbsp;</font></td>
</tr>
</table>
<p>&nbsp;</p> </td>
<td width="50%" align="middle" valign="top">
<br>

EOM
print "<br><table class=\"bodybg\" align=\"middle\" width=\"90%\" height=\"50\" border=\"0\" cellpadding=\"0\" cellspacing=\"10\">";
print "<tr>";
print "<td valign=\"top\"><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">Use one domainname per line !!!</font><br>";
print "</td>";
print "</tr>";
print "</table>";


print "</td>";

print<<BOT;
</tr>
</table>
<p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
[<a href="index.cgi" target="_self">Home</a>] [<a href="list.cgi" target="_self">$text{'index_list_domains'}</a>]<br><br>
<font color="#999999">
$text{'index_uptime'} $uptime[4] $up[0] - usage of $config{'webpfad'} = $index_kb MB
</font></p>
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
BOT
do 'version.pl';
print "</form><br>";