#!/usr/bin/perl
do '../web-lib.pl';
do 'isp4you.pl';
$|=1;$z="!";
&init_config("isp4you");
if($ENV{'REQUEST_METHOD'} eq 'GET') { &redirect("") }
&ReadParse();
$minpfad=$config_directory;

## check for missing endslash in webpfad and httpd.conf Pfad
if (($in{'httpd'} !~ m/\/$/) or ($in{'webpfad'} !~ m/\/$/)){
isp4you_header();
print "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">";
print "<br><br><p align=\"center\"><font face=\"Verdana,Arial,Helvetica\" size=\"1\">$text{'firstscreen_13'}<br><br>";
isp4you_check_footer();
}

## check termes
if ($in{'thermes'} == 0){
isp4you_header();
print "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">";
print "<br><br><p align=\"center\"><font face=\"Verdana,Arial,Helvetica\" size=\"1\">$text{'firstscreen_agree'}<br><br>";
isp4you_check_footer();
}

### check for ad
if (($in{'adminmail'} !~ m/\./) or ($in{'adminmail'} !~ m/\@/)) {
isp4you_header();
print "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">";
print "<br><br><p align=\"center\"><font face=\"Verdana,Arial,Helvetica\" size=\"1\">$text{'firstscreen_8'}<br><br>";
isp4you_check_footer();
}

system ("ping 212.202.235.4 -c 1 -w 1 > ping");
$lref = &read_file_lines("ping");$z="";
for (@$lref)
 { if (/100\%/) { $u ="0"; } else { $u ="1";  }}

system ("rm -rf ping");

if ($u == "1") {
&http_download ('remote.isp4you.net', 80, "/_remote/$in{'ip'}", 'rem', \error.cgi);

open FILE, "rem";
@r = <FILE>;
close FILE;
system ("rm -rf rem");


if ($r[0] != "$in{'ip'} ") {
isp4you_header();
print "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">";
print "<br><br><p align=\"center\"><font face=\"Verdana,Arial,Helvetica\" size=\"1\">$text{'firstscreen_9'}<br><br>";
isp4you_check_footer();
}

}

if ($in{'dbi_check'} == 0){
isp4you_header();
print "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">";
print "<br><br><p align=\"center\"><font face=\"Verdana,Arial,Helvetica\" size=\"1\">$text{'firstscreen_12'}<br><br>";
isp4you_check_footer();
}

$infile = time();
system ("touch /var/log/savacctx2");
open(INFILE,">/var/log/savacctx2");
print INFILE "$infile\n";
close(INFILE);

### check update folder when we are updating, should be present
if ($in{'update'} == 1) {
$verzeichnis = '/var/log/isp4you';
opendir(DIR, $verzeichnis) or die "Can not open this Directory !";
}

## check empty mysql user
######################################### IS BUGGY DON'T ASK ME WHY ???
if ($in{'mysql_user'} != "root" ){
isp4you_header();
print "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">";
print "<br><br><p align=\"center\"><font face=\"Verdana,Arial,Helvetica\" size=\"2\">$text{'firstscreen_10'}<br><br>";
isp4you_check_footer();
}


### Set webalizer and openssl
system ("rm -rf webalizer openssl");
system ("touch webalizer openssl");
system ("which webalizer >webalizer");
system ("which openssl >openssl");


open (A, "webalizer");
while(<A>) {
$webalizer_path = $_;
chomp($webalizer_path);
}
close (A);

open (B, "openssl");
while(<B>) {
$openssl = $_;
chomp($openssl);
}
close (B);

system ("rm -rf webalizer openssl");


############################################
#copy the caldera image in the caldera folder
############################################
system ("mkdir ../caldera/isp4you");
system ("mkdir ../caldera/isp4you/images");
system ("cp -r images/caldera/icon.gif ../caldera/isp4you/images");


#######################################
### create isp4you database
#######################################
use DBI;


