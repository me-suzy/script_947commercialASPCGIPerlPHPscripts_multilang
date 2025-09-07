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
use DBI;


## grep the color information from style.css
$styl = &read_file_lines("style.css");

for (@$styl[0]) { if (/\#999999\;$/) { $checked_2 ="checked"; } else { $checked_1 ="checked";  }}
for (@$styl[6]) { if (/\#999999\;/) { $checked_4 ="checked"; } else { $checked_3 ="checked";  }}
for (@$styl[15]) { if (/top_1/) { $checked_5 ="checked"; } else { $checked_6 ="checked";  }}
for (@$styl[12]) { if (/\#999999/) { $checked_8 ="checked"; } else { $checked_7 ="checked";  }}

### Make your changes
if ($in{'change'} eq 1){

$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to isp4you database');

## First grep infos if allready datas in the database
my (@data, @raw, %mailer);
$SELECTSQL =  "SELECT * FROM mailtext WHERE smart_user='$smart_user'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@row = $sth->fetchrow) {             # Query-results
push(@data, $row[0], $row[1], $row[2], $row[3], $row[4]);
$id =$data[1];
$smart_user_2 = $data[1];
$mail_header = $data[2];
$mail_footer  = $data[3];
$mail_body =  $data[4];
}

if ($mail_header ne "") {
$dbh->do("UPDATE mailtext SET smart_user='$smart_user', mailheader='$in{'mail_header'}', mailfooter='$in{'mail_footer'}', mailbody='' WHERE smart_user='$smart_user'");
} else {
$dbh->do("INSERT INTO mailtext VALUES ('', '$smart_user', '$in{'mail_header'}', '$in{'mail_footer'}', '')");
}

$dbh->disconnect;

## Change the style.css now
&replace_file_line ("style.css", 0, "BODY {font-family: Verdana, Arial, Helvetica, sans-serif; color: #$in{'font_color'};\n");

&replace_file_line ("style.css", 6, "a:link {color: #$in{'link_color'};font-weight: normal;font-face: Verdana, Arial, Helvetica;text-decoration: none;font-size: 10px;}\n");
&replace_file_line ("style.css", 7, "a:visited {color: #$in{'link_color'};font-weight: normal;font-face: Verdana, Arial, Helvetica;text-decoration: none;font-size: 10px;}\n");
&replace_file_line ("style.css", 8, "a:active {color: #$in{'link_color'};font-weight: normal;font-face: Verdana, Arial, Helvetica;text-decoration: none;font-size: 10px;}\n");
&replace_file_line ("style.css", 9, "a:hover {color: #$in{'link_color'};font-weight: normal;font-face: Verdana, Arial, Helvetica;text-decoration: underline;font-size: 10px;}\n");

&replace_file_line ("style.css", 15, ".back_top {background-image: url(images/back_top_$in{'header_color'}.gif);}\n");
&replace_file_line ("style.css", 12, ".input2 {font-family: Verdana,Arial,Helvetica;font-size: 11px;border: 1px dotted #$in{'formfield_color'};color: #$in{'formfield_color'};}\n");

if ($in{'list_color'} eq 1) {
&replace_file_line ("style.css", 17, ".bodybg {background-color: #ddddff;border: 1px solid #9999cc;}  \n");
&replace_file_line ("style.css", 18, ".bodybglight {background-color: #ceceff;border: 1px solid #aaaaFF;}\n");
} else {
&replace_file_line ("style.css", 17, ".bodybg {background-color: #cccccc;border: 1px solid #999999;}  \n");
&replace_file_line ("style.css", 18, ".bodybglight {background-color: #bcbcbc;border: 1px solid #999999;}\n");
}

&redirect("index.cgi");
}

### Grep the needed stuff from databse and from style.css
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to isp4you database');

isp4you_dom_counter();


my (@data, @raw, %lperson2);
$SELECTSQL =  "SELECT * FROM mailtext WHERE smart_user='$smart_user'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@row = $sth->fetchrow) {             # Query-results
push(@data, $row[0], $row[1], $row[2], $row[3], $row[4]);
$id =$data[1];
$smart_user_2 = $data[1];
$mail_header = $data[2];
$mail_footer  = $data[3];
$mail_body =  $data[4];
}

$dbh->disconnect;





isp4you_header();

system ("uptime > up");
$lref = &read_file_lines("up"); $up = "@$lref";
system ("rm -rf up");
@uptime = split(/\ /, $up);
@up = split(/\,/, $uptime[5]);


$index_kb=&disk_usage_kb("$config{'webpfad'}");
$index_kb =$index_kb/1024;
$index_kb =~ s/\.(\d{2})\d*/\.\1/g;

### the html for domainname
###
print <<EOM;
<link rel="stylesheet" type="text/css" href="style.css">
<p>



<form ACTION="administrate.cgi?change=1" METHOD=post>
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
<td width="85%"><font color="#00000" size="2" face="Verdana, Arial, Helvetica, sans-serif"></font></td>
</tr>
</table>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
    <td width="52%"><font size="1">Font color</font></td>
    <td width="5%"><font size="1"> black </font></td>
    <td width="3%"><font size="1">
      <input name="font_color" type="radio" value="000000" $checked_1>
      </font></td>
    <td width="4%"><font size="1">grey </font></td>
    <td width="36%"><font size="1">
      <input type="radio" name="font_color" value="999999" $checked_2>
      </font></td>
  </tr>
  <tr>
    <td width="52%"><font size="1">Link color</font></td>
    <td width="5%"><font size="1"> blue </font></td>
    <td width="3%"><font size="1">
      <input name="link_color" type="radio" value="7979CB" $checked_3>
      </font></td>
    <td width="4%"><font size="1">grey </font></td>
    <td width="36%"><font size="1">
      <input type="radio" name="link_color" value="999999" $checked_4>
      </font></td>
  </tr>
  <tr>
    <td width="52%"><font size="1">Header Background</font></td>
    <td width="5%"><font size="1"> blue </font></td>
    <td width="3%"><font size="1">
      <input name="header_color" type="radio" value="1" $checked_5>
      </font></td>
    <td width="4%"><font size="1">grey </font></td>
    <td width="36%"><font size="1">
      <input type="radio" name="header_color" value="2" $checked_6>
      </font></td>
  </tr>
  <tr>
    <td width="52%"><font size="1">Frame color</font></td>
    <td width="5%"><font size="1"> black </font></td>
    <td width="3%"><font size="1">
      <input name="frame_color" type="radio" value="000000" checked>
      </font></td>
    <td width="4%"><font size="1">grey </font></td>
    <td width="36%"><font size="1">
      <input type="radio" name="frame_color" value="999999">
      </font></td>
  </tr>
  <tr>
    <td width="52%"><font size="1">List color</font></td>
    <td width="5%"><font size="1"> blue </font></td>
    <td width="3%"><font size="1">
      <input name="list_color" type="radio" value="1" checked>
      </font></td>
    <td width="4%"><font size="1">grey </font></td>
    <td width="36%"><font size="1">
      <input type="radio" name="list_color" value="2">
      </font></td>
  </tr>
  <tr>
    <td width="52%"><font size="1">Formfield color</font></td>
    <td width="5%"><font size="1"> blue </font></td>
    <td width="3%"><font size="1">
      <input name="formfield_color" type="radio" value="7979CB" $checked_7>
      </font></td>
    <td width="4%"><font size="1">grey </font></td>
    <td width="36%"><font size="1">
      <input type="radio" name="formfield_color" value="999999" $checked_8>
      </font></td>
  </tr>
  <tr>
    <td width="52%"><font size="1">&nbsp;</font></td>
    <td width="5%"><font size="1">&nbsp;</font></td>
    <td width="3%">&nbsp;</td>
    <td width="4%">&nbsp;</td>
    <td width="36%">&nbsp;</td>
  </tr>
  <tr>
    <td width="52%"><font size="1">&nbsp;</font></td>
    <td width="5%"><font size="1">&nbsp;</font></td>
    <td width="3%">&nbsp;</td>
    <td width="4%">&nbsp;</td>
    <td width="36%">&nbsp;</td>
  </tr>
  <tr>
    <td width="52%"><font size="1">&nbsp</font></td>
    <td width="5%"><font size="1">&nbsp;</font></td>
    <td width="3%">&nbsp;</td>
    <td width="4%">&nbsp;</td>
    <td width="36%">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p> </td>
<td width="50%" align="middle" valign="top">
<br>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><font size="1">Mail Header Text</td>
  </tr>
  <tr>
    <td align="left" valign="top">
      <textarea name="mail_header" cols="25" rows="4" class="input2">$mail_header</textarea></td>
  </tr>
  <tr>
    <td align="left" valign="top"><font size="1">Mail Footer Text</td>
  </tr>
  <tr>
    <td align="left" valign="top">
      <textarea name="mail_footer" cols="25" rows="4" class="input2">$mail_footer</textarea></td>
  </tr>
</table>


EOM





print "</td>";

print<<BOT;
</tr>
</table>
<p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
[<a href="index.cgi" target="_self">Home</a>]<br><br>
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