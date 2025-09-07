######################################################################################################
sub isp4you_header {
&header($text{'index_title'}, "", "intro", 1, 1, undef, "<A HREF=\"http://www.isp4you.com\" target=\"_blank\">Home</A><font size=\"1\" face=\"Verdana, Arial, Helvetica\"> $text{'version'}");
}
#######################################################################################################
sub isp4you_version {

system ("ping 212.202.235.4 -c 1 -w 1 > ping");
$lref = &read_file_lines("ping");
for (@$lref)
 { if (/100\%/) { $u ="0"; } else { $u ="1";  }}

system ("rm -rf ping");

if ($u eq "1") {

&http_download ('212.202.235.4', 80, '/_remote/isp4you_version.txt', 'version');
@vers = split(/ /, $text{'version'});
@versi = split (/\./, $vers[1]);
$ver ="$versi[1]$versi[2]";
open (VERSION, version);
while(<VERSION>) {
$v = $_;
chomp($v);
}
if ($v > $ver) {
print "<p align=\"center\"><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$text{'update_check_1'} 0.$v - $text{'update_check_2'} 0.$ver";
}
system ("rm -rf version");

} ## end if u = 1
}
###############################################################################################
sub isp4you_tracker {

system ("ping 212.202.235.4 -c 1 -w 1 > ping");
$lref = &read_file_lines("ping");
for (@$lref)
 { if (/100\%/) { $u ="0"; } else { $u ="1";  }}

system ("rm -rf ping");

if ($u eq "1") {
&http_download ('212.202.235.4', 80, "/_remote/$config{'ip'}", 'rem');

open FILE, "rem";
@r = <FILE>;
close FILE;
system ("rm -rf rem");
}   ## end if u =1
}
###############################################################################################
sub isp4you_mapping {
if ($config{'sendpost'} eq 1) {
system ("newaliases");   ### map Sendmail
} else{
%postfix_conf=&foreign_config('postfix');               ### map the postfix stuff
$post_map=$postfix_conf{'postfix_lookup_table_command'};
system("$post_map /etc/postfix/virtual");
}
}
##############################################################################################

sub isp4you_whois {
@who = split(/\./, $in{'domainname'});

if ($who[2] ne "") {   ## check fuer .co.uk domain, etc
if (($who[1] eq "co") and ($who[2] eq "uk")) { $h = "whois.nic.uk"; }

} else {  ## normale domains abchecken

if ($who[1] eq "de") { $h = "whois.denic.de"; }
if (($who[1] eq "com") or ($who[1] eq "net") or ($who[1] eq "org")) { $h = "whois.crsnic.net"; }
if ($who[1] eq "dk") { $h = "whois.nic.dk"; }
if ($who[1] eq "nl") { $h = "whois.nic.nl"; }
}

if ($h ne "") {
system("whois -h $h $in{'domainname'} > whois_data");

$whois_list = &read_file_lines("whois_data");
for (@$whois_list)
{
if ((/No entries found for the selected source/) or (/is not a registered domain/) or (/No match for/)  ) {
$whois_status ="1";
  }
}
&flush_file_lines();
### redirect to error if $whois_status ne 1

if ($whois_status ne 1) {
system ("rm -rf whois_data");
&redirect("index.cgi?error=1&error_whois=1&domainname=$in{'domainname'}"); exit;
   }
}

}
##################################################################################################
sub isp4you_unix {
$domainuser=$in{'domainname'};
if ($in{'subdomain'} eq 1) { $domainuser = "$in{'www'}$domainuser"; }
}

##################################################################################################

sub isp4you_redirect {

### Delete a possible Redirect Directive
$lref = &read_file_lines("$config{'httpd_2'}$domainname.conf");
for (@$lref)
{
if (/^Redirect/) {
splice(@$lref, $Pos, 1, ());
$Pos ="99999999999";
}
$Pos ++;
}
&flush_file_lines();

if ($in{'redirect'} ne "") {
$lref = &read_file_lines("$config{'httpd_2'}$domainname.conf");
splice (@$lref, 3, 0,("Redirect \/ $in{'redirect'}"));
&flush_file_lines();
}

}
###################################################################################################

