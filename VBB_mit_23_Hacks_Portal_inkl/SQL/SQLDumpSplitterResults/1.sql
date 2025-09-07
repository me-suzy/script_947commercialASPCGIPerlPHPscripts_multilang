# MySQL-Front Dump 2.2
#
# Host: localhost   Database: vbulletin
#--------------------------------------------------------
# Server version 3.23.56-nt


#
# Table structure for table 'access'
#

DROP TABLE IF EXISTS access;
CREATE TABLE `access` (
  `userid` int(10) unsigned NOT NULL default '0',
  `forumid` smallint(5) unsigned NOT NULL default '0',
  `accessmask` smallint(5) unsigned NOT NULL default '0',
  UNIQUE KEY `userid` (`userid`,`forumid`)
) TYPE=MyISAM;



#
# Dumping data for table 'access'
#


#
# Table structure for table 'adminlog'
#

DROP TABLE IF EXISTS adminlog;
CREATE TABLE `adminlog` (
  `adminlogid` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `script` varchar(20) NOT NULL default '',
  `action` varchar(20) NOT NULL default '',
  `extrainfo` varchar(200) NOT NULL default '',
  `ipaddress` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`adminlogid`)
) TYPE=MyISAM;



#
# Dumping data for table 'adminlog'
#


#
# Table structure for table 'adminutil'
#

DROP TABLE IF EXISTS adminutil;
CREATE TABLE `adminutil` (
  `title` varchar(10) NOT NULL default '',
  `text` mediumtext NOT NULL,
  PRIMARY KEY  (`title`)
) TYPE=MyISAM;



#
# Dumping data for table 'adminutil'
#


#
# Table structure for table 'afterburner_sessions_day'
#

DROP TABLE IF EXISTS afterburner_sessions_day;
CREATE TABLE `afterburner_sessions_day` (
  `userid` int(11) NOT NULL default '0',
  `time` int(20) NOT NULL default '0'
) TYPE=MyISAM;



#
# Dumping data for table 'afterburner_sessions_day'
#


#
# Table structure for table 'afterburner_stat'
#

DROP TABLE IF EXISTS afterburner_stat;
CREATE TABLE `afterburner_stat` (
  `time` int(20) NOT NULL default '0',
  `memberson` int(11) NOT NULL default '0',
  `newthreads` int(11) NOT NULL default '0',
  `newposts` int(11) NOT NULL default '0',
  `maxuserontime` int(20) NOT NULL default '0',
  `maxuseron` int(11) NOT NULL default '0',
  `newpms` int(11) NOT NULL default '0',
  `newregs` int(11) NOT NULL default '0'
) TYPE=MyISAM;



#
# Dumping data for table 'afterburner_stat'
#


#
# Table structure for table 'announcement'
#

DROP TABLE IF EXISTS announcement;
CREATE TABLE `announcement` (
  `announcementid` smallint(5) unsigned NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `userid` int(10) unsigned NOT NULL default '0',
  `startdate` int(10) unsigned NOT NULL default '0',
  `enddate` int(10) unsigned NOT NULL default '0',
  `pagetext` mediumtext NOT NULL,
  `forumid` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`announcementid`),
  KEY `forumid` (`forumid`)
) TYPE=MyISAM;



#
# Dumping data for table 'announcement'
#


#
# Table structure for table 'attachment'
#

DROP TABLE IF EXISTS attachment;
CREATE TABLE `attachment` (
  `attachmentid` smallint(5) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `filename` varchar(100) NOT NULL default '',
  `filedata` mediumtext NOT NULL,
  `visible` smallint(5) unsigned NOT NULL default '0',
  `counter` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attachmentid`)
) TYPE=MyISAM;



#
# Dumping data for table 'attachment'
#


#
# Table structure for table 'avatar'
#

DROP TABLE IF EXISTS avatar;
CREATE TABLE `avatar` (
  `avatarid` smallint(5) unsigned NOT NULL auto_increment,
  `title` char(100) NOT NULL default '',
  `minimumposts` smallint(6) NOT NULL default '0',
  `avatarpath` char(100) NOT NULL default '',
  PRIMARY KEY  (`avatarid`)
) TYPE=MyISAM;



#
# Dumping data for table 'avatar'
#


#
# Table structure for table 'bbcode'
#

DROP TABLE IF EXISTS bbcode;
CREATE TABLE `bbcode` (
  `bbcodeid` smallint(5) unsigned NOT NULL auto_increment,
  `bbcodetag` varchar(200) NOT NULL default '',
  `bbcodereplacement` varchar(200) NOT NULL default '',
  `bbcodeexample` varchar(200) NOT NULL default '',
  `bbcodeexplanation` mediumtext NOT NULL,
  `twoparams` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`bbcodeid`)
) TYPE=MyISAM;



#
# Dumping data for table 'bbcode'
#


#
# Table structure for table 'bedankomat'
#

DROP TABLE IF EXISTS bedankomat;
CREATE TABLE `bedankomat` (
  `thxid` int(10) unsigned NOT NULL auto_increment,
  `threadid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned default '0',
  `ipaddress` varchar(20) default NULL,
  PRIMARY KEY  (`thxid`)
) TYPE=MyISAM;



#
# Dumping data for table 'bedankomat'
#


#
# Table structure for table 'calendar_events'
#

DROP TABLE IF EXISTS calendar_events;
CREATE TABLE `calendar_events` (
  `eventid` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `event` mediumtext NOT NULL,
  `eventdate` date NOT NULL default '0000-00-00',
  `public` smallint(5) unsigned NOT NULL default '0',
  `subject` varchar(254) NOT NULL default '',
  `allowsmilies` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`eventid`),
  KEY `userid` (`userid`)
) TYPE=MyISAM;



