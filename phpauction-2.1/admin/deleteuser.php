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
	
	if($action)
	{
	
		//-- Check if the users has some auction 
		
		$query = "select * from PHPAUCTION_auctions where user='$id'";
	
		$result = mysql_query($query);
		if(!$result){
			print "Database access error: abnormal termination".mysql_error();
			exit;
		}
		
		$num_auctions = mysql_num_rows($result);
		if($num_auctions > 0)
		{
			
			$ERR = "The user is the SELLER in the following auctions:<BR>";
			$i =  0;
			while($i < $num_auctions){
				$ERR_CODE=2;
				$ERR .= mysql_result($result,$i,"id")."<BR>";
				$i++;
			}
		}

		//-- Check if the user is BIDDER in some auction
		
		$query = "select * from PHPAUCTION_bids where bidder='$id'";
		$result = mysql_query($query);
		if(!$result){
			print "Database access error: abnormal termination".mysql_error();
			exit;
		}
		
		$num_auctions = mysql_num_rows($result);
		if($num_auctions > 0){
			$ERR_CODE=1;
			$ERR = "The user placed a bid in the following auctions:<BR>";
			$i =  0;
			while($i < $num_auctions){
				$ERR .= mysql_result($result,$i,"bidder")."<BR>";
				$i++;
			}
		}



		if($ERR_CODE!=1){

			//-- delete user
			$sql="delete from PHPAUCTION_users WHERE id='$id'";
			$res=mysql_query ($sql);

			//-- delete user bids
			$sql="delete from PHPAUCTION_bids WHERE bidder='$id'";
			$res=mysql_query ($sql);
			
			//-- Update counters
			$query = "select users from PHPAUCTION_counters";
			$result = mysql_query($query);
			
			$users = mysql_result($result,0,"users");
			$users = $users - 1;
			
			$query = "update PHPAUCTION_counters set users=$users";
			$result = mysql_query($query);
			

			Header("location: listusers.php?offset=$offset");
		}
		if($ERR_CODE==2){

			//-- delete user
			$sql="delete from PHPAUCTION_users WHERE id='$id'";
			$res=mysql_query ($sql);

			//-- delete user auctions
			$sql="delete from PHPAUCTION_auctions WHERE user='$id'";
			$res=mysql_query ($sql);
			
			//-- Update counters
			$query = "select users from PHPAUCTION_counters";
			$result = mysql_query($query);
			
			$users = mysql_result($result,0,"users");
			$users = $users - 1;
			
			$query = "update PHPAUCTION_counters set users=$users";
			$result = mysql_query($query);
			

			Header("location: listusers.php?offset=$offset");
		}	
	}
	

	if(!$action || ($action && $ERR)){

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


	include "./header.php";
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
		<B><? print $MSG_304; ?></B>
		</font><BR>
	 </TD>
	</TR>	
<TABLE WIDTH="700" BORDER="0" CELLPADDING="5">

<?
	if($ERR)
	{
?>
<TR>
  <TD WIDTH="204" VALIGN="top" ALIGN="right">
  	
  </TD>
  <TD WIDTH="486">
  	<? print $err_font.$ERR; ?>
  </TD>
</TR>
<? 
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
    <? print "$err_font $MSG_307"; ?>
    
   </TD>
</TR>
<TR>
  <TD WIDTH="204">&nbsp;</TD>
  <TD WIDTH="486">
	</FONT>
	 <FORM NAME=details ACTION="deleteuser.php" METHOD="POST">
	 <INPUT type="hidden" NAME="id" VALUE="<? echo $id; ?>">
	 <INPUT type="hidden" NAME="offset" VALUE="<? echo $offset; ?>">
	 <INPUT type="hidden" NAME="action" VALUE="Delete">	 
	 <INPUT TYPE=submit NAME=act VALUE="<? print $MSG_008; ?>">
	 </FORM>
	 </td>
	 
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
  
  
  <? include "./footer.php"; ?>