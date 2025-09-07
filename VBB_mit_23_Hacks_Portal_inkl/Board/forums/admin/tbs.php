<?php
error_reporting(7);
require('./global.php');
adminlog(iif($templatesetid!=0,"template set id = $templatesetid",""));
cpheader("<title>Template Backup System</title>");

// ##################################################
// ####### vBulletin Template Backup System #########
// ########### Hack version 1.0 (12.9.01) ###########
// ############## for vBulletin v2.2.1 ##############
// ##################################################
// ########### by Chen 'FireFly' Avinadav ###########
// ########## (chen.avinadav@vbulletin.com) #########
// ##################################################
// ###### Special thanks to Hooper for all his ######
// ###### help with development of this script ######
// ##################################################

if (!isset($action) or $action=='') {
  $action="modify";
}

// Groups for display in this script
// You can add your own if you wish
unset($groups);
$groups['calendar']='Calendar';
$groups['emailsubject']='Email Subject';
$groups['email']='Email';
$groups['error']='Error Message';
$groups['faq']='FAQ';
$groups['forumdisplay']='Forum Display';
$groups['forumhome']='Forum Home Page';
$groups['getinfo']='User Info Display';
$groups['memberlist']='Member List';
$groups['modify']='User Option';
$groups['new']='New Posting';
$groups['pagenav']='Page Navigation';
$groups['poll']='Polling';
$groups['postbit']='Postbit';
$groups['priv']='Private Messaging';
$groups['redirect']='Redirection Message';
$groups['register']='Registration';
$groups['search']='Search';
$groups['showthread']='Show Thread';
$groups['subscribe']='Subscribed Thread';
$groups['threads']='Thread Management';
$groups['usercp']='User Control Panel';
$groups['vbcode']='vB Code';
$groups['whosonline']='Who\'s Online';
$groups['showgroup']='Show Groups';
$groupcount=count($groups);
reset($groups);

?>
<script language="JavaScript" type="text/javascript">
<!--
var supported = (document.getElementById || document.all);

if (supported) {
	document.write("<STYLE TYPE='text/css'>");
	document.write(".para {display: none}");
	document.write("</STYLE>");

	var shown = new Array();
	for (var i=-<?php echo $groupcount; ?>;i<=<?php echo $groupcount; ?>;i++) {
		shown[i] = <?php echo iif($startwith!='exp' and $selecttemplates==1, "false", "true"); ?>;
	}
	shown[-999] = <?php echo iif($startwith!='exp' and $selecttemplates==1, "false", "true"); ?>;
	shown[999] = <?php echo iif($startwith!='exp' and $selecttemplates==1, "false", "true"); ?>;
}

function blocking(i) {
	if (!supported) {
		alert('This link does not work in your browser.');
		return;
	}
	shown[i] = (shown[i]) ? false : true;
	current = (shown[i]) ? 'block' : 'none';
	if (document.getElementById) {
		document.getElementById('group'+i).style.display = current;
	} else if (document.all) {
		document.all['group'+i].style.display = current;
	}
}
<?php
if ($highram==1) {
?>
function CheckAll() {
	for (var i=0;i<document.form.elements.length;i++) {
		var e = document.form.elements[i];
		if ((e.name != 'allbox') && (e.type=='checkbox'))
		e.checked = document.form.allbox.checked;
	}
}

function CheckCheckAll() {
	var TotalBoxes = 0;
	var TotalOn = 0;
	for (var i=0;i<document.form.elements.length;i++) {
		var e = document.form.elements[i];
		if ((e.name != 'allbox') && (e.type=='checkbox')) {
			TotalBoxes++;
			if (e.checked) {
				TotalOn++;
			}
		}
	}
	if (TotalBoxes==TotalOn) {
		document.form.allbox.checked=true;
	} else {
		document.form.allbox.checked=false;
	}
}
<?php
}
?>
//-->
</script>
<?php

