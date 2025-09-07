function tmt_print() {
    if (document.all) {
        var OLECMDID_PRINT = 6;
        var OLECMDEXECOPT_DONTPROMPTUSER = 2;
        var OLECMDEXECOPT_PROMPTUSER = 1;
        var WebBrowser = "<OBJECT ID='WebBrowser1' WIDTH=0 HEIGHT=0 CLASSID='CLSID:8856F961-340A-11D0-A96B-00C04FD705A2'></OBJECT>";
            document.body.insertAdjacentHTML("beforeEnd", WebBrowser);
            WebBrowser1.ExecWB(OLECMDID_PRINT, OLECMDEXECOPT_PROMPTUSER);
            WebBrowser1.outerHTML = "";
    } else {
        self.print();
    }
}

function fenster_auf(theURL,winName,features) { Infofenster=window.open(theURL,winName,features); }


function validate_form()
{

if (document.formular.www.value=="")
{ alert("You did not fill in something in.");
  document.formular.www.focus();
  return false; }

if (document.formular.www.value.indexOf('.')==-1)
{ alert ("Please insert a . to the field.");
  document.formular.www.focus();
  return false; }

if (document.formular.domainname.value=="")
{alert ("Please insert a domainname.");
  document.formular.domainname.focus();
  return false; }

if(document.formular.domainname.value.length <=  5 )
 {alert("This is an invalid domainname. The domainname is to short.");
 document.formular.domainname.focus();
 return false; }

if (document.formular.domainname.value.indexOf('.')==-1)
{ alert ("Please fill in a . to the domainname.");
  document.formular.domainname.focus();
  return false; }


if (document.formular.quota_index.value=="")				//Quota
{ alert ("Please fill in a quota entry - 0 for unlimeted webspace");
  document.formular.quota_index.focus();
  return false; }

  

if (document.formular.sendmail.value=="")				//Sendmail
{ alert ("Please fill in a sendmail entry - 0 for no mails");
  document.formular.sendmail.focus();
  return false; }


if (document.formular.webserver.checked == false && document.formular.ssl.checked == false && document.formular.bind8.checked == false && document.formular.mysql.checked == false && document.formular.webalizer.checked == false && document.formular.webminuser.checked == false)
{ alert ("What is it, you wanna create ?");
   return false; }


}