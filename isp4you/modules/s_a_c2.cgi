#!/usr/bin/perl
#    isp4you
#
#    (C) 2001-2003 by NH (Nick Herrmann) - nh@ingenieurbuero-herrmann.de
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

do '../../web-lib.pl';
do '../isp4you.pl';
$|=1;
&init_config("isp4you");
%access=&get_module_acl();
&ReadParse();

## Split the mysql database stuff
$domainmysql=$in{'domainname'};
$domainmysql =~ tr/./_/;
$domainmysql =~ tr/-/_/;

isp4you_unix();

## Grep the mailfooter and mailheader infos from the database
use DBI;
$dbh =DBI->connect("DBI:mysql:isp4you:$config{'mysql_host'}$config{'mysql_port'}",$config{'mysql_user'},$config{'mysql_pass'},{PrintError => 0, RaiseError => 0 }) || &error('could not connect to isp4you database');


my (@data, @raw, %mailer);
$SELECTSQL =  "SELECT * FROM mailtext WHERE smart_user='$smart_user'";
$msql =  sql($SELECTSQL);
my $sth =  $msql;
while(@row = $sth->fetchrow) {             # Query-results
$id =$data[1];
$smart_user_2 = $row[1];
$mail_header = $row[2];
$mail_footer  = $row[3];
$mail_body =  $row[4];
}


&header();

### the html <FORM> for domainname
if ($config{'sendpost'} eq 1) {
%mailconfig=&foreign_config('sendmail');
open(MAILX,"|$mailconfig{'sendmail_path'} -t");
} else {
open(MAILX,"|/usr/sbin/sendmail -t");
}
print MAILX "To: $in{'email'}\n";
print MAILX "From: $config{'adminmail'}\n";
print MAILX "Subject: $in{'www'}$in{'domainname'} - $text{'mail_sub'}\n\n";
print MAILX "$mail_header\n\n";
print MAILX "Host = $in{'www'}$in{'domainname'}\n";
print MAILX "$text{'mail_loginname'} = $domainuser\n";
print MAILX "$text{'domain_password'} = $in{'pass'}\n";
print MAILX "-" x 75 . "\n";
if ($in{'sendmail'} eq 1) {
for($i = 1; $i <= $in{'mail'}; $i +=1)
{
print MAILX "$text{'mail_email'}: mail$i\@$in{'domainname'}\n";
if ( $config{'unixsave'} eq 1 ) {
print MAILX "$text{'outputscreen_pop3'} $i: mail$i\_$domainuser\n";
} else {
print MAILX "$text{'outputscreen_pop3'} $i: mail$i\.$in{'domainname'}\n";
}
print MAILX "$text{'mail_password'}: $in{'pass'}\n\n";
}
}
if ($in{'mysql'} eq 1) {
print MAILX "$text{'outputscreen_mysql'}\n";
print MAILX "$text{'outputscreen_mysql_databasename'} $domainmysql\n";
print MAILX "$text{'outputscreen_database'} $domainmysql\n";
print MAILX "$text{'outputscreen_mysql_databasepass'} $in{'pass'}\n\n";
}

if ($in{'webalizer'} eq 1) {
print MAILX "$text{'outputscreen_webalizer'}\n";
print MAILX "$text{'outputscreen_URL'} http://$in{'www'}$in{'domainname'}/WEBSTATS\n";
print MAILX "$text{'mail_loginname'}: $in{'domainname'}\n";
print MAILX "$text{'domain_password'}: $in{'pass'}\n\n";
}

if ($in{'send_quota'} eq 1) {
print MAILX "$text{'outputscreen_quota_2'} $in{'quota'} MB\n\n";
}

if ($in{'webminuser'} eq 1) {
print MAILX "$text{'outputscreen_webminuser'}\n";
print MAILX "$text{'mail_loginname'} = $in{'domainname'}\n";
print MAILX "$text{'domain_password'} = $in{'pass'}\n\n";
}
print MAILX "-" x 75 . "\n\n";
print MAILX "$mail_footer\n\n";
print MAILX "\n\n";
close (MAILX);

print <<EOM;

<title>done</title>
<link rel="stylesheet" type="text/css" href="../style.css">
<form action="s_a_c2.cgi" method="post" name="formular" target="_self">
<table width="100%" height="25" border="0" cellpadding="1" cellspacing="1">
  <tr>
     <td class="bodybg"><font size="1"><b>$text{'s_a_c_title'}</b></font></td>
</tr>
</table>
<br>
<font size="1">$text{'s_a_c_text'} <b>$in{'email'}</b></font>
<br><br>
<font size="1"><a href="#" onClick="self.close()">$text{'s_a_c_window'}</a></font>
EOM
print "<br>";
