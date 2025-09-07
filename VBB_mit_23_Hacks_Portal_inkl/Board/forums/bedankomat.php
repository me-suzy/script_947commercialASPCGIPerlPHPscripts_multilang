<?php
error_reporting(7);

require("./global.php");

/*
// Bedankomat relies on this:

CREATE TABLE `bedankomat` (
`thxid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
`threadid` INT(10) UNSIGNED NOT NULL, 
`userid` INT(10) UNSIGNED DEFAULT '0', 
`ipaddress` VARCHAR(20)
);
INSERT INTO `setting` (`settingid`, `settinggroupid`, `title`, `varname`, `value`, `description`, `optioncode`, `displayorder`) VALUES ('', '9', 'Bedankomat Forenliste', 'bedankomat_forumlist', '1 3 21',
 'Enthält eine durch Leerzeichen getrennte Liste von ForumIDs, in denen der Bedankomat angeschaltet ist.', '', '99'); 
INSERT INTO `setting` (`settingid`, `settinggroupid`, `title`, `varname`, `value`, `description`, `optioncode`, `displayorder`) VALUES ('', '9', 'Bedankomat PushUp', 'bedankomat_pushup', '1',
 'Bedanken befördert Thread nach oben, indem die lastpost Zeit auf die aktuelle Zeit gesetzt wird.', 'yesno', '99'); 
INSERT INTO `setting` (`settingid`, `settinggroupid`, `title`, `varname`, `value`, `description`, `optioncode`, `displayorder`) VALUES ('', '9', 'Bedankomat Order By Username', 'bedankomat_orderbyusername', '1',
 'Alphabetische Liste der dankenden User. Nein bedeutet Liste in Reihenfolge der Danksagungen.', 'yesno', '99'); 

// Save options once in Admin CP after installation else settings won't get updated!
*/

$bedankomat_username='Henry James';
global $bedankomat_forum;

/* 
// Get settings from DB - no need to do this if you saved the settings once in AdminCP
if (!$bedankomat_forumlist) {
	$bedankomat_settings = $DB_site->query_first("SELECT setting.value AS bedankomat_forumlist
		FROM setting
		WHERE setting.varname = 'bedankomat_forumlist' LIMIT 1");
	$bedankomat_forumlist = $bedankomat_settings['bedankomat_forumlist'];
}
*/

// Get list of Bedankomat-enabled forums
if($bedankomat_forumlist) {
	$bedankomat_forums = explode(' ', $bedankomat_forumlist);
	while ( list($key, $val)=each($bedankomat_forums) ) {
  		$bedankomat_forum[$val] = 1;
	}
}

/*
if (!preg_match("/bedankomat/i",$PHP_SELF)) {
	// Called from another script...
}
*/

$permissions=getpermissions();
if (!$permissions['canreplyothers'] || !$permissions['canreplyown']) {
  echo "<h1>ho!</h1>";
  show_nopermission();
  exit;
}

$threadid = verifyid("thread",$threadid);
$threadinfo=getthreadinfo($threadid);
updateuserforum($threadinfo['forumid']);
if ($threadinfo[visible]==0 or $threadinfo[open]==0) {
  eval("standarderror(\"".gettemplate("error_threadrateclosed")."\");");
  exit;
}

if ($bedankomat_forum[$threadinfo['forumid']]!=1) {
	show_nopermission();
	exit;	
}

$forumid = intval($threadinfo['forumid']);
$foruminfo = verifyid('forum',$forumid,1,1);

