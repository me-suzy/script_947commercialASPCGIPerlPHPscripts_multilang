<?php

error_reporting(7);

$action = $HTTP_GET_VARS['action'];
$goto = $HTTP_GET_VARS['goto'];

if ( isset($goto) and ($goto=='lastpost' or $goto=='newpost')) {
  $noheader=1;
}
// Logician Download Thread Hack
// Download Thread
if ($action=="download") {

require("./global.php");

// Check Permissions so that a smartass wouldnt download a thread he doesnt allowed to see
$getperms=getpermissions($thread['forumid'],-1,-1,$forum['parentlist']);
if (!$getperms['canview'] or !$thread['visible']) 
{show_nopermission();}
if (!$getperms['canviewothers'] and ($thread['postuserid']!=$bbuserinfo['userid'] or $bbuserinfo['userid']==0)) 
{show_nopermission();}

$thread = verifyid("thread",$threadid,1,1);

  $thread_db=$DB_site->query_first("SELECT * from thread WHERE threadid=$threadid AND visible=1");
  $thread[postdate]=vbdate($dateformat,$thread_db[dateline]);
  $thread[posttime]=vbdate($timeformat,$thread_db[dateline]);
  $filename=preg_replace("/[.!:\\\?\^\'\"\$]/", '', $thread[title]);
  if (strlen($filename)<2) {$filename='Thread'.$threadid;}
  header("Content-disposition: attachment;filename=".$filename.".txt");
  header("Content-type: application/octet-stream");
  header("Pragma: no-cache");
  header("Expires: 0");
  print ("            * * *     $bbtitle THREAD  * * *\r\n\r\n");
  print ("-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\r\n");
  print ("Thema : $thread_db[title]\r\n");
  print ("Geschrieben am $thread[postdate] $thread[posttime] von $thread_db[postusername]\r\n");
  print ("Besuch bei $bburl/showthread.php?threadid=$threadid\r\n");
  print ("-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\r\n\r\n");

 $i=1;
 $post_db=$DB_site->query("SELECT p.dateline, p.title, p.pagetext, p.userid, user.username from post p LEFT JOIN user on user.userid=p.userid WHERE threadid=$threadid AND visible=1 ORDER BY dateline ASC");
 
 while ($post=$DB_site->fetch_array($post_db)) 
 {
 $post[postdate]=vbdate($dateformat,$post[dateline]);
 $post[posttime]=vbdate($timeformat,$post[dateline]);
  print ("[Beitrag $i]\r\n");
  print ("Verfasser : $post[username]\r\n");
  print ("Datum : $post[postdate] $post[posttime]\r\n");
  if ($post[title]) {print ("Titel : ".$post[title]."\r\n");}
  print ("\r\n$post[pagetext]\r\n\r\n");
  print ("-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\r\n\r\n");
  $i++;
  }

print ("Diese Nachricht stammt von $bbtitle auf $bburl um ".vbdate("d.m.Y h:i:s",time())."\r\n\r\n");
exit;
}
// Logician Download Thread Hack
// ############################################################################
// ############################# SHOW POST ####################################
// ############################################################################

