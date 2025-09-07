# acl_security_form(&options)
# Output HTML for editing security options for the apache module
sub acl_security_form
{

## Here you have to fill in the code for output
print "<tr>\n <td valign=top><b>Number of allowed domains</b>";
print "</td>\n";
print " <td colspan=3>\n";

# print $text{'page_title'};
$dom =  join("\n", split(/\s+/, $_[0]->{'dom'}));
$mail =  join("\n", split(/\s+/, $_[0]->{'mail'}));


print "<input type=text name=dom value=$dom>";
print "</td>\n</tr>\n";


print "<tr>\n <td valign=top><b>E-Mail address</b></td>\n";
print "<td colspan=3>\n";
print "<input type=text name=mail value=$mail></td></tr>\n";


}

# acl_security_save(&options)
# Parse the form for security options for the apache module
sub acl_security_save
{

## here you have to fill in the handling code for the saving the ACL

local @dom = split(/\s+/, $in{'dom'});
map { s/\/+/\//g } @dom;
map { s/([^\/])\/+$/$1/ } @dom;
$_[0]->{'dom'} = join(" ", @dom);


local @mail = split(/\s+/, $in{'mail'});
map { s/\/+/\//g } @mail;
map { s/([^\/])\/+$/$1/ } @mail;
$_[0]->{'mail'} = join(" ", @mail);

}
### END
