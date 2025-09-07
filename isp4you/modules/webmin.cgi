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


%webminaccess = &get_module_acl(undef, 'acl');
&foreign_require('acl', 'acl-lib.pl');
%webminconfig = &foreign_config('acl');

$user{'name'} = $domainuser;
$salt = chr(int(rand(26))+65).chr(int(rand(26))+65);
$user{'pass'} = crypt($pass, $salt);
$user{'sync'} = 0;
&foreign_call('acl', 'create_user', \%user);

if ($config{'sendpost'} eq 1) {
$module_sendmail="sendmail";
}
$module_mysql="mysql";
$module_passwd="passwd";
$module_file="file";
$module_dfwm="dfwm";


$domainmysql=$domainname;
$domainmysql =~ tr/./_/;
$domainmysql =~ tr/-/_/;


system("touch $minpfad/$domainuser$acl $minpfad/acl/$domainuser$acl");

open(WEBMIN,">>$minpfad/webmin.acl");
print WEBMIN "$domainuser: $module_sendmail $module_mysql $module_passwd $module_file $module_dfwm\n";
close(WEBMIN);

open(ACL0,">>$minpfad/$domainuser$acl");
print ACL0 "gedit_mode=2\n";
print ACL0 "uedit=$domainname\n";
print ACL0 "noconfig=1\n";
if ($config{'sub'} == 1) {
print ACL0 "root=$config{'webpfad'}$in{'www'}$domainname\n";
}
if ($config{'sub'} == 0) {
print ACL0 "root=$config{'webpfad'}$domainname\n";
}
print ACL0 "gedit=users\n";
print ACL0 "uedit2=\n";
print ACL0 "gedit2=\n";
print ACL0 "uedit_mode=2\n";
close(ACL0);


open(ACL1,">>$minpfad/acl/$domainuser$acl");
print ACL1 "groups=1\n";
print ACL1 "acl=1\n";
print ACL1 "lang=1\n";
print ACL1 "users=*\n";
print ACL1 "delete=1\n";
print ACL1 "create=1\n";
print ACL1 "others=1\n";
print ACL1 "mode=0\n";
print ACL1 "perms=0\n";
print ACL1 "rename=1\n";
print ACL1 "chcert=1\n";
print ACL1 "gassign=*\n";
print ACL1 "cert=$cert\n";
close(ACL1);

system("touch $minpfad/file/$domainuser$acl");

open(ACL5,">>$minpfad/file/$domainuser$acl");
print ACL5 "home=\n";
print ACL5 "unmask=022\n";
print ACL5 "uid=-1\n";
print ACL5 "follow=0\n";
print ACL5 "noconfig=1\n";
print ACL5 "root=$config{'webpfad'}$in{'www'}$domainname\n";
close(ACL5);

system("touch $minpfad/mysql/$domainuser$acl");

open(ACL6,">>$minpfad/mysql/$domainuser$acl");
print ACL6 "dbs=$domainmysql\n";
print ACL6 "buser=$domainname\n";
print ACL6 "edonly=0\n";
print ACL6 "delete=0\n";
print ACL6 "noconfig=1\n";
print ACL6 "create=0\n";
print ACL6 "stop=0\n";
if ($config{'sub'} == 1 ) {
print ACL6 "bpath=$config{'webpfad'}$in{'www'}$domainname\n";
}
if ($config{'sub'} == 0 ) {
print ACL6 "bpath=$config{'webpfad'}$domainname\n";
}
print ACL6 "perms=0\n";
close(ACL6);


system("touch $minpfad/passwd/$domainuser$acl");

open(ACL7,">>$minpfad/passwd/$domainuser$acl");
print ACL7 "high=\n";
print ACL7 "repeat=1\n";
print ACL7 "low=\n";
if ($config{'unixsave'} eq 1 ) {
print ACL7 "users=$domainname mail1\_$domainuser mail2\_$domainuser mail3\_$domainuser\n";
} else {
print ACL7 "users=$domainname mail1.$domainname mail2.$domainname mail3.$domainname\n";
}
print ACL7 "noconfig=1\n";
print ACL7 "others=1\n";
print ACL7 "mode=1\n";
print ACL7 "old=1\n";
close(ACL7);


system("touch $minpfad/sendmail/$domainuser$acl $minpfad/$sendmail2/$domainuser\.index");


if ($config{'sendpost'} eq 1) {
open(ACL8,">>$minpfad/sendmail/$domainuser$acl");
print ACL8 "vaddrs=\@$domainname\n";
print ACL8 "stop=0\n";
print ACL8 "canattach=0\n";
print ACL8 "vedit_0=\n";
print ACL8 "vedit_1=\n";
print ACL8 "vedit_2=1\n";
print ACL8 "mailers=0\n";
print ACL8 "apath=$pop$domainname$pop1\n";
print ACL8 "access=0\n";
print ACL8 "noconfig=1\n";
print ACL8 "from=$domainname\n";
print ACL8 "fromname=\n";
print ACL8 "trusts=0\n";
print ACL8 "sent=\n";
print ACL8 "vmax=\n";
print ACL8 "oaddrs=\n";
print ACL8 "relay=0\n";
print ACL8 "cgs=0\n";
print ACL8 "musers=\n";
print ACL8 "aliases=\@$domainname\n";
print ACL8 "opts=0\n";
print ACL8 "amode=2\n";
print ACL8 "amax=\n";
print ACL8 "boxname=0\n";
print ACL8 "domains=0\n";
print ACL8 "fmode=1\n";
print ACL8 "cws=0\n";
print ACL8 "mayq=0\n";
print ACL8 "mmode=4\n";
print ACL8 "mailq=0\n";
print ACL8 "omode=0\n";
print ACL8 "attach=10000\n";
print ACL8 "aedit_1=1\n";
print ACL8 "aedit_2=\n";
print ACL8 "aedit_3=\n";
print ACL8 "aedit_4=\n";
print ACL8 "aedit_5=1\n";
print ACL8 "vmode=2\n";
print ACL8 "aedit_6=\n";
print ACL8 "masq=0\n";
close(ACL8);
}

#if ($config{'conf_dfwm'} == 1 ) {
system("touch $minpfad/dfwm/$domainuser$acl");
open(DFWM,">>$minpfad/dfwm/$domainuser$acl");

if ($config{'sub'} == 1 ) {
if ($config{'subfolder'} == 0 ){
print DFWM "fdoms=$in{'www'}$domainname;$config{'webpfad'}$in{'www'}$domainname\n";
}
if ($config{'subfolder'} == 1 ){
print DFWM "fdoms=$in{'www'}$domainname;$config{'webpfad'}$in{'www'}$domainname/html\n";
}
}
if ($config{'sub'} == 0 ) {
if ($config{'subfolder'} == 0 ){
print DFWM "fdoms=$domainname;$config{'webpfad'}$domainname\n";
}
if ($config{'subfolder'} == 1){
print DFWM "fdoms=$domainname;$config{'webpfad'}$domainname/html\n";
}
}

print DFWM "noconfig=0\n";
close(DFWM);
### }


open(TAB,">>$minpfad/config");
print TAB "notabs_$domainuser=2\n";
close(TAB);

&restart_miniserv();