if ($action=="showpost") {

	$templatesused = 'postbit_search,postbit_buddy,postbit_useremail,icq,aim,yahoo,postbit_homepage,postbit_profile,postbit_ip_show,postbit_ip_hidden,postbit,postbit_sendpm,postbit_avatar,postbit_offline,postbit_online,postbit_editedby,postbit_signature,postbit_attachment,postbit_attachmentimage,showpost';
	include('./global.php');

	if (isset($postid) and $postid!=0 and $postid!="") {
	  $postid = verifyid("post",$postid);

	  $getthread=$DB_site->query_first("SELECT threadid FROM post WHERE postid='$postid'");
	  $threadid=$getthread[threadid];
	}

	$threadid = intval($threadid);
	$thread = verifyid("thread",$threadid,1,1);

	if (!$thread['visible']) {
	  $idname="thread";
	  eval("standarderror(\"".gettemplate("error_invalidid")."\");");
	  exit;
	}

	$forum=getforuminfo($thread['forumid']);

	$getperms=getpermissions($thread['forumid'],-1,-1,$forum['parentlist']);
	if (!$getperms['canview']) {
	  show_nopermission();
	}
	if (!$getperms['canviewothers'] and ($thread['postuserid']!=$bbuserinfo['userid'] or $bbuserinfo['userid']==0)) {
	  show_nopermission();
	}

	$bbcodeon=iif($forum[allowbbcode],$ontext,$offtext);
	$imgcodeon=iif($forum[allowimages],$ontext,$offtext);
	$htmlcodeon=iif($forum[allowhtml],$ontext,$offtext);
	$smilieson=iif($forum[allowsmilies],$ontext,$offtext);

	$post=$DB_site->query_first("
	SELECT
	post.*,post.username AS postusername,post.ipaddress AS ip,user.*,userfield.*,".iif($forum[allowicons],'icon.title as icontitle,icon.iconpath,','')."
	attachment.attachmentid,attachment.filename,attachment.visible AS attachmentvisible,attachment.counter
	".iif($avatarenabled,",avatar.avatarpath,NOT ISNULL(customavatar.avatardata) AS hascustomavatar,customavatar.dateline AS avatardateline","")."
	FROM post
	".iif($forum[allowicons],'LEFT JOIN icon ON icon.iconid=post.iconid','')."
	LEFT JOIN user ON user.userid=post.userid
	LEFT JOIN userfield ON userfield.userid=user.userid
	".iif ($avatarenabled,"LEFT JOIN avatar ON avatar.avatarid=user.avatarid
	                       LEFT JOIN customavatar ON customavatar.userid=user.userid","")."
	LEFT JOIN attachment ON attachment.attachmentid=post.attachmentid
	WHERE post.postid = '$postid'
	");

	if (!$getperms['cangetattachment']) {
		$viewattachedimages=0;
	}

	$postbits = getpostbit($post);

	updateuserforum($thread['forumid']);

	eval("dooutput(\"".gettemplate("showpost")."\");");
	exit;
}

// ##############################################################################
// ############################# SHOW THREAD ####################################
// ##############################################################################

$templatesused = 'showthread_ratingdisplay,postbit_search,postbit_buddy,postbit_ignore,postbit_useremail,icq,aim,yahoo,postbit_homepage,postbit_profile,postbit_ip_show,postbit_ip_hidden,postbit,postbit_sendpm,postbit_avatar,postbit_offline,postbit_online,postbit_editedby,postbit_signature,postbit_attachment,postbit_attachmentimage,showthread_adminoptions,showthread_threadrate,showthread_pollresults_voted,showthread_pollresults_closed,showthread_pollresults_cantvote,showthread_firstunread,showthread_nextnewestthread,showthread_nextoldestthread,forumrules,showthread';
require('./global.php');

// words to highlight from the search engine
$replacewords = array();
if (isset($highlight) && $highlight != '') {
  $highlight = urldecode($highlight);
  $highlightwords=explode(" ",str_replace("/","\/",quotemeta($highlight)));
  while (list($key,$val)=each($highlightwords)) {
    $val = strtolower($val);
    if ($val=='or' OR $val=='and' OR $val=='not') {
      continue;
    }
    if ($allowwildcards) {
      $val = str_replace("\*", "[a-zA-z]+", $val);
    }
    $replacewords[$key]="$val";
  }
}

// oldest first or newest first
if ($postorder==0) {
  $postorder="";
} else {
  $postorder="DESC";
}

// goto last post
if ($goto=="lastpost") {
  if (isset($threadid) and $threadid!=0) {
    $threadid = verifyid("thread",$threadid);

    if ($getlastpost=$DB_site->query_first("SELECT postid,post.dateline FROM post,thread WHERE post.threadid=thread.threadid AND thread.threadid='$threadid' AND post.visible=1 AND thread.visible=1 ORDER BY post.dateline DESC LIMIT 1")) {
      header("Location: showthread.php?s=$session[sessionhash]&postid=$getlastpost[postid]#post$getlastpost[postid]");
      exit;
    }
  }
  if (isset($forumid) and $forumid!=0) {
    $foruminfo=verifyid("forum",$forumid,1,1);
    $forumid=$foruminfo['forumid'];

		$forumslist = "";
		$getchildforums=$DB_site->query("SELECT forumid,parentlist FROM forum WHERE INSTR(CONCAT(',',parentlist,','),',$forumid,')>0");
		while ($getchildforum=$DB_site->fetch_array($getchildforums)) {
			if ($getchildforum[forumid]==$forumid) {
				$parentlist=$getchildforum[parentlist];
			}
			$forumslist.=",$getchildforum[forumid]";
		}

    $thread=$DB_site->query_first("SELECT threadid FROM thread WHERE forumid IN (0$forumslist) AND visible=1 AND (sticky=1 OR sticky=0) AND lastpost>='".($foruminfo[lastpost]-30)."' AND open<>10 ORDER BY lastpost DESC LIMIT 1");

    if ($getlastpost=$DB_site->query_first("SELECT postid FROM post WHERE threadid='$thread[threadid]' AND visible=1 ORDER BY postid DESC LIMIT 1")) {
      header("Location: showthread.php?s=$session[sessionhash]&postid=$getlastpost[postid]#post$getlastpost[postid]");
      exit;
    }
  }
}

// goto newest post
if ($goto=="newpost") {
  $threadid = verifyid("thread",$threadid);

	if (($tview = get_bbarraycookie('threadview', $threadid)) > $bbuserinfo['lastvisit']) {
    $bbuserinfo['lastvisit'] = $tview;
  }

  if ($posts=$DB_site->query_first("SELECT postid,dateline FROM post WHERE post.threadid=$threadid AND post.visible=1 AND post.dateline>'$bbuserinfo[lastvisit]' ORDER BY dateline LIMIT 1")) {
    header("Location: showthread.php?s=$session[sessionhash]&postid=$posts[postid]#post$posts[postid]");
    exit;
  } else {
    header("Location: showthread.php?s=$session[sessionhash]&threadid=$threadid&goto=lastpost");
    exit;
  }
}

if ($goto=="nextnewest") {
  $thread = verifyid("thread",$threadid,1,1);
  if ($getnextnewest=$DB_site->query_first("SELECT threadid
            FROM thread
            WHERE forumid='$thread[forumid]'
              AND lastpost>'$thread[lastpost]'
              AND visible=1
              AND open<>10
            ORDER BY lastpost LIMIT 1")) {
    $threadid=$getnextnewest[threadid];
    unset ($thread);
  } else {
    eval("standarderror(\"".gettemplate("error_nonextnewest")."\");");
  }
}

if ($goto=="nextoldest") {
  $thread = verifyid("thread",$threadid,1,1);
	if ($getnextoldest=$DB_site->query_first("SELECT threadid
			FROM thread
			WHERE forumid='$thread[forumid]'
				AND lastpost<'$thread[lastpost]'
				AND visible=1
				AND open<>10
			ORDER BY lastpost DESC LIMIT 1")) {
		$threadid=$getnextoldest[threadid];
		unset($thread);
	} else {
    eval("standarderror(\"".gettemplate("error_nonextoldest")."\");");
	}
}

$perpage = intval($perpage);
$umaxposts = explode(',', $usermaxposts . ",$maxposts");
$newmaxposts = max($umaxposts);
if ($perpage < 1 or $perpage > $newmaxposts) {
	if ($bbuserinfo['maxposts']!=-1 and $bbuserinfo['maxposts']!=0 and $bbuserinfo['maxposts'] <= $newmaxposts)	{
		$perpage = $bbuserinfo['maxposts'];
	} else {
		$perpage = $maxposts;
	}
}

if (isset($postid) and $postid!=0 and $postid!="") {
  $postid = verifyid("post",$postid);

  $getthread=$DB_site->query_first("SELECT threadid FROM post WHERE postid='$postid'");
  $threadid=$getthread[threadid];

  if (!$postorder) {
    $getpagenum=$DB_site->query_first("SELECT COUNT(*) AS posts FROM post WHERE threadid='$threadid' AND postid<='$postid'");
    if ($getpagenum[posts]%$perpage==0) {
      $pagenumber=$getpagenum[posts]/$perpage;
    } else {
      $pagenumber=intval($getpagenum[posts]/$perpage)+1;
    }
  } else {
    $getpagenum=$DB_site->query_first("SELECT COUNT(*) AS posts FROM post WHERE threadid='$threadid' AND postid>='$postid'");
    if ($getpagenum[posts]%$perpage==0) {
      $pagenumber=$getpagenum[posts]/$perpage;
    } else {
      $pagenumber=intval($getpagenum[posts]/$perpage)+1;
    }
  }
}

$threadid = intval($threadid);
$thread = verifyid("thread",$threadid,1,1);

if ($wordwrap!=0) {
  $thread['title']=dowordwrap($thread['title']);
}

if (!$thread['visible']) {
  $idname="thread";
  eval("standarderror(\"".gettemplate("error_invalidid")."\");");
  exit;
}

if ($thread['open'] == 10) {
   // send them to their correct thread
    header("Location: showthread.php?s=$session[sessionhash]&threadid=$thread[pollid]");
    exit;
}

$forum=getforuminfo($thread['forumid']);

$getperms=getpermissions($thread['forumid'],-1,-1,$forum['parentlist']);
if (!$getperms['canview']) {
  show_nopermission();
}
if (!$getperms['canviewothers'] and ($thread['postuserid']!=$bbuserinfo['userid'] or $bbuserinfo['userid']==0)) {
  show_nopermission();
}

if ((!isset($pagenumber) or $pagenumber==0) and $pagenumber!="lastpage") {
  $pagenumber=1;
}

if ($noshutdownfunc) {
  $DB_site->query("UPDATE thread SET views=views+1 WHERE threadid='$threadid'");
} else {
  $shutdownqueries[]="UPDATE LOW_PRIORITY thread SET views=views+1 WHERE threadid='$threadid'";
}

if ($bbuserinfo[cookieuser]) {
  set_bbarraycookie('threadview', $threadid, time());
}

// display ratings if enabled
$ratingdisplay = '';
if ($forum['allowratings']==1) {
	if ($thread['votenum'] > 0) {
		$thread['voteavg'] = sprintf('%.2f', ($thread['votetotal'] / $thread['votenum']));
		$thread['rating'] = intval(round($thread['voteavg']));
	}
	if ($thread['votenum']>=$showvotes) {
		eval("\$ratingdisplay = \"".gettemplate('showthread_ratingdisplay')."\";");
	}
}

// draw nav bar
$navbar=makenavbar($threadid,"thread",0);

$curforumid = $thread['forumid'];

makeforumjump();

if ($thread[pollid]) {
  $pollid=$thread[pollid];
  $pollinfo=$DB_site->query_first("SELECT * FROM poll WHERE pollid='$pollid'");

  $pollinfo[question]=bbcodeparse($pollinfo[question],$forum[forumid],1);

  $splitoptions=explode("|||", $pollinfo[options]);
  $splitvotes=explode("|||",$pollinfo[votes]);

  $showresults = 0;
  $uservoted = 0;
  $cantvote = 0;

  if (!$pollinfo['active'] OR !$thread['open'] or ($pollinfo['dateline'] + ($pollinfo['timeout'] * 86400) < time() AND $pollinfo['timeout'] != 0)){
    //thread/poll is closed, ie show results no matter what
    $showresults = 1;
  } else if (!$permissions['canvote']) {
  	$cantvote = true;
  } else {
    //get userid, check if user already voted
    if (get_bbarraycookie('pollvoted', $pollid) or ($bbuserinfo['userid'] and $uservote=$DB_site->query_first("SELECT pollvoteid FROM pollvote WHERE userid='$bbuserinfo[userid]' AND pollid=$pollid"))) {
      $uservoted = 1;
    }
  }

  $counter=0;
  while ($counter++ < $pollinfo[numberoptions]) {
    $pollinfo[numbervotes] += $splitvotes[$counter-1];
  }

  $counter=0;
  $pollbits="";
  $option = array();

  while ($counter++<$pollinfo[numberoptions]) {
    $option[question] = bbcodeparse($splitoptions[$counter-1],$forum[forumid],1);
    $option[votes] = $splitvotes[$counter-1];  //get the vote count for the option
    $option[number] = $counter;  //number of the option

    //Now we check if the user has voted or not
    if ($showresults OR $uservoted OR $cantvote) { // user did vote or poll is closed

      if ($option[votes] == 0){
        $option[percent] = 0;
      } else{
        $option[percent] = number_format($option[votes]/$pollinfo[numbervotes]*100,2);
      }

      $option[graphicnumber]=$option[number]%6 + 1;
      $option[barnumber] = round($option[percent])*2;
      if ($showresults) {
        eval("\$pollstatus = \"".gettemplate("showthread_pollresults_closed")."\";");
      } elseif ($cantvote) {
      	eval("\$pollstatus = \"".gettemplate("showthread_pollresults_cantvote")."\";");
      } elseif ($uservoted) {
        eval("\$pollstatus = \"".gettemplate("showthread_pollresults_voted")."\";");
      } else {
        $pollstatus = ''; // just in case
      }
      eval("\$pollbits .= \"".gettemplate("pollresult")."\";");
    } else {
      if ($pollinfo['multiple']) {
			eval("\$pollbits .= \"".gettemplate("polloption_multiple")."\";");
		} else {
			eval("\$pollbits .= \"".gettemplate("polloption")."\";");
		}
	 }
  }

  if ($pollinfo['multiple']) {
   	$pollinfo['numbervotes'] = $pollinfo['voters'];
	}

  if ($showresults OR $uservoted OR $cantvote) {
    eval("\$poll = \"".gettemplate("showthread_pollresults")."\";");
  } else {
    eval("\$poll = \"".gettemplate("showthread_polloptions")."\";");
  }
} else {
  $poll="";
}

$bbcodeon=iif($forum[allowbbcode],$ontext,$offtext);
$imgcodeon=iif($forum[allowimages],$ontext,$offtext);
$htmlcodeon=iif($forum[allowhtml],$ontext,$offtext);
$smilieson=iif($forum[allowsmilies],$ontext,$offtext);

$limitlower=($pagenumber-1)*$perpage+1;
$limitupper=($pagenumber)*$perpage;

$counter=0;

// can do it this way or use a strstr() for each post but I feel this will be quicker overall
unset($ignore);
$ignorelist = explode(' ', $bbuserinfo['ignorelist']);
while ( list($key, $val)=each($ignorelist) ) {
  $ignore[$val] = 1;
}

$postscount=$DB_site->query_first("SELECT COUNT(*) AS posts FROM post WHERE post.threadid='$threadid' AND post.visible=1");
$totalposts=$postscount[posts];

$getpostids=$DB_site->query("
	SELECT post.postid FROM post
	WHERE post.threadid='$threadid' AND post.visible=1
	ORDER BY dateline $postorder LIMIT ".($limitlower-1).",$perpage
");

if ($limitupper>$totalposts) {
  $limitupper=$totalposts;
  if ($limitlower>$totalposts) {
    $limitlower=$totalposts-$perpage;
  }
}
if ($limitlower<=0) {
  $limitlower=1;
}
$postids="post.postid IN (0";
while ($post=$DB_site->fetch_array($getpostids)) {
  $postids.=",".$post['postid'];
}

$postids.=")";


$posts=$DB_site->query("
SELECT
post.*,post.username AS postusername,post.ipaddress AS ip,user.*,userfield.*,".iif($forum[allowicons],'icon.title as icontitle,icon.iconpath,','')."
attachment.attachmentid,attachment.filename,attachment.visible AS attachmentvisible,attachment.counter
".iif($avatarenabled,",avatar.avatarpath,NOT ISNULL(customavatar.avatardata) AS hascustomavatar,customavatar.dateline AS avatardateline","")."
FROM post
".iif($forum[allowicons],'LEFT JOIN icon ON icon.iconid=post.iconid','')."
LEFT JOIN user ON user.userid=post.userid
LEFT JOIN userfield ON userfield.userid=user.userid
".iif ($avatarenabled,"LEFT JOIN avatar ON avatar.avatarid=user.avatarid
                       LEFT JOIN customavatar ON customavatar.userid=user.userid","")."
LEFT JOIN attachment ON attachment.attachmentid=post.attachmentid
WHERE $postids
ORDER BY dateline $postorder
");


if (!$getperms['cangetattachment']) {
	$viewattachedimages=0;
}

$postbits = '';
$counter=0;
$postdone = array();
$sigcache = array();
while ($post=$DB_site->fetch_array($posts) and $counter++<$perpage) {

  if ($postdone[$post[postid]]) {
    $counter--;
    continue;
  } else {
    $postdone[$post[postid]]=1;
  }
  $postbits .= getpostbit($post);
}

$pagenav = getpagenav($totalposts,"showthread.php?s=$session[sessionhash]&threadid=$threadid&perpage=$perpage".iif(isset($highlight), "&highlight=$highlight", ""));

$DB_site->free_result($posts);
unset($post);
unset($sigcache); //don't need the signature cache anymore

if ($thread[open]) {
  $replyclose="{replyimage}";
} else {
  $replyclose="{closedthreadimage}";
}

if ($thread[lastpost]>$bbuserinfo[lastvisit]) {
  // do blue arrow link

  if ($firstnew) {
		$newpostlink="#newpost";
  } else {
		$newpostlink="showthread.php?s=$session[sessionhash]&threadid=$threadid&goto=newpost";
  }

  eval("\$firstunread = \"".gettemplate("showthread_firstunread")."\";");

} else {
  $firstunread="";
}
if ($forum[allowratings]) {
  eval("\$threadrateselect = \"".gettemplate("showthread_threadrate")."\";");
} else {
  $threadrateselect = "&nbsp;";
}

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

if ($bedankomat_forum[$thread['forumid']]==1) {
	$bedankomaton=1;
}

// Bedankomat text/icon - Insert $thankubutton in "showthread" template
if ($bedankomaton && ($getperms['canreplyothers'] || $getperms['canreplyown'])) {
	$thankubutton = "<a href=\"bedankomat.php?s=$session[sessionhash]&threadid=$threadid\"><img src=\"{imagesfolder}/danke.gif\" border=\"0\" alt=\"\"></a>";
}

if (ismoderator($thread['forumid']) or $getperms['canopenclose'] or $getperms['candeletethread'] or $getperms['canmove']) {
  eval("\$adminoptions = \"".gettemplate("showthread_adminoptions")."\";");
} else {
  $adminoptions = "&nbsp;";
}
getforumrules($forum,$getperms);

updateuserforum($thread['forumid']);

eval("dooutput(\"".gettemplate("showthread")."\");");

?>