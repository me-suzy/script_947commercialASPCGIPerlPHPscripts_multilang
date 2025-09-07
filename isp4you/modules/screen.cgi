#    isp4you
#
#    (C) 2001-2003 by NH (Dipl. Wirt.-Ing. Nick Herrmann) - nh@ingenieurbuero-herrmann.de
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#
#    dutch translation by Philip HW Schroth - philip@schroth.nl and Frans Ronner - webmaster@fransonline.nl
#    spanish translation by Matias Carrasco - matias.carrasco@silice.biz
#    turkish translation by Tanju Ergan - admin@ergan.net
#    french translation by Pierre Gulliver - gulliverpierre@hotmail.com
#
#    All rights reserved worldwide

$LineCounter = $LineCount+1;
$left = $access{'dom'} - $LineCount;
$left--;

print <<EOM;

<link rel="stylesheet" type="text/css" href="style.css">

<script language="javascript" src="print.js" type="text/javascript"></script>

$ap_nok
<p>
<table width="650" border="0" align="center" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      <table width="650" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td bgcolor="#000000"> <table width="650" border="0" cellpadding="2" cellspacing="1">
              <tr>
                  <td height="50" valign="top" class="back_top" bgcolor="#C0BEFF">
<table width="100%" border="0" height="64" cellspacing="3" cellpadding="0">
                      <tr>
                      <td width="70%" height="50" rowspan="2" valign="top"> <font color="#00000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>$text{'outputscreen_domain'}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
                        <td width="30%" height="25">
<div align="right"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'index_user'}
                            $smart_user ($LineCounter /$left Domains)</font></div></td>
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

if ($in{'ssl'} eq 1) {

print <<CERT;

<form action="modules/screen-ssl.cgi?ssl=1&pass=$pass&webserver=1&domainname=$domainname&domainuser=$domainname&www=$in{'www'}" method="post" target="_self"><br>
<table width="95%" border="0" cellspacing="1" cellpadding="1" align="center">
    <tr bgcolor="#E2E2E2">
      <td class="bodybg" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>SSL
        Certificate Information</strong></font></td>
      <td class="bodybg" width="73%" height="25">&nbsp;</td>
    </tr>
    <tr bgcolor="#E2E2E2">
      <td class="bodybglight" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">Authority
        Name</font></td>
      <td class="bodybglight" width="73%" height="25"><input name="authority" type="text" class="input2" size="25" value="$in{'www'}$in{'domainname'}"> </td>
    </tr>
    <tr bgcolor="#E2E2E2">
      <td class="bodybg" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">E-Mail
        Address</font></td>
      <td class="bodybg" width="73%" height="25"><input name="email" type="text" class="input2" value="$config{'adminmail'}" size="25"></td>
    </tr>
    <tr bgcolor="#E2E2E2">
      <td class="bodybglight" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">Organization</font></td>
      <td class="bodybglight" width="73%" height="25"><input name="organization" type="text" class="input2" size="25"></td>
    </tr>
    <tr bgcolor="#E2E2E2">
      <td class="bodybg" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">Department</font></td>
      <td class="bodybg" width="73%" height="25"><input name="departement" type="text" class="input2" size="25"></td>
    </tr>
    <tr bgcolor="#E2E2E2">
      <td class="bodybglight" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">City</font></td>
      <td class="bodybglight" width="73%" height="25"><input name="city" type="text" class="input2" size="25"></td>
    </tr>
    <tr bgcolor="#E2E2E2">
      <td class="bodybg" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">State
        or Province</font></td>
      <td class="bodybg" width="73%" height="25"><input name="state" type="text" class="input2" size="25"></td>
    </tr>
    <tr bgcolor="#E2E2E2">
      <td class="bodybglight" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">Country</font></td>
      <td class="bodybglight" width="73%" height="25"><input name="country" type="text" class="input2" size="5" maxlength="2"></td>
    </tr>
    <tr bgcolor="#E2E2E2">
      <td class="bodybg" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td class="bodybg" width="73%" height="25"><input name="submit" class="input" type="submit" value="create certificate !"></td>
    </tr>
    <tr bgcolor="#E2E2E2">
      <td class="bodybglight" width="27%" height="25"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td class="bodybglight" width="73%" height="25">&nbsp;</td>
    </tr>
  </table>
</form>
CERT

}