if ($in{'update'} == 0) {



        ### Setup DBMS variables.
        $database = "mysql"; $host = "localhost"; $user = "root"; $mysql_pass = "$in{'mysql_pass'}";

        ### Connect to the database.
        my $dbh = DBI->connect("DBI:mysql:database=mysql;host=localhost", "$user","$mysql_pass");

        ### Create the database isp4you
        $sth = $dbh->prepare("CREATE DATABASE isp4you");
        $sth->execute();
        $sth->finish();


        ### Create the user.
        $sth = $dbh->prepare("INSERT INTO mysql.user VALUES ('localhost','isp4you', PASSWORD('$mysql_pass'), 'N', 'N', 'N', 'N','N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')");
        $sth->execute();
        $sth->finish();


        ### Create the database permissions.
        $sth = $dbh->prepare("INSERT INTO mysql.db VALUES ('localhost', 'isp4you','isp4you', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y','Y', 'Y')");
        $sth->execute();
        $sth->finish();


        ### Reload the grant tables.
        $sth = $dbh->prepare("FLUSH PRIVILEGES");
        $sth->execute();
        $sth->finish();



        $dbh->disconnect;

$dbh =DBI->connect("DBI:mysql:isp4you:localhost$config{'mysql_port'}",$in{'mysql_user'}, $in{'mysql_pass'} ,{ PrintError => 0, RaiseError => 0 }) || &error('could not connect to mysql database isp4you');


###########################################################
### Tabelle vorhanden? Falls nicht, Datenbank initialisieren
###########################################################
$dbh->do("SELECT * FROM domainen") || db_init($dbh);
$dbh->do("SELECT * FROM clients") || db_init_pro($dbh);
$dbh->do("SELECT * FROM domaininfos") || db_init_infos($dbh);
$dbh->do("SELECT * FROM mailtext") || db_init_text($dbh);
#################################################
### CREATE A TABLE WITH ROWS IF NOT PRESENTED
##################################################
sub db_init {

$dbh->do(<<EOT) || die $dbh->errstr;                     # Create Table Domainen
             create table domainen (
             id        int primary key auto_increment,   # id
             domainname      char(80),                   # Domainname
             user            char(80)                    # der User
             )
EOT
}

sub db_init_pro {
$dbh->do(<<PRO) || die $dbh->errstr;

             create table clients (
             id        int primary key auto_increment,
             domain_id    int,
             smart_user   char(80),
             company      char(80),
             name         char(80),
             street       char(80),
             zip          char(80),
             city         char(80),
             tel          char(80),
             fax          char(80),
             email        char(80)
             )
PRO
}


sub db_init_infos {
$dbh->do(<<INFOS) || die $dbh->errstr;

            create table domaininfos (
            id        int primary key auto_increment,
            info_id    int,
            smart_user char(80),
            apache     char(80),
            s_s_l      char(80),
            bind       char(80),
            mysql      char(80),
            webalizer  char(80),
            webmin     char(80),
            quota      char(80),
            mail       char(80),
            frontpage  char(80),
            proftp     char(80),
            alias      blob,
            error      char(80),
            dummy      char(80)
            )
INFOS
}

sub db_init_text {
$dbh->do(<<TEXT)

        create table mailtext (
        id        int primary key auto_increment,
        smart_user char(80),
        mailheader     blob,
        mailfooter     blob,
        mailbody       blob
        )
TEXT
}

$dbh->disconnect;

########################################
open(KONFIG,">$minpfad/isp4you/config") or print "$text{'error_apache'}\n" and exit;
print KONFIG "group=users\n";
print KONFIG "virtuser=/etc/mail/virtusertable\n";
print KONFIG "host1=ns.yourdomainname.org\n";
print KONFIG "host2=ns2.yourdomainname.org\n";
print KONFIG "subfolder=1\n";
print KONFIG "ip=$in{'ip'}\n";
print KONFIG "httpd_2=$in{'httpd'}\n";
print KONFIG "master=/var/named/\n";
print KONFIG "mailvariable=0\n";
print KONFIG "useradd=$in{'os_system'}\n";
print KONFIG "sub=1\n";
print KONFIG "adminmail=$in{'adminmail'}\n";
print KONFIG "usershell=/bin/false\n";
print KONFIG "sendresult=0\n";
print KONFIG "bindbin=/etc/init.d/named\n";
print KONFIG "webpfad=$in{'webpfad'}\n";
print KONFIG "mailserver=mail.yourmailserver.org\n";
print KONFIG "mxer=0\n";
print KONFIG "mysql_user=$in{'mysql_user'}\n";
print KONFIG "mysql_pass=$in{'mysql_pass'}\n";
print KONFIG "unixsave=0\n";
print KONFIG "pass_strength=4\n";
print KONFIG "mysql_host=localhost\n";
print KONFIG "webalizer_path=$webalizer_path\n";
print KONFIG "device=/dev/hda3\n";
print KONFIG "checked=0\n";
print KONFIG "sendpost=1\n";
print KONFIG "openssl=$openssl\n";
print KONFIG "mysql_port=\n";
print KONFIG "mail_dir=\n";
print KONFIG "quota_default=0\n";
print KONFIG "whois=0\n";
print KONFIG "disk_usage=1\n";
#print KONFIG "my_liste=1224400\n";
close(KONFIG);

system ("mkdir /var/log/isp4you");
system ("touch /var/log/isp4you/all");
system ("touch /var/log/isp4you/root");
system ("chmod 777 /var/log/isp4you/all");
system ("mkdir /var/log/isp4you/webalizer-data");

open(ALL,">/var/log/isp4you/all");
print ALL "firstdomain_dont_delete.isp4you\n";
close(ALL);

system ("rm -rf index.cgi");
system ("mv index-org.cgi index.cgi");
system ("rm -rf index2.cgi");

&redirect("index.cgi?z=$z");


} ### Neu Installation - Kein Update - Ende de if Abfrage