// #############################################################
// ################ Start getfilelist function #################
// #############################################################
function getfilelist($folder,$extension) {

	$retarray=array();
	$dir=dir("./$folder/.");
	while($filename=$dir->read()) {
		if (getextension($filename)==$extension) {
			$filetitle=substr($filename, 0, (strlen($filename)-(strlen($extension)+1)));
			$retarray[]=$filetitle;
		}
	}
	$dir->close();
	return $retarray;

}

// #############################################################
// ############### Start stripfromarray function ###############
// #############################################################
function stripfromarray($array) {

	while (list ($key,$val)=each($array)) {
		if ($val=="---") {
			unset($array["$key"]);
		}
	}
	return $array;

}

// #############################################################
// ###################### Start doexport #######################
// #############################################################
if ($HTTP_POST_VARS['action']=="doexport" and $selecttemplates==0) {

	if (trim($extension)!='' and trim($folder)!='') {
		echo "<p>Templates with <span class='rc'>this color</span> are templates you have created from scratch.<br>Templates with <span class='gc'>this color</span> are original templates you have edited.<br>Templates with <span class='cc'>this color</span> are the default templates that come with vBulletin.</p>";

		if (is_dir("./$folder")==0) {
			echo "<b>ERROR:</b> The folder '$folder' does not exist. Trying to create it ... ";
			if (mkdir("./$folder", 0777)==1) {
				echo "success!<br><br>\n";
			} else {
				echo "failed!<br>Exiting script.\n";
				exit;
			}				
		}
		$setinfo=$DB_site->query_first("SELECT * FROM templateset WHERE templatesetid=$templatesetid");
		echo "<b>Exporting '$setinfo[title]' template set!</b><br>";
		doformheader("","");
		maketableheader("Exporting created templates");
		$ourbg=getrowbg();
		$ourbg='firstalt';
		echo "<tr class='$ourbg' valign='top'><td colspan='2'>";
		if (is_array($templatelist)) {
			$todo="0,'".implode("','",stripfromarray($templatelist))."'";
		} else {
			$todo='';
		}
		$done='0';
		$doneecho='';
		$currentgroup='0';
		$templatecount=0;
		$templates=$DB_site->query("SELECT t1.* FROM template AS t1 LEFT JOIN template AS t2 ON (t1.title=t2.title AND t2.templatesetid=-1) WHERE t1.templatesetid=$templatesetid AND t1.title<>'options' AND ISNULL(t2.templateid)".iif($todo!='', " AND t1.title IN ($todo)", "")." ORDER BY t1.title");
		while ($template=$DB_site->fetch_array($templates)) {
			$path='./'.$folder.'/'.$template[title].'.'.$extension;
			writetofile($path, $template[template],$makebackup);
			$done.=",'$template[title]'";
			$oldgroup=$currentgroup;
			$currentgroup='0';
			$count=0;
			reset($groups);
			while (list ($groupname,$grouptitle)=each($groups)) {
				$count--;
				if (strpos(" $template[title]", $groupname)==1) {
					$currentgroup=$groupname;	
					break;
				}
			}
			$echocur='';
			if ($oldgroup!=$currentgroup) {
				$echocur="</td>\n</tr>\n".iif($templatecount!=0, "<tr class='secondalt' valign='top'><td colspan='2' height='10'></td>\n</tr>\n", "")."<tr class='$ourbg' valign='top'>\n<td colspan='2'>";
			}
			if ($currentgroup!='0' and $oldgroup!=$currentgroup) {
				$echocur="<tr class='secondalt' valign='top' id='submitrow'><td colspan='2'><b>".$groups["$currentgroup"]." templates:</b> <input type='submit' value='  Collapse  ' onClick=\"blocking(".$count."); if (this.value=='  Collapse  ') { this.value='   Expand   '; } else { this.value='  Collapse  '; } return false\"></td>\n</tr>\n\n<tr class='$ourbg' valign='top' id='group$count'>\n<td colspan='2'>\n\n";
			}
			$doneecho.=$echocur;
			$doneecho.=iif($currentgroup!='0', "&nbsp;&nbsp;&nbsp;&nbsp;", "")."<span class='rc'>$template[title]</span>".makelinkcode("edit","template.php?s=$session[sessionhash]&action=edit&templateid=$template[templateid]",1)."<br>\n";
			$templatecount++;
		}
		if ($doneecho!='') {
			echo $doneecho;
			makelabelcode("<b>Done exporting created templates!</b>");
		} else {
			makelabelcode("<b>No created templates to export!</b>");
		}

		restarttable();
		maketableheader("Exporting default templates");
		$ourbg=getrowbg();
		$ourbg='firstalt';
		echo "<tr class='$ourbg' valign='top'><td colspan='2'>";
		$doneecho='';
		$currentgroup='0';
		$templatecount=0;
		$templates=$DB_site->query("SELECT t1.title,t1.template,t2.templateid,t2.title,t2.template,NOT ISNULL(t2.templateid) AS found FROM template AS t1 LEFT JOIN template AS t2 ON (t1.title=t2.title AND t2.templatesetid=$templatesetid) WHERE t1.templatesetid=-1 AND t1.title<>'options' AND (".iif($todo!='', "t1.title IN ($todo) AND ", "")."t1.title NOT IN (".$done.")) ORDER BY t1.title");
		while ($template=$DB_site->fetch_array($templates)) {
			$path='./'.$folder.'/'.$template[title].'.'.$extension;
			writetofile($path,$template[template],$makebackup);
			$oldgroup=$currentgroup;
			$currentgroup='0';
			$count=0;
			reset($groups);
			while (list ($groupname,$grouptitle)=each($groups)) {
				$count++;
				if (strpos(" $template[title]", $groupname)==1) {
					$currentgroup=$groupname;	
					break;
				}
			}
			$echocur='';
			if ($oldgroup!=$currentgroup) {
				$echocur="</td>\n</tr>\n".iif($templatecount!=0, "<tr class='secondalt' valign='top'><td colspan='2' height='10'></td>\n</tr>\n", "")."<tr class='$ourbg' valign='top'>\n<td colspan='2'>";
			}
			if ($currentgroup!='0' and $oldgroup!=$currentgroup) {
				$echocur="<tr class='secondalt' valign='top' id='submitrow'><td colspan='2'><b>".$groups["$currentgroup"]." templates:</b> <input type='submit' value='  Collapse  ' onClick=\"blocking(".$count."); if (this.value=='  Collapse  ') { this.value='   Expand   '; } else { this.value='  Collapse  '; } return false\"></td>\n</tr>\n\n<tr class='$ourbg' valign='top' id='group$count'>\n<td colspan='2'>\n\n";
			}
			$doneecho.=$echocur;
			if ($template[found]) {
				$doneecho.=iif($currentgroup!='0', "&nbsp;&nbsp;&nbsp;&nbsp;", "")."<span class='cc'>$template[title]</span>".makelinkcode("edit","template.php?s=$session[sessionhash]&action=edit&templateid=$template[templateid]",1)."<br>\n";
			} else {
				$doneecho.=iif($currentgroup!='0', "&nbsp;&nbsp;&nbsp;&nbsp;", "")."<span class='gc'>$template[title]</span>".makelinkcode("edit","template.php?s=$session[sessionhash]&action=edit&templateid=$template[templateid]",1)."<br>\n";
			}
			$templatecount++;
		}
		if ($doneecho!='') {
			echo $doneecho;
			makelabelcode("<b>Done exporting original templates!</b>");
		} else {
			makelabelcode("<b>No original templates to export!</b>");
		}
		echo "</table></td></tr></table></form>";
		
		$action="modify";
	} else {
		echo "<b>ERROR:</b> A field is empty.\n";
		$action="modify";
	}

}

