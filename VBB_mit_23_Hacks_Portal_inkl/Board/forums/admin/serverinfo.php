<?php
error_reporting(7);

require("./global.php");

cpheader("<title>Information About Your vBulletin Server</title>");

if (isset($action)==0)
{
	$action="detail";
}

// Variables
$phpversion = phpversion(); 
$mysqlhost = mysql_get_host_info();
$mysqlproto = mysql_get_proto_info();
$mysqlserver = mysql_get_server_info();
$mysqlclient = mysql_get_client_info();

// Detailed Info
if ($action=="detail")
{
	doformheader("","");
	maketableheader("Server Information");
	makelabelcode("<b>Server Software</b>","$SERVER_SOFTWARE");
	makelabelcode("<b>Server IP address</b>","$SERVER_ADDR");
	makelabelcode("<b>Server Admin Email</b>","<a href=\"mailto:$SERVER_ADMIN\">$SERVER_ADMIN</a>");
	makelabelcode("<b>Server Protocol</b>","$SERVER_PROTOCOL");
	makelabelcode("<b>Server Port</b>","$SERVER_PORT");
	makelabelcode("<b>Server Signature</b>","$SERVER_SIGNATURE");
	maketableheader("PHP Information");
  	makelabelcode("<a href=\"http://www.php.net\" target=\"_blank\"><b>PHP Homepage</b></a>");
  	makelabelcode("<b>PHP Version</b>","$phpversion");
	maketableheader("MySQL Information", "", 0, 2);
  	makelabelcode("<a href=\"http://www.mysql.com\" target=\"_blank\"><b>MySQL Homepage</b></a>");
  	makelabelcode("<b>MySQL Host</b>","$mysqlhost");
  	makelabelcode("<b>MySQL Protocol</b>","$mysqlproto");
  	makelabelcode("<b>MySQL Server Version</b>","$mysqlserver");
  	makelabelcode("<b>MySQL Client Version</b>","$mysqlclient");
	maketableheader("Other Information");
	makelabelcode("<b>Your Current IP address</b>","$REMOTE_ADDR");
	dotablefooter();
}

// PHP Info
if ($action=="phpinfo")
{
	phpinfo();
}

cpfooter();
?>