#    isp4you
#
#    (C) 2001-2003 by NH (Dipl. Wirt.-Ing. Nick Herrmann) - nh@isp4you.com
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

%useradmin=&foreign_config('useradmin');
$passwd_file = $useradmin{'passwd_file'};


### Check for empty form ###
if ($in{'domainname'} eq "") { &redirect("index.cgi?error=1&error_empty=1"); exit; }

### Check for a unallowd dot in the end of the domainname
if ($in{'domainname'} !~ m/\.$/){
} else {
&redirect("index.cgi?error=1&error_endpoint=1&domainname=$in{'domainname'}"); exit;
}


### Check  for nothing has been typed
if (($in{'webserver'} eq "") and ($in{'bind8'} eq "") and ($in{'mysql'} eq "") and ($in{'webminuser'} eq "") and ($in{'sendmail'} eq 0)) {
&redirect("index.cgi?error=1&error_what=1");
exit;
}

$LineLen = length($in{'domainname'});

if ($LineLen < 5) { &redirect("index.cgi?error=1&error_less=1&domainname=$in{'domainname'}"); exit; }
#if ($in{'www'} =~ m/\?|\ |\_|\#|\@|\+|\*|\!|\"|\'|\$|\%|\/|\(|\)|\=|\,/) { &redirect("index.cgi?error=1&error_stup=1&domainname=$in{'domainname'}"); exit; }
# if ($in{'domainname'} =~ m/\?|\ |\_|\@|\#|\+|\*|\!|\"|\'|\$|\%|\/|\(|\)|\=|\,/) { &redirect("index.cgi?error=1&error_stup=1&domainname=$in{'domainname'}"); exit; }
if ($in{'www'} =~ m/^[A-Za-z0-9\-\.]+$/) { } else { &redirect("index.cgi?error=1&error_stup=1&domainname=$in{'domainname'}"); exit; }
if ($in{'domainname'} =~ m/^[A-Za-z0-9\-\.]+$/) { } else { &redirect("index.cgi?error=1&error_stup=1&domainname=$in{'domainname'}"); exit; }

if ($in{'domainname'} !~ m/\./) { &redirect("index.cgi?error=1&error_point=1&domainname=$in{'domainname'}"); exit; }
if ($in{'quota_index'} =~ m/\D/) { &redirect("index.cgi?error=1&error_quota=1&domainname=$in{'domainname'}"); exit; }
if ($in{'sendmail'} =~ m/\D/) { &redirect("index.cgi?error=1&error_sendmail=1&domainname=$in{'domainname'}"); exit; }

## check for point on the end of the name
@pointer = split(/\./, $domainname);
if (($pointer[1] eq "") or ($pointer[3] ne "")) { &redirect("index.cgi?error=1&error_point=1&domainname=$in{'domainname'}"); exit; }


###############################################################
###############################################################
if ($in{'subdomain'} eq 0) {
### Check if a user with same name is allready in /etc/passwd
###
###
open (PASSWD, "$passwd_file");
while(<PASSWD>) {
$ThisLine = $_;
chomp ($ThisLine);
@splitter = split(/\:/, $ThisLine);
$splitme = $splitter[0];


if ($domainuser eq $splitme) {   ## Dont change $domainuser cause of Unix Save Names (Suse 8.x) and normal accounts !!!
close(PASSWD);
&redirect("index.cgi?error=1&error_user=1&domainname=$domainuser"); exit; } }
close(PASSWD);


}  ## End Subdomain eq 0

################################################################################
## We check now if the NameVirtualHost Directive is set up
################################################################################
#######################################$name_virt = "#NameVirtualHost";
$lref = &read_file_lines("$httpd");
for (@$lref)
{
if (/^NameVirtualHost/) {
$name_alert = "1";
}
} ## for schleife

if ($name_alert ne "1"){
&redirect("index.cgi?error=1&error_namevirtual=1&domainname=$in{'domainname'}");
exit;
}



if ($in{'subdomain'} eq 1) {
open (PASSWD, "$passwd_file");
while(<PASSWD>) {
$ThisLine = $_;
chomp ($ThisLine);
@splitter = split(/\:/, $ThisLine);
$splitme = $splitter[0];


$subwww="$in{'www'}$in{'domainname'}";



if ($subwww eq $splitme) {
close(PASSWD);
&redirect("index.cgi?error=1&error_user=1&domainname=$in{'domainname'}");
exit;
}
}
close(PASSWD);


}
#############################################################
### Let us check for postfix user, if the have set the relay host directive
if (($config{'sendpost'} ne 1 ) and ($in{'sendmail'} ne 0) ) {

$post_main=$postfix_conf{'postfix_config_file'};

$lref = &read_file_lines("$post_main");
for (@$lref)
{
if (/^relay_domains/) {
$postfix_alert = "1";
}
}


if ($postfix_alert ne "1") {
&redirect("index.cgi?error=1&error_postfix=1&domainname=$in{'domainname'}");
exit;
}
}