// #############################################################
// ################# Start select for export ###################
// #############################################################
if ($HTTP_POST_VARS['action']=="doexport" and $selecttemplates==1) {

	if (trim($extension)!='' and trim($folder)!='') {
		echo "<p>Templates with <span class='rc'>this color</span> are templates you have created from scratch.<br>Templates with <span class='gc'>this color</span> are original templates you have edited.<br>Templates with <span class='cc'>this color</span> are the default templates that come with vBulletin.</p>";

		doformheader("tbs","doexport",0,1,"form");
		maketableheader("Export one of your template sets");
		$ourbg='firstalt';
		echo "<tr class='$ourbg' valign='top' id='submitrow'>\n<td colspan='2'><p>Please select the templates you'd like to export:".iif($highram==1, "<br>\nCheck all boxes: <input type='checkbox' value='Check All' name='allbox' onClick='CheckAll();' style='background-color:#D0D0D0;' checked> (takes a while)\n","")."</p></td>\n</tr>\n";
		echo "</table></td></tr></table>";
		echo "<br><br>\n\n";
		echo "<table cellpadding='1' cellspacing='0' border='0' align='center' width='90%' class='tblborder'><tr><td>\n";
		echo "<table cellpadding='4' cellspacing='0' border='0' width='100%'>\n";
		maketableheader("Your created templates:&nbsp;&nbsp;&nbsp;<input type='submit' value='".iif($startwith!='exp',"   Expand   ","  Collapse  ")."' onClick=\"blocking(-999); if (this.value=='  Collapse  ') { this.value='   Expand   '; } else { this.value='  Collapse  '; } return false\" style='	BACKGROUND-COLOR:#40364d; COLOR: #f5d300; FONT-WEIGHT: bold;'>","",0);

		echo "</table></td></tr></table>";
		echo "<br>\n\n";
		echo "<table cellpadding='1' cellspacing='0' border='0' align='center' width='90%' class='tblborder' id='group-999'".iif($startwith!='exp',"style='display: none'","")."><tr><td>\n";
		echo "<table cellpadding='4' cellspacing='0' border='0' width='100%'>\n";
		echo "<tr class='$ourbg' valign='top'><td colspan='2'>";

		$done='0';
		$currentgroup='0';
		$templatecount=0;
		$templates=$DB_site->query("SELECT t1.* FROM template AS t1 LEFT JOIN template AS t2 ON (t1.title=t2.title AND t2.templatesetid=-1) WHERE t1.templatesetid=$templatesetid AND t1.title<>'options' AND ISNULL(t2.templateid) ORDER BY t1.title");
		while ($template=$DB_site->fetch_array($templates)) {
			$oldgroup=$currentgroup;
			$currentgroup='0';
			$count=0;
			reset($groups);
			while (list ($groupname,$grouptitle)=each($groups)) {
				$count--;
				if (strpos(" $template[title]", $groupname)==1) {
					$currentgroup=$groupname;	
					break;
				}
			}
			$echocur='';
			if ($oldgroup!=$currentgroup) {
				$echocur="</td>\n</tr>\n".iif($templatecount!=0, "<tr class='secondalt' valign='top'><td colspan='2' height='10'></td>\n</tr>\n", "")."<tr class='$ourbg' valign='top'>\n<td colspan='2'>";
			}
			if ($currentgroup!='0' and $oldgroup!=$currentgroup) {
				$echocur="<tr class='secondalt' valign='top' id='submitrow'><td colspan='2'><b>".$groups["$currentgroup"]." templates:</b> <input type='submit' value='".iif($startwith!='exp',"   Expand   ","  Collapse  ")."' onClick=\"blocking(".$count."); if (this.value=='  Collapse  ') { this.value='   Expand   '; } else { this.value='  Collapse  '; } return false\"></td>\n</tr>\n\n<tr class='$ourbg' valign='top' id='group$count'".iif($startwith!='exp',"style='display: none'","").">\n<td colspan='2'>\n\n";
			}
			echo $echocur;
			echo iif($currentgroup!='0', "&nbsp;&nbsp;&nbsp;&nbsp;", "")."<input type='checkbox' name='templatelist[]' value='".$template[title]."'".iif($checkcreated==1, " checked", "")."> <span class='rc'>$template[title]</span>".makelinkcode("edit","template.php?s=$session[sessionhash]&action=edit&templateid=$template[templateid]",1).makelinkcode("remove","template.php?s=$session[sessionhash]&action=remove&templateid=$template[templateid]",1)."<br>\n";
			$done.=",'".$template[title]."'";
			$templatecount++;
		}
		if ($DB_site->num_rows($templates)==0) {
			echo "No created templates to export!";
		}

		echo "</table></td></tr></table>";
		echo "<br>\n\n";
		echo "<table cellpadding='1' cellspacing='0' border='0' align='center' width='90%' class='tblborder'><tr><td>\n";
		echo "<table cellpadding='4' cellspacing='0' border='0' width='100%'>\n";
		maketableheader("Your default templates:&nbsp;&nbsp;&nbsp;<input type='submit' value='".iif($startwith!='exp',"   Expand   ","  Collapse  ")."' onClick=\"blocking(999); if (this.value=='  Collapse  ') { this.value='   Expand   '; } else { this.value='  Collapse  '; } return false\" style='	BACKGROUND-COLOR:#40364d; COLOR: #f5d300; FONT-WEIGHT: bold;'>","",0);

		echo "</table></td></tr></table>";
		echo "<br>\n\n";
		echo "<table cellpadding='1' cellspacing='0' border='0' align='center' width='90%' class='tblborder' id='group999'".iif($startwith!='exp',"style='display: none'","")."><tr><td>\n";
		echo "<table cellpadding='4' cellspacing='0' border='0' width='100%'>\n";
		echo "<tr class='$ourbg' valign='top'><td colspan='2'>";
		$currentgroup='0';
		$templatecount=0;
		$templates=$DB_site->query("SELECT t1.title,t2.templateid,t2.title,NOT ISNULL(t2.templateid) AS found FROM template AS t1 LEFT JOIN template AS t2 ON (t1.title=t2.title AND t2.templatesetid=$templatesetid) WHERE t1.templatesetid=-1 AND t1.title<>'options' ORDER BY t1.title");
		while ($template=$DB_site->fetch_array($templates)) {
			$oldgroup=$currentgroup;
			$currentgroup='0';
			$count=0;
			reset($groups);
			while (list ($groupname,$grouptitle)=each($groups)) {
				$count++;
				if (strpos(" $template[title]", $groupname)==1) {
					$currentgroup=$groupname;	
					break;
				}
			}
			$echocur='';
			if ($oldgroup!=$currentgroup) {
				$echocur="</td>\n</tr>\n".iif($templatecount!=0, "<tr class='secondalt' valign='top'><td colspan='2' height='10'></td>\n</tr>\n", "")."<tr class='$ourbg' valign='top'>\n<td colspan='2'>";
			}
			if ($currentgroup!='0' and $oldgroup!=$currentgroup) {
				$echocur="<tr class='secondalt' valign='top' id='submitrow'><td colspan='2'><b>".$groups["$currentgroup"]." templates:</b> <input type='submit' value='".iif($startwith!='exp',"   Expand   ","  Collapse  ")."' onClick=\"blocking(".$count."); if (this.value=='  Collapse  ') { this.value='   Expand   '; } else { this.value='  Collapse  '; } return false\"></td>\n</tr>\n\n<tr class='$ourbg' valign='top' id='group$count'".iif($startwith!='exp',"style='display: none'","").">\n<td colspan='2'>\n\n";
			}
			echo $echocur;
			if ($template[found]) {
				echo iif($currentgroup!='0', "&nbsp;&nbsp;&nbsp;&nbsp;", "")."<input type='checkbox' name='templatelist[]' value='".$template[title]."'".iif($checkcustom==1, " checked", "")."> <span class='cc'>$template[title]</span>".makelinkcode("edit","template.php?s=$session[sessionhash]&action=edit&templateid=$template[templateid]",1).makelinkcode("revert to original","template.php?s=$session[sessionhash]&action=remove&templateid=$template[templateid]",1).makelinkcode("view original","template.php?s=$session[sessionhash]&action=view&title=".urlencode($template[title]),1)."<br>\n";
			} else {
				echo iif($currentgroup!='0', "&nbsp;&nbsp;&nbsp;&nbsp;", "")."<input type='checkbox' name='templatelist[]' value='".$template[title]."'".iif($checkoriginal==1, " checked", "")."> <span class='gc'>$template[title]</span>".makelinkcode("change original","template.php?s=$session[sessionhash]&action=add&templatesetid=$templatesetid&title=".urlencode($template[title]),1)."<br>\n";
			}
			$templatecount++;
		}
		if ($DB_site->num_rows($templates)==0) {
			echo "No default templates to export!";
		}

		restarttable();
		makelabelcode("Click below to export the selected templates!");
		makehiddencode("templatelist[]","---");
		makehiddencode("extension",$extension);
		makehiddencode("folder",$folder);
		makehiddencode("templatesetid",$templatesetid);
		makehiddencode("makebackup",$makebackup);
		makehiddencode("selecttemplates",0);
		doformfooter("Export Templates");
	} else {
		echo "<b>ERROR:</b> A field is empty.\n";
		$action="modify";
	}

}

