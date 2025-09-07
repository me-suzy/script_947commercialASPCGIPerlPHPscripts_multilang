do '../web-lib.pl';
do 'isp4you.pl';
$|=1;
&init_config("isp4you");
%apacheconfig=&foreign_config('apache');
%mysqlconfig=&foreign_config('mysql');
isp4you_header();
