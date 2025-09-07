<?php
error_reporting(7);

require("./global.php");

cpheader();

adminlog(iif($userid!=0," user id = $userid",iif($pmid!=0,"private message id = $pmid","")));

if (isset($action)==0) {
  $action="list";
}

// which users are allowed to view user's pm's
// separate each userid with a comma
$canviewpms = "";

// ###################### Start list #######################
if($action=="list" && checklogperms($canviewpms,1,"<p>Viewing User PM's is restricted.</p>")) {
  $user=$DB_site->query_first("SELECT * FROM user WHERE user.userid=$userid");
  echo "<p>Viewing PM's - $user[username] (Userid $user[userid])</p>";
  echo "<br>";
  echo "<table cellpadding='0' cellspacing='0' border='0'  width='100%' align='center'><tr><td>";
  echo "<table cellpadding='4' cellspacing='1' border='0'  width='100%'>";
  echo "<tr class='tblhead'>";
  echo "<td align='center'><b><span class='tblhead'>Message Title</span></b></td>";
  echo "<td align='center' nowrap><b><span class='tblhead'>Sender</span></b></td>";
  echo "<td align='center' nowrap><b><span class='tblhead'>Date/Time Sent</span></b></td>";
  echo "</tr>";

  $query = $DB_site->query("SELECT
  privatemessage.*,
  IF(ISNULL(touser.username),'[Deleted User]',touser.username) AS tousername,
  IF(ISNULL(fromuser.username),'[Deleted User]',fromuser.username) AS fromusername
  FROM privatemessage
  LEFT JOIN user AS touser ON (touser.userid=privatemessage.touserid)
  LEFT JOIN user AS fromuser ON (fromuser.userid=privatemessage.fromuserid)
  WHERE privatemessage.userid='$user[userid]' AND privatemessage.fromuserid!='$user[userid]' ORDER BY dateline DESC");
  while($pminfo = $DB_site->fetch_array($query)) {

    if($clr == "firstalt") {
      $clr = "secondalt";
    }
    else {
      $clr = "firstalt";
    }
      $pminfo[datesent]=vbdate($dateformat,$pminfo[dateline]);
      $pminfo[timesent]=vbdate($timeformat,$pminfo[dateline]);
      echo "<tr align='center' class='$clr'>";
      echo "<td align='left' width='75%'><a href='pm.php?s=$session[sessionhash]&action=show&pmid=$pminfo[privatemessageid]'>$pminfo[title]</a></td>";
      echo "<td width='25%' align='center'>$pminfo[fromusername]</td>";
      echo "<td align='left' nowrap>$pminfo[datesent]<br>$pminfo[timesent]</td>";
      echo "</tr>";
  }
  if(!isset($clr) || $clr == "") {
    echo "<tr align='center' class='firstalt'>";
    echo "<td align='center' colspan='3'>User has no PM's</td>";
    echo "</tr>";
  }
  echo "<tr class='tblhead'>";
  echo "<td colspan='3'><span class='tblhead'><a href='javascript:history.back(1)'>Back</a></span></td>";
  echo "</tr>";
  echo "</table>";
  echo "</td>";
  echo "</tr>";
  echo "</table>";

}

// ###################### Start pmcodeparse #######################
function pmcodeparse($bbcode) {
  global $privallowhtml, $privallowbbimagecode, $privallowbbcode;
  $bbcode=bbcodeparse2($bbcode,$privallowhtml,$privallowbbimagecode,0,$privallowbbcode);
  return $bbcode;
}

// ###################### Start show #######################
if($action=="show" && checklogperms($canviewpms,1,"<p>Viewing User PM's is restricted.</p>")) {

  $privatemessageid = verifyid("privatemessage",$pmid);

  $message = $DB_site->query_first("SELECT privatemessage.*
                                    FROM privatemessage
                                    WHERE privatemessageid=$pmid");
  $message[postdate]=vbdate($dateformat,$message[dateline]);
  $message[posttime]=vbdate($timeformat,$message[dateline]);

  if ($message[fromuserid]!=0) {
    $post=$DB_site->query_first("SELECT
                                 user.*,userfield.*".iif($avatarenabled,",avatar.avatarpath,customavatar.dateline AS avatardateline,NOT ISNULL(customavatar.avatardata) AS hascustomavatar ","")."
                                 FROM user,userfield
                                 ".iif ($avatarenabled,"LEFT JOIN avatar ON avatar.avatarid=user.avatarid
                                                        LEFT JOIN customavatar ON customavatar.userid=user.userid ","")."
                                 WHERE userfield.userid=user.userid AND user.userid=$message[fromuserid]");
    $userinfo=$post;
          if ($bbuserinfo['showavatars']) {
            if ($post[avatarid]!=0) {
              $avatarurl=$post[avatarpath];
            } else {
              if ($post[hascustomavatar] and $avatarenabled) {
                $avatarurl="avatar.php?userid=$post[userid]&dateline=$post[avatardateline]";
              } else {
                $avatarurl="";
              }
            }
            if ($avatarurl=="") {
              $post[avatar]="";
            } else {
              $post[avatar] = "<img src='$bburl/$avatarurl' border='0' alt=''>";
            }
        }

    $post[joindate]=vbdate($registereddateformat,$post[joindate]);
    if ($post[customtitle]==2)
      $post[usertitle] = htmlspecialchars($post[usertitle]);

    if ($message[showsignature] and $allowsignatures and trim($post[signature])!="" && $bbuserinfo['showsignatures']) {
      $post[signature]=pmcodeparse($post[signature]);
      $post[signature] = "<font size='2'><br>__________________<br>$post[signature]</font>";
    } else {
      $post[signature] = "";
    }
    $fromuserinfo=$post;
  } else {
    $fromuserinfo['username'] = "N/A";
  }

  $touserinfo = getuserinfo($message[touserid]);

  $message[message]=pmcodeparse($message[message]);
  $message[message] .= $fromuserinfo[signature];

  echo "<p>This Private Message was sent to $touserinfo[username] by $fromuserinfo[username]</p>";

  echo "<table cellpadding='0' cellspacing='0' border='0'  width='100%' align='center'><tr><td>";
  echo "<table cellpadding='4' cellspacing='1' border='0'  width='100%'>";
  echo "<tr class='tblhead'>";
  echo "<td align='center' width='20%'><b><span class='tblhead'>Author</span></b></td>";
  echo "<td align='center' width='80%'><b><span class='tblhead'>Message</span></b></td>";
  echo "</tr>";
  echo "<tr align='center' class='firstalt'>";
  echo "<td align='left' width='20%' valign='top' nowrap>";
  echo "<table width='100%' cellpadding='4' cellspacing='0' border='0'>";
  echo "<tr>";
  echo "<td width='100%'><font size='2'><b>$fromuserinfo[username]</b></font><br>";
  echo "<font size='1'>$fromuserinfo[usertitle]</font><br><br>";
  echo "$post[avatar]<br><br>";
  echo "<font size='1'>Registered: $fromuserinfo[joindate]<br>";
  echo "Posts: $fromuserinfo[posts]</font></td>";
  echo "</tr>";
  echo "</table>";
  echo "</td>";

  echo "<td class='firstalt' valign='top' width='80%'>";
  echo "<table width='100%' cellpadding='4' cellspacing='0' border='0'>";
  echo "<tr>";
  echo "<td width='100%'><font size='1'><b>$message[title]</b></font>";
  echo "<p><font size='2'>$message[message]</font></p></td>";
  echo "</tr>";
  echo "</table>";
  echo "</td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td class='secondalt' width='20%' height='16' nowrap><font size='1'>$message[foldericon] $message[postdate] $message[posttime]</font></td>";
  echo "<td class='secondalt' width='80%' valign='middle' height='16'>";
  echo "<table width='100%' border=0 cellpadding=0 cellspacing=0>";
  echo "<tr>";
  echo "<td><font size='1'>&nbsp;</font></td>";
  echo "</tr>";
  echo "</table>";

  echo "<tr class='tblhead'>";
  echo "<td colspan='2'><span class='tblhead'><a href='pm.php?s=$session[sessionhash]&action=list&userid=$touserinfo[userid]'>Back To User's PM's</a></span></td>";
  echo "</tr>";

  echo "</td>";
  echo "</tr>";
  echo "</table>";
  echo "</td>";
  echo "</tr>";
  echo "</table>";

}

cpfooter();
?>