// #############################################################
// ###################### Start doimport #######################
// #############################################################
if ($HTTP_POST_VARS['action']=="doimport" and $selecttemplates==0) {

	if (trim($extension)!='' and trim($folder)!='') {
		if (is_dir("./$folder")==0) {
			echo "<b>ERROR:</b> The folder '$folder' does not exist.\n";
		} else {
			$setinfo=$DB_site->query_first("SELECT * FROM templateset WHERE templatesetid=$templatesetid");
			echo "<b>Importing $setinfo[title] template set!</b><br>";
			doformheader("","");
			maketableheader("Importing custom templates");
			$notdone='';
			$doneecho='';
			if (is_array($templatelist)) {
				$templatelist=stripfromarray($templatelist);
				$templist=array_unique($templatelist);
			} else {
				$templist=getfilelist($folder,$extension);
			}
			asort($templist);
			reset($templist);
			while (list ($templatekey,$templatetitle)=each($templist)) {
				$path='./'.$folder.'/'.$templatetitle.'.'.$extension;
				$newtemplate=readfromfile($path);
				if (trim($newtemplate)!='' and $oldtemplate=$DB_site->query_first("SELECT * FROM template WHERE (templatesetid=-1 OR templatesetid=$templatesetid) AND title='$templatetitle' ORDER BY templatesetid DESC LIMIT 1")) {
					if ($newtemplate!=$oldtemplate[template]) {
						if ($oldtemplate[templatesetid]!=-1) {
							$DB_site->query("UPDATE template SET template='".addslashes($newtemplate)."' WHERE title='".addslashes($templatetitle)."' AND templatesetid=$oldtemplate[templatesetid]");
							$doneecho.="<tr class='".getrowbg()."' valign='top'>\n<td colspan='2'><p>$templatetitle".makelinkcode("edit","template.php?s=$session[sessionhash]&action=edit&templateid=$oldtemplate[templateid]",1)."</p></td>\n</tr>\n";
						} else {
							$DB_site->query("INSERT INTO template (templateid,templatesetid,title,template) VALUES (NULL,'$templatesetid','".addslashes($templatetitle)."','".addslashes($newtemplate)."')");
							$newid=$DB_site->insert_id();
							$doneecho.="<tr class='".getrowbg()."' valign='top'>\n<td colspan='2'><p>$templatetitle".makelinkcode("edit","template.php?s=$session[sessionhash]&action=edit&templateid=$newid",1)."</p></td>\n</tr>\n";
						}
					} else {
						$notdone.="<tr class='".getrowbg()."' valign='top'>\n<td colspan='2'><p>$templatetitle (unchanged template)".makelinkcode("edit","template.php?s=$session[sessionhash]&action=edit&templateid=$oldtemplate[templateid]",1)."</p></td>\n</tr>\n";
					}
				} elseif (trim($newtemplate)!='') {
					$DB_site->query("INSERT INTO template (templateid,templatesetid,title,template) VALUES (NULL,'$templatesetid','".addslashes($templatetitle)."','".addslashes($newtemplate)."')");
					$newid=$DB_site->insert_id();
					$doneecho.="<tr class='".getrowbg()."' valign='top'>\n<td colspan='2'><p>$templatetitle".makelinkcode("edit","template.php?s=$session[sessionhash]&action=edit&templateid=$newid",1)."</p></td>\n</tr>\n";
				} else {
					$notdone.="<tr class='".getrowbg()."' valign='top'>\n<td colspan='2'><p>$templatetitle (empty file)</p></td>\n</tr>\n";
				}
			}
			if ($doneecho!='') {
				echo $doneecho;
				makelabelcode("<b>Done importing templates!</b>");
			} else {
				makelabelcode("<b>No templates to import!</b>");
			}
			if ($notdone!='') {
				restarttable();
				maketableheader("Templates that weren't updated");
				echo $notdone;
			}
			echo "</table></td></tr></table></form>";
		}
		$action="modify";
	} else {
		echo "<b>ERROR:</b> A field is empty.\n";
		$action="modify";
	}

}

