<?php
error_reporting(7);

$templatesused = "postbit_signature,printthreadbit,printthread";

require("./global.php");

// oldest first or newest first
if ($postsorder==0) {
  $postorder="";
} else {
  $postorder="DESC";
}

$threadid = verifyid("thread",$threadid);

$threadinfo=getthreadinfo($threadid);
if ($wordwrap!=0) {
  $threadinfo[title]=dowordwrap($threadinfo[title]);
}

if (!$threadinfo[visible]) {
  $idname="thread";
  eval("standarderror(\"".gettemplate("error_invalidid")."\");");
  exit;
}

$getperms=getpermissions($thread[forumid]);
if (!$getperms[canview]) {
  show_nopermission();
}
if (!$getperms[canviewothers] and ($thread[userid]!=$bbuserinfo[userid] or $bbuserinfo['userid']==0)) {
  show_nopermission();
}

$foruminfo=getforuminfo($threadinfo[forumid]);

updateuserforum($threadinfo['forumid']);

// split thread over pages if necessary
$countposts = $DB_site->query_first("SELECT COUNT(*) AS total FROM post WHERE threadid=".intval($threadid)." AND visible=1");
$totalposts = $countposts['total'];

// get perpage
if (!isset($perpage)) {
	if ($bbuserinfo['maxposts'] > 0) {
		$perpage = $bbuserinfo['maxposts'];
	} else {
		$perpage = $maxposts;
	}
}
$perpage = intval($perpage);

// get startat
$pagenumber = intval($pagenumber);
if ($pagenumber < 1) {
	$pagenumber = 1;
}
$startat = ($pagenumber - 1) * $perpage;

$pagenav = getpagenav($totalposts,"printthread.php?s=$session[sessionhash]&threadid=$threadid&perpage=$perpage");
// end page splitter

$posts=$DB_site->query("
	SELECT post.*,post.username AS postusername,user.username,user.signature,icon.title AS icontitle,icon.iconpath
	FROM post
	LEFT JOIN icon ON icon.iconid=post.iconid
	LEFT JOIN user ON user.userid=post.userid
	WHERE post.threadid='$threadid' AND post.visible=1
	ORDER BY dateline $postorder
	LIMIT $startat,$perpage
");

$postbits = '';
$sigcache = array();
while ($post=$DB_site->fetch_array($posts)) {

  $post[postdate]=vbdate($dateformat,$post[dateline]);
  $post[posttime]=vbdate($timeformat,$post[dateline]);

  if ($wordwrap!=0) {
    $post[title]=dowordwrap($post[title]);
  }

  if (!$foruminfo[allowicons] or $post[iconid]==0) {
    $post[icon]="";
  } else {
    $post[icon]="<img src=\"$post[iconpath]\" alt=\"$post[icontitle]\" width=\"15\" height=\"15\" border=\"0\">";
  }

  if ($post[userid]==0) {
    $post[username]=$post[postusername];
  } else {
    if ($post[showsignature] and $allowsignatures and trim($post[signature])!="" and ($bbuserinfo[userid]==0 or $bbuserinfo[showsignatures])) {
      if (!isset($sigcache["$post[userid]"])) {
        $post[signature]=bbcodeparse($post[signature],0,$allowsmilies);
        eval("\$post[signature] = \"".gettemplate("postbit_signature")."\";");
        $sigcache["$post[userid]"] = $post[signature];
      } else {
        $post[signature] = $sigcache["$post[userid]"];
      }
    } else {
      $post[signature] = "";
    }
  }

  $post['message']=bbcodeparse($post['pagetext'],$foruminfo['forumid'],$post['allowsmilie']).$post['signature'];
// ##### [HIDE] [/HIDE] HACK Start #####
  $HIDE_shown = 0; 
  if (substr($post['message'],0,6)=="[HIDE]"){$post['message'] = str_replace("[HIDE]"," [HIDE]",$post['message']); }
  if ($session[userid] != 0){
    if ($bbuserinfo['usergroupid']!=6) {
      $hasposted=$DB_site->query_first("SELECT userid FROM post WHERE threadid='$threadid' AND userid='$bbuserinfo[userid]' LIMIT 1");
    }
    if ($hasposted['userid'] || $bbuserinfo['usergroupid']==6) {
      $post['message'] = str_replace("[HIDE]","<font color='red'><b>[VERSTECKTER TEXT]:</b></font><br>",$post['message']);
      $post['message'] = str_replace("[/HIDE]","",$post['message']);
      $HIDE_shown = 1;
    }
  }

  if ($HIDE_shown == 0) { 
    for ($i = lock_count($post['message'],"[HIDE]"); $i > 0; $i--) { 
      $lock_part1 = strpos($post['message'], "[HIDE]");
      $lock_part2 = (strpos($post['message'],"[/HIDE]")-strpos($post['message'],"[HIDE]"))+7;
      $lock_mess = substr ($post['message'], $lock_part1, $lock_part2);
      $post['message'] = str_replace( $lock_mess, "<b>[VERSTECKTER TEXT]</b><br>Du musst auf dieses Thema antworten, um den versteckten Text zu sehen!<br>", $post['message']);
    } 
  } 
  // ##### [HIDE] [/HIDE] HACK End #####
  eval("\$postbits .= \"".gettemplate("printthreadbit")."\";");

}

eval("dooutput(\"".gettemplate("printthread")."\");");

?>