#
# Dumping data for table 'calendar_events'
#


#
# Table structure for table 'customavatar'
#

DROP TABLE IF EXISTS customavatar;
CREATE TABLE `customavatar` (
  `userid` int(10) unsigned NOT NULL default '0',
  `avatardata` mediumtext NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `filename` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`userid`)
) TYPE=MyISAM;



#
# Dumping data for table 'customavatar'
#


#
# Table structure for table 'forum'
#

DROP TABLE IF EXISTS forum;
CREATE TABLE `forum` (
  `forumid` smallint(5) unsigned NOT NULL auto_increment,
  `styleid` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `description` varchar(250) NOT NULL default '',
  `active` smallint(6) NOT NULL default '0',
  `displayorder` smallint(6) NOT NULL default '0',
  `replycount` int(10) unsigned NOT NULL default '0',
  `lastpost` int(11) NOT NULL default '0',
  `lastposter` varchar(50) NOT NULL default '',
  `threadcount` mediumint(8) unsigned NOT NULL default '0',
  `allowposting` tinyint(4) NOT NULL default '0',
  `cancontainthreads` smallint(6) NOT NULL default '0',
  `daysprune` smallint(5) unsigned NOT NULL default '0',
  `newpostemail` varchar(250) NOT NULL default '',
  `newthreademail` varchar(250) NOT NULL default '',
  `moderatenew` smallint(6) NOT NULL default '0',
  `moderateattach` smallint(6) NOT NULL default '0',
  `allowbbcode` smallint(6) NOT NULL default '0',
  `allowimages` smallint(6) NOT NULL default '0',
  `allowhtml` smallint(6) NOT NULL default '0',
  `allowsmilies` smallint(6) NOT NULL default '0',
  `allowicons` smallint(6) NOT NULL default '0',
  `parentid` smallint(6) NOT NULL default '0',
  `parentlist` varchar(250) NOT NULL default '',
  `allowratings` smallint(6) NOT NULL default '0',
  `countposts` smallint(6) NOT NULL default '1',
  `styleoverride` smallint(5) NOT NULL default '0',
  `showlasttitle` char(3) NOT NULL default '',
  `lasttitle` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`forumid`)
) TYPE=MyISAM;



#
# Dumping data for table 'forum'
#


#
# Table structure for table 'forumpermission'
#

DROP TABLE IF EXISTS forumpermission;
CREATE TABLE `forumpermission` (
  `forumpermissionid` smallint(5) unsigned NOT NULL auto_increment,
  `forumid` smallint(5) unsigned NOT NULL default '0',
  `usergroupid` smallint(5) unsigned NOT NULL default '0',
  `canview` smallint(6) NOT NULL default '0',
  `cansearch` smallint(6) NOT NULL default '0',
  `canemail` smallint(6) NOT NULL default '0',
  `canpostnew` smallint(6) NOT NULL default '0',
  `canmove` smallint(6) NOT NULL default '0',
  `canopenclose` smallint(6) NOT NULL default '0',
  `candeletethread` smallint(6) NOT NULL default '0',
  `canreplyown` smallint(6) NOT NULL default '0',
  `canreplyothers` smallint(6) NOT NULL default '0',
  `canviewothers` smallint(6) NOT NULL default '0',
  `caneditpost` smallint(6) NOT NULL default '0',
  `candeletepost` smallint(6) NOT NULL default '0',
  `canpostattachment` smallint(6) NOT NULL default '0',
  `canpostpoll` smallint(6) NOT NULL default '0',
  `canvote` smallint(6) NOT NULL default '0',
  `cangetattachment` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`forumpermissionid`),
  KEY `ugid_fid` (`usergroupid`,`forumid`)
) TYPE=MyISAM;



#
# Dumping data for table 'forumpermission'
#


#
# Table structure for table 'icon'
#

DROP TABLE IF EXISTS icon;
CREATE TABLE `icon` (
  `iconid` smallint(5) unsigned NOT NULL auto_increment,
  `title` char(100) NOT NULL default '',
  `iconpath` char(100) NOT NULL default '',
  PRIMARY KEY  (`iconid`)
) TYPE=MyISAM;



#
# Dumping data for table 'icon'
#


#
# Table structure for table 'lasttitle'
#

DROP TABLE IF EXISTS lasttitle;
CREATE TABLE `lasttitle` (
  `field` char(250) NOT NULL default '',
  `lasttitlelength` char(250) NOT NULL default ''
) TYPE=MyISAM;



#
# Dumping data for table 'lasttitle'
#


#
# Table structure for table 'moderator'
#

DROP TABLE IF EXISTS moderator;
CREATE TABLE `moderator` (
  `moderatorid` smallint(5) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `forumid` smallint(6) NOT NULL default '0',
  `newthreademail` smallint(6) NOT NULL default '0',
  `newpostemail` smallint(6) NOT NULL default '0',
  `caneditposts` smallint(6) NOT NULL default '0',
  `candeleteposts` smallint(6) NOT NULL default '0',
  `canviewips` smallint(6) NOT NULL default '0',
  `canmanagethreads` smallint(6) NOT NULL default '0',
  `canopenclose` smallint(6) NOT NULL default '0',
  `caneditthreads` smallint(6) NOT NULL default '0',
  `caneditstyles` smallint(6) NOT NULL default '0',
  `canbanusers` smallint(6) NOT NULL default '0',
  `canviewprofile` smallint(6) NOT NULL default '0',
  `canannounce` smallint(6) NOT NULL default '0',
  `canmassmove` smallint(6) NOT NULL default '0',
  `canmassprune` smallint(6) NOT NULL default '0',
  `canmoderateposts` smallint(6) NOT NULL default '0',
  `canmoderateattachments` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`moderatorid`),
  KEY `userid` (`userid`,`forumid`)
) TYPE=MyISAM;



