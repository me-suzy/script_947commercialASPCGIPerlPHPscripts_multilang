#!/usr/bin/perl
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

######################
local $smart_user = $ENV{'REMOTE_USER'};
local $smart_ip = $ENV{'REMOTE_ADDR'};
######################

do '../../web-lib.pl';
do '../isp4you.pl';
$|=1;
&init_config("isp4you");

%access=&get_module_acl();

if($ENV{'REQUEST_METHOD'} eq 'GET') { &redirect("") }
&ReadParse();


$LineCount eq 0;
$domaincount ="/var/log/isp4you/$smart_user";
open (INFILE, $domaincount);
while(<INFILE>) {
$TheLine = $_;
chomp($TheLine);
$LineCount = $LineCount +1;
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
                  <td height="50" valign="top" class="back_top" bgcolor="#C0BEFF">
<table width="100%" border="0" cellspacing="3" cellpadding="0">
                      <tr>

                      <td width="70%" height="50" rowspan="2" valign="top"> <font color="#00000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
                        <td width="30%" height="25">
<div align="right"><font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">$text{'index_user'}
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

          <td height="350" valign="top">


EOM

if ($in{'ssl'} eq 1) {

print "<br><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">";
print " <b>&nbsp;&nbsp;$text{'ssl_created_1'}</b></font>";



 ### Setup our destinations.
 ### der Pfad zur dem Homeverzeichnis des SSL Hostes, dann in der Ordner ssl gehen


 $server_cert = "$config{'webpfad'}$in{'www'}$in{'domainname'}/ssl/certificate.pem";
 $server_key  = "$config{'webpfad'}$in{'www'}$in{'domainname'}/ssl/key.pem";


  ### Setup temporary files.
  $cert_temp = &tempname();
  $key_temp = &tempname();

  # pipe to ssl
    open CA, "| $config{'openssl'} req -new -x509 -days 1460 -nodes -out $cert_temp -keyout $key_temp -config openssl.cnf > /dev/null 2>&1";
    print CA "$in{'country'}\n";
    print CA "$in{'state'}\n";
    print CA "$in{'city'}\n";
    print CA "$in{'departement'}\n";
    print CA "$in{'organization'}\n";
    print CA "$in{'authority'}\n";
    print CA "$in{'emailAddress'}\n";
    close CA;


  ### If they exist, move the two files into place.
  if (-r $cert_temp && -r $key_temp) {
    system "mv -f $cert_temp $server_cert";
    system "mv -f $key_temp $server_key";
    system "chmod 400 $server_cert";
    system "chmod 400 $server_key";
  }

  unlink $cert_temp, $key_temp;


}

print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">";

if($in{'webserver'} eq 1) {
print "<p>\n";
print "&nbsp;&nbsp;$text{'thedomain'} <b>$in{'www'}$in{'domainname'}</b> $text{'made'} <p>\n";
print "&nbsp;&nbsp;$text{'mail_loginname'}: $in{'domainuser'}<br>\n";
if ($config{'useradd'} eq 1){
print "&nbsp;&nbsp;Password: The password ist shown on Top.<p>";
} else {
print "&nbsp;&nbsp;$text{'domain_password'}: $in{'pass'}<p>";
}

if ($config{'sub'} eq 1 ) {
if($config{'subfolder'} eq 1) {
print "&nbsp;&nbsp;$text{'pfad'} $config{'webpfad'}$in{'www'}$in{'domainname'}$confgi{'html'}<p>\n";
   }
   if($config{'subfolder'} eq 0) {
   print "&nbsp;&nbsp;$text{'pfad'} $config{'webpfad'}$in{'www'}$in{'domainname'}<p>\n";
      }
      }
      if ($config{'sub'} eq 0 ) {
      if($config{'subfolder'} eq 1) {
      print "&nbsp;&nbsp;$text{'pfad'} $config{'webpfad'}$in{'domainname'}$config{'html'}<p>\n";;
         }
     if($config{'subfolder'} eq 0) {
     print "&nbsp;&nbsp;$text{'pfad'} $config{'webpfad'}$in{'domainname'}<p>\n";
}
}
}


for($i = 1; $i <= $in{'sendmail'}; $i +=1)
{
print "&nbsp;&nbsp;$text{'mail_email'}: <b> mail$i\@$in{'domainname'}</b><br>\n";
if ($config{'unixsave'} eq 1) {
print "&nbsp;&nbsp;$text{'outputscreen_pop3'} mail$i\_$in{'domainuser'}<br>\n";
} else {
print "&nbsp;&nbsp;$text{'outputscreen_pop3'} mail$i.$in{'domainuser'}<br>\n";
}
print "&nbsp;&nbsp;$text{'mail_password'}: $in{'pass'}<p>\n";
}

if ($in{'mysql'} eq 1) {
print "&nbsp;&nbsp;$text{'outputscreen_mysql_databasename'} <b>$in{'domainuser'}</b><br>\n";
print "&nbsp;&nbsp;$text{'outputscreen_database'} $in{'domainuser'}<br>\n";
print "&nbsp;&nbsp;$text{'outputscreen_mysql_databasepass'} $in{'pass'}<br><br>\n\n";

}
if ($in{'webalizer'} eq 1) {
print "&nbsp;&nbsp;$text{'outputscreen_webalizer'}<br>\n";
print "&nbsp;&nbsp;$text{'outputscreen_URL'} <b>http://$in{'www'}$in{'domainname'}/WEBSTATS</b><br>\n";
print "&nbsp;&nbsp;$text{'mail_loginname'} = $in{'domainuser'}<br>\n";
print "&nbsp;&nbsp;$text{'domain_password'} = $in{'pass'}<br><br>";
}

if ($in{'quota_index'} ne 0) {
$mail_quota = ($in{'quota_index'} / 1024);
print "&nbsp;&nbsp;$text{'outputscreen_quota_1'}<br>";
if ($in{'quota_index'} eq 0) {
print "&nbsp;&nbsp;$text{'outputscreen_quota_2'} unlimeted MB<br><br>";
} else {
print "&nbsp;&nbsp;$text{'outputscreen_quota_2'} $mail_quota MB<br><br>";
}
}



if ($in{'webminuser'} eq 1) {
print "&nbsp;&nbsp;$text{'outputscreen_webminuser'}<br>\n";
print "&nbsp;&nbsp;$text{'mail_loginname'} = $in{'domainuser'}<br>\n";
print "&nbsp;&nbsp;$text{'domain_password'} = $in{'pass'}<p><br>";
}



print "</font>";

if ($gconfig{'real_os_type'} eq "SuSE Linux") {
$reloader = "apache";
} else {
$reloader = "httpd";
}
#### reload the apache, if the sysntax is ok
isp4you_apache_reload();






print <<BOTTOM;

            <p align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
                [<a href="../index.cgi" target="_self">Home</a>] [<a href="javascript:tmt_print()">print domaininfos</a>] [<a href="javascript:fenster_auf('s_a_c.cgi?domainname=$s_a_c&loginname=$domainname&pass=$pass&www=$in{'www'}','','resizable=no,scrollbars=auto,toolbar=no,width=300,height=250')">$text{'s_a_c_link'}</a>]<br><br></font></p>
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

