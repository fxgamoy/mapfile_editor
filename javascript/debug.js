var bdebug=true;
var wdebug=null;
if(bdebug){
    wdebug=window.open("/debug.phtml","debug");
}

function debug(m){
  if(bdebug) wdebug.document.getElementById("debug").innerHTML+=m+"<br>";
}

function clearDebug(){
   if(bdebug) wdebug.document.getElementById("debug").innerHTML="";
}
