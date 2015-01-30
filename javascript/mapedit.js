var isIE = navigator.appVersion.match(/MSIE/) == "MSIE";
var ongletActive='parametres';
var currentLayer=null;
var currentClass=null;
var currentStyle=null;
var mapFile=null;
var GUI=false;


function initnifty(){
//alert("arrondi");
Nifty("div.arrondi,div.arrondif,div.layer_classe_style_attr","transparent big bottom");
Nifty("div#ongletsprinc a","transparent top");
Nifty("div#onglets a","small transparent top");
Nifty("div#container","bottom");
Nifty("div#ficheonglet","bottom");
}

function initGUI(){
  if(!GUI){
    GUI=true;
    Event.observe(document,"keypress",onKeyPress,true);
	}
}

function onKeyPress(e){
  if(isIE){
    if(e.keyCode!="17"){
      //alert("v="+e.keyCode+"  "+e.ctrlKey);
      if(e.keyCode=="83" && e.ctrlKey){
        save();
        Event.stop(e);
        return false;
      }
    }
  }else{
    //alert("FF="+e.which+"  "+e.ctrlKey);
    if(e.which=="115" && e.ctrlKey){
        save();
        Event.stop(e);
        return false;
    }
  }
  
}

initGUI();

/***************************************
 ***************************************/
function moveLayerUp(layer){
  mapFile.moveLayerUp(layer);
  mapFile.clearGUI('layers');
  $('mapfile').removeChild($('mapfile_layers'));
  new Insertion.Bottom('mapfile',mapFile.getHTMLLayers());
 
  if(currentLayer!=null){
    var tmp;
    if(currentLayer==layer-1) tmp=currentLayer+1;
    else if(currentLayer==layer) tmp=currentLayer-1;
    else tmp=currentLayer;
    currentLayer=null;
    toggleLayer(tmp);
    if(currentClass!=null){
      var tmp=currentClass;
      currentClass=null;
      toggleClass(tmp);
      if(currentStyle!=null){
        var tmp=currentStyle;
        currentStyle=null;
        toggleStyle(currentClass,tmp);
      }
    }
  }
}
function moveLayerDown(layer){
  mapFile.moveLayerDown(layer);
  mapFile.clearGUI('layers');
  $('mapfile').removeChild($('mapfile_layers'));
  new Insertion.Bottom('mapfile',mapFile.getHTMLLayers());
 
  if(currentLayer!=null){
    var tmp;
    if(currentLayer==layer+1) tmp=currentLayer-1;
    else tmp=currentLayer;
    currentLayer=null;
    toggleLayer(tmp);
    if(currentClass!=null){
      var tmp=currentClass;
      currentClass=null;
      toggleClass(tmp);
      if(currentStyle!=null){
        var tmp=currentStyle;
        currentStyle=null;
        toggleStyle(currentClass,tmp);
      }
    }
  }
}
function newLayer(){

  var oLayer=mapFile.newLayer();
  //debug("NEW LAYER <br>"+oLayer.toSource()+"<br>");
	if(ongletActive=="layers"){
	  currentLayer=null;
	  currentClass=null;
	  currentStyle=null;
	  $('mapfile').innerHTML="";
	  new Insertion.Top('mapfile',mapFile.getHTMLLayers());
	  mapFile.initGUI();
	}
}

function deleteLayer(layer){
  //alert(layer+"  "+currentLayer);
  if(currentLayer!=null){
    //alert(currentLayer+" "+mapFile.MFLayer.length);
    //alert(mapFile.getLayer(currentLayer).toSource());
    var oLayer=mapFile.getLayer(currentLayer);
		oLayer.clearGUI();
		//alert(oLayer.state+"  "+mapFile.state);
		if(oLayer.state=="MODIFY")mapFile.modifyLayer(oLayer);
		mapFile.initState();
  }
  mapFile.deleteLayer(layer);
  $('mapfile').removeChild($('mapfile_layers'));
  new Insertion.Bottom('mapfile',mapFile.getHTMLLayers());
  
  
  
	if(layer==currentLayer){
    currentLayer=null;
    currentClass=null;
    currentStyle=null;
  }else if(currentLayer!=null){
    var tmp=currentLayer;
		if(layer<currentLayer)tmp--;
    currentLayer=null;
    toggleLayer(tmp);
    if(currentClass!=null){
      var tmp=currentClass;
      currentClass=null;
      toggleClass(tmp);
      if(currentStyle!=null){
        var tmp=currentStyle;
        currentStyle=null;
        toggleStyle(currentClass,tmp);
      }
    }
  }
}

