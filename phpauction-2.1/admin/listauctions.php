<?
	include "loggedin.inc.php";


   require('../includes/messages.inc.php');
   require('../includes/config.inc.php');
	
	
	//-- Set offset and limit for pagination
	
	$limit = 20;
	if(!$offset) $offset = 0;
	
	
	include "./header.php";
?>
<STYLE type="text/css">
<!--
.unnamed1 {  font: 10pt Tahoma, Arial; color: #000066; text-decoration: none}
-->
</STYLE>

<?    require('../includes/styles.inc.php'); ?>

	
<TABLE WIDTH=650 CELPADDING=0 CELLSPACING=1 BORDER=0 ALIGN="CENTER" CELLPADDING="3">
	<TR> 
		<TD ALIGN=CENTER COLSPAN=7> <BR>
			<FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="4">
			<B>
			<? print $MSG_067; ?>
			</B> </FONT><BR>
		</TD>
	</TR>
	<?
		$query = "select count(id) as auctions from PHPAUCTION_auctions";
		$result = mysql_query($query);
		if(!$result){
			print "$ERR_001<BR>$query<BR>".mysql_error();
			exit;
		}
		$num_auctions = mysql_result($result,0,"auctions");
		print "<TR><TD COLSPAN=5><FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\" SIZE=2><B>
				$num_auctions $MSG_311</B></TD></TR>";
	?>
	<TR BGCOLOR="#999999"> 
		<TD ALIGN=CENTER> <FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2" COLOR="#000000"> 
			<B> 
			<? print $MSG_312; ?>
			</B> </FONT> </TD>
		<TD ALIGN=CENTER> <FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2" COLOR="#000000"> 
			<B> 
			<? print $MSG_313; ?>
			</B> </FONT> </TD>
		<TD ALIGN=CENTER> <FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2" COLOR="#000000"> 
			<B> 
			<? print $MSG_314; ?>
			</B> </FONT> </TD>
		<TD ALIGN=CENTER> <FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2" COLOR="#000000"> 
			<B> 
			<? print $MSG_315; ?>
			</B> </FONT> </TD>
		<TD ALIGN=LEFT> <FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2" COLOR="#000000"> 
			<B> 
			<? print $MSG_316; ?>
			</B> </FONT> </TD>
		<TD ALIGN=LEFT> <FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2" COLOR="#000000"> 
			<B> 
			<? print $MSG_317; ?>
			</B> </FONT> </TD>
		<TD ALIGN=LEFT> <FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2" COLOR="#000000"> 
			<B> 
			<? print $MSG_297; ?>
			</B> </FONT> </TD>
	
	<TR> 
		<?
		$query = "select a.id, u.nick, a.title, a.starts, a.description, c.cat_name, d.description as duration, a.suspended from PHPAUCTION_auctions a, PHPAUCTION_users u, PHPAUCTION_categories c, PHPAUCTION_durations d where u.id = a.user and c.cat_id = a.category and d.days = a.duration order by nick limit $offset, $limit";
		$result = mysql_query($query);
		if(!$result){
			print "Database access error: abnormal termination<BR>$query<BR>".mysql_error();
			exit;
		}
		$num_auction = mysql_num_rows($result);
		$i = 0;
		$bgcolor = "#FFFFFF";
		while($i < $num_auction){

			if($bgcolor == "#FFFFFF"){
				$bgcolor = "#EEEEEE";
			}else{
				$bgcolor = "#FFFFFF";
			}

			$id = mysql_result($result,$i,"id");
			$title = stripslashes(mysql_result($result,$i,"title"));
			$nick = mysql_result($result,$i,"nick");
			$tmp_date = mysql_result($result,$i,"starts");
			$duration = mysql_result($result,$i,"duration");
			$category = mysql_result($result,$i,"cat_name");
			$description = stripslashes(mysql_result($result,$i,"description"));
			$suspended = mysql_result($result,$i,"suspended");
			$day = substr($tmp_date,6,2);
			$month = substr($tmp_date,4,2);
			$year = substr($tmp_date,0,4);
			$date = "$day/$month/$year";
			
			print "<TR BGCOLOR=$bgcolor>
					<TD>
						<FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\" SIZE=2>";
						if($suspended == 1)
						{
							print "<FONT COLOR=red><B>$title</B></FONT>";
						}
						else
						{
							print $title;
						}
						print "</FONT>
						</TD>
						<TD>
						<FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\" SIZE=2>
						".$nick."
						</FONT>
						</TD>
						<TD>
						<FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\" SIZE=2>
						".$date."
						</FONT>
						</TD>
						<TD>
						<FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\" SIZE=2>
						$duration	
						</FONT>
						</TD>
						<TD>
						<FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\" SIZE=2>
						$category	
						</FONT>
						</TD>
						<TD>
						<FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\" SIZE=2>
						$description	
						</FONT>
						</TD>
						<TD ALIGN=LEFT>
						<A HREF=\"editauction.php?id=$id&offset=$offset\" class=\"nounderlined\">$MSG_298</A><BR>
						<A HREF=\"deleteauction.php?id=$id&offset=$offset\" class=\"nounderlined\">$MSG_299</A><BR>
						<A HREF=\"excludeauction.php?id=$id&offset=$offset\" class=\"nounderlined\">";
						if($suspended == 0)
						{
							print $MSG_300;
						}
						else
						{
							print $MSG_310;
						}
						print "</A><BR>
						</TD>
						<TR>";

			$i++;
		}

		print "</TABLE></FONT><BR>";



		//-- Build navigation line

		print "<SPAN CLASS=\"navigation\">";
		$num_pages = ceil($num_auctions / $limit);
		$i = 0;
		while($i < $num_pages ){

			$of = ($i * $limit);

			if($of != $offset){
				print "<A HREF=\"listauctions.php?offset=$of\" CLASS=\"navigation\">".($i + 1)."</A>";
				if($i != $num_pages) print " | ";
			}else{
				print $i + 1;
				if($i != $num_pages) print " | ";
			}

			$i++;
		}
		print "</SPAN>";



	  ?>
		<BR>
		<BR>
		<CENTER>
			<A HREF="admin.php" CLASS="links">Admin Home</A> 
		</CENTER>
		<BR>
</TABLE>

  
  
  <? include "./footer.php"; ?>