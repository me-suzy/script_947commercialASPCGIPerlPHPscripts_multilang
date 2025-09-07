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
local $smart_ip = $ENV{'REMOTE_ADDR'};
######################

do '../web-lib.pl';
do 'isp4you.pl';
$|=1;
&init_config("isp4you");
if ($config{'sendpost'} eq 1) {
%mailconfig=&foreign_config('sendmail');
}

if($ENV{'REQUEST_METHOD'} eq 'GET') { &redirect("") }
&ReadParse();

### some variables, which we need
###
###
$minpfad=$config_directory;
$html="/html/";
$httpd_2=$config{'httpd_2'};
$httpd="$httpd_2/httpd.conf";
$domainname=$in{'domainname'};
$domainmysql=$in{'domainname'};
$domainmysql =~ tr/./_/;
$domainmysql =~ tr/-/_/;
$domainuser=$in{'domainname'};
###
###
### end of the variables

#################################################
#### do some detail sanity checks            ####
#################################################
if ($in{'alias'} =~ m/\?|\ |\_|\#|\+|\*|\!|\"|\'|\$|\%|\/|\(|\)|\=|\,/) { &redirect("detail.cgi?error=1&error_stup=1&domainname=$in{'domainname'}"); exit; }
if ($in{'alias'} ne "") {
if ($in{'alias'} !~ m/\./) { &redirect("detail.cgi?error=1&error_point=1&domainname=$in{'domainname'}"); exit; }
}
if ($in{'redirect'} =~ m/\?|\ |\_|\#|\+|\*|\!|\"|\'|\$|\%|\(|\)|\=|\,/) { &redirect("detail.cgi?error=1&error_stup=1&domainname=$in{'domainname'}"); exit; }
if ($in{'redirect'} ne "") {
if ($in{'redirect'} !~ m/\./) { &redirect("detail.cgi?error=1&error_point=1&domainname=$in{'domainname'}"); exit; }
if ($in{'redirect'} !~ m/\http\:\/\//) { &redirect("detail.cgi?error=1&error_http=1&domainname=$in{'domainname'}"); exit; }
}
if ($in{'quota_index'} =~ m/\D/) { &redirect("detail.cgi?error=1&error_quota=1&domainname=$in{'domainname'}"); exit; }


use DBI;

$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to isp4you database');


### Mysql select fuer domainname um die id zu ermitteln
my (@id, @reihe);
$SELECTSQL =  "SELECT id FROM domainen WHERE domainname = '$in{'domainname'}'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@reihe = $sth->fetchrow) {
push(@id, $reihe[0]);
$id = $id[0];
}

### Mysql select um die Infos aus der Tabelle domaininfos zu bekommen
my (@data, @row);
$SELECTSQL =  "SELECT * FROM domaininfos WHERE info_id='$id' AND smart_user='$smart_user'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@row = $sth->fetchrow) {             # Query-results
$id = $row[0];
$info_id = $row[1];
$smart_user_2 = $row[2];
$apache  = $row[3];
$ssl =  $row[4];
$bind = $row[5];
$mysql_check = $row[6];
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

### Quoten neu setzen wenn Eingabe ungleich der Daten aus der Datenbank sind
if ($in{'quota_index'} ne "") {
if ($in{'quota_index'} ne $quota) {
$quota_index = $in{'quota_index'};
isp4you_quota();
}}

### ServerAlias setzen, aendern oder loeschen
if ($in{'alias'} ne $alias) {
isp4you_alias();
system("rm -rf alias");
}

### Redirect setzen, aendern oder loeschen
isp4you_redirect();

### 401 Error Dokument anlegen wenn nicht schon gesetzt
if ($in{'401'} eq 1) {
if ($error eq "") {
isp4you_401();
$error = $in{'401'};
}}
### 401 Error Dokument loeschen
if ($in{'401'} eq "") {
system ("ex +/ErrorDocument/ -c /ErrorDocument/d -c x -s $config{'httpd_2'}$domainname.conf");  ## delete Error Documente
system ("ex +/ErrorDocument/ -c /ErrorDocument/d -c x -s $config{'httpd_2'}$domainname.conf");
system ("ex +/ErrorDocument/ -c /ErrorDocument/d -c x -s $config{'httpd_2'}$domainname.conf");
system ("ex +/ErrorDocument/ -c /ErrorDocument/d -c x -s $config{'httpd_2'}$domainname.conf");
}

### Options Indexes anlegen wenn nicht schon gesetzt
if ($in{'indexes'} eq 1) {
if ($frontpage eq "") {
isp4you_indexes();
$error = $in{'indexes'};
}}
### Options Indexes loeschen
if ($in{'indexes'} eq "") {
system ("ex +/Indexes/ -c /Indexes/d -c x -s $config{'httpd_2'}$domainname.conf");  ## delete Options Indexes
}


### mysql anlegen wenn nicht schon getan
if ($in{'mysql'} eq 1 ){
if ($mysql_check eq ""){
isp4you_pass();
do 'modules/mysql.cgi';
$mysql_check ="1";
$mysql_test = "1";
}
}

### mysql loeschen, wenn unmarkiert
if ($in{'mysql'} eq ""){
if ($mysql_check eq 1){
$dbh->disconnect();
$dbh =DBI->connect("DBI:mysql:mysql:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'}, $config{'mysql_pass'} ,{ PrintError => 0 }) || &error('could not connect to the mysql database');
$dbh->do("DELETE FROM user WHERE User='$domainmysql' ");
$dbh->do("DELETE FROM db WHERE Db='$domainmysql' ");
system("mysqladmin drop -f $domainmysql -u $config{'mysql_user'} -p$config{'mysql_pass'}");
$mysql_check ="";
}}

### Webalizer erstellen
if ($in{'webalizer'} eq 1){
if ($webalizer eq ""){
isp4you_webalizer_detail();
isp4you_robots();
do 'modules/webalizer.cgi';
$webalizer ="1";
}}

### Webalizer loeschen
if ($in{'webalizer'} eq ""){
if ($webalizer eq 1){
isp4you_webalizer_delete();
$webalizer ="";
}}


### ProFTP Account erstellen
if ($in{'proftp'} eq 1 ) {
if ($proftp eq "") {
isp4you_pro_ftpd();
$proftp = "1";
}}

### add mail accounts
if ($in{'sendmail'} ne 0 ) {
isp4you_pass();
do 'pro/add_mail.cgi';
##$mail = $mail+$in{'sendmail'};              ## z.B. 3-3

$a_mail = $in{'mail1'} + $in{'sendmail'};
$b_mail = $in{'mail2'} + $in{'sendmail'};
$mail = "$a_mail-$b_mail";
}

## UPDATE die domaininfos datenbank
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'});
$dbh->do("UPDATE domaininfos SET info_id='$id[0]', smart_user='$smart_user', apache ='$apache', s_s_l='$ssl', bind='$bind', mysql='$mysql_check', webalizer='$webalizer', webmin='$webmin', quota='$in{'quota_index'}', mail='$mail', frontpage='$in{'indexes'}', proftp='$in{'proftp'}', alias='$in{'alias'}', error='$in{'401'}', dummy='$dummy' WHERE info_id='$info_id'");
$dbh->disconnect();
system ("rm -rf userpass");
&redirect("list.cgi?detail=1&pass=$pass&mysql=$in{'mysql'}&mysql_test=$mysql_test&mail=$in{'sendmail'}");