#
# Dumping data for table 'moderator'
#


#
# Table structure for table 'nuke_advblocks'
#

DROP TABLE IF EXISTS nuke_advblocks;
CREATE TABLE `nuke_advblocks` (
  `bid` int(10) unsigned NOT NULL auto_increment,
  `bkey` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `url` varchar(255) NOT NULL default '',
  `position` char(1) NOT NULL default 'l',
  `weight` decimal(10,1) NOT NULL default '0.0',
  `active` tinyint(3) unsigned NOT NULL default '1',
  `refresh` int(10) unsigned NOT NULL default '0',
  `last_update` timestamp(14) NOT NULL,
  `templates` int(1) NOT NULL default '1',
  PRIMARY KEY  (`bid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_advblocks'
#


#
# Table structure for table 'nuke_advheadlines'
#

DROP TABLE IF EXISTS nuke_advheadlines;
CREATE TABLE `nuke_advheadlines` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sitename` varchar(255) NOT NULL default '',
  `rssurl` varchar(255) NOT NULL default '',
  `siteurl` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_advheadlines'
#


#
# Table structure for table 'nuke_banner'
#

DROP TABLE IF EXISTS nuke_banner;
CREATE TABLE `nuke_banner` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `imptotal` int(11) NOT NULL default '0',
  `impmade` int(11) NOT NULL default '0',
  `clicks` int(11) NOT NULL default '0',
  `imageurl` varchar(100) NOT NULL default '',
  `clickurl` varchar(200) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`bid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_banner'
#


#
# Table structure for table 'nuke_bannerclient'
#

DROP TABLE IF EXISTS nuke_bannerclient;
CREATE TABLE `nuke_bannerclient` (
  `cid` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `contact` varchar(60) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `login` varchar(10) NOT NULL default '',
  `passwd` varchar(10) NOT NULL default '',
  `extrainfo` text NOT NULL,
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_bannerclient'
#


#
# Table structure for table 'nuke_bannerfinish'
#

DROP TABLE IF EXISTS nuke_bannerfinish;
CREATE TABLE `nuke_bannerfinish` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `impressions` int(11) NOT NULL default '0',
  `clicks` int(11) NOT NULL default '0',
  `datestart` datetime NOT NULL default '0000-00-00 00:00:00',
  `dateend` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`bid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_bannerfinish'
#


#
# Table structure for table 'nuke_centerblocks'
#

DROP TABLE IF EXISTS nuke_centerblocks;
CREATE TABLE `nuke_centerblocks` (
  `bid` int(10) NOT NULL auto_increment,
  `bkey` varchar(15) NOT NULL default '',
  `title` varchar(60) NOT NULL default '',
  `content` text NOT NULL,
  `url` varchar(200) NOT NULL default '',
  `position` char(1) NOT NULL default '',
  `weight` int(10) NOT NULL default '1',
  `active` int(1) NOT NULL default '1',
  `refresh` int(10) NOT NULL default '0',
  `last_update` timestamp(14) NOT NULL,
  `templates` int(1) NOT NULL default '1',
  PRIMARY KEY  (`bid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_centerblocks'
#


#
# Table structure for table 'nuke_counter'
#

DROP TABLE IF EXISTS nuke_counter;
CREATE TABLE `nuke_counter` (
  `type` varchar(80) NOT NULL default '',
  `var` varchar(80) NOT NULL default '',
  `count` int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_counter'
#


#
# Table structure for table 'nuke_downloads_categories'
#

DROP TABLE IF EXISTS nuke_downloads_categories;
CREATE TABLE `nuke_downloads_categories` (
  `cid` int(11) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `cdescription` text NOT NULL,
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_downloads_categories'
#


#
# Table structure for table 'nuke_downloads_downloads'
#

DROP TABLE IF EXISTS nuke_downloads_downloads;
CREATE TABLE `nuke_downloads_downloads` (
  `lid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `hits` int(11) NOT NULL default '0',
  `submitter` varchar(60) NOT NULL default '',
  `downloadratingsummary` double(6,4) NOT NULL default '0.0000',
  `totalvotes` int(11) NOT NULL default '0',
  `totalcomments` int(11) NOT NULL default '0',
  `filesize` int(11) NOT NULL default '0',
  `version` varchar(10) NOT NULL default '',
  `homepage` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`lid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_downloads_downloads'
#


#
# Table structure for table 'nuke_downloads_editorials'
#

DROP TABLE IF EXISTS nuke_downloads_editorials;
CREATE TABLE `nuke_downloads_editorials` (
  `downloadid` int(11) NOT NULL default '0',
  `adminid` varchar(60) NOT NULL default '',
  `editorialtimestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `editorialtext` text NOT NULL,
  `editorialtitle` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`downloadid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_downloads_editorials'
#


#
# Table structure for table 'nuke_downloads_modrequest'
#

DROP TABLE IF EXISTS nuke_downloads_modrequest;
CREATE TABLE `nuke_downloads_modrequest` (
  `requestid` int(11) NOT NULL auto_increment,
  `lid` int(11) NOT NULL default '0',
  `cid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `modifysubmitter` varchar(60) NOT NULL default '',
  `brokendownload` int(3) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `version` varchar(10) NOT NULL default '',
  `homepage` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`requestid`),
  KEY `requestid` (`requestid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_downloads_modrequest'
#


#
# Table structure for table 'nuke_downloads_newdownload'
#

DROP TABLE IF EXISTS nuke_downloads_newdownload;
CREATE TABLE `nuke_downloads_newdownload` (
  `lid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `name` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `submitter` varchar(60) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `version` varchar(10) NOT NULL default '',
  `homepage` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`lid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_downloads_newdownload'
#


#
# Table structure for table 'nuke_downloads_subcategories'
#

DROP TABLE IF EXISTS nuke_downloads_subcategories;
CREATE TABLE `nuke_downloads_subcategories` (
  `sid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`sid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_downloads_subcategories'
#


#
# Table structure for table 'nuke_downloads_votedata'
#

DROP TABLE IF EXISTS nuke_downloads_votedata;
CREATE TABLE `nuke_downloads_votedata` (
  `ratingdbid` int(11) NOT NULL auto_increment,
  `ratinglid` int(11) NOT NULL default '0',
  `ratinguser` varchar(60) NOT NULL default '',
  `rating` int(11) NOT NULL default '0',
  `ratinghostname` varchar(60) NOT NULL default '',
  `ratingcomments` text NOT NULL,
  `ratingtimestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ratingdbid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_downloads_votedata'
#


#
# Table structure for table 'nuke_ephem'
#

DROP TABLE IF EXISTS nuke_ephem;
CREATE TABLE `nuke_ephem` (
  `eid` int(11) NOT NULL auto_increment,
  `did` int(2) NOT NULL default '0',
  `mid` int(2) NOT NULL default '0',
  `yid` int(4) NOT NULL default '0',
  `content` text NOT NULL,
  PRIMARY KEY  (`eid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_ephem'
#


#
# Table structure for table 'nuke_faqanswer'
#

DROP TABLE IF EXISTS nuke_faqanswer;
CREATE TABLE `nuke_faqanswer` (
  `id` tinyint(4) NOT NULL auto_increment,
  `id_cat` tinyint(4) NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `answer` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_faqanswer'
#


#
# Table structure for table 'nuke_faqcategories'
#

DROP TABLE IF EXISTS nuke_faqcategories;
CREATE TABLE `nuke_faqcategories` (
  `id_cat` tinyint(3) NOT NULL auto_increment,
  `categories` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_cat`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_faqcategories'
#


#
# Table structure for table 'nuke_forumblocks'
#

DROP TABLE IF EXISTS nuke_forumblocks;
CREATE TABLE `nuke_forumblocks` (
  `bid` int(10) unsigned NOT NULL auto_increment,
  `bkey` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `url` varchar(255) NOT NULL default '',
  `position` char(1) NOT NULL default 'l',
  `weight` decimal(10,1) NOT NULL default '0.0',
  `active` tinyint(3) unsigned NOT NULL default '1',
  `refresh` int(10) unsigned NOT NULL default '0',
  `last_update` timestamp(14) NOT NULL,
  `templates` int(1) NOT NULL default '1',
  PRIMARY KEY  (`bid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_forumblocks'
#


#
# Table structure for table 'nuke_links_categories'
#

DROP TABLE IF EXISTS nuke_links_categories;
CREATE TABLE `nuke_links_categories` (
  `cid` int(11) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `cdescription` text NOT NULL,
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_links_categories'
#


#
# Table structure for table 'nuke_links_editorials'
#

DROP TABLE IF EXISTS nuke_links_editorials;
CREATE TABLE `nuke_links_editorials` (
  `linkid` int(11) NOT NULL default '0',
  `adminid` varchar(60) NOT NULL default '',
  `editorialtimestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `editorialtext` text NOT NULL,
  `editorialtitle` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`linkid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_links_editorials'
#


#
# Table structure for table 'nuke_links_links'
#

DROP TABLE IF EXISTS nuke_links_links;
CREATE TABLE `nuke_links_links` (
  `lid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(60) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `hits` int(11) NOT NULL default '0',
  `submitter` varchar(60) NOT NULL default '',
  `linkratingsummary` double(6,4) NOT NULL default '0.0000',
  `totalvotes` int(11) NOT NULL default '0',
  `totalcomments` int(11) NOT NULL default '0',
  PRIMARY KEY  (`lid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_links_links'
#


#
# Table structure for table 'nuke_links_modrequest'
#

DROP TABLE IF EXISTS nuke_links_modrequest;
CREATE TABLE `nuke_links_modrequest` (
  `requestid` int(11) NOT NULL auto_increment,
  `lid` int(11) NOT NULL default '0',
  `cid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `modifysubmitter` varchar(60) NOT NULL default '',
  `brokenlink` int(3) NOT NULL default '0',
  PRIMARY KEY  (`requestid`),
  KEY `requestid` (`requestid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_links_modrequest'
#


#
# Table structure for table 'nuke_links_newlink'
#

DROP TABLE IF EXISTS nuke_links_newlink;
CREATE TABLE `nuke_links_newlink` (
  `lid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `name` varchar(60) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `submitter` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`lid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_links_newlink'
#


#
# Table structure for table 'nuke_links_subcategories'
#

DROP TABLE IF EXISTS nuke_links_subcategories;
CREATE TABLE `nuke_links_subcategories` (
  `sid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`sid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_links_subcategories'
#


#
# Table structure for table 'nuke_links_votedata'
#

DROP TABLE IF EXISTS nuke_links_votedata;
CREATE TABLE `nuke_links_votedata` (
  `ratingdbid` int(11) NOT NULL auto_increment,
  `ratinglid` int(11) NOT NULL default '0',
  `ratinguser` varchar(60) NOT NULL default '',
  `rating` int(11) NOT NULL default '0',
  `ratinghostname` varchar(60) NOT NULL default '',
  `ratingcomments` text NOT NULL,
  `ratingtimestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ratingdbid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_links_votedata'
#


#
# Table structure for table 'nuke_message'
#

DROP TABLE IF EXISTS nuke_message;
CREATE TABLE `nuke_message` (
  `title` varchar(100) NOT NULL default '',
  `content` text NOT NULL,
  `date` varchar(14) NOT NULL default '',
  `expire` int(7) NOT NULL default '0',
  `active` int(1) NOT NULL default '1',
  `view` int(1) NOT NULL default '1'
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_message'
#


#
# Table structure for table 'nuke_referer'
#

DROP TABLE IF EXISTS nuke_referer;
CREATE TABLE `nuke_referer` (
  `rid` int(11) NOT NULL auto_increment,
  `url` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`rid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_referer'
#


#
# Table structure for table 'nuke_reviews'
#

DROP TABLE IF EXISTS nuke_reviews;
CREATE TABLE `nuke_reviews` (
  `id` int(10) NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `title` varchar(150) NOT NULL default '',
  `text` text NOT NULL,
  `reviewer` varchar(20) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `score` int(10) NOT NULL default '0',
  `cover` varchar(100) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `url_title` varchar(50) NOT NULL default '',
  `hits` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_reviews'
#


#
# Table structure for table 'nuke_reviews_add'
#

DROP TABLE IF EXISTS nuke_reviews_add;
CREATE TABLE `nuke_reviews_add` (
  `id` int(10) NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `title` varchar(150) NOT NULL default '',
  `text` text NOT NULL,
  `reviewer` varchar(20) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `score` int(10) NOT NULL default '0',
  `url` varchar(100) NOT NULL default '',
  `url_title` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_reviews_add'
#


#
# Table structure for table 'nuke_reviews_comments'
#

DROP TABLE IF EXISTS nuke_reviews_comments;
CREATE TABLE `nuke_reviews_comments` (
  `cid` int(10) NOT NULL auto_increment,
  `rid` int(10) NOT NULL default '0',
  `userid` varchar(25) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  `score` int(10) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_reviews_comments'
#


#
# Table structure for table 'nuke_reviews_main'
#

DROP TABLE IF EXISTS nuke_reviews_main;
CREATE TABLE `nuke_reviews_main` (
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_reviews_main'
#


#
# Table structure for table 'nuke_seccont'
#

DROP TABLE IF EXISTS nuke_seccont;
CREATE TABLE `nuke_seccont` (
  `artid` int(11) NOT NULL auto_increment,
  `secid` int(11) NOT NULL default '0',
  `title` text NOT NULL,
  `content` text NOT NULL,
  `counter` int(11) NOT NULL default '0',
  PRIMARY KEY  (`artid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_seccont'
#


#
# Table structure for table 'nuke_sections'
#

DROP TABLE IF EXISTS nuke_sections;
CREATE TABLE `nuke_sections` (
  `secid` int(11) NOT NULL auto_increment,
  `secname` varchar(40) NOT NULL default '',
  `image` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`secid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_sections'
#


#
# Table structure for table 'nuke_topics'
#

DROP TABLE IF EXISTS nuke_topics;
CREATE TABLE `nuke_topics` (
  `topicid` int(3) NOT NULL auto_increment,
  `topicname` varchar(20) NOT NULL default '',
  `topicimage` varchar(20) NOT NULL default '',
  `topictext` varchar(40) NOT NULL default '',
  `counter` int(11) NOT NULL default '0',
  PRIMARY KEY  (`topicid`)
) TYPE=MyISAM;



#
# Dumping data for table 'nuke_topics'
#


#
# Table structure for table 'poll'
#

DROP TABLE IF EXISTS poll;
CREATE TABLE `poll` (
  `pollid` int(10) unsigned NOT NULL auto_increment,
  `question` varchar(100) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `options` text NOT NULL,
  `votes` text NOT NULL,
  `active` smallint(6) NOT NULL default '1',
  `numberoptions` smallint(5) unsigned NOT NULL default '0',
  `timeout` smallint(5) unsigned NOT NULL default '0',
  `multiple` smallint(5) unsigned NOT NULL default '0',
  `voters` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pollid`)
) TYPE=MyISAM;



#
# Dumping data for table 'poll'
#


#
# Table structure for table 'pollvote'
#

DROP TABLE IF EXISTS pollvote;
CREATE TABLE `pollvote` (
  `pollvoteid` int(10) unsigned NOT NULL auto_increment,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `votedate` int(10) unsigned NOT NULL default '0',
  `voteoption` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pollvoteid`),
  KEY `userid` (`userid`,`pollid`)
) TYPE=MyISAM;



#
# Dumping data for table 'pollvote'
#


#
# Table structure for table 'post'
#

DROP TABLE IF EXISTS post;
CREATE TABLE `post` (
  `postid` int(10) unsigned NOT NULL auto_increment,
  `threadid` int(10) unsigned NOT NULL default '0',
  `username` varchar(50) NOT NULL default '',
  `userid` int(10) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `attachmentid` smallint(5) unsigned NOT NULL default '0',
  `pagetext` mediumtext NOT NULL,
  `allowsmilie` smallint(6) NOT NULL default '0',
  `showsignature` smallint(6) NOT NULL default '0',
  `ipaddress` varchar(16) NOT NULL default '',
  `iconid` smallint(5) unsigned NOT NULL default '0',
  `visible` smallint(6) NOT NULL default '0',
  `edituserid` int(10) unsigned NOT NULL default '0',
  `editdate` int(10) unsigned NOT NULL default '0',
  `topic` int(3) NOT NULL default '0',
  PRIMARY KEY  (`postid`),
  KEY `iconid` (`iconid`),
  KEY `userid` (`userid`),
  KEY `threadid` (`threadid`,`userid`)
) TYPE=MyISAM;



#
# Dumping data for table 'post'
#


#
# Table structure for table 'privatemessage'
#

DROP TABLE IF EXISTS privatemessage;
CREATE TABLE `privatemessage` (
  `privatemessageid` int(10) unsigned NOT NULL auto_increment,
  `folderid` smallint(6) NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `touserid` int(10) unsigned NOT NULL default '0',
  `fromuserid` int(10) unsigned NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `message` mediumtext NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `showsignature` smallint(6) NOT NULL default '0',
  `iconid` smallint(5) unsigned NOT NULL default '0',
  `messageread` smallint(6) NOT NULL default '0',
  `readtime` int(10) unsigned NOT NULL default '0',
  `receipt` smallint(6) unsigned NOT NULL default '0',
  `deleteprompt` smallint(6) unsigned NOT NULL default '0',
  `multiplerecipients` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`privatemessageid`),
  KEY `userid` (`userid`)
) TYPE=MyISAM;



#
# Dumping data for table 'privatemessage'
#


#
# Table structure for table 'profilefield'
#

DROP TABLE IF EXISTS profilefield;
CREATE TABLE `profilefield` (
  `profilefieldid` smallint(5) unsigned NOT NULL auto_increment,
  `title` char(50) NOT NULL default '',
  `description` char(250) NOT NULL default '',
  `required` smallint(6) NOT NULL default '0',
  `hidden` smallint(6) NOT NULL default '0',
  `maxlength` smallint(6) NOT NULL default '250',
  `size` smallint(6) NOT NULL default '25',
  `displayorder` smallint(6) NOT NULL default '0',
  `editable` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`profilefieldid`)
) TYPE=MyISAM;



#
# Dumping data for table 'profilefield'
#


#
# Table structure for table 'replacement'
#

DROP TABLE IF EXISTS replacement;
CREATE TABLE `replacement` (
  `replacementid` smallint(5) unsigned NOT NULL auto_increment,
  `replacementsetid` smallint(6) NOT NULL default '0',
  `findword` text NOT NULL,
  `replaceword` text NOT NULL,
  PRIMARY KEY  (`replacementid`),
  KEY `replacementsetid` (`replacementsetid`)
) TYPE=MyISAM;



#
# Dumping data for table 'replacement'
#


#
# Table structure for table 'replacementset'
#

DROP TABLE IF EXISTS replacementset;
CREATE TABLE `replacementset` (
  `replacementsetid` smallint(5) unsigned NOT NULL auto_increment,
  `title` char(250) NOT NULL default '',
  PRIMARY KEY  (`replacementsetid`)
) TYPE=MyISAM;



#
# Dumping data for table 'replacementset'
#


#
# Table structure for table 'search'
#

DROP TABLE IF EXISTS search;
CREATE TABLE `search` (
  `searchid` int(10) unsigned NOT NULL auto_increment,
  `query` mediumtext NOT NULL,
  `postids` mediumtext NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `querystring` varchar(200) NOT NULL default '',
  `showposts` smallint(6) NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `ipaddress` varchar(20) NOT NULL default '',
  `topic` int(3) NOT NULL default '0',
  PRIMARY KEY  (`searchid`),
  KEY `querystring` (`querystring`),
  KEY `userid` (`userid`)
) TYPE=MyISAM;



#
# Dumping data for table 'search'
#


#
# Table structure for table 'searchindex'
#

DROP TABLE IF EXISTS searchindex;
CREATE TABLE `searchindex` (
  `wordid` int(10) unsigned NOT NULL default '0',
  `postid` int(10) unsigned NOT NULL default '0',
  `intitle` smallint(5) unsigned NOT NULL default '0',
  UNIQUE KEY `wordid` (`wordid`,`postid`)
) TYPE=MyISAM;



#
# Dumping data for table 'searchindex'
#


#
# Table structure for table 'session'
#

DROP TABLE IF EXISTS session;
CREATE TABLE `session` (
  `sessionhash` char(32) NOT NULL default '',
  `userid` int(10) unsigned NOT NULL default '0',
  `host` char(50) NOT NULL default '',
  `useragent` char(50) NOT NULL default '',
  `lastactivity` int(10) unsigned NOT NULL default '0',
  `location` char(255) NOT NULL default '',
  `styleid` smallint(5) unsigned NOT NULL default '0',
  `althash` char(32) NOT NULL default '',
  PRIMARY KEY  (`sessionhash`)
) TYPE=HEAP;



#
# Dumping data for table 'session'
#


#
# Table structure for table 'setting'
#

DROP TABLE IF EXISTS setting;
CREATE TABLE `setting` (
  `settingid` smallint(5) unsigned NOT NULL auto_increment,
  `settinggroupid` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `varname` varchar(100) NOT NULL default '',
  `value` mediumtext NOT NULL,
  `description` mediumtext NOT NULL,
  `optioncode` mediumtext NOT NULL,
  `displayorder` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`settingid`)
) TYPE=MyISAM;



#
# Dumping data for table 'setting'
#


#
# Table structure for table 'settinggroup'
#

DROP TABLE IF EXISTS settinggroup;
CREATE TABLE `settinggroup` (
  `settinggroupid` smallint(5) unsigned NOT NULL auto_increment,
  `title` char(100) NOT NULL default '',
  `displayorder` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`settinggroupid`)
) TYPE=MyISAM;



#
# Dumping data for table 'settinggroup'
#


#
# Table structure for table 'smilie'
#

DROP TABLE IF EXISTS smilie;
CREATE TABLE `smilie` (
  `smilieid` smallint(5) unsigned NOT NULL auto_increment,
  `title` char(100) NOT NULL default '',
  `smilietext` char(10) NOT NULL default '',
  `smiliepath` char(100) NOT NULL default '',
  PRIMARY KEY  (`smilieid`)
) TYPE=MyISAM;



#
# Dumping data for table 'smilie'
#


#
# Table structure for table 'style'
#

DROP TABLE IF EXISTS style;
CREATE TABLE `style` (
  `styleid` smallint(5) unsigned NOT NULL auto_increment,
  `replacementsetid` smallint(5) unsigned NOT NULL default '0',
  `templatesetid` smallint(5) unsigned NOT NULL default '0',
  `title` char(250) NOT NULL default '',
  `userselect` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`styleid`)
) TYPE=MyISAM;



#
# Dumping data for table 'style'
#


#
# Table structure for table 'subscribeforum'
#

DROP TABLE IF EXISTS subscribeforum;
CREATE TABLE `subscribeforum` (
  `subscribeforumid` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `forumid` smallint(5) unsigned NOT NULL default '0',
  `emailupdate` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`subscribeforumid`),
  KEY `userid` (`userid`)
) TYPE=MyISAM;



#
# Dumping data for table 'subscribeforum'
#


#
# Table structure for table 'subscribethread'
#

DROP TABLE IF EXISTS subscribethread;
CREATE TABLE `subscribethread` (
  `subscribethreadid` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `threadid` int(10) unsigned NOT NULL default '0',
  `emailupdate` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`subscribethreadid`),
  KEY `threadid` (`threadid`)
) TYPE=MyISAM;



#
# Dumping data for table 'subscribethread'
#


#
# Table structure for table 'template'
#

DROP TABLE IF EXISTS template;
CREATE TABLE `template` (
  `templateid` smallint(5) unsigned NOT NULL auto_increment,
  `templatesetid` smallint(6) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `template` mediumtext NOT NULL,
  PRIMARY KEY  (`templateid`),
  KEY `title` (`title`(30),`templatesetid`)
) TYPE=MyISAM;



#
# Dumping data for table 'template'
#


#
# Table structure for table 'templateset'
#

DROP TABLE IF EXISTS templateset;
CREATE TABLE `templateset` (
  `templatesetid` smallint(5) unsigned NOT NULL auto_increment,
  `title` char(250) NOT NULL default '',
  PRIMARY KEY  (`templatesetid`)
) TYPE=MyISAM;



#
# Dumping data for table 'templateset'
#


#
# Table structure for table 'thread'
#

DROP TABLE IF EXISTS thread;
CREATE TABLE `thread` (
  `threadid` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `forumid` smallint(5) unsigned NOT NULL default '0',
  `pollid` int(10) unsigned NOT NULL default '0',
  `open` tinyint(4) NOT NULL default '0',
  `replycount` int(10) unsigned NOT NULL default '0',
  `postusername` varchar(50) NOT NULL default '',
  `postuserid` int(10) unsigned NOT NULL default '0',
  `lastposter` varchar(50) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `iconid` smallint(5) unsigned NOT NULL default '0',
  `notes` varchar(250) NOT NULL default '',
  `visible` smallint(6) NOT NULL default '0',
  `sticky` smallint(6) NOT NULL default '0',
  `votenum` smallint(5) unsigned NOT NULL default '0',
  `votetotal` smallint(5) unsigned NOT NULL default '0',
  `attach` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`threadid`),
  KEY `iconid` (`iconid`),
  KEY `forumid` (`forumid`,`visible`,`sticky`,`lastpost`),
  KEY `pollid` (`pollid`),
  KEY `pollid_2` (`pollid`),
  KEY `pollid_3` (`pollid`),
  KEY `pollid_4` (`pollid`),
  KEY `pollid_5` (`pollid`),
  KEY `pollid_6` (`pollid`),
  KEY `pollid_7` (`pollid`),
  KEY `pollid_8` (`pollid`)
) TYPE=MyISAM;



#
# Dumping data for table 'thread'
#


#
# Table structure for table 'threadrate'
#

DROP TABLE IF EXISTS threadrate;
CREATE TABLE `threadrate` (
  `threadrateid` int(10) unsigned NOT NULL auto_increment,
  `threadid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `vote` smallint(6) NOT NULL default '0',
  `ipaddress` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`threadrateid`),
  KEY `threadid` (`threadid`)
) TYPE=MyISAM;



#
# Dumping data for table 'threadrate'
#


#
# Table structure for table 'user'
#

DROP TABLE IF EXISTS user;
CREATE TABLE `user` (
  `userid` int(10) unsigned NOT NULL auto_increment,
  `usergroupid` smallint(5) unsigned NOT NULL default '0',
  `username` varchar(50) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `styleid` smallint(5) unsigned NOT NULL default '0',
  `parentemail` varchar(50) NOT NULL default '',
  `coppauser` smallint(6) NOT NULL default '0',
  `homepage` varchar(100) NOT NULL default '',
  `icq` varchar(20) NOT NULL default '',
  `aim` varchar(20) NOT NULL default '',
  `yahoo` varchar(20) NOT NULL default '',
  `signature` mediumtext NOT NULL,
  `adminemail` smallint(6) NOT NULL default '0',
  `showemail` smallint(6) NOT NULL default '0',
  `invisible` smallint(6) NOT NULL default '0',
  `usertitle` varchar(250) NOT NULL default '',
  `customtitle` smallint(6) NOT NULL default '0',
  `joindate` int(10) unsigned NOT NULL default '0',
  `cookieuser` smallint(6) NOT NULL default '0',
  `daysprune` smallint(6) NOT NULL default '0',
  `lastvisit` int(10) unsigned NOT NULL default '0',
  `lastactivity` int(10) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `posts` smallint(5) unsigned NOT NULL default '0',
  `timezoneoffset` varchar(4) NOT NULL default '',
  `emailnotification` smallint(6) NOT NULL default '0',
  `buddylist` mediumtext NOT NULL,
  `ignorelist` mediumtext NOT NULL,
  `pmfolders` mediumtext NOT NULL,
  `receivepm` smallint(6) NOT NULL default '0',
  `emailonpm` smallint(6) NOT NULL default '0',
  `pmpopup` smallint(6) NOT NULL default '0',
  `avatarid` smallint(6) NOT NULL default '0',
  `options` smallint(6) NOT NULL default '15',
  `birthday` date NOT NULL default '0000-00-00',
  `maxposts` smallint(6) NOT NULL default '-1',
  `startofweek` smallint(6) NOT NULL default '1',
  `ipaddress` varchar(20) NOT NULL default '',
  `referrerid` int(10) unsigned NOT NULL default '0',
  `nosessionhash` smallint(6) NOT NULL default '0',
  `inforum` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userid`),
  KEY `usergroupid` (`usergroupid`),
  KEY `username` (`username`),
  KEY `inforum` (`inforum`)
) TYPE=MyISAM;



#
# Dumping data for table 'user'
#


#
# Table structure for table 'useractivation'
#

DROP TABLE IF EXISTS useractivation;
CREATE TABLE `useractivation` (
  `useractivationid` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `activationid` char(20) NOT NULL default '',
  `type` smallint(5) unsigned NOT NULL default '0',
  `usergroupid` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`useractivationid`),
  KEY `userid` (`userid`,`type`)
) TYPE=MyISAM;



#
# Dumping data for table 'useractivation'
#


#
# Table structure for table 'userfield'
#

DROP TABLE IF EXISTS userfield;
CREATE TABLE `userfield` (
  `userid` int(10) unsigned NOT NULL default '0',
  `field1` char(250) NOT NULL default '',
  `field2` char(250) NOT NULL default '',
  `field3` char(250) NOT NULL default '',
  `field4` char(250) NOT NULL default '',
  PRIMARY KEY  (`userid`)
) TYPE=MyISAM;



#
# Dumping data for table 'userfield'
#


#
# Table structure for table 'usergroup'
#

DROP TABLE IF EXISTS usergroup;
CREATE TABLE `usergroup` (
  `usergroupid` smallint(5) unsigned NOT NULL auto_increment,
  `title` char(100) NOT NULL default '',
  `usertitle` char(100) NOT NULL default '',
  `cancontrolpanel` smallint(6) NOT NULL default '0',
  `canmodifyprofile` smallint(6) NOT NULL default '0',
  `canviewmembers` smallint(6) NOT NULL default '0',
  `canview` smallint(6) NOT NULL default '0',
  `cansearch` smallint(6) NOT NULL default '0',
  `canemail` smallint(6) NOT NULL default '0',
  `canpostnew` smallint(6) NOT NULL default '0',
  `canmove` smallint(6) NOT NULL default '0',
  `canopenclose` smallint(6) NOT NULL default '0',
  `candeletethread` smallint(6) NOT NULL default '0',
  `canreplyown` smallint(6) NOT NULL default '0',
  `canreplyothers` smallint(6) NOT NULL default '0',
  `canviewothers` smallint(6) NOT NULL default '0',
  `caneditpost` smallint(6) NOT NULL default '0',
  `candeletepost` smallint(6) NOT NULL default '0',
  `canusepm` smallint(6) NOT NULL default '0',
  `canpostpoll` smallint(6) NOT NULL default '0',
  `canvote` smallint(6) NOT NULL default '0',
  `canpostattachment` smallint(6) NOT NULL default '0',
  `canpublicevent` smallint(6) NOT NULL default '0',
  `canpublicedit` smallint(6) NOT NULL default '0',
  `canthreadrate` smallint(6) NOT NULL default '1',
  `maxbuddypm` smallint(6) unsigned NOT NULL default '5',
  `maxforwardpm` smallint(6) unsigned NOT NULL default '5',
  `cantrackpm` smallint(6) NOT NULL default '1',
  `candenypmreceipts` smallint(6) NOT NULL default '1',
  `canwhosonline` smallint(6) NOT NULL default '1',
  `canwhosonlineip` smallint(6) NOT NULL default '0',
  `ismoderator` smallint(6) NOT NULL default '0',
  `showgroup` smallint(5) unsigned NOT NULL default '0',
  `cangetattachment` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`usergroupid`),
  KEY `showgroup` (`showgroup`)
) TYPE=MyISAM;



#
# Dumping data for table 'usergroup'
#


#
# Table structure for table 'usertitle'
#

DROP TABLE IF EXISTS usertitle;
CREATE TABLE `usertitle` (
  `usertitleid` smallint(5) unsigned NOT NULL auto_increment,
  `minposts` smallint(5) unsigned NOT NULL default '0',
  `title` char(250) NOT NULL default '',
  PRIMARY KEY  (`usertitleid`)
) TYPE=MyISAM;



#
# Dumping data for table 'usertitle'
#


#
# Table structure for table 'word'
#

DROP TABLE IF EXISTS word;
CREATE TABLE `word` (
  `wordid` int(10) unsigned NOT NULL auto_increment,
  `title` char(50) NOT NULL default '',
  PRIMARY KEY  (`wordid`),
  UNIQUE KEY `title` (`title`)
) TYPE=MyISAM;



#
# Dumping data for table 'word'
#