if ($in{'update'} == 1) {

## UPDATE
$dbh =DBI->connect("DBI:mysql:isp4you:localhost$config{'mysql_port'}",$in{'mysql_user'},$in{'mysql_pass'} ,{ PrintError => 0, RaiseError => 0 }) || &error('could not connect to mysql database isp4you');

$dbh->do("SELECT * FROM mailtext") || db_init_mail($dbh);

sub db_init_mail {
$dbh->do(<<MAIL)


        create table mailtext (
        id        int primary key auto_increment,
        smart_user char(80),
        mailheader     blob,
        mailfooter     blob,
        mailbody       blob
        )

MAIL
}


$dbh->do(<<EOT)

    ALTER TABLE domaininfos CHANGE alias alias BLOB
		
EOT

### ALTER TABLE domaininfos CHANGE ssl s_s_l char(80)

}    # Ende Update

if ($in{'update'} == 1) {


## add disk_uasge to the module_config with 1
open(KONFIGUP,">>$minpfad/isp4you/config");
print KONFIGUP "disk_usage=1\n";
close(KONFIGUP);


$lref = &read_file_lines("$minpfad/isp4you/config");
for (@$lref)
{
if (/^ip=/) { $my_Pos = $Pos; }
if (/^sub=/) { $sub_Pos = $Pos; }
if (/^subfolder=/) { $subfolder_Pos = $Pos; }
$Pos ++;
}

&flush_file_lines();
&replace_file_line ("$minpfad/isp4you/config", $my_Pos, "ip\=$in{'ip'}\n");
&replace_file_line ("$minpfad/isp4you/config", $sub_Pos, "sub=1\n");
&replace_file_line ("$minpfad/isp4you/config", $subfolder_Pos, "subfolder=1\n");

system ("rm -rf index.cgi");
system ("mv index-org.cgi index.cgi");
system ("rm -rf index2.cgi");
&redirect("index.cgi?z=$z");
}




