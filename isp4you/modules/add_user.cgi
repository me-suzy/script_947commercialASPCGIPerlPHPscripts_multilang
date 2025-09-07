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

&lock_file(userpass);
open(USERPASS,">userpass");
print USERPASS "$domainuser:$pass\n";


### write the new domain to the list of all domains
open(DOMAINS,">>$smart_user2");
if ($in{'subdomain'} eq 1) {
print DOMAINS "$in{'www'}$in{'domainname'}\n";
} else {
print DOMAINS "$in{'domainname'}\n";
}
close(DOMAINS);


open(ALLDOMAINS, ">>/var/log/isp4you/all");
if ($in{'subdomain'} eq 1) {
print ALLDOMAINS "$in{'www'}$in{'domainname'}\n";
} else {
print ALLDOMAINS "$in{'domainname'}\n";
}
close (ALLDOMAINS);
###
### end write to list

### write to mysql database
###
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}", $config{'mysql_user'}, $config{'mysql_pass'} ,{ PrintError => 0, RaiseError => 0 }) || &error('could not connect to database isp4you');

if ($in{'subdomain'} eq 1) {
$dbh->do("INSERT INTO domainen VALUES ('', '$in{'www'}$domainname', '$smart_user')");
} else {
$dbh->do("INSERT INTO domainen VALUES ('', '$domainname', '$smart_user')");
}

 my (@id, @reihe);
   if ($in{'subdomain'} eq 1) {
   $SELECTSQL =  "SELECT id FROM domainen WHERE domainname='$in{'www'}$domainname'";
   } else {
   $SELECTSQL =  "SELECT id FROM domainen WHERE domainname='$domainname'";
   }
   $msql =  sql($SELECTSQL);
   my $sth =  $msql;
   while(@reihe = $sth->fetchrow) {             # Query-results
   push(@id, $reihe[0]);
   }

$dbh->do("INSERT INTO clients VALUES ('', '$id[0]', '$smart_user', '', '' ,'', '', '', '', '', '')");
if ($in{'subdomain'} eq 1) {
$dbh->do("INSERT INTO domaininfos VALUES ('', '$id[0]', '$smart_user', '$in{'webserver'}', '$in{'ssl'}', '$in{'bind8'}', '$in{'mysql'}', '$in{'webalizer'}', '$in{'webminuser'}', '$in{'quota_index'}', '$in{'sendmail'}-$in{'sendmail'}','','','','','0')");
} else {
$dbh->do("INSERT INTO domaininfos VALUES ('', '$id[0]', '$smart_user', '$in{'webserver'}', '$in{'ssl'}', '$in{'bind8'}', '$in{'mysql'}', '$in{'webalizer'}', '$in{'webminuser'}', '$in{'quota_index'}', '$in{'sendmail'}-$in{'sendmail'}','','','$in{'domainname'}','','0')");
}
$dbh->disconnect();

system("bin/useradd -m -d $config{'webpfad'}$in{'www'}$domainname -s $config{'usershell'} -g $config{'group'} $domainuser");
system("mkdir $config{'webpfad'}$in{'www'}$domainname/html $config{'webpfad'}$in{'www'}$domainname/cgi-bin $config{'webpfad'}$in{'www'}$domainname/logs");
if ($in{'ssl'}) {
mkdir ("$config{'webpfad'}$in{'www'}$domainname/ssl", 0551);
}
system("chown $domainuser:$config{'group'} $config{'webpfad'}$in{'www'}$domainname/html $config{'webpfad'}$in{'www'}$domainname/cgi-bin $config{'webpfad'}$in{'www'}$domainname/logs");
if ($in{'ssl'}) {
system("chown $domainuser:$config{'group'} $config{'webpfad'}$in{'www'}$domainname/ssl");
}
system("chmod 551 $config{'webpfad'}$in{'www'}$domainname");
system("rm -rf $config{'webpfad'}$in{'www'}$domainname/.*");

close(USERPASS);
&unlock_all_files();

### WEBALIZER SUPPORT

if ($in{'webalizer'} eq 1) {
system ("mkdir $config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS");
system ("chown $domainuser:$config{'group'} $config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS");
system ("chmod 555 $config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS");
system ("htpasswd -bcm $config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS/.htpasswd $domainuser $pass");
system ("touch $config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS/.htaccess");
open (HTACCESS,">>$config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS/.htaccess");
print HTACCESS "AuthUserFile $config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS/.htpasswd\n";
print HTACCESS "AuthName \"$text{'webstats_htpasswd'} $domainname\"\n";
print HTACCESS "AuthType Basic\n\n";
print HTACCESS "<Limit GET>\n";
print HTACCESS "require valid-user\n";
print HTACCESS "</Limit>\n";
close (HTACCESS);

isp4you_robots();
}