// #############################################################
// ################# Start select for import ###################
// #############################################################
if ($HTTP_POST_VARS['action']=="doimport" and $selecttemplates==1) {

	if (trim($extension)!='' and trim($folder)!='') {
		doformheader("tbs","doimport",0,1,"form");
		maketableheader("Import one of your template sets");
		$ourbg='firstalt';
		echo "<tr class='$ourbg' valign='top' id='submitrow'>\n<td colspan='2'><p>Please select the templates you'd like to import:".iif($highram==1, "<br>\nCheck all boxes: <input type='checkbox' value='Check All' name='allbox' onClick='CheckAll();' style='background-color:#D0D0D0;' checked> (takes a while)\n","")."</p></td>\n</tr>\n";
		echo "</table></td></tr></table>";
		echo "<br><br>\n\n";
		echo "<table cellpadding='1' cellspacing='0' border='0' align='center' width='90%' class='tblborder'><tr><td>\n";
		echo "<table cellpadding='4' cellspacing='0' border='0' width='100%'>\n";
		maketableheader("Your templates:&nbsp;&nbsp;&nbsp;<input type='submit' value='".iif($startwith!='exp',"   Expand   ","  Collapse  ")."' onClick=\"blocking(999); if (this.value=='  Collapse  ') { this.value='   Expand   '; } else { this.value='  Collapse  '; } return false\" style='	BACKGROUND-COLOR:#40364d; COLOR: #f5d300; FONT-WEIGHT: bold;'>","",0);

		echo "</table></td></tr></table>";
		echo "<br>\n\n";
		echo "<table cellpadding='1' cellspacing='0' border='0' align='center' width='90%' class='tblborder' id='group999'".iif($startwith!='exp',"style='display: none'","")."><tr><td>\n";
		echo "<table cellpadding='4' cellspacing='0' border='0' width='100%'>\n";
		echo "<tr class='$ourbg' valign='top'><td colspan='2'>";

		$templist=getfilelist($folder,$extension);
		asort($templist);
		reset($templist);
		$done='0';
		$currentgroup='0';
		$templatecount=0;
		while (list ($templatekey,$templatetitle)=each($templist)) {
			$oldgroup=$currentgroup;
			$currentgroup='0';
			$count=0;
			reset($groups);
			while (list ($groupname,$grouptitle)=each($groups)) {
				$count++;
				if (strpos(" $templatetitle", $groupname)==1) {
					$currentgroup=$groupname;	
					break;
				}
			}
			$echocur='';
			if ($oldgroup!=$currentgroup) {
				$echocur="</td>\n</tr>\n".iif($templatecount!=0, "<tr class='secondalt' valign='top'><td colspan='2' height='10'></td>\n</tr>\n", "")."<tr class='$ourbg' valign='top'>\n<td colspan='2'>";
			}
			if ($currentgroup!='0' and $oldgroup!=$currentgroup) {
				$echocur="<tr class='secondalt' valign='top' id='submitrow'><td colspan='2'><b>".$groups["$currentgroup"]." templates:</b> <input type='submit' value='".iif($startwith!='exp',"   Expand   ","  Collapse  ")."' onClick=\"blocking(".$count."); if (this.value=='  Collapse  ') { this.value='   Expand   '; } else { this.value='  Collapse  '; } return false\"></td>\n</tr>\n\n<tr class='$ourbg' valign='top' id='group$count'".iif($startwith!='exp',"style='display: none'","").">\n<td colspan='2'>\n\n";
			}
			echo $echocur;
			echo iif($currentgroup!='0', "&nbsp;&nbsp;&nbsp;&nbsp;", "")."<input type='checkbox' name='templatelist[]' value='".$templatetitle."'".iif($checktemplates==1, " checked", "")."> $templatetitle".makelinkcode("view file","tbs.php?s=$session[sessionhash]&action=viewtemplate&title=".urlencode($templatetitle)."&folder=".urlencode($folder)."&extension=".urlencode($extension),1)."<br>\n";
			$done.=",'".$templatetitle."'";
			$templatecount++;
		}

		restarttable();
		makelabelcode("Click below to import the selected templates!");
		makehiddencode("templatelist[]","---");
		makehiddencode("extension",$extension);
		makehiddencode("folder",$folder);
		makehiddencode("templatesetid",$templatesetid);
		makehiddencode("selecttemplates",0);
		doformfooter("Import Templates");
	} else {
		echo "<b>ERROR:</b> A field is empty.\n";
		$action="modify";
	}

}

