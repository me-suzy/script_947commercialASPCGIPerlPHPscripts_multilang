<?php
//8.1
$blocks_modules[login] = array(
	'title' => "Login",
	'func_display' => 'blocks_login_block',
	'func_add' => '',
	'func_update' => '',
	'text_type' => 'Login',
	'text_type_long' => '',
	'text_content' => "User's Login",
	'support_nukecode' => false,
	'allow_create' => false,
	'allow_delete' => false,
	'form_url' => false,
	'form_content' => false,
	'form_refresh' => false,
    'show_preview' => true,
    'has_templates' => true
    );
function blocks_login_block($row) {
  global $displayloggedin,$cookietimeout,$info,$DB_site,$bburl,$bbuserinfo,$user,$username,$userid,$newusername,$newuserid,$newsforum,$block_sidetemplate;
  $numbersmembers=$DB_site->query_first('SELECT COUNT(*) AS users,MAX(userid) AS max FROM user');
  $numbermembers=$numbersmembers['users'];
  $getnewestusers=$DB_site->query_first("SELECT userid,username FROM user WHERE userid=$numbersmembers[max]");
  $newusername=$getnewestusers['username'];
  $newuserid=$getnewestusers['userid'];
  if ($displayloggedin) {
    $datecut=time()-$cookietimeout;
	$guestonline = $DB_site->query_first("SELECT COUNT(host) AS sessions FROM session WHERE userid=0 AND lastactivity>$datecut");
	$membersonline = $DB_site->query("SELECT DISTINCT userid FROM session WHERE userid<>0 AND lastactivity>$datecut");
	$guests = number_format($guestonline['sessions']);
	$members = number_format($DB_site->num_rows($membersonline));
	$totalonline = number_format($guests + $members);
  }
  $newpms=$DB_site->query_first("SELECT COUNT(*) AS messages FROM privatemessage WHERE userid=$bbuserinfo[userid] AND dateline>$bbuserinfo[lastvisit] AND folderid=0");
  if ($bbuserinfo['userid']!=0) {
    include("config.php");
    $userid=$bbuserinfo['userid'];
    $username=$bbuserinfo['username'];
    if($row[templates]) {
		 eval("\$block_content .= \"".gettemplate('P_logoutcode')."\";");
	}else{
		// Edit logout code here
	    $block_content = "<p align=\"center\"><smallfont>Willkommen <b><i>$username</i></b>.<br><br>
		Dein letzter Besuch:<br> $bbuserinfo[lastvisitdate].<br><br>
                Du hast <a href=\"$bburl/private.php?s=$session[sessionhash]\"><b>$newpms[messages]</b></a> neue<br>Nachricht(en)<br><br>
		Unser neustes Mitglied ist: <a href=\"$bburl/member.php?s=$session[sessionhash]&action=getinfo&userid=$newuserid\"><br>
		<b>$newusername</b></a><br><br></smallfont><table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#0E1F34\" width=\"100%\">
		<tr>
		<td width=\"100%\" bgcolor=\"{categorybackcolor}\">
	    <p align=\"center\"><font size = \"2\" color =\"{categoryfontcolor}\"><b>Optionen</b></font></td>
		</tr>
		<tr>
		<td width=\"100%\" bgcolor=\"{secondaltcolor}\"><smallfont>
		<li><smallfont><a href=\"$bburl/newthread.php?s=$session[sessionhash]&action=newthread&forumid=$newsforum\">News senden</a></smallfont></li>
		<li><smallfont><a href=\"$bburl/search.php?s=$session[sessionhash]&action=getnew\">Neue Beiträge</a></smallfont></li>
	        <li><smallfont><a href=\"$bburl/usercp.php?s=$session[sessionhash]\">Mein Profil</a></smallfont></li>
		<li><smallfont><a href=\"$bburl/online.php?s=$session[sessionhash]\">$totalonline Benutzer Online</a></smallfont></li>
		<li><smallfont><a href=\"$bburl/member2.php?s=$session[sessionhash]&action=viewlist&userlist=buddy\">Meine Freunde</a></smallfont></li>
		<li> <smallfont><a href=\"$bburl/member.php?s=$session[sessionhash]&action=logout\">Abmelden</a></smallfont></li>
		</smallfont><br><br></td>
		</tr>
		</table>
		<p align=\"center\">";
    }
  } else {
	if($row[templates]) {
		eval("\$block_content .= \"".gettemplate('P_logincode')."\";");
	}else{
		$user="";
		// Edit Login code here
		$block_content = "<form action=\"$bburl/member.php\" method=\"post\">
		<input type=\"hidden\" name=\"action\" value=\"login\"><input type=\"hidden\" name=\"s\" value=\"$session[sessionhash]\">
		<input type=\"hidden\" name=\"url\" value=\"$nukeurl/index.php\">
		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
		<tr><td nowrap>
		<br>
		</td></tr>
		<tr><td>
		<p align=\"center\"><smallfont><b>Benutzername</b>&nbsp;</smallfont><br><INPUT TYPE=\"TEXT\" NAME=\"username\" SIZE=14>
		<br><smallfont><b>Passwort</b>&nbsp;&nbsp;</smallfont><br><INPUT TYPE=\"PASSWORD\" NAME=\"password\" SIZE=14><br>
		<center>
		<input type=\"submit\" value=\"Anmelden!\"></center>
		</p>
		</td></tr>
		<tr><td nowrap>
		<p align=\"center\"><smallfont><br>
		<a href=\"$bburl/register.php?action=signup\">Noch nicht registriert?</a><br><br>
		<a href=\"$bburl/member.php\">Kennwort vergessen?</a>
		</smallfont>
		</td></tr>
		</table>
		</form>";
	}
  }
  $block_title = $row[title];
  eval("dooutput(\"".gettemplate($block_sidetemplate)."\");");
}
?>