function deleteClass(layer,classe){
  alert("TODO");
  
  var oLayer=mapFile.getLayer(layer);
  if(oLayer.state=="MODIFY"){
    mapFile.modifyLayer(oLayer);
    mapFile.initState();
  }
  oLayer=mapFile.deleteClass(layer,classe);
  //alert($("layer_"+layer+"_attr"));
  var oDiv= $("layer_"+layer+"_attr");
  var id="mapfile";
  //oLayer.clearGUI();
  oDiv.parentNode.removeChild(oDiv);
  new Insertion.After(id,oLayer.getHTML());
  oLayer.initGUI();
  currentClass=null;
  currentStyle=null;
  //toggleClass(classe);
}
function newOutputformat(){
  var result=mapFile.newOutputformat();
  var html=result.split("|")[1];
  $("mapfile_attr").removeChild($("mapfile_outputformat_attr"));
  new Insertion.After("outputformat_object",html);
  //On affiche les outputformat (initialise au passage)
  toggleOutputformatObject();
  //mapFile.outputformatObject.initGUI();
  alert(result);
}

function newClass(layer){
    //Si le layer est fourni 
		//c'est un click sur la layer lui meme
	alert("new class "+layer);	
  if(layer!=null){
    if(currentLayer!=null){
      var oLayer=mapFile.getLayer(currentLayer);
      if(oLayer.state=="MODIFY"){
        mapFile.modifyLayer(oLayer);
        mapFile.initState();
      }
      
      if(layer==currentLayer){
        oLayer.clearGUI();
        //oLayer=mapFile.newClass(layer);
        mapFile.newClass(layer);
        var oDiv= $(oLayer.id+"_attr");
        var id=oLayer.id;
        alert(oLayer.id+"  "+oLayer.getHTML());
        oDiv.parentNode.removeChild(oDiv);
        //new Insertion.After(id,oLayer.getHTML());
        new Insertion.Top('mapfile',oLayer.getHTML());
        $('layer_'+currentLayer+'_attr').style.display="block";
				oLayer.initGUI();
        
      }
      //new Insertion.After('layer_'+oLayer.index,oLayer.getHTML());
      
    }else{
      //alert(layer);
      var oLayer=mapFile.newClass(layer);
      toggleLayer(layer);
    }
  }else if(currentLayer!=null){
    alert("TODO");
    /*mapFile.getLayer(currentLayer).clearGUI();
    var oLayer=mapFile.newClass(currentLayer);
  
    var oDiv= $("layer_"+oLayer.index+"_attr");
    var id="layer_"+oLayer.index;
    oDiv.parentNode.removeChild(oDiv);
    new Insertion.After(id,oLayer.getHTML());
    oLayer.initGUI();*/
  }
  currentClass=null;
  currentStyle=null;
}



function newStyle(classe){
  //Si la classe est fourni, alors c'est un click sur l'icone
  //alert(classe+"  "+currentClass);
  if(classe!=null){
    if(currentClass!=null){
      var oLayer=mapFile.getLayer(currentLayer);
      var oClass=oLayer.getClass(classe);
      if(oLayer.state=="MODIFY"){
        mapFile.modifyLayer(oLayer);
        mapFile.initState();
      }
      
      if(classe==currentClass){
        oLayer.clearGUI();
        mapFile.newStyle(currentLayer,classe);
       // var oClass=oLayer.getClass(classe);
        currentClass=null;
        var oDiv= $(oLayer.id+"_attr");
        var id=oLayer.id;
        oDiv.parentNode.removeChild(oDiv);
        //new Insertion.After(id,oLayer.getHTML());
        new Insertion.Top('mapfile',oLayer.getHTML());
        $('layer_'+currentLayer+'_attr').style.display="block";
				oLayer.initGUI();
        toggleClass(classe);
        
      }
      //new Insertion.After('layer_'+oLayer.index,oLayer.getHTML());
      
    }
  }else if(currentClass!=null){
    alert("TODO");
    /*var oLayer=mapFile.newStyle(currentLayer,currentClass);
    oLayer.getClass(currentClass).clearGUI();
    var oClass=oLayer.getClass(currentClass);
    
    var oDiv= $("layer_"+oLayer.index+"_classe_"+oClass.index+"_attr");
    var id="layer_"+oLayer.index+"_classe_"+oClass.index
    oDiv.parentNode.removeChild(oDiv);
    new Insertion.After(id,oClass.getHTML());
    oLayer.initGUI();
    oClass.initGUI();*/
  }
  currentStyle=null;
}

