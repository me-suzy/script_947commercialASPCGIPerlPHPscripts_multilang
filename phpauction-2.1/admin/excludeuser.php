<?
	include "loggedin.inc.php";

	include "../includes/config.inc.php";
	include "../includes/messages.inc.php";
	include "../includes/countries.inc.php";
	
	$username = $name;


	//-- Data check
	if(!$id){
		header("Location: listusers.php");
		exit;
	}
	
	if($action){
	
		if($mode == "activate")
		{
			$sql="update PHPAUCTION_users set suspended=0 WHERE id='$id'";
		}
		else
		{
			$sql="update PHPAUCTION_users set suspended=1 WHERE id='$id'";
		}
		$res=mysql_query ($sql);

		Header("Location: listusers.php");
		exit;
	}
	

	if(!$action || ($action && $updated)){

		$query = "select * from PHPAUCTION_users where id=\"$id\"";
		$result = mysql_query($query);
		if(!$result){
			print "Database access error: abnormal termination".mysql_error();
			exit;
		}

		$username = mysql_result($result,0,"name");

		$nick = mysql_result($result,0,"nick");
		$password = mysql_result($result,0,"password");
		$email = mysql_result($result,0,"email");
		$address = mysql_result($result,0,"address");
		
		$country = mysql_result($result,0,"country");
		$country_list="";
		while (list ($code, $descr) = each ($countries))
		{
			$country_list .= "<option value=\"$code\"";
			if ($code == $country)
			{
				$country_list .= " selected";
			}
			$country_list .= ">$descr</option>\n";
		};
		
		$prov = mysql_result($result,0,"country");
		$zip = mysql_result($result,0,"zip");

		$birthdate = mysql_result($result,0,"birthdate");
		$birth_day = substr($birthdate,6,2);
		$birth_month = substr($birthdate,4,2);
		$birth_year = substr($birthdate,0,4);
		$birthdate = "$birth_day/$birth_month/$birth_year";


		$phone = mysql_result($result,0,"phone");
		$suspended = mysql_result($result,0,"suspended");

		$rate_num = mysql_result($result,0,"rate_num");
		$rate_sum = mysql_result($result,0,"rate_sum");
		if ($rate_num) 
		{
			$rate = round($rate_sum / $rate_num);
		}
		else 
		{
			$rate=0;
		}

	}


	require('./header.php');
	require('../includes/styles.inc.php'); 
?>

<TABLE WIDTH="100%" BORDER="0" CELLPADDING="5" BGCOLOR="#FFFFFF">
<TR>
<TD>
	<TABLE WIDTH=100% CELPADDING=0 CELLSPACING=0 BORDER=0 BGCOLOR="#FFFFFF">
	<TR>
	 <TD ALIGN=CENTER COLSPAN=5>
		<BR>
		<FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="4">
		<B>
		<?
			if($suspended > 0)
			{
				print $MSG_306;
			}
			else
			{
				print $MSG_305;
			}
		?></B>
		</FONT>
		<BR>
	 </TD>
	</TR>	

<TABLE WIDTH="700" BORDER="0" CELLPADDING="5">

<?
if($updated){
			print "<TR>
  					<TD>
  					</TD>
  					<TD WIDTH=486>
					<FONT FACE=\"Verdana,Arial, Helvetica\" SIZE=2 COLOR=red>";
					if($updated) print "Users data updated";					
					print "</TD>
					</TR>";
}
?>


<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
  	<?print $std_font; ?>
  	<? print "$MSG_302"; ?>
  	</FONT>
  </TD>
  <TD WIDTH="486">
  	<?print $std_font.$username; ?>
  </TD>
</TR>

<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
   <? print $std_font; ?>
   <? print "$MSG_003"; ?>
   </FONT>
  </TD>
  <TD WIDTH="486">
  	<? print $std_font.$nick; ?>
  	</FONT>
  </TD>
</TR>

