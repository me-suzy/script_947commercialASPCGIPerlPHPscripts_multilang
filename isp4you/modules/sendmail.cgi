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
#    french translation by Pierre Gulliver - gulliverpierre@hotmail.com
#
#    All rights reserved worldwide

if ($config{'sendpost'} eq 1) {
#&lock_file($mailconfig{'sendmail_cf'});
open(MAIL,">>$mailconfig{'sendmail_cf'}");
print MAIL "Cw$domainname\n";
close(MAIL);
} else {
## Hier kommt die domain in die main.cf wenn postfix gewaehlt wurde
$post_main=$postfix_conf{'postfix_config_file'};
$lref = &read_file_lines("$post_main");
for (@$lref)
{
if (/^relay_domains/) {
splice(@$lref, $Pos, 1, ("@$lref[$Pos]\, $in{'domainname'}"));
$Pos ="99999999999";
}
$Pos ++;
}
&flush_file_lines();
}

&lock_file(userpass);
open(USERPASS,">>userpass");
for($i = 1; $i <= $in{'sendmail'}; $i++)
{
print USERPASS "mail$i.$domainname:$pass\n";
}

for($i = 1; $i <= $in{'sendmail'}; $i++) {
if ($config{'mail_dir'} eq ""){
system("bin/useradd -M -d /dev/null -s /bin/false -g $config{'group'} mail$i.$domainuser");
} else {
system("bin/useradd -M -d $config{'mail_dir'}$domainuser$i -s /bin/false -g $config{'group'} mail$i.$domainuser");
system("mkdir $config{'mail_dir'}$domainuser$i");
system("chown mail$i.$domainuser:mail $config{'mail_dir'}$domainuser$i");
system("chmod 551 $config{'mail_dir'}$domainuser$i");
}

# Hardcore mailcreators should become a message on the screen that we are still working
if (($i eq 51) or ($i eq 100) or ($i eq 150) or ($i eq 200) or ($i eq 300)) { print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'sendmail_created_1'} $i $text{'sendmail_created_2'}</font><br>"; }
if (($i eq 400) or ($i eq 500) or ($i eq 600)) { print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'sendmail_created_1'} $i $text{'sendmail_created_2'}</font><br>"; }
if (($i eq 700) or ($i eq 800) or ($i eq 900)) { print "<font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'sendmail_created_1'} $i $text{'sendmail_created_2'}</font><br>"; }
} ## for i mail

if ($config{'sendpost'} eq 1) {
&lock_file($config{'virtuser'});
open(VIRTUSER,">>$config{'virtuser'}");
print VIRTUSER "\n";
for($i = 1; $i <= $in{'sendmail'}; $i++)
{
print VIRTUSER "mail$i\@$domainname mail$i.$domainuser\n";
}
close(VIRTUSER);
}


if ($config{'sendpost'} eq 0) {
&lock_file($config{'virtuser'});
open(VIRTUSER,">>$config{'virtuser'}");
print VIRTUSER "\n";
print VIRTUSER "$domainname   VIRTUALDOMAIN\n";
for($i = 1; $i <= $in{'sendmail'}; $i++)
{
print VIRTUSER "mail$i\@$domainname mail$i.$domainuser\n";
}
close(VIRTUSER);
}
close(USERPASS);
&unlock_all_files();


## Mapp the new aliases
isp4you_mapping();

