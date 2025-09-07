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

###############
local $smart_user = $ENV{'REMOTE_USER'};
local $smart_ip = $ENV{'REMOTE_ADDR'};
##################

do '../../web-lib.pl';
do '../isp4you.pl';
$|=1;
&init_config("isp4you");
%access=&get_module_acl();
&ReadParse();

### COUNT FROM MYSQL DATABASE
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to mysql database');

isp4you_dom_counter();

###############################################
### Update User info if add = 1
###############################################

if ($in{'update'} eq 1) {
$dbh->do("UPDATE clients SET domain_id='$in{'domain_id'}', smart_user='$smart_user', company='$in{'company'}', name='$in{'name'}', street='$in{'street'}', zip='$in{'zip'}', city='$in{'city'}', tel='$in{'tel'}', fax='$in{'fax'}', email='$in{'email'}' WHERE id='$in{'id'}'");
}

###############################################
#### Datenbank abfragen
###############################################
   my (@id, @reihe);
   $SELECTSQL =  "SELECT id FROM domainen WHERE domainname='$in{'domain'}'";
   $msql =  sql($SELECTSQL);
   my $sth =  $msql;
   while(@reihe = $sth->fetchrow) {             # Query-results
   push(@id, $reihe[0]);
   }

   my (@wert, @row);
   $SELECTSQL =  "SELECT * FROM clients WHERE domain_id='$id[0]' AND smart_user='$smart_user'";
   $msql =  sql($SELECTSQL);
   my $sth =  $msql;
   while(@row = $sth->fetchrow) {             # Query-results
   push(@wert, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10]);
   }

$dbh->disconnect;

isp4you_header();


### the html <FORM> for domainname
###
###

print <<EOM;

<link rel="stylesheet" type="text/css" href="../style.css">

<script language="javascript" src="../print.js" type="text/javascript"></script>
<p>
<form ACTION="user.cgi?update=1&domain=$in{'domain'}&domain_id=$id[0]&id=$wert[0]" METHOD=post>
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
                        <td width="54%" height="50" rowspan="2" valign="top">
<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>$text{'user_info'}</b> $in{'domain'}</font></td>
                        <td width="46%" height="25">
<div align="right"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'index_user'}
                             $smart_user ($LineCount /$left $text{'index_domains'})</font></div></td>
                      </tr>
                      <tr>
                        <td width="46%" height="25">
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
            <td valign="top" align="middle"> <br>
        <table width="96%" border="0" cellspacing="1" cellpadding="5">
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybg" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'user_reseller'}</font></td>
                  <td class="bodybg" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    $smart_user
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybglight" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'user_company'}</font></td>
                  <td class="bodybglight" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    <input name="company" type="text" class="input2" value="$wert[3]" size="30">
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybg" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'user_name'}</font></td>
                  <td class="bodybg" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    <input name="name" type="text" class="input2" value="$wert[4]" size="30">
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybglight" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'user_street'}</font></td>
                  <td class="bodybglight" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    <input name="street" type="text" class="input2" value="$wert[5]" size="30">
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybg" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'user_zip'}</font></td>
                  <td class="bodybg" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    <input name="zip" type="text" class="input2" value="$wert[6]" size="30">
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybglight" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'user_city'}</font></td>
                  <td class="bodybglight" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    <input name="city" type="text" class="input2" value="$wert[7]" size="30">
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybg" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'user_tel'}</font></td>
                  <td class="bodybg" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    <input name="tel" type="text" class="input2" value="$wert[8]" size="30">
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybglight" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'user_fax'}</font></td>
                  <td class="bodybglight" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    <input name="fax" type="text" class="input2" value="$wert[9]" size="30">
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybg" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'user_email'}</font></td>
                  <td class="bodybg" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    <input name="email" type="text" class="input2" value="$wert[10]" size="30">
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybglight" width="19%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
                    </font></td>
                  <td class="bodybglight" width="81%"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">
                    <input name="submit" class="input" type="submit" id="submit" value="$text{'user_change'}">
                    </font></td>
                </tr>
              </table>
 <p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">[<a href="../list.cgi" target="_self">$text{'index_list_domains'}</a>] [<a href="javascript:tmt_print()" target="_self">$text{'user_print'}</a>]</font></p>
<br>
</td>
          <td bgcolor="#000000" width="1"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#000000"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
      </table>
    </td>
  </tr>
</table></form>
EOM
#print "<br>";
