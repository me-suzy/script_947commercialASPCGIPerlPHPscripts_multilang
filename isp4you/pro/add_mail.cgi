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
$a = $in{'mail2'};    ## linker wert aus datenbank
$z = $in{'mail1'};    ## rechter wert aus datenbank
$b = $a;
$a++;
$c = $in{'sendmail'};

$endmail = $b + $c;

system ("rm -rf userpass");
open(USERPASS,">>userpass");


for($i = $a; $i <= $endmail; $i++)
{
print USERPASS "mail$i.$domainname:$pass\n"
}

### Check for Postfix Users if this is the first mail at all and set the special Postfix variable
if (($config{'sendpost'} eq "0") and ($in{'mail2'} eq "0")) {
$postfix_mailinfo = "\n$domainname VIRTUALDOMAIN";
}

## Dieser Block funzelt fuer Beide Systeme (Postfix und Sendmail)
open(VIRTUSER,">>$config{'virtuser'}");
print VIRTUSER "$postfix_mailinfo\n";

for($i = $a; $i <= $endmail; $i++)
{
print VIRTUSER "mail$i\@$domainname mail$i.$domainuser\n";
}
close(VIRTUSER);
#####################################################################
for($i = $a; $i <= $endmail; $i++)
{
if ($config{'mail_dir'} eq ""){
system("bin/useradd -M -d /dev/null -s /bin/false -g $config{'group'} mail$i.$domainuser");
} else {
system("bin/useradd -M -d $config{'mail_dir'}$domainuser$i -s /bin/false -g $config{'group'} mail$i.$domainuser");
system("mkdir $config{'mail_dir'}$domainuser$i");
system("chown mail$i.$domainuser:mail $config{'mail_dir'}$domainuser$i");
system("chmod 551 $config{'mail_dir'}$domainuser$i");
}


}


open(USERPASS, "userpass");
system("chpasswd <userpass");
close(USERPASS);
########################################################################################




if (($config{'mxer'} eq 1) and ($config{'sendpost'} eq 1)) {
$mxer_conf =$config{'virtuser'};
@mxer_conf = split(/\//, $mxer_conf);
$mxer_conf_2 ="\/$mxer_conf[1]\/$mxer_conf[2]";   # /etc/mail/ for example


#### insert stuff to access
####
open(ACCESS,">>$mxer_conf_2/access");
print ACCESS "\n";
for($i = $a; $i <= $endmail; $i++)
{
print ACCESS "From\:mail$i\@$domainname RELAY\n";
}
close(ACCESS);
}

#### insert stuff to genericstable
####
open(GENERICS,">>$mxer_conf_2/genericstable");
print GENERICS "\n";
for($i = $a; $i <= $endmail; $i++)
{
print GENERICS "From\:mail$i\@$domainname RELAY\n";
}
close(GENERICS);
}


isp4you_mapping();

