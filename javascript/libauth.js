function sendForm(parametre)
{
	var param="";
	if(!parametre)
	{
		var nbElements=document.forms.main.length;
  	param=document.forms.main.elements[0].name+"="+document.forms.main.elements[0].value;
  	for(i=1;i<nbElements;i++)
			param+="&"+document.forms.main.elements[i].name+"="+document.forms.main.elements[i].value;
  }
	else
 		param=parametre;

	if(document.all)
  {
    Swidth=window.document.body.clientWidth;
    Sheight=window.document.body.clientHeight;
  }
  else
  {
    Swidth=window.innerWidth;
    Sheight=window.innerHeight;
  }
  Swidth=Swidth-200;
  Sheight=Sheight-50;
	param+= "&w="+Swidth+"&h="+Sheight;

 // alert("oups "+param);
	var file="lib/libauth.php";
	var ajaxRequest=new Ajax.Request(file,{method:'post',requestHeaders:["Content-Type", "application/x-www-form-urlencoded; charset=iso-8859-1"],parameters: param, onComplete: lfProgress,onFailure:reportError});
}

function lfProgress(serverResponse)
{
  if(serverResponse.responseText=="ok")
	{
	  document.location.href="index.phtml";
	}
	else
	{
		$('error').innerHTML="Echec de connexion";
	}
}

function reportError(serverResponse){
	alert("Error:"+serverResponse.responseText);
}