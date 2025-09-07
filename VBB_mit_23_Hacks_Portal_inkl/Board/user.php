<?php
error_reporting(7);
include("config.php");
global $uname;

if ($action=="switchdisplay") {
    chdir($vbpath . "/"); 
 	require($vbpath . "/global.php");
	if ($bbshowleftcolumn) {
		$showleftcolumn=0;
		$lcmessage="Linker Block wird ausgeblendet";
    } else {
		$showleftcolumn =1;
        $lcmessage="Linker Block wird eingeblendet";
	}
    vbsetcookie("bbshowleftcolumn",$showleftcolumn);
	echo standardredirect($lcmessage,"$bburl/index.php?s=$session[sessionhash]");
}	

if ($op==""){
    header("location:forums/register.php?action=signup");
}

switch($op) {

	case "userinfo":
    header("location:forums/member.php?action=getinfo&username=$uname");
    break;

    
}

?>