<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
   <? print $std_font; ?>
   <? print "$MSG_004"; ?>
   </FONT>
  </TD>
  <TD WIDTH="486">
   <? print $std_font.$password; ?>
  	</FONT>
  </TD>
</TR>

<TR>
  <TD WIDTH="204"  VALIGN="top" ALIGN="right">
   <? print $std_font; ?>
   <? print "$MSG_303"; ?>
   </FONT>
  </TD>
  <TD WIDTH="486">
   <? print $std_font.$email; ?>
  </TD>
</TR>

<TR>
  <TD WIDTH="204"  VALIGN="top" ALIGN="right">
   <? print $std_font; ?>
   <? print "$MSG_252"; ?>
   </FONT>
  </TD>
  <TD WIDTH="486">
  	<? print $std_font.$birthdate; ?>
  </TD>
</TR>

<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
  	<? print $std_font; ?>
  	<? print "$MSG_009"; ?>
  	</FONT>
  </TD>
  <TD WIDTH="486">
  	<? print $std_font.$address; ?>
  </TD>
</TR>


<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
   <? print $std_font; ?>
   <? print "$MSG_014"; ?>
   </FONT>
  </TD>
  <TD WIDTH="486">
	<? print $std_font.$countries[$country]; ?>
	
  </TD>
</TR>

<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
   <? print $std_font; ?>
   <? print "$MSG_012"; ?>
   </FONT>
  </TD>
  <TD WIDTH="486">
  	<? print $std_font.$zip; ?>
  </TD>
</TR>

<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
   <? print $std_font; ?>
   <? print "$MSG_013"; ?>
   </FONT>
  </TD>
  <TD WIDTH="486">
  	<? print $std_font.$phone; ?>
  </TD>
</TR>

<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
   <? print $std_font; ?>
   <? print "$MSG_222"; ?>
   </FONT>
  </TD>
  <TD WIDTH="486">
    <? if(!$rate) $rate=0; ?>
  	<IMG src="../images/estrella_<? echo $rate; ?>.gif">
  </TD>
</TR>

<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
   <? print $std_font; ?>
   <? print "$MSG_300"; ?>
   </FONT>
  </TD>
  <TD WIDTH="486">
	<? 
		print $std_font; 
		if($suspended == 0)
			print "$MSG_029";
		else
			print "$MSG_030";
		
	?>
	
  </TD>
</TR>

<TR>
  <TD WIDTH="204">&nbsp;</TD>
  <TD WIDTH="486">
	</FONT>
    <? print $err_font; ?>
    <?
    		if($suspended > 0)
    		{
    			print $MSG_309;
    			$mode = "activate";
    		}
    		else
    		{
    			print $MSG_308;
    			$mode = "suspend";
    		}
    	?>
   </TD>
</TR>
<TR>
  <TD WIDTH="204">&nbsp;</TD>
  <TD WIDTH="486">
	</FONT>
		 <FORM NAME=details ACTION="excludeuser.php" METHOD="POST">
		 <INPUT type="hidden" NAME="id" VALUE="<? echo $id; ?>">
		 <INPUT type="hidden" NAME="offset" VALUE="<? echo $offset; ?>">
		 <INPUT type="hidden" NAME="action" VALUE="Delete">	 
		 <INPUT type="hidden" NAME="mode" VALUE="<? print $mode; ?>">	 
		 <INPUT TYPE=submit NAME=act VALUE="<? print $MSG_030; ?>">   
		 </FORM>
	 </TD>
	 
</TR>
</TABLE>

</center>

 <BR>
  <BR>
  <CENTER>
  <FONT face="Tahoma, Arial" size="2"><A HREF="admin.php">Admin home</A> | 
  <FONT face="Tahoma, Arial" size="2"><A HREF="listusers.php?offset=<? print $offset; ?>">Users list</A></FONT>
  </CENTER>
  
</TD>
</TR>
</TABLE>

</TD>
</TR>
</TABLE>

  
  <!-- Closing external table (header.php) -->
  </TD>
  </TR>
</TABLE>

<? include "footer.php"; ?>