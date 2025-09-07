line0=Mainconfiguratie,11
usershell=Shell voor de nieuwe gebruikers,1,/bin/sh-/bin/sh,/bin/bash-/bin/bash,/bin/false-/bin/false
#group=Group van de gebruikers:,1,users-users,mail-mail
#unixsave=Voor Suse 8.x and RH 9.0 gebruikers (gebruik _ in plaats van . in de naam) ?,1,1-Yes,0-No
pass_strength=Wachtwoord sterkte (6/8/10 karakters) ?,1,3-6,4-8,5-10
#ip=IP adres van uw web server:
adminmail=Uw e-mail adres

line1=Webserver-Configuratie,11
webpfad=Pad naar de webmappen - vereist een eind schuine streep (/):
httpd_2=Pad waar de httpd.conf is opgeslagen - vereist een eind schuine streep (/):
#subfolder=Moeten er 3 submappen html cgi-bin logs aangemaakt worden ?,1,1-Yes,0-No
#sub=Moeten we een thuis map met www ervoor aanmaken ?,1,1-Yes,0-No

line2=Mail-Configuratie,11
mail_dir=Pad naar de mailusers nieww DIR - vereist een eind schuine streep (/),3
sendpost=Uw Mail server system:,1,1-Sendmail,0-Postfix
virtuser=Pad naar de virtusertable(sendmail) of postfix virtual file:
mailvariable=Aantal voor gedefinieerde mail accounts:
sendresult=Wilt u een bevestigings mail?,1,1-Yes,0-No
mxer=Zijn mailusers in staat om mail over uw mxer te sturen ?,1,1-Yes,0-No

line3=Naamserver-Configuratie,11
bindbin=Pad voor het herladen van Bind:
master=Pad naar de hoofdbestanden van bind8 vereist een eind schuine streep (/):
host1=Eerste naam server: (FQDN)
host2=Tweede naam server: (FQDN)
mailserver=Mail server voor de e-mails:

line4=MySQL-Support-Configuratie,11
mysql_user=Uw MySQL gebruikersnaam:
mysql_pass=Uw MySQL wachtwoord:
mysql_host=Uw MySQL host:

line5=Others,11
webalizer_path=Volledig pad naar uw webalizer programma:
openssl=Volledig pad naar uw openssl programma:
device=Device waar de quotas op draaien:
checked=Default zijn alle opties aan,1,1-Yes,0-No
quota_default=voor gedefininieerde Quotas op het indexscherm:
whois=use whois control option,1,1-Yes,0-No
disk_usage=show disk usage,1,1-Yes,0-No
