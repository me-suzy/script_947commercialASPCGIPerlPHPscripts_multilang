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

system ("touch /etc/cron.daily/webalizer_$domainuser");
system ("chmod 700 /etc/cron.daily/webalizer_$domainuser");

open(WEBALIZER,">>/etc/cron.daily/webalizer_$domainuser") or print "$text{'error_webalizer'}\n" and die;
print WEBALIZER "#!/bin/sh\n";
print WEBALIZER "$config{'webalizer_path'} -c /var/log/isp4you/webalizer-data/$domainuser > /dev/null 2>&1\n";
close(WEBALIZER);

### webalizer for the specified domainname crate and write datas to it
###

system ("touch /var/log/isp4you/webalizer-data/$domainuser");

open(WEBALIZER2,">>/var/log/isp4you/webalizer-data/$domainuser") or print "$text{'error_webalizer2'}\n" and die;


print WEBALIZER2 "OutputDir $config{'webpfad'}$in{'www'}$domainname/html/WEBSTATS\n";
print WEBALIZER2 "LogFile $config{'webpfad'}$in{'www'}$domainname/logs/access_log\n";
print WEBALIZER2 "HostName $in{'www'}$domainname\n";
print WEBALIZER2 "HistoryName webalizer.hist\n";
print WEBALIZER2 "LogType web\n";
print WEBALIZER2 "GroupAgent MSIE\n";
print WEBALIZER2 "GroupAgent Mozilla\n";
print WEBALIZER2 "GroupAgent Lynx\n";
print WEBALIZER2 "ReportTitle $text{'webalizer_stats'}\n";
print WEBALIZER2 "HourlyGraph yes\n";
print WEBALIZER2 "HourlyStats yes\n";
print WEBALIZER2 "TimeMe yes\n";
print WEBALIZER2 "GraphLegend yes\n";
print WEBALIZER2 "GraphLines 2\n";
print WEBALIZER2 "PageType htm*\n";
print WEBALIZER2 "PageType php*\n";
print WEBALIZER2 "GroupReferrer yahoo.com\n";
print WEBALIZER2 "GroupReferrer excite.com\n";
print WEBALIZER2 "GroupReferrer infoseek.com\n";
print WEBALIZER2 "GroupReferrer webcrawler.com\n";
print WEBALIZER2 "GroupReferrer google.de\n";
print WEBALIZER2 "TopAgents 15\n";
print WEBALIZER2 "TopSearch 20\n";
print WEBALIZER2 "TopUsers 30\n";
print WEBALIZER2 "MangleAgents 5\n";
print WEBALIZER2 "\n";
print WEBALIZER2 "AllAgents yes\n";
print WEBALIZER2 "AllReferrers yes\n";
print WEBALIZER2 "AllSites yes\n";
print WEBALIZER2 "AllURLs yes\n";
print WEBALIZER2 "AllSearchStr yes\n";
print WEBALIZER2 "AllUsers yes\n";
print WEBALIZER2 "\n";
print WEBALIZER2 "HideSite $domainname\n";
print WEBALIZER2 "HideSite $in{'www'}$domainname\n";
print WEBALIZER2 "HideSite localhost\n";
print WEBALIZER2 "\n";
print WEBALIZER2 "HideReferrer Direct Request\n";
print WEBALIZER2 "HideReferrer localhost\n";
print WEBALIZER2 "HideReferrer $config{'ip'}\n";
print WEBALIZER2 "HideReferrer $domainname\n";
print WEBALIZER2 "HideReferrer $in{'www'}$domainname\n";
print WEBALIZER2 "\n";
print WEBALIZER2 "HideURL http://$domainname\n";
print WEBALIZER2 "HideURL http://$in{'www'}$domainname\n";
print WEBALIZER2 "\n";
print WEBALIZER2 "IgnoreSite $domainname\n";
print WEBALIZER2 "IgnoreSite $in{'www'}$domainname\n";
print WEBALIZER2 "IgnoreSite localhost\n";
print WEBALIZER2 "\n";
print WEBALIZER2 "IgnoreReferrer Direct Request\n";
print WEBALIZER2 "IgnoreReferrer localhost\n";
print WEBALIZER2 "IgnoreReferrer $config{'ip'}\n";
print WEBALIZER2 "IgnoreReferrer $domainname\n";
print WEBALIZER2 "IgnoreReferrer $in{'www'}$domainname\n";
print WEBALIZER2 "\n";
print WEBALIZER2 "IgnoreURL http://$domainname\n";
print WEBALIZER2 "IgnoreURL http://$in{'www'}$domainname\n";
print WEBALIZER2 "\n";
close(WEBALIZER2);