$bedankomat_changed=0;
if ($bbuserinfo['userid'] != 0) {
   if ($rating=$DB_site->query_first("SELECT thxid
                                      FROM bedankomat
                                      WHERE userid = '$bbuserinfo[userid]'
                                      AND threadid = '$threadid'")) {
      $bedankomat_exit="standarderror(\"".gettemplate("error_threadratevoted")."\");";
    } else {
      $DB_site->query("INSERT INTO bedankomat (thxid,threadid,userid)
                       VALUES (NULL, '$threadid','$bbuserinfo[userid]')");
      $bedankomat_changed=1;
      $bedankomat_exit="standardredirect(\"".gettemplate("redirect_threadrate_add")."\",\"showthread.php?s=$session[sessionhash]&threadid=$threadid\");";
    }
} else {
   // Check for entry in Database for this Ip Addr/Threadid
   // Check for cookie on user's computer for this threadid
   if ($rating=$DB_site->query_first("SELECT threadid
                                      FROM bedankomat
                                      WHERE ipaddress = '".addslashes($ipaddress)."'
                                      AND threadid = '$threadid'")) {
        $bedankomat_exit="standarderror(\"".gettemplate("error_threadratevoted")."\");";
   } elseif (isset($bbbedankomat[$threadid])) {
      $bedankomat_exit="standarderror(\"".gettemplate("error_threadratevoted")."\");";
   } else {
      $DB_site->query("INSERT INTO bedankomat (thxid,threadid,ipaddress)
                       VALUES (NULL, '$threadid','".addslashes($ipaddress)."')");
      $bedankomat_changed=1;
      vbsetcookie("bbbedankomat[$threadid]",$vote);
      $bedankomat_exit="standardredirect(\"".gettemplate("redirect_threadrate_add")."\",\"showthread.php?s=$session[sessionhash]&threadid=$threadid\");";
   }
}

$bedankomate = $DB_site->query_first("SELECT COUNT(*) AS nums
				       FROM bedankomat
				       WHERE threadid = '$threadid'");
if ($bedankomat_changed==1) {
	$bedankomates = intval($bedankomate['nums']);

	// Get UserID of Bedankomat if user exists
	if ($bedankomate=$DB_site->query_first("SELECT userid
					   FROM user
					   WHERE username='$bedankomat_username'")) {
		$bedankomat_userid=$bedankomate['userid'];
	} else {
		$bedankomat_userid=0;
	}
	
	if ($bedankomat_orderbyusername>0) {
		$order_by='ORDER BY user.username ASC';
	}
	$bedankomate = $DB_site->query("SELECT bedankomat.userid AS buserid,
						 user.username AS busername
					    FROM bedankomat
					    LEFT JOIN user
					    	ON user.userid = bedankomat.userid
					    WHERE threadid = '$threadid' $order_by");
					    
	// Make list of users who said thx
	while ($bedankomatuser=$DB_site->fetch_array($bedankomate)) {
		if (isset($bedankomatuser['busername'])) {
			if ($bedankomatusers) {
				$bedankomatusers .= ', ';
			}
			if ($foruminfo['allowhtml']) {
				$bedankomatusers .= '<b><a href="'.$bburl.'/member.php?s='.$session[sessionhash].'&action=getinfo&userid='
					    .$bedankomatuser['buserid'].'">'
					    .$bedankomatuser['busername'].'</a></b> ';
			} elseif ($foruminfo['allowbbcode']) {
				$bedankomatusers .= '[b][url="'.$bburl.'/member.php?s='.$session[sessionhash].'&action=getinfo&userid='
					    .$bedankomatuser['buserid'].'"]'
					    .$bedankomatuser['busername'].'[/url][/b] ';
			} else {
				$bedankomatusers .= $bedankomatuser['busername']." ";
			}
			unset($bedankomatuser['busername']);
		} else {
			@$DB_site->query_first("DELETE FROM bedankomat WHERE userid='".$bedankomatuser['buserid']."'");
			if ($bedankomates>0) {
				$bedankomates--;
			}
		}
	}			    
	if ($bedankomates<1) {
		$bedankomates++;
	}
			
	// $bedankomates = Num of Thx; $bedankomateusers = HTML list of thx-users
	//eval("\$bedankomat_pagetext .= \"".gettemplate("bedankomat_pagetext")."\";");
	$bedankomat_pagetext="Für diesen Thread bedankten sich die folgenden $bedankomates User:\n".$bedankomatusers; 
		
	// Insert fake Bedankomat posting
	$threadinfo[dateline]++;	// For better sorting
	
	if ($bedankomates==1) {
	  @$DB_site->query("INSERT INTO post (postid, threadid, username, userid, title, dateline, pagetext, ipaddress, visible)
        	         VALUES (NULL, '$threadid','$bedankomat_username','$bedankomat_userid',
                 	'$bedankomates * THX!', '$threadinfo[dateline]', '".addslashes($bedankomat_pagetext)."',
                 	'127.0.0.1', 1)");
        } else {
          @$DB_site->query("UPDATE post
          		     SET
          			username='$bedankomat_username',
          			userid='$bedankomat_userid',
          			title='$bedankomates &times; THX!',
          			dateline='$threadinfo[dateline]',
          			pagetext='".addslashes($bedankomat_pagetext)."',
          			ipaddress='127.0.0.1',
          			visible=1
          		     WHERE
          		     	threadid = '$threadid'
			        AND username = '$bedankomat_username'");	
        }
	
	// Update some values in case something changed
	if ($bedankomat_userid>0) {
		$user=@$DB_site->query_first("SELECT COUNT(*) AS posts FROM post WHERE username='$bedankomat_username'");
		@$DB_site->query("UPDATE post SET userid='$bedankomat_userid' WHERE username='$bedankomat_username'");
		@$DB_site->query("UPDATE user SET posts='".$user['posts']."' WHERE username='$bedankomat_username'");
	}
	
	// Set lastpost of thread to current time to push thread up
	if ($bedankomat_pushup>0){
		@$DB_site->query("UPDATE thread SET lastpost='".time()."' WHERE threadid='$threadid'");
	}
}

eval($bedankomat_exit);
exit;
?>