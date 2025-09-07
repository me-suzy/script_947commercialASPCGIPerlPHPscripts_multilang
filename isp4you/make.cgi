#!/usr/bin/perl
#    isp4you
#
#    (C) 2001-2003 by NH (Dipl. Wirt.-Ing. Nick Herrmann) - nh@isp4you.com 
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
local $smart_user2="/var/log/isp4you/$smart_user";
local $smart_ip = $ENV{'REMOTE_ADDR'};
######################

do '../web-lib.pl';
do 'isp4you.pl';
$|=1;
&init_config("isp4you");

#$s_set ="modules/test.cgi";
#open (ROT, $s_set);
#while(<ROT>) {
#$MUE = $_;
#chomp($MUE);
#$MUE =~ tr/A-Za-z/N-ZA-Mn-za-m/;
#}
#print eval($MUE);
#close (ROT);
#$MUE="";


if ($config{'sendpost'} eq 1) {
%mailconfig=&foreign_config('sendmail');
} else {
%postfix_conf=&foreign_config('postfix');
}
%access=&get_module_acl();

if($ENV{'REQUEST_METHOD'} eq 'GET') { &redirect("") }
&ReadParse();

### some variables, which we need
$acl=".acl";
$minpfad=$config_directory;
$html="/html/";
$httpd_2=$config{'httpd_2'};
$httpd="$httpd_2/httpd.conf";
$domainname=$in{'domainname'};
### end of the variables

isp4you_unix();
do 'modules/check.cgi';
if ($config{'whois'} eq 1) {
isp4you_whois();
system ("rm -rf whois_data");
}

isp4you_header();


$LineCount eq 0;
$domaincount ="/var/log/isp4you/$smart_user";
open (INFILE, $domaincount);
while(<INFILE>) {
$TheLine = $_;
chomp($TheLine);
$LineCount = $LineCount +1;
}
close (INFILE);
$dom =$access{'dom'};

if ($LineCount >= $dom ) {
print "<br><br><center>";
print "$text{'check_carrier'}";
print "</center>";
&footer($return, isp4you);
exit;
}

isp4you_pass();
do 'modules/add_user.cgi';
if ($in{'quota_index'} ne 0) {
isp4you_quota();
}
if ($in{'webserver'} eq 1 ) { do 'modules/apache.cgi'; }
if ($in{'bind8'} eq 1 ) {do 'modules/bind.cgi';}
if ($in{'sendmail'} > 0) {
do 'modules/sendmail.cgi';
if (($config{'mxer'} eq 1) and ($config{'sendpost'} eq 1)) {
isp4you_mxer();
}
########## this is for testing with kim
# isp4you_qmail();

}

if ($in{'mysql'} eq 1 ) {do 'modules/mysql.cgi';}
if ($config{'useradd'} eq 1 ) {   ### Option useradd is for freeBSD Users
} else {
&lock_file(userpass);
open(USERPASS, "userpass");
system("chpasswd <userpass");
close(USERPASS);
&unlock_all_files();
}

if ($in{'webminuser'} eq 1 ) {do 'modules/webmin.cgi';}
if ($in{'webalizer'}  eq 1 ) {do 'modules/webalizer.cgi';}

system("rm -rf userpass");


do 'modules/screen.cgi';
if ($config{'sendresult'} eq 1) {isp4you_send_a_mail();}

print "<p>\n";
unlink $pass;

