<?php
$templatesused='ref_for_user,ref_for_user1,ref_for_user2,ref_for_user3';
require("./global.php");
$hackwriter="<br><center><b><smallfont>Hack written by <a href=\"http://www.the-afterburner.de\">Afterburner</a></smallfont></b></center>";

  $users = $DB_site->query("SELECT COUNT(*) AS count, user.joindate, user.lastvisit, user.posts, user.usertitle, user.username, user.userid FROM user AS users
                   LEFT JOIN user ON (users.referrerid = user.userid)
                   WHERE users.referrerid <> 0
                   GROUP BY users.referrerid
                   ORDER BY count DESC");
$zaehlen=1;				   
  if ($DB_site->num_rows($users)==0) {
      $nichts ="Es sind keine Referals vorhanden / no referrals are available";
  }
  
    while ($user=$DB_site->fetch_array($users)) {
	 $userdate = vbdate($dateformat,$user[joindate]);
	 $usertime = vbdate($timeformat,$user[joindate]);
	 $userlastdate = vbdate($dateformat,$user[lastvisit]);
	 $userlasttime = vbdate($timeformat,$user[lastvisit]);
       eval("\$ref_for_user1 .= \"".gettemplate("ref_for_user1")."\";");
	   $zaehlen++;
    }
    


if ($action=='showreferrals') {

   $username=$DB_site->query_first("SELECT username FROM user WHERE userid = '$referrerid'");
   $users = $DB_site->query("SELECT username, posts, userid, usertitle, joindate, lastvisit
                             FROM user
                             WHERE referrerid = '$referrerid'                           
                             ORDER BY joindate");
$zaehlen1=1;	
   while ($user=$DB_site->fetch_array($users)) {
	 $userdate = vbdate($dateformat,$user[joindate]);
	 $usertime = vbdate($timeformat,$user[joindate]);
	 $userlastdate = vbdate($dateformat,$user[lastvisit]);
	 $userlasttime = vbdate($timeformat,$user[lastvisit]);
     $profile = "<a href=\"member.php?s=$session[sessionhash]&action=getinfo&userid=$user[userid]\">$user[username]</a>";
eval("\$ref_for_user2 .= \"".gettemplate("ref_for_user2")."\";");
$zaehlen1++;
   }
$off=1;
}


if ($off!=1)
{
eval("dooutput(\"".gettemplate("ref_for_user")."\");"); 
} else {
eval("dooutput(\"".gettemplate("ref_for_user3")."\");"); 
}
?>