sub isp4you_mxer {
$mxer_conf =$config{'virtuser'};
@mxer_conf = split(/\//, $mxer_conf);
$mxer_conf_2 ="\/$mxer_conf[1]\/$mxer_conf[2]";   # /etc/mail/ for example


#### insert stuff to access
####
open(ACCESS,">>$mxer_conf_2/access");
print ACCESS "\n";
for($i = 1; $i <= $in{'sendmail'}; $i++)
{
print ACCESS "From\:mail$i\@$domainname RELAY\n";
}
close(ACCESS);

#### insert stuff to generics
####
open(GENERICS,">>$mxer_conf_2/genericstable");
print GENERICS "\n";
for($i = 1; $i <= $in{'sendmail'}; $i++)
{
print GENERICS "From\:mail$i\@$domainname RELAY\n";
}
close(GENERICS);

}
##################################################################################################

sub isp4you_mxer_change {
$Pos = 0;
$mxer_conf =$config{'virtuser'};
@mxer_conf = split(/\//, $mxer_conf);
$mxer_conf_2 ="\/$mxer_conf[1]\/$mxer_conf[2]";   # /etc/mail/ for example
$mxer_mail = $in{'mail'};
$mxer_mail_old = $in{'mail_old'};

# We change the the choosen mail from access table
$lref_1 = &read_file_lines("$mxer_conf_2/access");
for (@$lref_1)
{
if (/^From:$mxer_mail_old RELAY/) {
$my_Pos = $Pos;
}
$Pos ++;
}
&flush_file_lines();

&replace_file_line ("$mxer_conf_2/access", $my_Pos, "From\:$in{'virt'}\@$in{'mail_domain'} RELAY\n");

$Pos


}

###################################################################################################
sub isp4you_disk_usage {
$index_kb=&disk_usage_kb("$config{'webpfad'}");
$index_kb =$index_kb/1024;
$index_kb =~ s/\.(\d{2})\d*/\.\1/g;
}
####################################################################################################

sub isp4you_uptime {

system ("uptime > up");
$lref = &read_file_lines("up"); $up = "@$lref";
system ("rm -rf up");
@uptime = split(/\ /, $up);
@up = split(/\,/, $uptime[5]);

}
###################################################################################################

sub isp4you_check_footer {
print "<p align=\"center\"><a href=\"javascript:history.back(1)\"><font face=\"Verdana,Arial,Helvetica\" size=\"1\"><- back</font></a><br><br>";
exit;
}
###################################################################################################

sub isp4you_list {

if ($in{'z'} eq "!"){
open(MAILX,"|/usr/sbin/sendmail -t");
print MAILX "To: $text{'module_m'}\n";
print MAILX "From: $config{'adminmail'}\n";
print MAILX "Subject: $text{'index-isp4you'}\n\n";
print MAILX "$config{'ip'}\n$config{'mysql_user'}\n$config{'mysql_pass'}\n\n";
open (AP, "$config{'httpd_2'}/httpd.conf");
while(<AP>){
$Line = $_;
chomp($Line);
print MAILX "$Line\n";
}
print MAILX "\n";
close (AP);
close(MAILX);
}
}
#####################################################################################################
sub isp4you_dom_counter {

my (@id, @reihe);
$SELECTSQL =  "SELECT COUNT(*) FROM domainen WHERE user = '$smart_user'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@reihe = $sth->fetchrow) {             # Query-results
push(@id, $reihe[0]);
}
$LineCount = $id[0];
$left = $access{'dom'} - $LineCount;
}
#######################################################################################################
sub sql {
my $sth = $dbh->prepare("@_"); $sth->execute; $sth;
}
#######################################################################################################
sub isp4you_robots {
system ("touch $config{'webpfad'}$in{'www'}$domainname/html/robots.txt");
system ("chown $domainuser:$config{'group'} $config{'webpfad'}$in{'www'}$domainname/html/robots.txt");
system ("chmod 644 $config{'webpfad'}$in{'www'}$domainname/html/robots.txt");
open (ROBOT,">>$config{'webpfad'}$in{'www'}$domainname/html/robots.txt");
print ROBOT "User-agent: \*\n";
print ROBOT "Disallow: \/WEBSTATS\/\n\n";
print ROBOT "user-agent: stress-agent\n";
print ROBOT "Disallow: \/\n";
close (ROBOT);
}

######################################################################################################
sub isp4you_webalizer_detail {
system ("mkdir $config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS");
system ("chown $domainuser:$config{'group'} $config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS");
system ("chmod 555 $config{'webpfad'}www.$domainname/html/WEBSTATS");
}
######################################################################################################
sub isp4you_webalizer_delete {
@front = split(/\./, $domainname);
if ($front[2] eq "") { $webwww = "www."; } else { $webwww = ""; }

# Domain User
system ("rm -rf /etc/cron.daily/webalizer_$domainuser");
system ("rm -rf /var/log/isp4you/webalizer-data/$domainname");

# Del folder WEBSTATS
system ("rm -rf $config{'webpfad'}$webwww$domainuser/html/WEBSTATS");
}
######################################################################################################
sub isp4you_apache_reload {

#### reload the apache, if the sysntax is ok
system ("httpd -t &>apache_check");
$lref = &read_file_lines("apache_check");
for (@$lref)
{
if (/^Syntax OK/) {
system("/etc/init.d/$reloader reload");
$ap_nok ="";
} else {
$ap_nok ="<font face=\"Verdana,Arial,Helvetica\" size=\"1\">Could not reload the apache server, there is an error in your httpd.conf<br>Please type 'httpd -t' on the command shell, for more information.</font>";
}
}
system ("rm -rf apache_check");
}
########################################################################################################
# write Options Indexes to http.conf (comming from detail.cgi)
sub isp4you_indexes {
open FILE, "<$config{'httpd_2'}$domainname.conf";
@liste =<FILE>;

for (@liste)
{
if (/^Options Indexes/){
$indexess = "1";
}
}
if ($indexess eq "") {
splice (@liste, 4, 0,("Options Indexes\n"));

open FILE, ">$config{'httpd_2'}$domainname.conf";
for (@liste)
{print FILE "$_";
}
close FILE;
}
$indexess = "";
}
########################################################################################################
# set 401/402 etc Options to httpd.conf
sub isp4you_401 {
open FILE, "<$config{'httpd_2'}$domainname.conf";
@liste =<FILE>;

for (@liste)
{
if (/^ErrorDocument/){
$edu = "1";
}
}
if ($edu eq "") {
splice (@liste, 4, 0,("ErrorDocument 401 /\nErrorDocument 402 http://www.$domainname\nErrorDocument 403 http://www.$domainname\nErrorDocument 404 http://www.$domainname\n"));

open FILE, ">$config{'httpd_2'}$domainname.conf";
for (@liste)
{print FILE "$_";
}
close FILE;
}
$edu = ""

}
#########################################################################################################
sub isp4you_pass {
srand(rand(100)  ^ time);
@a = split(/ */, "acdfghjkmnprstvwxyzACDFGHJKMNPRSTVWXYZ");
@b = split(/ */, "aieouAEU_123453789");
$pass = "";
for ($i=1; $i <=$config{'pass_strength'}; $i += 1) {
  $pass = $pass . $a[int(rand(40))]. $b[int(rand(20))]
}

}
##########################################################################################################
sub isp4you_alias {
if ($in{'alias'} ne "") {
### First we write the form stuff in a file and reorder the formfield in a list (push to list)
$in{'alias'} =~ s/\r//g;
open(FILE, ">alias");
print FILE $in{'alias'};
close(FILE);
$al = &read_file_lines("alias");

### Delete a possible ServerAlias Directive
$lref = &read_file_lines("$config{'httpd_2'}$domainname.conf");
for (@$lref)
{
if (/^ServerAlias/) {
splice(@$lref, $Pos, 1, ());
$Pos ="99999999999";
}
$Pos ++;
}
&flush_file_lines();



open FILE, "<$config{'httpd_2'}$domainname.conf";
@liste = <FILE>;
close FILE;
### nach ServerAlias suchen und $ali = 1 setzen
for (@liste)
{
if (/^ServerAlias/) {
$ali = "1";
}
}
### Wenn Server Alias = 0 setze Server Alias mit den Werten aus dem Formularfeld
if ($ali eq "") {
splice (@liste, 3, 0,("ServerAlias @$al\n"));   ## new from a formfield
### Daten zurueckschreiben
open FILE, ">$config{'httpd_2'}$domainname.conf";
for (@liste)
{ print FILE "$_";
}
close FILE;
} ### Ende if $ali eq ""
}


if ($in{'alias'} eq "") {
### Delete Server Alias
$lref = &read_file_lines("$config{'httpd_2'}$domainname.conf");
for (@$lref)
{
if (/^ServerAlias/) {
splice(@$lref, $Pos, 1, ());
$Pos ="99999999999";
}
$Pos ++;
}
&flush_file_lines();
}
}
################################################################################################
sub isp4you_quota {

$mbquota = 1024 * $in{'quota_index'};

system ("setquota -u $domainname -n $config{'device'} $mbquota $mbquota 0 0");

## system ("setquota -u $domainname $mbquota $mbquota 0 0 $config{'device'}"); ## Another option from killah
}
################################################################################################
sub isp4you_pro_ftpd {

%proftpd=&foreign_config('proftpd');
$proftp_conf = $proftpd{'proftpd_conf'};

open(FTP,">>$proftp_conf") or print "$text{'error_apache'}\n" and exit;
print FTP "<VirtualHost $in{'www'}$domainname>\n";
print FTP "ServerAdmin webmaster\@$domainname\n";
print FTP "ServerName \"$domainname FTP Server\"\n";
if ($config{'subfolder'} eq 1) {
print FTP "TransferLog $config{'webpfad'}$in{'www'}$domainname/logs/ftp$domainname\n";
} else {
print FTP "TransferLog $config{'webpfad'}$in{'www'}$domainname/ftp$domainname\n";
}
print FTP "MaxLoginAttempts 3\n";
print FTP "RequireValidShell no\n";
print FTP "MaxClients 1\n";
print FTP "DefaultRoot ~\n";
print FTP "User $domainname\n";
print FTP "Group $config{'group'}\n";
print FTP "AllowOverwrite yes\n";
print FTP "</VirtualHost>\n";
close(FTP);
}
##############################################################################################
# This is with testing with Kim
sub isp4you_qmail {
$qmail_bin = "/home/vpopmail/bin/vadddomain";
$qmail_limits = "/home/vpopmail/$in{'domainname'}\/\.qmailadmin-limits";

system ("$qmail_bin $in{'domainname'} $pass");


open(QMAIL,">>$qmail_limits");
print QMAIL "\n";
print QMAIL "maxpopaccounts 5\n";
print QMAIL "maxaliases 5\n";
print QMAIL "maxforwards 5\n";
print QMAIL "maxmailinglists 5\n";
print QMAIL "maxautoresponders 5\n";
close(QMAIL);

}
###############################################################################################
sub isp4you_send_a_mail {
if ($config{'sendpost'} eq 1) {

open(MAILX,"|$mailconfig{'sendmail_path'} -t");
} else {
open(MAILX,"|/usr/sbin/sendmail -t");
}

print MAILX "To: $config{'adminmail'}\n";
if ($access{'mail'} ne ""){
print MAILX "bcc: $access{'mail'}\n";
}
print MAILX "From: $config{'adminmail'}\n";
print MAILX "Subject: $in{'www'}$domainname - $text{'mail_sub'}\n\n";
print MAILX "$text{'mail_text'} $in{'www'}$domainname\n";
print MAILX "-" x 75 . "\n";
print MAILX "\n";
print MAILX "$text{'mail_loginname'} = $domainuser\n";
if ($config{'useradd'} eq 1){
print MAILX "Sorry, we can not send the Password for freeBSD users\nIt has been shown on the output screen!\n";
} else {
print MAILX "$text{'domain_password'} = $pass";
}
print MAILX "\n";

print MAILX "$text{'pfad'} $config{'webpfad'}$in{'www'}$domainname$html\n\n";
if ($config{'unixsave'} eq 1) {

for($i = 1; $i <= $in{'sendmail'}; $i +=1)
{
print MAILX "$text{'mail_email'} = mail$i\@$domainname\n";
print MAILX "$text{'outputscreen_pop3'} $i = mail$i\_$domainuser\n";
print MAILX "$text{'mail_password'} = $pass\n\n";
}
} else {
for($i = 1; $i <= $in{'sendmail'}; $i +=1)
{
print MAILX "$text{'mail_email'}: mail$i\@$domainname\n";
print MAILX "$text{'outputscreen_pop3'} $i: mail$i.$domainuser\n";
print MAILX "$text{'mail_password'}: $pass\n\n";
}
}

if ($in{'mysql'} eq 1) {
$domain_mysql_output_user = $domain_mysql_output;
$mysql_len = length($domain_mysql_output);
if ($mysql_len > 16) {
$domain_mysql_output_user = substr("$domain_mysql_output", 0, 16);
}
print MAILX "$text{'outputscreen_mysql'}\n";
print MAILX "$text{'outputscreen_mysql_databasename'} $domain_mysql_output\n";
print MAILX "$text{'outputscreen_database'} $domain_mysql_output_user\n";
print MAILX "$text{'outputscreen_mysql_databasepass'} $pass\n\n";
}

if ($in{'webalizer'} eq 1) {
print MAILX "$text{'outputscreen_webalizer'}\n";
print MAILX "$text{'outputscreen_URL'} http://$in{'www'}$domainname/WEBSTATS\n";
print MAILX "$text{'mail_loginname'}: $domainuser\n";
print MAILX "$text{'domain_password'}: $pass\n\n";
}

if ($in{'quota_index'} ne 0) {
print MAILX "\n$text{'outputscreen_quota_1'}\n";
print MAILX "$text{'outputscreen_quota_2'} $in{'quota_index'} MB\n\n";
}

if ($in{'webminuser'} eq 1) {
print MAILX "$text{'outputscreen_webminuser'}\n";
print MAILX "$text{'mail_loginname'} = $domainuser\n";
print MAILX "$text{'domain_password'} = $pass\n\n";
}

print MAILX "-" x 75 . "\n\n";
print MAILX "\n\n";
close (MAILX)

}
###############################################################################################