function deleteStyle(layer,classe,style){
  var oLayer=mapFile.getLayer(layer);
  if(oLayer.state=="MODIFY"){
    mapFile.modifyLayer(oLayer);
    mapFile.initState();
  }
  oLayer=mapFile.deleteStyle(layer,classe,style);
  //alert($("layer_"+layer+"_attr"));
  var oDiv= $("layer_"+layer+"_attr");
  var id="mapfile";
  //oLayer.clearGUI();
  oDiv.parentNode.removeChild(oDiv);
  new Insertion.After(id,oLayer.getHTML());
  oLayer.initGUI();
  currentClass=null;
  currentStyle=null;
  toggleClass(classe);
}

function flipOnglet(name,arg){
  
   //alert(ongletActive+"  "+name);
  if(name==ongletActive){
    return;
  }
 
  if(ongletActive!=null){
    $(ongletActive).className="";
    if(ongletActive=="layers"){
      $('mapfile').removeChild($('mapfile_layers'));
      $(ongletActive).innerHTML='<a href="javascript:flipOnglet(\'layers\')">Layers</a>';
    }else if(ongletActive=="parametres"){
      if(mapFile.state=="MODIFY")
		  {
        mapFile.modifyMF();
        mapFile.initState();
      }
       mapFile.clearGUI("parameters");
       $('mapfile_attr').style.display="none";
      //$('mapfile').removeChild($('mapfile_attr')); 
      $('parametres').innerHTML='<a href="javascript:flipOnglet(\'parametres\')">Paramètres</a>';
    }else if(ongletActive=="affiche_layer"){
      oLayer=mapFile.getLayer(currentLayer);
      //alert(currentLayer+"  "+oLayer.state);
      if(oLayer.state=="MODIFY"){
        mapFile.modifyLayer(oLayer);
        mapFile.initState();
      }
      $('layer_'+currentLayer+'_attr').style.display="none";
      $('affiche_layer').innerHTML='<a href="javascript:flipOnglet(\'affiche_layer\')">Layer '+(oLayer.index+1)+' - '+oLayer.name+'</a>';
    }
  }
  if(name=="layers")
	{
    
    //mapFile.clearGUI('parameters');
    
    new Insertion.Top('mapfile',mapFile.getHTMLLayers());
    mapFile.initGUI('layers');
    $('layers').className="activelink";
    $('layers').innerHTML="<a href=\"#\">Layers</a>";
    Nifty("div#onglets a","small transparent top");
		ongletActive='layers';
	}
	else if(name=="parametres")
	{
    if(mapFile.state=="MODIFY"){
			mapFile.modifyLayer(mapFile.getLayer(currentLayer));
      mapFile.initState();
    }
    //new Insertion.Top('mapfile',mapFile.getHTMLParameters());
    $('mapfile_attr').style.display="block";
		mapFile.initGUI('parameters');
    $('parametres').className="activelink";
    $('parametres').innerHTML='<a href=\"#\">Paramètres</a>';
    ongletActive='parametres';
    Nifty("div#onglets a","small transparent top");
    /*currentLayer=null;
    currentClass=null;
    currentStyle=null;*/
  }
  else if(name=="affiche_layer")
	{
	  if(arg) currentLayer=arg.layer;
    //oLayer=mapFile.getLayer(currentLayer);
    oLayer=mapFile.loadLayer(currentLayer)
    if(arg) {
      //alert("on ajoute");
      //$("mapfile").appendChild(oLayer.getHTML());
      new Insertion.Top('mapfile',oLayer.getHTML());
    }
    else  $('layer_'+currentLayer+'_attr').style.display="block";
    //$('layer_'+currentLayer+'_attr').style.display="block";
    oLayer.initGUI();
    $('affiche_layer').className="activelink";
    $('affiche_layer').style.display="block";
    $('affiche_layer').innerHTML='<a href=\"#\">Layer '+(oLayer.index+1)+' - '+oLayer.name+'</a>';
    ongletActive='affiche_layer';
    currentClass=null;
    currentStyle=null;
  }
}
/***********************************************
 * Fonction appelee quand les donnees du mapfile
 * ont ete modifiees
 *********************************************/