print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">";

if($in{'webserver'} eq 1) {
print "<br><p>\n";
print "&nbsp;&nbsp;$text{'thedomain'} <b>$in{'www'}$domainname</b> $text{'made'} <p>\n";
### Subdomainausgabe
print "&nbsp;&nbsp;$text{'mail_loginname'}: $domainuser<br>\n";
if ($config{'useradd'} eq 1){
print "&nbsp;&nbsp;Password: The password ist shown on Top.<p>";
} else {
print "&nbsp;&nbsp;$text{'domain_password'}: $pass<p>";
}
print "&nbsp;&nbsp;$text{'pfad'} $config{'webpfad'}$in{'www'}$domainname$html<p>\n";
}


for($i = 1; $i <= $in{'sendmail'}; $i +=1)
{
print "&nbsp;&nbsp;$text{'mail_email'}: <b> mail$i\@$domainname</b><br>\n";
if ($config{'unixsave'} eq 1) {
print "&nbsp;&nbsp;$text{'outputscreen_pop3'}: mail$i\_$domainuser<br>\n";
} else {
print "&nbsp;&nbsp;$text{'outputscreen_pop3'}: mail$i.$domainuser<br>\n";
}
print "&nbsp;&nbsp;$text{'mail_password'}: $pass<p>\n";
}

if ($in{'bind8'} eq 1) {
print "&nbsp;&nbsp;$text{'outputscreen_nameserver'}<br><br>\n\n";
}

if ($in{'mysql'} eq 1) {
$domain_mysql_output = $domainuser;
$domain_mysql_output =~ tr/./_/;
$domain_mysql_output =~ tr/-/_/;

$domain_mysql_output_user = $domain_mysql_output;
$mysql_len = length($domain_mysql_output);
if ($mysql_len > 16) {
$domain_mysql_output_user = substr("$domain_mysql_output", 0, 16);
}

print "&nbsp;&nbsp;$text{'outputscreen_mysql_databasename'} <b>$domain_mysql_output</b><br>\n";
print "&nbsp;&nbsp;$text{'outputscreen_database'} $domain_mysql_output_user<br>\n";
print "&nbsp;&nbsp;$text{'outputscreen_mysql_databasepass'} $pass<br><br>\n\n";

}
if ($in{'webalizer'} eq 1) {
print "&nbsp;&nbsp;$text{'outputscreen_webalizer'}<br>\n";
print "&nbsp;&nbsp;$text{'outputscreen_URL'} <b>http://$in{'www'}$domainname/WEBSTATS</b><br>\n";
print "&nbsp;&nbsp;$text{'mail_loginname'} = $domainuser<br>\n";
print "&nbsp;&nbsp;$text{'domain_password'} = $pass<br><br>";
}

if ($in{'quota_index'} ne 0) {
print "&nbsp;&nbsp;$text{'outputscreen_quota_1'}<br>";
print "&nbsp;&nbsp;$text{'outputscreen_quota_2'} $in{'quota_index'} MB<br><br>";
} else {
print "&nbsp;&nbsp;$text{'outputscreen_quota_2'} unlimeted MB<br>";
}

if ($in{'webminuser'} eq 1) {
print "&nbsp;&nbsp;$text{'outputscreen_webminuser'}<br>\n";
print "&nbsp;&nbsp;$text{'mail_loginname'} = $domainuser<br>\n";
print "&nbsp;&nbsp;$text{'domain_password'} = $pass<p><br>";
}

print "</font>";

if ($in{'subdomain'} eq 1) {
$s_a_c = "$in{'www'}$in{'domainname'}";
} else {
$s_a_c = "$in{'domainname'}";
}

print <<BOTTOM;

            <p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
                [<a href="index.cgi" target="_self">Home</a>] [<a href="javascript:tmt_print()">print domaininfos</a>] [<a href="javascript:fenster_auf('modules/s_a_c.cgi?domainname=$s_a_c&loginname=$domainname&pass=$pass&www=$in{'www'}','','resizable=no,scrollbars=auto,toolbar=no,width=350,height=250')">$text{'s_a_c_link'}</a>]<br><br></font></p>
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

BOTTOM

