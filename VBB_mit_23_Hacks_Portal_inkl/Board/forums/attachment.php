<?php
error_reporting(7);

// Attachment Viewer Hack
if ($fullpage!=1) {
  $noheader=1;
} else {
  $noheader=0;
}
// Attachment Viewer Hack


require("./global.php");

if ($postid) {
  $postid=verifyid("post",$postid);
} else {
  $attachmentid=verifyid("attachment",$attachmentid);
}

$getforuminfo=$DB_site->query_first("SELECT forumid".
                                     iif($postid,',attachmentid ','')."
                                     FROM thread,post
                                     WHERE post.threadid=thread.threadid ".
                                      iif($postid,"AND post.postid='$postid'","AND post.attachmentid='$attachmentid'")."
                                      ");

$permissions=getpermissions($getforuminfo[forumid]);
if (!$permissions[canview] or !$permissions[cangetattachment]) {
  show_nopermission();
}

if ($postid) {
  $attachmentid=$getforuminfo[attachmentid];
}

if (!$attachmentinfo=$DB_site->query_first("SELECT filename,filedata,dateline,visible
							FROM attachment
							WHERE attachmentid='$attachmentid'")){
  $idname='attachment';
  eval("standarderror(\"".gettemplate('error_invalidid')."\");");
  exit;
}

if ($attachmentinfo['visible'] == 0) {
	if (!ismoderator($getforuminfo[forumid],"canmoderateattachments")) {
		$idname='attachment';
		eval("standarderror(\"".gettemplate('error_invalidid')."\");");
 		exit;
	}
}
// Attachment Viewer Hack
if ($fullpage!=1) {
// Attachment Viewer Hack

updateuserforum($getforuminfo['forumid']);

if ($noshutdownfunc) {
  $DB_site->query("UPDATE attachment SET counter=counter+1 WHERE attachmentid='$attachmentid'");
} else {
  $shutdownqueries[]="UPDATE LOW_PRIORITY attachment SET counter=counter+1 WHERE attachmentid='$attachmentid'";
}

if (strstr($HTTP_USER_AGENT,"MSIE")) {
  $attachment = '';
} else {
  $attachment = ' atachment;';
  // We could still be MSIE behind a firewall blocking USER_AGENT so use something that will work!!
}

header("Cache-control: max-age=31536000");
header("Expires: " . gmdate("D, d M Y H:i:s",time()+31536000) . "GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s",$attachmentinfo[dateline]) . "GMT");
header("Content-disposition:$attachment filename=$attachmentinfo[filename]");
header("Content-Length: ".strlen($attachmentinfo[filedata]));
$extension=strtolower(substr(strrchr($attachmentinfo[filename],"."),1));

if ($extension=='gif') {
  header('Content-type: image/gif');
} elseif ($extension=='jpg' or $extension=='jpeg') {
  header('Content-type: image/jpeg');
} elseif ($extension=='png') {
  header('Content-type: image/png');
} elseif ($extension=='pdf') {
  header('Content-type: application/pdf');
} else {
  header('Content-type: unknown/unknown');
}
echo $attachmentinfo[filedata];
// Attachment Viewer Hack
} else {
  $getuser=$DB_site->query_first("SELECT userid,username,threadid FROM post WHERE attachmentid='$attachmentid'");
  $attachuserid=$getuser[userid];
  $attachusername=$getuser[username];
  $navbar=makenavbar($getuser[threadid],"thread",1);
  $threadinfo=getthreadinfo($getuser[threadid]);
  $nav_url="showthread.php?s=$session[sessionhash]&threadid=$threadinfo[threadid]";
  eval("dooutput(\"".gettemplate('attachmentview')."\");");
}
// Attachment Viewer Hack

?>