function reloadMFData(o){
  //alert("relaodMFData "+o.toSource());
  var attr=o.attr.split("_");
  var tmp=attr.shift();
  //alert(tmp+"  "+o.value);
  var type=(typeof mapFile.get(tmp));
  //alert(type);
  
  switch(type){
    case "object":
      if(reloadObject(mapFile.get(tmp),attr,o.value)){
        mapFile.state="MODIFY";
      }
      break;
    default:
      if(mapFile.get(tmp)!=o.value){
		    mapFile.set(tmp,o.value);
		    mapFile.state="MODIFY";
		  }
      break;
  }
  
}
function reloadObject(o,attr,v){
  var tmp=attr.shift();
  //alert(tmp+"  "+v);
  var type=(typeof o[tmp]);
  //alert(type);
  switch(type){
    case "object":
      if(reloadObject(o[tmp],attr,v)){
        o.state="MODIFY";
        return true;
      }
      break;
    default:
      if(o[tmp]!=v){
        o[tmp]=v;
        o.state="MODIFY";
        return true;
      }
      break;
  }
}

/*****************************************
 * 
 *****************************************/
function expandLayer(index){
  oLayer=mapFile.getLayer(index);
  var id=oLayer.id+"_attr";
  if($(id)){
    $(id).style.display="block";
  }else{
   
    var content=oLayer.getHTML();
    var id=oLayer.id;
    new Insertion.After(id,content);
  }
  oLayer.initGUI();
  oLayer.expand=true;
  currentLayer=index;
}

function hideLayer(oLayer){
  var id=oLayer.id+"_attr";
  $(id).style.display="none";
  /*var oDiv= $(id);
  oLayer.clearGUI();
  oDiv.parentNode.removeChild(oDiv);*/
  oLayer.expand=false;
  currentClass=null;
  currentStyle=null;
  currentLayer=null;
}
function toggleLayer(index){
  //alert(currentLayer+"  "+index+"  "+(currentLayer==index));
  //Si le layer cliqué est celui affiche
  //alert(index+"  "+currentLayer);
  if(index==currentLayer){
    var oLayer=mapFile.getLayer(currentLayer);
    //On verifie qu'il est pas modifie
		if(oLayer.state=="MODIFY"){
      alert(oLayer.state);
      mapFile.modifyLayer(oLayer);
      oLayer.state="LOADED";
      mapFile.initState();
    }
    hideLayer(oLayer);
  }else{
    if(currentLayer!=null){
      var oLayer=mapFile.getLayer(currentLayer);
      //On verifie qu'il est pas modifie
		  if(oLayer.state=="MODIFY"){
        alert(oLayer.state);
        mapFile.modifyLayer(oLayer);
        oLayer.state="LOADED";
        mapFile.initState();
      }
      hideLayer(oLayer);
    }
    expandLayer(index);
  }
  initnifty();
}
/*********************************************
**********************************************/
function expandClass(oLayer,index){
  oClass=oLayer.getClass(index);
  var id=oLayer.id+"_classe_"+index+"_attr";
  $(id).style.display="block";
  oClass.expand=true;
	currentClass=index;
	//oClass.initGUI();
	//oClass.expand=true;
	
  /*var content=oClass.getHTML();
  //alert(content);
  var id=oClass.id;
  new Insertion.After(id,content);
	oClass.initGUI();
	*/
}

