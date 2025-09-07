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
######################
do '../web-lib.pl';
do 'isp4you.pl';

$|=1;
&init_config("isp4you");
%access=&get_module_acl();
%globalacess=&get_module_acl(undef, 'isp4you');
%bind8config=&foreign_config('bind8');
%mailconfig=&foreign_config('sendmail');    ## Dont delete , we need it to delete the CWdomain.org stuff from sendmail.cf
$mailpfad = $mailconfig{'sendmail_cf'};     ## either , look one line above...
%postconfig=&foreign_config('postfix');    ## for later comming postfix deletion config information
&ReadParse();

$domainname=$in{'domainen.selected'}; $domainuser=$in{'domainen.selected'}; $domainmysql=$in{'domainen.selected'};
$domainmysql =~ tr/./_/; $domainmysql =~ tr/-/_/;

$domainmysql_user = $domainmysql;
$mysql_len = length($domainmysql);
if ($mysql_len > 16) {
$domainmysql_user = substr("$domainmysql", 0, 16);
}



### Mysql Stuff for Mysql Database Users
$minpfad=$config_directory;
$httpd_2=$config{'httpd_2'};
$httpd = "$config{'httpd_2'}httpd.conf";
$httpd_3=".conf";
$virtuser=$config{'virtuser'};
$named=$config{'named_conf'};


### grep domaininformation from isp4you/domaininfos
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0  }) || &error('could not connect to mysql database isp4you');

### Mysql select fuer domainname um die id zu ermitteln
my (@id, @reihe);
$SELECTSQL =  "SELECT id FROM domainen WHERE domainname = '$domainname'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@reihe = $sth->fetchrow) {
# push(@id, $reihe[0]);
$id = $reihe[0];
}

### grep the infos from domaininfos table
my (@data, @row);
$SELECTSQL =  "SELECT * FROM domaininfos WHERE info_id='$id' AND smart_user='$smart_user'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@row = $sth->fetchrow) {             # Query-results
## push(@data, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10] , $row[11] , $row[12] , $row[13] , $row[14] , $row[15]);
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

# DELETE from table domainen and clients
 my (@id, @reihe);
   $SELECTSQL =  "SELECT id FROM domainen WHERE domainname='$domainname'";
   $msql =  sql($SELECTSQL);
   my $sth =  $msql;
   while(@reihe = $sth->fetchrow) {             # Query-results
   push(@id, $reihe[0]);}

$dbh->do("DELETE FROM domainen WHERE domainname='$domainname' AND user='$smart_user' ");
$dbh->do("DELETE FROM clients WHERE domain_id='$id[0]' AND smart_user='$smart_user' ");
$dbh->do("DELETE FROM domaininfos WHERE info_id='$id[0]' AND smart_user='$smart_user' ");
$dbh->disconnect();

# DELETE from table mysql / User and Db
$dbh =DBI->connect("DBI:mysql:mysql:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'}, $config{'mysql_pass'} ,{ PrintError => 0, RaiseError => 0 }) || &error(couldnt to the mysql user database);
$dbh->do("DELETE FROM user WHERE User='$domainmysql_user' ");
$dbh->do("DELETE FROM db WHERE Db='$domainmysql' ");


### Delete the database.
$sth = $dbh->prepare("DROP DATABASE $domainmysql");
$sth->execute();
$sth->finish();
$dbh->disconnect();

### delete user and users homedirectory
system("userdel -r $domainuser");


$i="1";
for($i = 1; $i <= $mail; $i++) {
system("userdel -r mail$i.$domainname");
}

### delete from the domainlist
$Pos =0;

$lref = &read_file_lines("/var/log/isp4you/all");
for (@$lref)
 {
 if (/^$domainname/) {
 splice(@$lref, $Pos, 1, ());
 $Pos ="99999999999";
 }
 $Pos ++;
 }
 &flush_file_lines();
$Pos =0;

$lref = &read_file_lines("/var/log/isp4you/$smart_user");
for (@$lref)
 {
 if (/^$domainname/) {
 splice(@$lref, $Pos, 1, ());
 $Pos ="99999999999";
 }
 $Pos ++;
 }
 &flush_file_lines();

### end delete from domainlist
$Pos =0;

### delete from the httpd.conf file
system ("rm -rf $httpd_2$domainname$httpd_3");


$lref = &read_file_lines("$httpd");
for (@$lref)
{
if (/^Include $httpd_2$domainname$httpd_3/) {
splice(@$lref, $Pos, 1, ());
$Pos ="99999999999";
}
$Pos ++;
}
&flush_file_lines();

if ($gconfig{'real_os_type'} eq "SuSE Linux") {
$reloader = "apache";
} else {
$reloader = "httpd";
}

isp4you_apache_reload();

### end delete from the httpd.conf file
#######################################


if($mail ne 0) {
### delete Sendmail stuff
system ("ex +/$domainname/ -c /$domainname/d -c x -s $mailpfad");  ## delete from the sendmail_cf file
## unixsave and not unixsave

$i="1";
for($i = 1; $i <= $mail; $i++) {
system ("ex +/$domainname/ -c /$domainname/d -c x -s $virtuser");  ## delete from the virtusertable file
}
for($i = 1; $i <= $mail; $i++) {
system ("ex +/$domainuser/ -c /$domainuser/d -c x -s $virtuser");
}
### end sendmail stuff

### delete from access table if config mxer eq 1
if ($config{'mxer'} eq 1) {

$i="1";

$mxer_conf =$config{'virtuser'};
@mxer_conf = split(/\//, $mxer_conf);
$mxer_conf_2 ="\/$mxer_conf[1]\/$mxer_conf[2]";   # /etc/mail/ for example

for($i = 1; $i <= $mail; $i++) {
system ("ex +/$domainuser/ -c /$domainuser/d -c x -s $mxer_conf_2/access");
system ("ex +/$domainuser/ -c /$domainuser/d -c x -s $mxer_conf_2/genericstable");
}
}




isp4you_mapping();
}



### delete bind master file
###
if($bind eq 1) {
system("rm -rf $config{'master'}$domainname\.hosts");


###############################
### delete from /etc/named.conf
### TEST TEST TEST ############

$Pos =0;
$lref = &read_file_lines("/etc/named.conf");
for (@$lref) {

if (/^zone \"$domainname\"/) {

splice(@$lref, $Pos, 4, ());
$Pos ="99999999";
} ## Ende if zone
$Pos++;
} ## Ende for 1
&flush_file_lines();
}


###################################
###  delete webalizer files
###################################
if($webalizer eq 1) {
system ("rm -rf /var/log/isp4you/webalizer-data/$domainuser");
system ("rm -rf /etc/cron.daily/webalizer_$domainuser");
}
###################################
### delete webmin user
###################################

if($webmin eq 1) {
&foreign_require('acl', 'acl-lib.pl');
&foreign_call('acl', 'delete_user', $domainname);
&webmin_log("delete", "user", $in{'user'});
system("rm -rf $minpfad/$domainname\.acl");
#system("rm -rf $minpfad/$domainname$sendindex");
&restart_miniserv();
}

$domainname= ""; $in{'domainen.selected'}= ""; $domainuser= "";

&redirect("list.cgi?ap_nok=$ap_nok");
