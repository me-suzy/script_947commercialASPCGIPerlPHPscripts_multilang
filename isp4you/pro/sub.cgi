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

#####################
local $smart_user = $ENV{'REMOTE_USER'};
local $smart_ip = $ENV{'REMOTE_ADDR'};
######################

do '../../web-lib.pl';
do '../isp4you.pl';
$|=1;
&init_config("isp4you");
&ReadParse();
%access=&get_module_acl();


### COUNT FROM MYSQL DATABASE
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to mysql database');

isp4you_dom_counter();
$dbh->disconnect;


isp4you_header();

if ($config{'checked'} == 1) { $checked="checked"; }
### the htmlfor domainname
print <<EOM;

<link rel="stylesheet" type="text/css" href="../style.css">

<p>
<form ACTION="../make.cgi?subdomain=1&domainname=$in{'domainname'}&webserver=1&virt=$in{'virt'}" METHOD=post>
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
                              <td width="85%"><font color="#00000" size="2" face="Verdana, Arial, Helvetica, sans-serif">$text{'sub_title'}</font></td>
                            </tr>
                          </table>
                          <input name="www" type="text" class="input2" id="www" value="ftp." size="7">
                          <font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>$in{'domainname'}</b></font>
                            <input type="image" src="../images/create.gif" border="0" align="top" width="67" height="19" name="submit">
              </p>
                          </td>
                        <td width="50%" height="25">
<div align="right"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'sub_user'}
                            $smart_user ($LineCount /$left Domains)</font></div></td>
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
                        <td width="50%"><font size="1">$text{'index_ssl'}</font></td>
                        <td width="50%"><font size="1">
                         <input name="ssl" type="checkbox" value="1">
                         </font></td>
                     </tr>

                      <tr>
                        <td width="50%"><font size="1">$text{'sub_nameserver'}</font></td>
                        <td width="50%"><font size="1">
                          <input name="bind" type="checkbox" value="1" $checked>
                          </font></td>
                      </tr>

                       <tr>
                        <td width="50%"><font size="1">MySQL</font></td>
                        <td width="50%"><font size="1">
                          <input name="mysql" type="checkbox" value="1" $checked>
                          </font></td>
                      </tr>


EOM

if ($in{'virt'} eq ""){
print<<BOTTOM;
                      <tr>
                        <td width="50%"><font size="1">$text{'sub_webalizer'}</font></td>
                        <td width="50%"><font size="1">
                          <input name="webalizer" type="checkbox" value="1" $checked>
                          </font></td>
                      </tr>
                      <tr>
                        <td width="50%"><font size="1">$text{'sub_quota'}</font></td>
                        <td width="50%"><font size="1">
                          <input name="quota_index" class="input2" size="5"value="$config{'quota_default'}">
                         </font></td>
                      </tr>
BOTTOM
}
print<<DOWN;
                      <tr>
                        <td width="50%"><font size="1">&nbsp;</font></td>
                        <td width="50%"><font size="1">&nbsp;</font></td>
                      </tr>
                    </table>
                    <p>&nbsp;</p> </td>
                  <td width="50%" valign="top">
<div align="center"></div></td>
                </tr>
              </table>
              <p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
                [<a href="../list.cgi" target="_self">$text{'index_list_domains'}</a>]</font></p>
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

DOWN
print "<br>";
