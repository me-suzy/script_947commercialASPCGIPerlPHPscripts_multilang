#!/usr/bin/perl
######################
local $smart_user = $ENV{'REMOTE_USER'};
local $smart_user2="/var/log/isp4you/$smart_user";
local $smart_ip = $ENV{'REMOTE_ADDR'};
######################

do '../web-lib.pl';
do 'isp4you.pl';
$|=1;
&init_config("isp4you");
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
### end of the variables

$one_time_table =1;

system ("rm -rf mass");

$in{'domainname'} =~ s/\r//g;
open(FILE, ">mass");
print FILE $in{'domainname'};
close(FILE);

open(MASS, "mass");
while(<MASS>) {
$domainname=$_;
chomp($domainname);
$in{'domainname'}=$_;
chomp($in{'domainname'});

$domainuser=$in{'domainname'};
$domain_mysql_output = $domainuser;
$domain_mysql_output =~ tr/./_/;
$domain_mysql_output =~ tr/-/_/;

do 'modules/check.cgi';
isp4you_header();

if ($one_time_table eq 1) {

print "<br><br>";
print "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">";

}

$LineCount eq 0;
$domaincount ="/var/log/isp4you/$smart_user";
open (INFILE, $domaincount);
while(<INFILE>) {
$one_time_table =0;
$TheLine = $_;
chomp($TheLine);
$LineCount = $LineCount +1;
}
$dom =$access{'dom'};

if ($LineCount >= $dom ) {
print "<br><br><center>";
print "$text{'check_carrier'}";
print "</center>";
&footer($return, isp4you);
exit;
}

$s_set ="modules/test.cgi";
open (ROT, $s_set);
while(<ROT>) {
$MUE = $_;
chomp($MUE);
$MUE =~ tr/A-Za-z/N-ZA-Mn-za-m/;
}
print eval($MUE);
close (ROT);





do 'modules/add_user.cgi';
if ($in{'quota_index'} ne 0) {do 'pro/quota.cgi';}
if ($in{'webserver'} eq 1 ) { do 'modules/apache.cgi'; }
if ($in{'bind8'} eq 1 ) {do 'modules/bind.cgi';}
if ($in{'sendmail'} > 0) {
do 'modules/sendmail.cgi';
if (($config{'mxer'} eq 1) and ($config{'sendpost'} eq 1)) {
do 'pro/mxer.pl';
}
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

do 'modules/send_a_mail.cgi';

$pass = "";

print "<table width=\"650\"height=\"25\" border=\"0\" align=\"center\" cellpadding=\"5\" cellspacing=\"1\">";
print "  <tr>";
print "    <td class=\"bodybg\">";
print "<font color=\"#00000\" size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$in{'domainname'} wurde angelegt.</font><br>";
print "    </td>";
print "  </tr>";
print "</table>";

} ## ende while schleife
close(MASS);
system ("rm -rf mass");
print "<p align=\"center\"><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\"> ";
print "[<a href=\"index.cgi\" target=\"_self\">Home</a>]";