// #############################################################
// ######################## Start viewtemplate ##################
// #############################################################
if ($action=="viewtemplate") {

	$path='./'.$folder.'/'.$title.'.'.$extension;
	$template=readfromfile($path);

	doformheader("","");
	maketableheader("View Template");
	maketextareacode($title,"",$template,20,80);
	echo "</table>\n</td></tr></table></form>";

}

// #############################################################
// ####################### Start export/import #################
// #############################################################
if ($action=="modify") {

	doformheader("tbs","doexport");
	maketableheader("Export one of your template sets");
	makeinputcode("Extension for created files:","extension","html");
	makeinputcode("Folder of files:<br>PHP must have access to write in this directory (usually CHMOD 0777)<br>No prefix or suffix slashes! Relative to admin folder.","folder","templates");
	makechoosercode("Read from template set:","templatesetid","templateset",-1,iif($debug,"Global template set",""));
	makeyesnocode("Backup files with the same name in destination folder:","makebackup",0);
	makeyesnocode("Choose templates to export selectively?<br>(otherwise all templates will be exported)","selecttemplates",1);
	makeyesnocode("Initially check <span class='rc'>created</span> templates.","checkcreated", 1);
	makeyesnocode("Initially check <span class='cc'>edited</span> templates.","checkcustom", 1);
	makeyesnocode("Initially check <span class='gc'>original</span> templates.","checkoriginal", 1);
	makeyesnocode("Do you want to use the 'Check All' checkbox?<br>(Warning: Using this requires a minimum amount of RAM.<br>If you have problems with this feature please choose no)","highram", 0);
	echo "<tr class='".getrowbg()."' valign='top'>\n<td><p>All groups are initially...</p></td>\n<td><p><select name='startwith'>\n";
    echo "<option value='exp'>expanded</option>\n";
    echo "<option value='col' selected>collapsed</option>\n";
	echo "</select>\n</p></td>\n</tr>\n";
	doformfooter("Export Templates");

	doformheader("tbs","doimport");
	maketableheader("Import into one of your template sets");
	makeinputcode("Extension of files:","extension","html");
	makeinputcode("Folder of files:<br>No prefix or suffix slashes! Relative to admin folder.","folder","templates");
	makechoosercode("Save to template set:","templatesetid","templateset",-1,iif($debug,"Global template set",""));
	makeyesnocode("Choose templates to export selectively?<br>(otherwise all templates will be exported)","selecttemplates",1);
	makeyesnocode("Initially check all templates.","checktemplates", 1);
	makeyesnocode("Do you want to use the 'Check All' checkbox?<br>(Warning: Using this requires a minimum amount of RAM.<br>If you have problems with this feature please choose no)","highram", 0);
	echo "<tr class='".getrowbg()."' valign='top'>\n<td><p>All groups are initially...</p></td>\n<td><p><select name='startwith'>\n";
    echo "<option value='exp'>expanded</option>\n";
    echo "<option value='col' selected>collapsed</option>\n";
	echo "</select>\n</p></td>\n</tr>\n";
	doformfooter("Import Templates");

}

cpfooter();

?>