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
if ($in{'subdomain'} eq 1) {
system ("touch $config{'httpd_2'}$in{'www'}$domainname.conf");
} else {
system ("touch $config{'httpd_2'}$domainname.conf");
}

&lock_file($httpd);
open (HTTPDINC, ">>$httpd") || &error('could not open the httpd.conf');
if ($in{'subdomain'} eq 1) {
print HTTPDINC "Include $config{'httpd_2'}$in{'www'}$domainname.conf\n";
} else {
print HTTPDINC "Include $config{'httpd_2'}$domainname.conf\n";
}
close(HTTPDINC);
&unlock_all_files();

if ($in{'subdomain'} eq 1) {
open(HTTPD, ">>$config{'httpd_2'}$in{'www'}$domainname.conf");
} else {
open(HTTPD, ">>$config{'httpd_2'}$domainname.conf");
}

if ($in{'ssl'} eq 1) { print HTTPD "<VirtualHost $config{'ip'}:443>\n"; } else { print HTTPD "<VirtualHost $config{'ip'}:80>\n"; }

print HTTPD "DocumentRoot $config{'webpfad'}$in{'www'}$domainname/html\n";
print HTTPD "ServerName $in{'www'}$domainname\n";
print HTTPD "ServerAdmin webmaster\@$domainname\n";
print HTTPD "ScriptAlias /cgi-bin/ $config{'webpfad'}$in{'www'}$domainname/cgi-bin/\n";
print HTTPD "ErrorLog $config{'webpfad'}$in{'www'}$domainname/logs/error_log\n";
print HTTPD "CustomLog $config{'webpfad'}$in{'www'}$domainname/logs/access_log \"%h %l %u %t \\\"%r\\\" %s %b \\\"%{Referer}i\\\" \\\"%{User-agent}i\\\"\"\n";
print HTTPD "LogFormat \"%h %l %u %t \\\"%r\\\" %>s %b \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\"\" combined\n";
print HTTPD "LogFormat \"%h %l %u %t \\\"%r\\\" %>s %b\" common\n";
print HTTPD "LogFormat \"%{Referer}i -> %U\" referer\n";
print HTTPD "LogFormat \"%{User-agent}i\" agent\n";
print HTTPD "<Directory \"$config{'webpfad'}$in{'www'}$domainname/html\">\n";
print HTTPD "  AllowOverride all\n";
print HTTPD "</Directory>\n";
print HTTPD "<Directory \"$config{'webpfad'}$in{'www'}$domainname/cgi-bin\">\n";
print HTTPD "  Options +ExecCGI\n";
print HTTPD "</Directory>\n";
if ($in{'ssl'} eq 1) {
print HTTPD "SSLEngine on\n";
#print HTTPD "SSLLog $config{'webpfad'}$in{'www'}$domainname/logs/ssl_engine_log\n";
print HTTPD "SSLCertificateFile $config{'webpfad'}$in{'www'}$domainname/ssl/certificate.pem\n";
print HTTPD "SSLCertificateKeyFile $config{'webpfad'}$in{'www'}$domainname/ssl/key.pem\n";
}

if ($in{'subdomain'} ne 1) {
print HTTPD "ServerAlias $domainname\n";
}
print HTTPD "</VirtualHost>\n";
print HTTPD "\n";
close(HTTPD);


if ($in{'ssl'} ne 1) {
if ($gconfig{'real_os_type'} eq "SuSE Linux") {
$reloader = "apache";
} else {
$reloader = "httpd";
}


isp4you_apache_reload();
}   ## end subdomain ne 1

