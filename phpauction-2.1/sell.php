<?php

/*

   Copyright (c), 1999, 2000 - phpauction.org                  
   
   This program is free software; you can redistribute it and/or modify 
   it under the terms of the GNU General Public License as published by 
   the Free Software Foundation (version 2 or later).                                  
                                                                        
   This program is distributed in the hope that it will be useful,      
   but WITHOUT ANY WARRANTY; without even the implied warranty of       
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        
   GNU General Public License for more details.                         
                                                                        
   You should have received a copy of the GNU General Public License    
   along with this program; if not, write to the Free Software          
   Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
   
*/

	require('./includes/messages.inc.php');
	require('./includes/config.inc.php');
	require('./includes/auction_types.inc.php');
	require('./includes/countries.inc.php');
	require('./includes/datacheck.inc.php');

	
	//-- Genetares the UCTION's unique ID
	
	function generate_id()
	{
		global $title, $description;
		$continue = true;
		//$auction_id = uniqid(""); 
				
		$auction_id = md5(uniqid(rand()));
		//$auction_id = eregi_replace("[a-f]","",$auction_id);
	
		
		return $auction_id;
	}

	if (empty($action))
	{
		// initialize some variables here

		if ($mode=="recall")
		{
			// recall the variables from current session
#			if (isset($sessionVars["SELL_DATA_CORRECT"]))
#			{

				// delete uploaded image
				if (isset($sessionVars["SELL_file_uploaded"]))
				{
					if (file_exists($image_upload_path.$sessionVars["SELL_pict_url"]))
						unlink($image_upload_path.$sessionVars["SELL_pict_url"]);
					unset($sessionVars["SELL_file_uploaded"]);
					$sessionVars["SELL_pict_url"] = $sessionVars["SELL_pict_url_original"];
					session_name($SESSION_NAME);
					session_register("sessionVars");
					//putSessionVars();
				}

				$title = $sessionVars["SELL_title"];
				$description = $sessionVars["SELL_description"];
				$pict_url = $sessionVars["SELL_pict_url_original"];
				$atype = $sessionVars["SELL_atype"];
				$iquantity = $sessionVars["SELL_iquantity"];
				$minimum_bid = $sessionVars["SELL_minimum_bid"];
				$with_reserve = ($sessionVars["SELL_with_reserve"])?"yes":"no";
				$reserve_price = $sessionVars["SELL_reserve_price"];
				$duration = $sessionVars["SELL_duration"];
				$increments = $sessionVars["SELL_increments"];
				$customincrement = $sessionVars["SELL_customincrement"];
				$country = $sessionVars["SELL_country"];
				$location_zip = $sessionVars["SELL_location_zip"];
				$shipping = $sessionVars["SELL_shipping"];
				$international = ($sessionVars["SELL_international"])?"on":"";
				$category = $sessionVars["SELL_category"];
				$imgtype = $sessionVars["SELL_imgtype"];
#			}
		}
		else
		{
			// auction type
			reset($auction_types);
			list($atype,) = each($auction_types);

			// quantity of items
			$iquantity = 1;


			// country
			reset($countries);
			list($country,) = each($countries);

			// shipping
			$shipping = 1;

			// image type
			$imgtype = 1;

			$with_reserve = "no";
		}
	}
	elseif ($action=='first')
	{
		unset($auction_id);

		// perform data check here
		$ERR = "ERR_".CheckSellData();

		//$ERR = "ERR_".CheckMoney($minimum_bid);
		
		// if no other errors - handle upload here
		if (!$$ERR)
		{
			unset($file_uploaded);

			/* generate a auction ID on this step */
			$auction_id = generate_id();

			if ( $userfile!="none" )
			{
				$inf = GetImageSize ( $userfile );
				$er = false;
				// make a check
				if ($inf)
				{
					$inf[2] = intval($inf[2]); // check for uploaded file type
					if ( ($inf[2]!=1) && ($inf[2]!=2) )
					{
						$er = true;
						$ERR = "ERR_602";
					}
					else
					{
						// check for file size
						if ( intval($userfile_size)>$MAX_UPLOAD_SIZE )
						{
							$er = true;
							$ERR = "ERR_603";
						}
					}
				}
				else
				{
					$ERR = "ERR_602";
					$er = true;
				}

				if (!$er)
				{
					// really save this file
					$ext = ($inf[2]==1)?".gif":".jpg";
					$fname = $image_upload_path.$auction_id.$ext;

					if ( file_exists($fname) )
						unlink ($fname);
					copy ( $userfile, $fname );

					$uploaded_filename = $auction_id.$ext;
					$file_uploaded = true;
				}
				else
				{
					// there is an error
					unset($file_uploaded);
				}

			}
			else
			{
				unset($file_uploaded);
			}
		}
	}

	/*	if script called the first time OR
		an error occured THEN
		display form
	*/
	if ( empty($action) || (($action=='first')&&($$ERR)) )
	{
		// display form here
		include "header.php";

		$auc_id =$sessionVars["SELL_auction_id"];
		$filename = "counter/".auction_id.".txt";
		$newfile = fopen($filename, "w+") or die("Couldn't create file."); fclose($newfile);
		$tfail = fopen("$filename", "w");
		$faili = "1";
		fwrite($tfail, "$faili", 500000);
		fclose($tfail);

		// prepare variables for templates/template

			// simple fields
			$titleH =  htmlspecialchars($title);
			$descriptionH = htmlspecialchars($description);
			$pict_urlH = htmlspecialchars($pict_url);

			// ------------------------------------- auction type
			$T=	"<SELECT NAME=\"atype\">\n";
			reset($auction_types); while(list($key,$val)=each($auction_types)){
				$T.=
					"	<OPTION VALUE=\"".
					$key.
					"\" ".
					(($key==$atype)?"SELECTED":"")
					.">".$val."</OPTION>\n";
			}
			$T.="</SELECT>\n";
			$TPL_auction_type = $T;

			// ------------------------------------- duration
			
			//-- 
			$query = "select * from PHPAUCTION_durations order by days";
			$res_durations = mysql_query($query);
			if(!$res_durations)
			{
				print $ERR_001." - ".mysql_error();
			}
			$num_durations = mysql_num_rows($res_durations);
			$i = 0;
			$T=	"<SELECT NAME=\"duration\">\n";
			while($i < $num_durations){
					
				$days 				= mysql_result($res_durations,$i,"days");
				$duration_descr 	= mysql_result($res_durations,$i,"description");
				$T.= "	<OPTION VALUE=\"$days\"";
				
				if($days == $duration)
				{
					$T .= " SELECTED";
				}
				$T .= ">$duration_descr</OPTION>";
				
				$i++;
			}
			$T.="</SELECT>\n";
			$TPL_durations_list = $T;

			// -------------------------------------- country
			$T=	"<SELECT NAME=\"country\">\n";
			reset($countries); while(list($key,$val)=each($countries)){
				$T.=
					"	<OPTION VALUE=\"".
					$key.
					"\" ".
					(($key==$country)?"SELECTED":"")
					.">".$val."</OPTION>\n";
			}
			$T.="</SELECT>\n";
			$TPL_countries_list = $T;

			// -------------------------------------- payment
			
			//--
			$qurey = "select * from PHPAUCTION_payments";
			$res_payment = mysql_query($qurey);
			if(!$res_payment)
			{
				print $ERR_001." - ".mysql_error();
				exit;
			}
			$num_payments = mysql_num_rows($res_payment);
			$T=	"";
			
			$i = 0;
			while($i < $num_payments)
			{
				$payment_descr = mysql_result($res_payment,$i,"description");
				
				$T.="<INPUT TYPE=CHECKBOX NAME=\"payment[]\" VALUE=\"$payment_descr\"";
				if($payment_descr == $payment[$i])
				{
					$T .= " CHECKED";
				}
				
				$T .= "> $std_font $payment_descr</FONT><BR>";
				
				$i++;
			}
			$TPL_payments_list = $T;

			// -------------------------------------- category
			$T=	"<SELECT NAME=\"category\">\n";
			$result = mysql_query("SELECT * FROM PHPAUCTION_categories_plain");
			if($result):
				while($row=mysql_fetch_array($result)){
					$T.=
						"	<OPTION VALUE=\"".
						$row[cat_id].
						"\" ".
						(($row[cat_id]==$category)?"SELECTED":"")
						.">".$row[cat_name]."</OPTION>\n";
				}
			endif;
			$T.="</SELECT>\n";
			$TPL_categories_list = $T;

			// -------------------------------------- shipping
			if ( intval($shipping)==1 )
				$TPL_shipping1_value = "CHECKED";

			if ( intval($shipping)==2 )
				$TPL_shipping2_value = "CHECKED";

			if ( !empty($international) )
				$TPL_international_value = "CHECKED";

			// -------------------------------------- reserved price
			if ( $with_reserve=="yes" )
				$TPL_with_reserve_selected = "CHECKED";
			else
				$TPL_without_reserve_selected = "CHECKED";

			// -------------------------------------- photo source
			if ( intval($imgtype)==1 )
				$TPL_imgtype2_SELECTED = "CHECKED";
			else
				$TPL_imgtype1_SELECTED = "CHECKED";

			$TPL_error_value = $$ERR;

			// update current session
			if ( isset($sessionVars["SELL_DATA_CORRECT"]) )
				unset($sessionVars["SELL_DATA_CORRECT"]);
			session_name($SESSION_NAME);
			session_register("sessionVars");
			
			//putSessionVars();

		// include corresponding templates/template and exit
		include "templates/template_sell_php.html";
		include "footer.php";
		exit;
	}

	/*	all data is ok.
		TODO: update current session variables and proceed further
	*/
	if ($action=="first" && !$$ERR)
	{
			// auction title
		$sessionVars["SELL_title"] = strip_tags($title);
			// auction description
		$sessionVars["SELL_description"] = $description;
		
			// image URL
		if (!isset($file_uploaded))
		{
			$sessionVars["SELL_pict_url"] = $pict_url;
			unset($sessionVars["SELL_original_filename"]);
		}
		else
		{
			// the URL is uploaded image
			$sessionVars["SELL_pict_url"] = $uploaded_filename;
			$sessionVars["SELL_original_filename"] = $userfile_name;
		}

			// data from "picture URL" input field
		$sessionVars["SELL_pict_url_original"] = $pict_url;

			// flag if file is uploaded
		if (!isset($file_uploaded))
			unset($sessionVars["SELL_file_uploaded"]);
		else
			$sessionVars["SELL_file_uploaded"] = true;

		// auction type
		$sessionVars["SELL_atype"] = $atype;

		// quantity of items for sale
		$sessionVars["SELL_iquantity"] = $iquantity;

		// minimum bid
		$sessionVars["SELL_minimum_bid"] = $minimum_bid;

		// increments information
		$sessionVars["SELL_increments"] = $increments;
		$sessionVars["SELL_customincrement"] = $customincrement;

		// reserved price flag
		$sessionVars["SELL_with_reserve"] = ($with_reserve=="yes")?true:false;

		// reserved price value
		$sessionVars["SELL_reserve_price"] = $reserve_price;

		// auction duration
		$sessionVars["SELL_duration"] = $duration;

		// country
		$sessionVars["SELL_country"] = $country;

		// zip code
		$sessionVars["SELL_location_zip"] = $location_zip;

		// shipping method
		$sessionVars["SELL_shipping"] = $shipping;

		// international shipping
		$sessionVars["SELL_international"] = (strlen($international)==0)?false:true;

		// payment methods: text and index
		reset($payment); 
		while(list($key,$val) = each($payment))
		{
			$sessionVars["SELL_payment".$key] = $payment[$key];
		}

			// category ID
		$sessionVars["SELL_category"] = $category;

			// auction id
		if (isset($auction_id))
			$sessionVars["SELL_auction_id"] = $auction_id;
		else
			$sessionVars["SELL_auction_id"] = generate_id();

			// image type
		$sessionVars["SELL_imgtype"] = $imgtype;

			// set that first step is passed
		$sessionVars["SELL_DATA_CORRECT"] = true;
		session_name($SESSION_NAME);
		session_register("sessionVars");
		//putSessionVars();
#		print "Sessions vars are put";
	}

	// check second data - login and password
	if ( $action=="second")
	{
		$nickH = htmlspecialchars($nick);

		$result = mysql_query("SELECT * FROM PHPAUCTION_users WHERE nick='".AddSlashes($nick)."'");
		if ($result)
			$num = mysql_num_rows($result);
		else
			$num = 0;
		
		if ($num==0)
			$ERR = "ERR_025";

		if ($num>0)
		{
			$pass = mysql_result ($result,0,"password");
			$user_id = mysql_result ($result,0,"id");
			if (md5($MD5_PREFIX.$password) != $pass)
			{
				$ERR = "ERR_026";
			}
			else
			{
				if(mysql_result($result,0,"suspended") > 0)
				{
					$ERR = "ERR_618";
				}
			}
		}
	}

	if ( ($action=="first" && !$$ERR) || ($action=="second" && $$ERR) )
	{
		// display preview form

			// error text
		$TPL_error = $$ERR;

			// title text
		$TPL_title_value = strip_tags($sessionVars["SELL_title"]);

			// description text
		$TPL_description_shown_value = stripslashes(nl2br($sessionVars["SELL_description"]));

			// picture URL
			if( intval($sessionVars["SELL_imgtype"])==0 )
			{
//				print "URL";
				// URL specified
				if ( strlen($sessionVars["SELL_pict_url_original"])==0 )
					$TPL_pict_URL_value = $MSG_114;
				else
					$TPL_pict_URL_value = "<IMG SRC=\"".$sessionVars["SELL_pict_url_original"]."\">";
			}
			else
			{
				// a file uploaded
				if ( empty($sessionVars["SELL_file_uploaded"]) )
					$TPL_pict_URL_value = $MSG_114;
				else
					$TPL_pict_URL_value = "<IMG SRC=\"".$uploaded_path.$sessionVars["SELL_pict_url"]."\">";
			}

/*
		$TPL_pict_URL_value = (strlen($sessionVars["SELL_pict_url"])>0)
			? "<IMG SRC=\"".$uploaded_path.$sessionVars["SELL_pict_url"]."\">"
			: "no image";
*/

			// minimum bid
		$TPL_minimum_bid_value = print_money($sessionVars["SELL_minimum_bid"]);

			// reserved price
		if ($sessionVars["SELL_with_reserve"])
			$TPL_reserve_price_displayed = "$std_font ".print_money($sessionVars["SELL_reserve_price"])."</FONT>";
		else
			$TPL_reserve_price_displayed = "$std_font no </FONT>";

			// auction duration
		
		//--
		$query = "select description from PHPAUCTION_durations where days=".$sessionVars["SELL_duration"];
		$res_duration_descr = mysql_query($query);
		$duration_descr = mysql_result($res_duration_descr,0,"description");
		$TPL_durations_list = $duration_descr;

		#// Bids increment
		if($sessionVars["SELL_increments"] == 1)
		{
			$TPL_increments = $MSG_614;
		}
		else
		{
			$TPL_increments = print_money($sessionVars["SELL_customincrement"]);
		}
		
		
		
		
			// auction type
		$TPL_auction_type = $auction_types[$sessionVars["SELL_atype"]];
		if ( intval($sessionVars["SELL_atype"])==2 )
			$TPL_auction_type .= "</TD></TR><TR><TD ALIGN=RIGHT> $std_font <B>Quantity:</B> </FONT></TD><TD>$std_font".$sessionVars["SELL_iquantity"]."</TD></TR>";

			// country
		$TPL_countries_list = $countries[$sessionVars["SELL_country"]];

			// zip code
		$TPL_location_zip = $sessionVars["SELL_location_zip"];

			// shipping
		if ( intval($sessionVars["SELL_shipping"]) == 1 )
		{
			$TPL_shipping_value = $MSG_038;
		}
		else
		{
			$TPL_shipping_value = $MSG_032;
		}
		if ( $sessionVars["SELL_international"] )
		{
			$TPL_international_value  = "<BR>";
			$TPL_international_value .= $MSG_033;
		}
		else
		{
			$TPL_international_value  = "<BR>";
			$TPL_international_value .= $MSG_043;
		}

		// payment methods
		
		//--
		$query = "select * from PHPAUCTION_payments";
		$res_payments = mysql_query($query);
		if(!$res_payments)
		{
			print $ERR_001." - ".mysql_error();
			exit;
		}
		
		$num_payments = mysql_num_rows($res_payments);
		$i = 0;
		while($i < $num_payments){
		
			if(isset($sessionVars["SELL_payment".$i]))
			{
				$TPL_payment_methods .= "$std_font".$sessionVars["SELL_payment".$i]."</FONT><BR>";
			}
			$i++;
		}

			// category name
		$cat_id = intval($sessionVars["SELL_category"]);
		$result = mysql_query("SELECT * FROM PHPAUCTION_categories WHERE cat_id=$cat_id");
		$parent_id = mysql_result($result,0,"parent_id");
		$category_name = mysql_result($result,0,"cat_name");

		$T = "";
		while($parent_id!=0)
		{
			// get info about this parent
			$result = mysql_query("SELECT * FROM PHPAUCTION_categories WHERE cat_id=$parent_id");
			$pparent_id = intval(mysql_result($result,0,"parent_id"));
			$pcat_id = mysql_result($result,0,"cat_id");
			$pcat_name = mysql_result($result,0,"cat_name");

			$T = "$pcat_name &gt; ".$T;

			// get parent of this parent
			if ($pparent_id!=0)
				$parent_id = mysql_result( mysql_query("SELECT * FROM PHPAUCTION_categories WHERE cat_id=$pparent_id"),0,"parent_id" );
			else
				$parent_id = 0;
		}
		$T .= $category_name;
		$TPL_categories_list = $T;
		
		$sessionVars[categoriesList] = $TPL_categories_list;
		session_name($SESSION_NAME);
		session_register("sessionVars");

		include "header.php";
		include "templates/template_sell_preview_php.html";
		include "footer.php";
		exit;
	}

	if ($action=='second' && !$$ERR)
	{
	
		//-- If a suggested category is present send an e-mail
		//-- to the site administrator
		if($suggested_category)
		{
			$to 		= $adminEmail;
			$subject	= $MSG_254;
			$message	= $suggested_category."\n".
						  $MSG_255.
						  $sessionVars["SELL_auction_id"];
			
			mail($to,$subject,$message);
			
		}


		// really add item to database and display confirmation message
		if ( !$sessionVars["SELL_DATA_CORRECT"] )
			header ( "Location: sell.php" );

		// prepare some things
			// payments list
			
			$payment_text = "";
		//--
		$query = "select * from PHPAUCTION_payments";
		$res_payments = mysql_query($query);
		if(!$res_payments)
		{
			print $ERR_001." - ".mysql_error();
			exit;
		}
		
		$num_payments = mysql_num_rows($res_payments);
		$i = 0;
		while($i < $num_payments)
		{
			$val = mysql_result($res_payments,$i,"description");
			if ( isset($sessionVars["SELL_payment".$i]) )
				$payment_text .= $sessionVars["SELL_payment".$i]." \n";
				
			$i++;
		}
			// auction starts
				$time = time();
				$a_starts = date("Y-m-d H:i:s",$time);

			// auction ends
				$a_ends = $time+$sessionVars["SELL_duration"]*24*60*60;
				$a_ends = date("Y-m-d H:i:s", $a_ends);

			// picture URL
				$pcURL = "";
				if ( ($sessionVars["SELL_file_uploaded"]) && (strlen($sessionVars["SELL_original_filename"])>0) )
					$pcURL = $sessionVars["SELL_pict_url"];
				else
					$pcURL = $sessionVars["SELL_pict_url_original"];

		$result = mysql_query("SELECT * FROM PHPAUCTION_auctions WHERE id=".$sessionVars["SELL_auction_id"]);
		if ($result)
			$nr = mysql_num_rows($result);
		else
			$nr = 0;

		if ($nr>0)
		{
			header ( "Location: item.php?mode=1&id=$sessionVars[SELL_auction_id]");
			exit;
		}

		include "header.php";

		$query = 
			"INSERT INTO PHPAUCTION_auctions VALUES ('".$sessionVars["SELL_auction_id"]."', '". // auction id
			$user_id."', '".
			addslashes($sessionVars["SELL_title"])."', '". // auction title
			$a_starts."', '". // auction starts
			addslashes($sessionVars["SELL_description"])."', '". // auction description
			addslashes($pcURL)."', ". // picture URL
			$sessionVars["SELL_category"].", '". // category
			$sessionVars["SELL_minimum_bid"]."', '".// minimum bid
			(($sessionVars["SELL_with_reserve"])?$sessionVars["SELL_reserve_price"]:"0")."', '".// reserve price
			$sessionVars["SELL_atype"]."', '".// auction type
			$sessionVars["SELL_duration"]."', ".
			doubleval($sessionVars["SELL_customincrement"]).", '".
			$sessionVars["SELL_country"]."', '".// country
			$sessionVars["SELL_location_zip"]."', '".// zip code
			$sessionVars["SELL_shipping"]."', '".// shipping method
			$payment_text."', '".// payment method
			(($sessionVars["SELL_international"])?"1":"0")."', '".// international shipping
			$a_ends."', '".// ends
			"0', '".// current bid
			"0', ".// closed
			(($sessionVars["SELL_file_uploaded"])?"1":"0").", ".
			$sessionVars["SELL_iquantity"].", ".// quantity
            "'0' ".//suspended
			")";// photo uploaded

		
		if (!mysql_query($query))
			print $ERR_001.mysql_error()."<BR>$query";
		else
		{
			//-- Update COUNTERS table
			
			$query = "select auctions from PHPAUCTION_counters";
			$result_counters = mysql_query($query);
			if($result_counters){
				$auction_counter = mysql_result($result_counters,0,"auctions") + 1;
				$query = "update PHPAUCTION_counters set auctions = $auction_counter";
				$result = mysql_query($query);
			}
			
			$TPL_auction_id = $sessionVars["SELL_auction_id"];
			include "templates/template_sell_result_php.html";
		}

		include "footer.php";

			// and increase category counters
		$ct = intval($sessionVars["SELL_category"]);
		$row = mysql_fetch_array(mysql_query("SELECT * FROM PHPAUCTION_categories WHERE cat_id=$ct"));
		$counter = $row[counter]+1;
		$subcoun = $row[sub_counter]+1;
		$parent_id = $row[parent_id];
		mysql_query("UPDATE PHPAUCTION_categories SET counter=$counter, sub_counter=$subcoun WHERE cat_id=$ct");

			// update recursive categories
		while ( $parent_id!=0 )
		{
			// update this parent's subcounter
				$rw = mysql_fetch_array(mysql_query("SELECT * FROM PHPAUCTION_categories WHERE cat_id=$parent_id"));
				$subcoun = $rw[sub_counter]+1;
				mysql_query("UPDATE PHPAUCTION_categories SET sub_counter=$subcoun WHERE cat_id=$parent_id");
			// get next parent
				$parent_id = intval($rw[parent_id]);
		}
			// Send confirmation email
    		$result = mysql_query("SELECT * FROM PHPAUCTION_users WHERE nick='".AddSlashes($nick)."'");
        	$user_name = mysql_result ($result,0,"name");
        	$user_email = mysql_result ($result,0,"email");
        	$user_address = mysql_result ($result,0,"address");
        	$user_city = mysql_result ($result,0,"city");
        	$user_country = mysql_result ($result,0,"country");
        	$user_zip = mysql_result ($result,0,"zip");                        
			$title = $sessionVars["SELL_title"];
			$auction_id = $sessionVars["SELL_auction_id"];
			$description = $sessionVars["SELL_description"];
			$pict_url = $pcURL;
			$minimum_bid = $sessionVars["SELL_minimum_bid"];
			$reserve_price = $sessionVars["SELL_reserve_price"];
			$duration = $sessionVars["SELL_duration"];
			$customincrement = $sessionVars["SELL_customincrement"];
			//$cat_name = $sessionVars["SELL_category"];
			$cat_name = $category_name;
			$ends = $a_ends;
         $auction_url = $SETTINGS[siteurl] . "item.php?mode=1&id=".$sessionVars["SELL_auction_id"];
        	include('./includes/auction_confirmation.inc.php');
                        
	}

	// clear this session from SELL_ variables
	reset($sessionVars); while(list($key,$val)=each($sessionVars)){
		if ( strpos($key,"SELL_")==0 )
			unset($sessionVars[$key]);
	}
	session_name($SESSION_NAME);
	session_register("session_Vars");
	//putSessionVars();

	// to be continued
	exit;
?>