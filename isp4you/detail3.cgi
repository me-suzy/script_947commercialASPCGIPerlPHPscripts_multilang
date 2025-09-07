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

if($ENV{'REQUEST_METHOD'} eq 'GET') { &redirect("") }
&ReadParse();

### some variables, which we need
$httpd_2=$config{'httpd_2'};
$httpd="$httpd_2/httpd.conf";
### end of the variables

$in{'manual'} =~ s/\r//g;
open(FILE, ">$config{'httpd_2'}$in{'domainname'}.conf");
 print FILE $in{'manual'};
 close(FILE);

&redirect("list.cgi");

