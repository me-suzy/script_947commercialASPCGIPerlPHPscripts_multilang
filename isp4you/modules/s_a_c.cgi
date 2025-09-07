#!/usr/bin/perl
#    isp4you
#
#    (C) 2001-2003 by NH (Nick Herrmann) - nh@ingenieurbuero-herrmann.de
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

do '../../web-lib.pl';
do '../isp4you.pl';
$|=1;
&init_config("isp4you");
%access=&get_module_acl();
&ReadParse();

use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},,{PrintError => 0, RaiseError => 0 }) || &error('could not connect to mysql database');



### Mysql select fuer domainname um die id zu ermitteln
my (@id, @reihe);
$SELECTSQL =  "SELECT id FROM domainen WHERE domainname = '$in{'domainname'}'";
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
$dbh->disconnect;

### checked vorbereiten
if ($mysql eq 1) { $checked_mysql ="checked"; }
if ($webalizer eq 1) { $checked_webalizer ="checked"; }
if ($proftp eq 1) { $checked_proftp ="checked"; }
if ($mail ne 0) { $checked_mail ="checked"; }
if ($quota ne 0) { $checked_quota ="checked"; }
if ($webmin eq 1) { $checked_webminuser ="checked"; }


&header();

### the html <FORM> for domainname
print <<EOM;
<script language="JavaScript1.2">
function init() {
moveTo ( 350,300 )   // 1. = waagerecht 2. = hoehe
}

function validate_form()
{
if ((document.formular.email.value.indexOf('\@')==-1) | (document.formular.email.value.indexOf('\.')==-1) )
{ alert ("$text{'s_a_c_emailerror'}");
  document.formular.email.focus();
    return false; }
    }
</script>
<body onload="init()">
<title>send domaininfos</title>
<link rel="stylesheet" type="text/css" href="../style.css">
<form action="s_a_c2.cgi" onSubmit="return validate_form(this)" method="post" name="formular" target="_self">

<table width="100%" height="25" border="0" cellpadding="1" cellspacing="1">
  <tr>
     <td class="bodybg"><font size="1"><b>$text{'s_a_c_title'}</b></font></td>
</tr>
</table>


<table width="100%" border="0" cellspacing="5" cellpadding="0">
                      <tr>
                        <td width="50%"><font size="1">E-Mail</font></td>
                        <td width="50%"><font size="1">
                          <input name="sendmail" type="checkbox" value="1" $checked_mail>
                          </font></td>
                      </tr>
                      <tr>
                        <td width="50%"><font size="1">$text{'index_mysql'}</font></td>
                        <td width="50%"><font size="1">
                          <input name="mysql" type="checkbox" value="1" $checked_mysql>
                          </font></td>
                      </tr>
                      <tr>
                        <td width="50%"><font size="1">$text{'index_webalizer'}</font></td>
                        <td width="50%"><font size="1">
                          <input name="webalizer" type="checkbox" value="1" $checked_webalizer>
                          </font></td>
                      </tr>
                      <tr>
                    <td width="50%"><font size="1">$text{'index_webmin'}</font></td>
            <td width="50%"><font size="1">
            <input name="webminuser" type="checkbox" value="1" $checked_webminuser>
            </font></td>
              </tr>
                      <tr>
                    <td width="50%"><font size="1">$text{'index_quota'}</font></td>
            <td width="50%"><font size="1">
            <input name="send_quota" type="checkbox" value="1" $checked_quota>
                </font></td>
              </tr>




                      <tr>
                        <td width="50%"><font size="1"> </font></td>
                        <td width="50%"><font size="1"> </font></td>
                      </tr>
                    </table>



<input name="email" class="input2" type="text" size="25">
<input type="submit" class="input" name="Submit" value="$text{'s_a_c_send'}">
<input name="pass" type="hidden" value="$in{'pass'}">
<input name="domainname" type="hidden" value="$in{'domainname'}">
<input name="www" type="hidden" value="$in{'www'}">
<input name="mail" type="hidden" value="$mail">
<input name="quota" type="hidden" value="$quota">

</form>
EOM

print "<br>";
