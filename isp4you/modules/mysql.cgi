
$domainmysql=$domainname;
if ($in{'subdomain'} eq 1) { $domainmysql="$in{'www'}$domainname"; }
$domainmysql =~ tr/./_/;
$domainmysql =~ tr/-/_/;

use DBI;

$database = "mysql"; $host = "localhost"; $user = "root";
$mysql_pass ="$config{'mysql_pass'}";


my $dbh = DBI->connect("DBI:mysql:database=$database;host=$host", "$user", "$mysql_pass");



$sth = $dbh->prepare("CREATE DATABASE $domainmysql");
$sth->execute();
$sth->finish();


### Create the user.
$sth = $dbh->prepare("INSERT INTO mysql.user VALUES ('localhost',
  '$domainmysql', PASSWORD('$pass'), 'N', 'N', 'N', 'N',
  'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')");
$sth->execute();
$sth->finish();

### Create the database permissions.
$sth = $dbh->prepare("INSERT INTO mysql.db VALUES ('localhost', '$domainmysql',
  '$domainmysql', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y',
  'Y', 'Y')");

$sth->execute();
$sth->finish();
$sth = $dbh->prepare("FLUSH PRIVILEGES");
$sth->execute();
$sth->finish();
$dbh->disconnect();