function hideClass(oClass){
var id=oClass.id+"_attr";
  $(id).style.display="none";
  /*var id=oClass.id
  var oDiv= $(id+"_attr");
  oClass.clearGUI();
  oDiv.parentNode.removeChild(oDiv);*/
  oClass.expand=false;
  currentClass=null;
  currentStyle=null;
}
function toggleClass(index){
  //alert(index+"  "+mapFile.currentClass);
  //Si la class est celle qui est depliee
  //alert(currentLayer+"  "+index+"  "+mapFile.getLayer(currentLayer).getClass(index).listener.length);
  if(index==currentClass){
    oLayer=mapFile.getLayer(currentLayer);
    oClass=oLayer.getClass(index);
    hideClass(oClass);
  }else{
    var oLayer=mapFile.getLayer(currentLayer);
    if(currentClass!=null){
      oClass=oLayer.getClass(currentClass);
      hideClass(oClass);
    }
    expandClass(oLayer,index);
  }
  initnifty();
}
/******************************************
 *******************************************/
function expandStyle(oLayer,classe,style){
  var id=oLayer.id+"_classe_"+classe+"_style_"+style+"_attr";
  $(id).style.display="block";
	currentStyle=style;
  initnifty();
}

function hideStyle(oStyle){
  var id=oStyle.id
  $(id+"_attr").style.display="none";
  currentStyle=null;
  /*var oDiv= $(id+"_attr");
  oStyle.clearGUI();
	oDiv.parentNode.removeChild(oDiv);
  oStyle.expand=false;
  currentStyle=null;*/
}
/**
 * fonction permettant l'affichage ou 
 * le masquage des styles.
 */
function toggleStyle(classe,style){
  //Si le style cliquer est celui qui est deplie
  //alert(classe+"  "+style+"  "+mapFile.currentStyle);
  if(style==currentStyle){
    //On verifie qu'il est pas modifie
    oLayer=mapFile.getLayer(currentLayer);
    oStyle=oLayer.getClass(classe).getStyle(currentStyle);
    hideStyle(oStyle);
  }else{
    var oLayer=mapFile.getLayer(currentLayer);
    if(currentStyle!=null){
      oStyle=oLayer.getClass(classe).getStyle(currentStyle);
      hideStyle(oStyle);
    }
    expandStyle(oLayer,classe,style);
  }
}
/*********************************************
 *********************************************/
function toggleLabel(id){
  //alert(id);
	var p=id.split("_");
  if(p[0]=="mapfile"){
    if(p[1]=="scalebar"){
      
      var id=id+"_attr";
      if($(id).style.display=="block"){
        $(id).style.display="none";
      }else{
        $(id).style.display="block";
      }
    }
    if(p[1]=="legend"){
      var oLabel=mapFile.legendObject.label;
       var id=id+"_attr";
      if($(id).style.display=="block"){
        $(id).style.display="none";
      }else{
        $(id).style.display="block";
      }
      
    }
  }else if(p[0]=="layer"){
    var oLayer=mapFile.getLayer(parseInt(p[1]));
    if(p[2]=="classe"){
      var id=id+"_attr";
      if($(id).style.display=="block"){
        $(id).style.display="none";
      }else{
        $(id).style.display="block";
      }
    }
    //var oLabel=mapFile.getLayer.label;
  }
  initnifty();
}

/**
 * fonction permettant l'affichage ou 
 * le masquage de l'objet web.
 */
function toggleWebObject(){
  //Si le style cliquer est celui qui est deplie
  //alert(classe+"  "+style+"  "+mapFile.currentStyle);
  
  if(mapFile.webObject.expand){
    mapFile.webObject.expand=false;
    //mapFile.webObject.clearGUI();
    $(mapFile.webObject.id+'_attr').style.display="none";
    
  }else{
    mapFile.webObject.expand=true;
    $(mapFile.webObject.id+'_attr').style.display="block";
    mapFile.webObject.initGUI();
  }
}

/**
 * fonction permettant l'affichage ou 
 * le masquage de l'objet reference.
 */
