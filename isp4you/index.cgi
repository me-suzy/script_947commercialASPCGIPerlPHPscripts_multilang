#!/usr/bin/perl
#    isp4you
#
#    (C) 2001-2003 by NH (Dipl.Wirt-Ing. Nick Herrmann) - nh@isp4you.com
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
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
local $smart_server = $ENV{'SERVER_ADDR'};
######################

do '../web-lib.pl';
do 'isp4you.pl';
$|=1;
&init_config("isp4you");
%apacheconfig=&foreign_config('apache');
%mysqlconfig=&foreign_config('mysql');

if ($config{'adminmail'} ne "") {$marked ="checked";}

isp4you_header();

$LineCount eq 0;
$domaincount ="/var/log/isp4you/$smart_user";
open (INFILE, $domaincount);
while(<INFILE>) {
chomp($_);
$LineCount = $LineCount +1;
}
close(INFILE);
if ($LineCount eq "") {
$LineCount = "0";
}

### Hostname ermitteln
system ("hostname -i >hostn");
open (A, "hostn");
while(<A>) {
$hostn = $_;
chomp($hostn);
}
close (A);
@hostns = split(/ /, $hostn);
$hostn = $hostns[0];
system ("rm -rf hostn");

### set the varibale - check if /etc/webmin/isp4you/config is present
if ($config{'ip'} ne "") {
$hostn = $config{'ip'};
}

if ($config{'webpfad'} ne "") {
$webpfad = $config{'webpfad'}
} else {
$webpfad ="/home/httpd/";
}

if ($config{'httpd_2'} ne "") {
$httpd_path = $config{'httpd_2'};
} else {
$httpd_path = "$apacheconfig{'httpd_dir'}/conf/";
}

### the html <FORM> for First Run
print <<TOP;

<link rel="stylesheet" type="text/css" href="style.css">

<script language="javascript">
function validate_form()
{

if (document.formular.adminmail.value.indexOf('\.')==-1)
{ alert ("Please fill in a valid e-mail address.");
  document.formular.adminmail.focus();
  return false; }

  if (document.formular.adminmail.value.indexOf('\@')==-1)
{ alert ("Please fill in a valid e-mail address.");
  document.formular.adminmail.focus();
  return false; }

if (document.formular.ip.value.length <8)
{ alert("The IP address is to short.");
  document.formular.ip.focus();
  return false; }

if (document.formular.ip.value.length >15)
{ alert("The IP address is to long.");
  document.formular.ip.focus();
  return false; }

if (document.formular.ip.value.indexOf('\.')==-1)
{ alert ("Please fill in a valid IP address.");
  document.formular.ip.focus();
  return false; }

   if (document.formular.dbi_check.checked == false )
{ alert ("You must have DBI installed on your system.");
   return false; }

   if (document.formular.thermes.checked == false )
{ alert ("You must accept the termes and conditions of the software isp4you.");
   return false; }


}
</script>

<p>
<form ACTION="index2.cgi" METHOD="post" name="formular" target="_self" onSubmit="return validate_form(this)">
<table width="650" border="0" align="center" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      <table width="650" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td bgcolor="#000000"> <table width="650" border="0" cellpadding="2" cellspacing="1">
              <tr>
                  <td height="50" valign="top" class="back_top" bgcolor="#cccccc">
<table width="100%" height="64" border="0" cellspacing="3" cellpadding="0">
                      <tr>
                        <td width="50%" height="50" rowspan="2" valign="top">
            <img src="$text{'firstscreen_inst'}" width="164" height="33">
                         <br>
                        </td>
                        <td width="50%" height="25">
<div align="right"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">User:
                            $smart_user ($LineCount Domains)</font></div></td>
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
            <td height="300" align="middle" valign="top"> <p><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
                <br>
                &nbsp;<font color="#000000">$text{'firstscreen_0_a'}<br>
                &nbsp;<font color="#000000">$text{'firstscreen_0_c'} </font></font></p>
              <table width="95%" border="0" cellspacing="1" cellpadding="1">

