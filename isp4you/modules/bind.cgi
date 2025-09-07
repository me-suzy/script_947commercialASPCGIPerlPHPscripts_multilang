
%bind8config=&foreign_config('bind8');
$named=$bind8config{'named_conf'};
$serial=time;
$refresh="10800";
$retry="3600";
$expire="604801";
$ttl="60800";

$bindmail = $config{'adminmail'};  ### neue variable , damit die original config mail erhalten bleibt !!!
$bindmail =~ tr /@/./;


open(NAMED,">>$named");
print NAMED "zone \"$domainname\"  {\n";
print NAMED "   type master;\n";
print NAMED "   file \"$config{'master'}$domainname\.hosts\";\n";
print NAMED " };";
print NAMED "\n\n";
close(NAMED);

system("touch $config{'master'}$domainname\.hosts");

open(BIND,">>$config{'master'}$domainname\.hosts") or print "$text{'error_master'}\n" and die;
print BIND  "\$ttl 60800\n";
print BIND  "$domainname.   IN    SOA     $config{'host1'}.  $bindmail. (\n";
print BIND  "            $serial\n";
print BIND  "            $refresh\n";
print BIND  "            $retry\n";
print BIND  "            $expire\n";
print BIND  "            $ttl   )\n";
print BIND  "$domainname.                  IN   NS   $config{'host1'}.\n";
print BIND  "$domainname.                  IN   NS   $config{'host2'}.\n";
print BIND  "$in{'www'}$domainname.              IN   A    $config{'ip'}\n";
print BIND  "$domainname.                  IN   A    $config{'ip'}\n";
print BIND  "*.$domainname.                IN   A    $config{'ip'}\n";
print BIND  "$domainname.                  IN   MX   1  $config{'mailserver'}.\n";
close(BIND);


if($in{'bind8'} eq 1 ) {
system("$config{'bindbin'} reload");
}