function toggleReferenceObject(){
  //Si le style cliquer est celui qui est deplie
  //alert(classe+"  "+style+"  "+mapFile.currentStyle);
  
  if(mapFile.referenceObject.expand){
    mapFile.referenceObject.expand=false;
    mapFile.referenceObject.clearGUI();
   $(mapFile.referenceObject.id+'_attr').style.display="none";
  }else{
   // alert("yep ");//+mapFile.referenceObject.getHTML());
    mapFile.referenceObject.expand=true;
    $(mapFile.referenceObject.id+'_attr').style.display="block";
    	Nifty("div.arrondif");
		mapFile.referenceObject.initGUI();


  }

}

/**
 * fonction permettant l'affichage ou 
 * le masquage de l'objet scalebar.
 */
function toggleScalebarObject(){
  //Si le style cliquer est celui qui est deplie
  //alert(classe+"  "+style+"  "+mapFile.currentStyle);
  
  if(mapFile.scalebarObject.expand){
    mapFile.scalebarObject.expand=false;
    mapFile.scalebarObject.clearGUI();
    $(mapFile.scalebarObject.id+'_attr').style.display="none";
  }else{
    mapFile.scalebarObject.expand=true;
   $(mapFile.scalebarObject.id+'_attr').style.display="block";
    mapFile.scalebarObject.initGUI();
  }
}

/**
 * fonction permettant l'affichage ou 
 * le masquage de l'objet legend.
 */
function toggleLegendObject(){
  //Si le style cliquer est celui qui est deplie
  //alert(classe+"  "+style+"  "+mapFile.currentStyle);
  
  if(mapFile.legendObject.expand){
    mapFile.legendObject.expand=false;
    mapFile.legendObject.clearGUI();
    $(mapFile.legendObject.id+'_attr').style.display="none";
  }else{
    mapFile.legendObject.expand=true;
   $(mapFile.legendObject.id+'_attr').style.display="block";
    mapFile.legendObject.initGUI();
  }
}

/**
 * fonction permettant l'affichage ou 
 * le masquage de l'objet outputformat.
 */
function toggleOutputformatObject(){
  //Si le style cliquer est celui qui est deplie
  //alert(classe+"  "+style+"  "+mapFile.currentStyle);
  var oDiv=$('mapfile_outputformat_attr');
  if(oDiv.style.display=="none"){
    mapFile.outputformatObject.expand=true;
    oDiv.style.display="block";
    mapFile.outputformatObject.initGUI();
  }else{
    mapFile.outputformatObject.expand=false;
    oDiv.style.display="none";
  }
}

/**
 * fonction permettant l'affichage d'une aide contextuelle.
 */
function gethelp(obj,prop)
{
  var param='obj='+obj+"&prop="+prop;
	var ajaxRequest=new Ajax.Request("lib/gethelp.php",{method:'post',parameters:param,requestHeaders:["Content-Type", "application/x-www-form-urlencoded; charset=iso-8859-1"], onComplete: complete,onFailure:error,asynchronous:false});

  var tmp2=function(){
    dialog.hide();
  };
	if(ajaxRequest.transport.responseText!="")
	{
    try
		{
      if(dialog)
	  		dialog.hide();
		}catch(e){
	  //nothing
		}

	 try{
     dialog=new DialogWindow("mapwin",300,120,{modal:false,ok:{label:"Ok",click:tmp2},drag:true,close:tmp2});
     dialog.setTitle("Aide contextuelle");

		}catch(e){
	  	alert("Création de la fenêtre (l.217) - "+e.message);
		}
	//	alert(ajaxRequest.transport.responseText);
		var rep=ajaxRequest.transport.responseText ;

		//rep=newrep.replace(/&gt;/,">");
	//	alert(rep);
		var content="<table style=\"width:100%;padding:5px;\"><tr><td style=\"text-align:right;\">";
		content+="<img src=\"images/help.png\" width=\"16px\" height=\"16px\" class=\"png\"></td></tr>";
		content+="<tr><td class=\"corpsblack\"><br>"+rep+"</td></tr></table>";
		dialog.setContent(content);
		dialog.setBackground("#DFDFDF");
		dialog.show();
  }
}
/********************************************
 * Fonction recevant le retour correct du serveur.
 ********************************************/