TOP
if ($config{'ip'} ne "") {
print <<EOF;
                <tr valign="top" class="text" bgcolor="#E2E2E2">
                  <td class="bodybg" width="69%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;$text{'firstscreen_update'}</font></td>
                  <td class="bodybg" width="31%" height="25" valign="middle"><div align="left"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">Update:</font>
                      <font color="#000000">
                      <input type="checkbox" name="update" value="1" $marked>
                      </font></div></td>
                </tr>

EOF
}
print <<XYZ;

                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybglight" width="69%" height="25" valign="middle"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">1.
                    &nbsp;$text{'firstscreen_1'}</font></td>
                  <td class="bodybglight" width="31%" height="25"> <div align="left"> <font color="#000000">
                      <input type="text" class="input2" name="webpfad" value="$webpfad" size="25">
XYZ
print &file_chooser_button("webpfad", 0, 0);
print <<TIF;
                      </font></div></td>
                </tr>

                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybg" width="69%" height="25" valign="middle" ><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">2.
                    &nbsp;$text{'firstscreen_2'}</font></td>
                  <td class="bodybg" width="31%" height="25"> <div align="left"> <font color="#000000">
                      <input type="text" class="input2" name="httpd" value="$httpd_path" size="25" >
TIF
print &file_chooser_button("httpd", 0, 0);
print <<EOM;
                      </font></div></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybglight" width="69%" height="25" valign="middle"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">3.
                    &nbsp;$text{'firstscreen_3'}</font></td>
                  <td class="bodybglight" width="31%" height="25"> <div align="left"> <font color="#000000">
                      <input type="text" class="input2" name="ip" value="$hostn" size="25" >
                      </font></div></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybg" width="69%" height="25" valign="middle"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">4.
                    &nbsp;$text{'firstscreen_4'}</font></td>
                  <td class="bodybg" width="31%" height="25"> <div align="left"> <font color="#000000">
                      <input type="text" class="input2" name="adminmail" size="25" value="$config{'adminmail'}">
                      </font></div></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybg" width="69%" height="25" valign="middle"><p align="left"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">5.
                      &nbsp;$text{'firstscreen_6'}<br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$text{'firstscreen_6_b'} <a href="../cpan/" target="_self">DBI DBD::mysql</a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$text{'firstscreen_6_c'} <a href="../software/search.cgi?search=DBI" target="_self">DBI</a> - <a href="../software/search.cgi?search=DBD" target="_self">DBD::mysql</a>
                      </font></td>
                  <td class="bodybg" width="31%" height="25"> <div align="left"> <font color="#000000">
EOM

            print"          <input type=\"checkbox\" name=\"dbi_check\" value=\"1\" $marked>";
print <<BOTTOM;

                      </select>
                      </font></div></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybglight" height="25" valign="middle" ><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">6.&nbsp;&nbsp;$text{'firstscreen_agb'}</font></td>
                  <td class="bodybglight" height="25"><font color="#000000">
                    <input type="checkbox" name="thermes" value="1">
                    </font></td>
                </tr>
                <tr valign="top" bgcolor="#E2E2E2">
                  <td class="bodybg" width="69%" height="30">&nbsp;</td>
                  <td class="bodybg" width="31%" height="30" valign="middle"> <div align="left">
                <input type="image" src="images/create.gif" border="0" align="top" width="67" height="19" name="submit">
                    </div></td>
                </tr>
              </table>
              <font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
              </font></td>
          <td bgcolor="#000000" width="1"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#000000"><img src="images/spacer.gif" width="1" height="1" border="0"></td>
        </tr>
      </table>

    </td>
  </tr>
</table>

<input type="hidden" name="mysql_user" value="$mysqlconfig{'login'}">
<input type="hidden" name="mysql_pass" value="$mysqlconfig{'pass'}">
<input type="hidden" name="os_system" value="0">

BOTTOM
print"</form>";