function complete(r){
  //alert("complete "+r.responseText);
}

/********************************************
 * Fonction recevant l'erreur dans le cas
 * d'une requete sur le serveur.
 ********************************************/
function error(r){
  alert(r.responseText);
}
/*******************************************
 * Ouvre un mapfile isigeo
 *******************************************/
function openMFIsigeo(){
  if(viewer!=null && !viewer.closed){
    viewer.close();
  }
  if(mapFile!=null){
    if(mapFile.state=="MODIFY"  || mapFile.serverState=="MODIFY"){
      saveConfirm(openMFIsigeo);
      return;
    }else{
      
			if(ongletActive!="parametres"){
        flipOnglet('parametres');
      }
      $('affiche_layer').className="activelink";
      $('affiche_layer').style.display="none";
			var nb=$('mapfile').childNodes.length;
      for(var i=0;i<nb;i++){
        $('mapfile').removeChild($('mapfile').childNodes[0]);
      }
      //alert($('mapfile').childNodes.length);
      $('global').style.visibility='hidden';
    }
  }
  mapFile=new MapFile();
  //alert(mapFile);
  var rep=mapFile.launch("getMapFileList",{});
 alert("REP "+rep);
 // debug(rep);
  eval("mapFileList="+rep);
	//alert(rep);
  var content="<div  style=\"margin:10px\">";
  content+="<span class=\"corpsblack\">S&eacute;lectionnez le fichier mapfile<br>et cliquez sur le bouton ok</span><br><br><select id=\"mapFileForm\" name=\"loadmap\" class=\"corpsblack\">";
  
	var i;
	for(i=0;i<mapFileList.length;i++){
    content+="<option value=\""+mapFileList[i].name+"\"> "+mapFileList[i].name+"</option>";
	  	//content+="<option value=\""+mapFileList[i].name+"\"> "+mapFileList[i].name+" - "+mapFileList[i].libelle+"</option>";
	}

	content+="</select>";
	content+="</div>";
	
	var tmp1=function(){
	  //alert($('mapFileForm')+"  "+$('mapFileForm').value);
	  //alert ($('mapFileForm').value );
	  var tmp=$('mapFileForm').value;
	  dialog.hide();
    mapFile.openMFIsigeo(tmp);
    
    new Insertion.Top('mapfile',mapFile.getHTMLParameters());
    $('global').style.visibility='visible';
    mapFile.initGUI("parameters");
    //Nifty("div.arrondi","small transparent");
    //alert("toto");

		initnifty();
  };
  
  var tmp2=function(){
    dialog.hide();
  };
  
  try{
    dialog=new DialogWindow("mapwin",300,120,{modal:true,ok:{label:"Ok",click:tmp1},cancel:{label:"Cancel",click:tmp2},drag:true,close:tmp2});
    dialog.setTitle("Ouvrir un mapfile");

	}catch(e){
	  alert("Création de la fenêtre (l.217) - "+e.message);
	}
	dialog.setContent(content);
	dialog.setBackground("#DFDFDF");
	dialog.show();
}

function newMFIsigeo(){

  if(viewer!=null && !viewer.closed){
    viewer.close();
  }

  if(mapFile!=null){
    if(mapFile.state=="MODIFY"  || mapFile.serverState=="MODIFY"){
      saveConfirm(newMFIsigeo);
      return;
    }else{
      if(ongletActive!="parametres"){
        flipOnglet('parametres');
      }
			var nb=$('mapfile').childNodes.length;
      for(var i=0;i<nb;i++){
        $('mapfile').removeChild($('mapfile').childNodes[0]);
      }
      //alert($('mapfile').childNodes.length);
      $('global').style.visibility='hidden';
    }
  }
  
  mapFile=new MapFile();
  
  var content="<div  style=\"margin:10px\">";
  content+="<span style=\"color:#000000;font-size:12px;font-weight:bold;\">Rentrer le nom du mapfile:&nbsp;&nbsp;</span><input id=\"MFName\" type=\"text\" value=\"\" />";
	content+="</div><br>";
	
	var tmp1=function(){
    var rep=mapFile.newMFIsigeo($('MFName').value);
    //debug("BOUH "+rep);
     new Insertion.Top('mapfile',mapFile.getHTMLParameters());
    $('global').style.visibility='visible';
    mapFile.initGUI("parameters");
    dialog.hide();
  };
  
  var tmp2=function(){
    dialog.hide();
  };
  
  try{
    dialog=new DialogWindow('newwin',500,150,{modal:true,ok:{label:"Ok",click:tmp1},cancel:{label:"Cancel",click:tmp2},drag:true,close:tmp2});
    dialog.setTitle("Nouveau mapfile");
	}catch(e){
	  alert("Création de la fenêtre (l.341) - "+e.message);
	}
	dialog.setContent(content);
	dialog.setBackground("#DFDFDF");
	dialog.show();
}

function reportError(m){
 alert("Error - "+m.responseText);
}


/**********************************
 * Fonction d'enregistrement
 **********************************/
function save(){
  if(mapFile){
    WindowManager.onLoad();
    //alert("load ");
    if(ongletActive!="parametres"){
      //alert("mapfile state "+mapFile.state);
      //alert(mapFile.getLayer(5).getClass(1).getStyle(0).encodeJSON());

      if(mapFile.state=="MODIFY"){
        mapFile.modifyLayer(mapFile.getLayer(currentLayer));
        mapFile.initState();
      }
    }else{
      //alert(mapFile.state);
     if(mapFile.state=="MODIFY"){
        mapFile.modifyMF();
        mapFile.initState();
      }
    }
    //alert("save");
    mapFile.save();
    WindowManager.endLoad();
  }
}

function saveAs(){
  if(mapFile!=null){
    var content="<div  style=\"margin:10px\">";
    content+="<span style=\"color:#000000;font-size:12px;font-weight:bold;\">Rentrer le nom du mapfile:&nbsp;&nbsp;</span><input id=\"MFName\" type=\"text\" value=\"\" />";
  	content+="</div><br>";
  	
  	var tmp1=function(){
      var rep=mapFile.saveAs($('MFName').value);
      dialog.hide();
      //openMFIsigeo();
    };
    
    var tmp2=function(){
      dialog.hide();
    };
    
    try{
      dialog=new DialogWindow('namewin',500,150,{modal:true,ok:{label:"Ok",click:tmp1},cancel:{label:"Cancel",click:tmp2},drag:true,close:tmp2});
      dialog.setTitle("Nouveau mapfile");
  	}catch(e){
  	  alert("Création de la fenêtre (l.341) - "+e.message);
  	}
  	dialog.setContent(content);
  	dialog.setBackground("#6A75A1");
  	dialog.show();
	}
}

function saveConfirm(callBack){
  var content="<div  style=\"margin:10px\">";
  content+="<span style=\"color:#000000;font-size:12px;font-weight:bold;\">Le mapfile courant a été modifié, voulez-vous sauvegarder ces modifications?</span>";
	content+="</div><br>";
	
	var tmp1=function(w){
    //var rep=mapFile.save();
    if(ongletActive!="parametres"){
      flipOnglet("parametres");
    }else{
      mapFile.modifyMF();
      mapFile.initState();
    }
    mapFile.save();
		var nb=$('mapfile').childNodes.length;
    for(var i=0;i<nb;i++){
      $('mapfile').removeChild($('mapfile').childNodes[0]);
    }

    $('global').style.visibility='hidden';
		w.hide();
		callBack();
  };
  
  var tmp2=function(w){
    w.hide();
    callBack();
  };
  
  var close=function(w){
    w.hide();
  };
  
  try{
    dialog=new DialogWindow('savewin',500,150,{modal:true,ok:{label:"Oui",click:tmp1},cancel:{label:"Non",click:tmp2},drag:true,close:close});
    dialog.setTitle("Confirmation");
	}catch(e){
	  alert("Création de la fenêtre (l.341) - "+e.message);
	}
	dialog.setContent(content);
	dialog.setBackground("#6A75A1");
	dialog.show();
}