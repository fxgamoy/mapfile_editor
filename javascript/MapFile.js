/**
 * Ce fichier contient l'ensemble des classes
 * permettant de gerer les donnees d'un mapfile.
 */


WindowManager=Class.create();

WindowManager.onLoad=function(){
  DialogWindow.block();
  var inner=DialogWindow.getInner();
  var left=(inner.width/2);
  var top=(inner.height/2);
  var content="<img id=\"mapfile_imgloading\" style=\"z-index:2000;position:absolute;top:"+top+";left:"+left+";\" src=\"../../images/ajax-loader.gif\"/>"
  new Insertion.Bottom("global",content);
}
WindowManager.endLoad=function(){
  $("global").removeChild($("mapfile_imgloading"));
  DialogWindow.unBlock();
}

MFObject=Class.create();

MFObject.prototype={

   initialize:function(){
     Object.extend(this,new Listener());
   },
   
   _onBlur:function(attr,v){
     //alert("on blur "+attr+"  "+this[attr]+"  "+v);
     if(this[attr]!=v){
       this.set(attr,v);
     }
  },
  
  _set:function(p,v){
    //alert(p+"  "+v);
    this.state="MODIFY";
    this[p]=v;
    //alert(this.listener.length+"  "+p+"  "+arguments.callee.caller+"  "+this.listener[0]);
    this.notifyListener({attribute:this.id});
  },
  
  _get:function(n){
    return this[n];
  },
  
  _initField:function(){
    var self=this;
    for(var p in this){
      var type=(typeof this[p]);
      if(type!="function" && type!="object"){
        if($(this.id+"_"+p)){
           //debug(this.id+"_"+p);
           try{
             //$(this.id+"_"+p).onblur=eval("function(e){self.onBlur(\""+p+"\",this.value);};");
              eval("$("+this.id+"_"+p+").onblur=function(e){self.onBlur(\""+p+"\",this.value);};");
           }catch(e){
             alert("error initField: "+$(this.id+"_"+p)+"  "+p+self.onBlur+"  "+this.value);
           }
        }
      } 
    }
  },
  
  _clearField:function(){
    for(var p in this){
      var type=(typeof this[p]);
      if(type!="function" && type!="object"){
        if($(this.id+"_"+p)){
           $(this.id+"_"+p).onblur=null;
        }
      } 
    }
  },
  
  _encodeJSON:function(){
    var data="";
    var pref="";
    //alert(this.toSource());
    for(var p in this.data){
      tmp="";
      var type=(typeof this.data[p]);
     // alert(p);
      if(type!="function" && p!="collapsed" && p!="expand" && p!="collapsed" && p!="listener"){
        switch(type){
          case "object":
              tmp+=p+":"+this.data[p].encodeJSON();
              break;
            case "number":
              tmp+=p+":"+this.data[p];
              break;
            case "string":
              tmp+=p+":\""+this.data[p]+"\"";
              break;
        }
        if(tmp!=""){
          data+=pref+tmp;
          pref=",";
        }
      }
    }
    data+="}";
    //alert(data);
    return data;
  }
  
};

MapFile=Class.create();

MapFile.prototype={
  
	initialize: function(){
	  var self=this;
	  this.data=null;           //donnee du mapfile 
	  this.MFLayer=null;
		this.serverState="LOADED";
		this.onChange=function(a,v){self.__onChange(a,v);};
		this.changeOccurs=function(e){self._changeOccurs(e);};
		this.state="LOADED";
		Object.extend(this,new Listener());
	},
	
	newMFIsigeo:function(n){
	  var rep=mapFile.launch("newMFIsigeo",{name:n});
	  alert(rep);
	  var d=rep.split("|");
	  this.MFName=d[1];
	  this.html=d[2];
	  this.initMF();
	  this.MFLayer=new Array();
	  
	},
	
	__onChange:function(attr,v){
    alert("MAPFILE CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    this.notifyListener({attribute:"mapfile"});
  },
	
	initState:function(){
	  
	  this.state="LOADED";
	  this.scalebarObject.state="LOADED";
	  this.webObject.state="LOADED";
	  //this.legendObject.state="LOADED";
	  //this.outputformatObject.state="LOADED";
	  this.referenceObject.state="LOADED";
	  
	  this.scalebarObject.expand=false;
	  this.webObject.expand=false;
	  //this.legendObject.expand=false;
	  //this.outputformatObject.expand=false;
	  this.referenceObject.expand=false;
	},
	
	openMFIsigeo:function(n){
    WindowManager.onLoad();
	  this.tmpname="tmp_"+n;
	  this.MFName=n;
	  var result=this.launch("openMFIsigeo",{name:n});
		//debug(result+"<br>---------------------");
	//	alert(result.split("|")[2]);
		if(result.split("|")[0]!=""){
      alert("Erreur lors de l'ouverture du mapfile:"+result.split("|")[0]);
    }
     debug(result.split("|")[0]+"<br>");
    //debug(result.split("|")[2]);
		//eval("this.data="+result.split("|")[2]);
		 // debug("<br><pre>"+result.split("|")[2]+"</pre><br>");
     this.html=result.split("|")[2]
		 this.initMF();
	  debug(result.split("|")[3]);
	  eval("this.MFLayer="+result.split("|")[3]);
	       //alert(this.MFLayer[0].toSource());
		for(var i=0;i<this.MFLayer.length;i++){
      this.MFLayer[i].state="NOT LOADED";
    }
    //new Insertion.Top('mapfile',this.getHTMLParameters());
    $('global').style.visibility='visible';
		//this.initGUI("parameters");
		 WindowManager.endLoad();

	},
	
	save:function(){
	  this.serverState="LOADED";
	  //alert("On enregistre les modif dans le fichier");
	  var result=this.launch("save",{name:this.MFName});
	  debug(result);
	  this.serverState="LOADED";
	  alert("SAVE "+result);
	},
	
	saveAs:function(n){
	  this.serverState="LOADED";
	  var result=this.launch("saveAs",{name:n});
	  this.MFName=result;
	},
	
	get:function(n){
	  //alert("argh "+n);
	  return this.data[n];
	},
	
	set:function(name,value){
	  this.data[name]=value;
	},
	getZlHTML:function(p){
  	var content=p+" : ";
    content+="<select id=\""+this.id+"_"+p+"\"  class=\"data_label\">";
    //alert(this.zl.toSource()+"  "+p);
    //alert(this.zl[p]);
    for(var i=0;i<this.data.zl[p].length;i++){
      if(this.data.zl[p][i]==this.data[p])
        content+="<option SELECTED value=\""+this.data.zl[p][i]+"\">"+this.data.zl[p][i]+"</option>";
      else
        content+="<option value=\""+this.data.zl[p][i]+"\">"+this.data.zl[p][i]+"</option>";
    }
    content+="</select>";
    return content;
  },
	getHTMLParameters:function(){
    return this.html; 
	},
	
	getHTMLLayers:function()
	{
	  var content="<div id=\"mapfile_layers\">";
	  //alert(this.MFLayer.length);
    for(i=0;i<this.MFLayer.length;i++)
		{
      oLayer=this.MFLayer[i];  
      var name=oLayer.name;
      if(name=="" || name=="(null)")
			{
        name="Layer "+(i+1);
    	}
     	content+="<div id=\"drag_b_"+i+"\"></div>";
  	  content+="<div id=\"layer_"+i+"\" class=\"layer\" onmouseover=\"$('layer_"+i+"').className='layerover'\" onmouseout=\"$('layer_"+i+"').className='layer'\">";
      content+="<table class=\"corpsblack\" style=\"width:98%;margin:O;padding:0;\">";
			//content+="<tr><td  style=\"width:400px;\"><a href=\"javascript:toggleLayer("+i+");\">"+name+"</a></td>";
      content+="<tr><td  style=\"width:400px;\"><a href=\"javascript: flipOnglet('affiche_layer',{layer:"+i+"});\">"+name+"</a></td>";
    
			content+="<td style=\"width:24px;text-align:center;\"><img id=\"td_layer_"+i+"_imgloading\" border=0 style=\"visibility:hidden;\" height=15 src=\"../../images/ajax-loader.gif\" /></td>";

      if(i!=0)
			  content+="<td style=\"width:24px;\"><a style=\"display:inline;\" title=\"Deplacer le layer vers le haut\" href=\"javascript:moveLayerUp("+i+");\"> <img border=0 src=\"images/raiselayer.png\" /></a></td>";
			if(i!=this.MFLayer.length-1)	
				content+="<td style=\"width:24px;\"><a style=\"display:inline;\" title=\"Deplacer le layer vers le bas\" href=\"javascript:moveLayerDown("+i+");\"> <img border=0  src=\"images/lowerlayer.png\" /></a></td>";
			content+="<td style=\"width:24px;text-align:center;\"><a style=\"display:inline;\" title=\"Supprimer ce layer\" href=\"javascript:deleteLayer("+i+");\"> <img border=0  src=\"images/deleteClass.png\" /></a></td></tr></table>";
			content+="</div>";
		  if(i<this.MFLayer.length-1)
		 	  content+="<div id=\"drag_a"+i+"\"></div>";
    } //for
    content+="</div>";
    return content;
	},
	
	moveLayerUp:function(l){
	  this.serverState="MODIFY";
	  var result=this.launch("moveLayerUp",{layer:l});
	  //debug("moveLayerUp "+result);
	  this.MFLayer.clear();
	  eval("this.MFLayer="+result.split("|")[1]);
		for(var i=0;i<this.MFLayer.length;i++){
      this.MFLayer[i].state="NOT LOADED";
    }
	},
	moveLayerDown:function(l){
	  this.serverState="MODIFY";
	  var result=this.launch("moveLayerDown",{layer:l});
	  //debug("moveLayerDown "+result);
	  this.MFLayer.clear();
    eval("this.MFLayer="+result.split("|")[1]);
  	for(var i=0;i<this.MFLayer.length;i++){
      this.MFLayer[i].state="NOT LOADED";
    }
	},
	
	newLayer:function(){
	  this.serverState="MODIFY";
	  var result=this.launch("newLayer",{name:this.MFName});
	  var part=result.split("|");
	  var data=eval("this.MFLayer="+part[1]);
	  for(var i=0;i<this.MFLayer.length;i++){
      this.MFLayer[i].state="NOT LOADED";
    }
	  return this.MFLayer[this.MFLayer.length-1];
	},
	
	deleteLayer:function(l){
	  this.serverState="MODIFY";
	  var result=this.launch("deleteLayer",{layer:l});
	  //debug("youhou "+result);
	  this.MFLayer.clear();
	  eval("this.MFLayer="+result.split("|")[1]);
	},
	
	newOutputformat:function(l){
	  // alert(this.currentLayer);
	  this.serverState="MODIFY";
	  var result=this.launch("newOutputformat",{name:this.MFName});
    return result;
	},

	/**
	 *
	 */
	
	newClass:function(l){
	  // alert(this.currentLayer);
	  this.serverState="MODIFY";
	  var result=this.launch("newClass",{name:this.MFName,layer:l});
	  //debug(result);
	  var part=result.split("|");
	  //alert(this.MFLayer[this.currentLayer].classes.length);
	  //eval("this.MFLayer["+l+"]="+result);
	  this.MFLayer[l].html=part[1];
	  this.initLayer(l);
	  //return this.MFLayer[l];
	},
	
	deleteClass:function(l,c){

    this.serverState="MODIFY";
	  var result=this.launch("deleteClass",{layer:l,classe:c});
	  debug(result);
	  var part=result.split("|");
	  //eval("this.MFLayer["+l+"]="+part[0]);
	  this.MFLayer[l].html=part[1];
	  this.initLayer(this.MFLayer[l],l,name);
	  this.MFLayer[l].addListener(this.changeOccurs);
	  return this.MFLayer[l];

	  /*this.serverState="MODIFY";
	  var result=this.launch("deleteClass",{layer:l,classe:c});
	  //debug(result);
	  eval("this.MFLayer["+l+"]="+result);
	  this.initLayer(l);
	  return this.MFLayer[l];  */
	},
	
	newStyle:function(l,c){
	  this.serverState="MODIFY";
	  //alert(this.currentLayer+"  "+this.currentClass);
	  var result=this.launch("newStyle",{name:this.MFName,layer:l,classe:c});
	  //debug("NEWSTYLE<br>"+result);
	  //eval("this.MFLayer["+l+"]="+result);
	  var part=result.split("|");
	  //this.initLayer(l);
	   this.MFLayer[l].html=part[1];
	 //debug('layer_'+this.currentLayer+"_classe_"+this.currentClass+"_attr");
	   
	  //this.MFLayer[this.MFLayer.length]=
	},
	
	deleteStyle:function(l,c,s){
	  this.serverState="MODIFY";
	  var result=this.launch("deleteStyle",{layer:l,classe:c,style:s});
	  debug(result);
	  var part=result.split("|");
	  //eval("this.MFLayer["+l+"]="+part[0]);
	  this.MFLayer[l].html=part[1];
	  this.initLayer(this.MFLayer[l],l,name);
	  this.MFLayer[l].addListener(this.changeOccurs);
	  return this.MFLayer[l];
	},
	
	initMF:function(){
	 // debug("totototot<br>"+this.data.toSource()+"<br>---------------------");
	   this.webObject=new WebObject("mapfile_web");
	   this.referenceObject=new ReferenceObject("mapfile_reference");
	   this.scalebarObject=new ScalebarObject("mapfile_scalebar");
	   this.extent=new Extent("mapfile_extent");
	   this.legendObject=new LegendObject("mapfile_legend");
     this.outputformatObject=new OutputformatObject("mapfile_outputformat");

	},
	
	initLayer:function(l,i,name){

	  //this.MFLayer[i].schema=eval(part[0]);
	  this.MFLayer[i].parent=this;
	  this.MFLayer[i].index=i;
	  this.MFLayer[i].name=name;
    this.MFLayer[i].id="layer_"+i;
    this.MFLayer[i].state="LOADED";
	  this.MFLayer[i].addListener(this.changeOccurs);


	  /*var id="layer_"+i;

	  this.MFLayer[i]=Object.extend(new Layer(id),this.MFLayer[i]);
	  this.MFLayer[i].addListener(this.changeOccurs);
	  this.MFLayer[i].offsite=Object.extend(new Color(id+"_offsite"),this.MFLayer[i].offsite);
	  this.MFLayer[i].index=i;
	  if(this.MFLayer[i].classes){
	    for(j=0;j<this.MFLayer[i].classes.length;j++){
	      //alert(LayerClass.getHTML);
	      var cid=id+"_classe_"+j;
	      
	      this.MFLayer[i].classes[j]=Object.extend(new LayerClass(this.MFLayer[i],cid),this.MFLayer[i].classes[j]);
	      //On initialise le label
				var tid=cid+"_label";
				this.MFLayer[i].classes[j].label=new LabelObject(tid,this.MFLayer[i].classes[j].label);

	      for(k=0;k<this.MFLayer[i].classes[j].styles.length;k++){
	        var sid=cid+"_style_"+k;
	        this.MFLayer[i].classes[j].styles[k]=Object.extend(new ClassStyle(k,this.MFLayer[i].classes[j],sid),this.MFLayer[i].classes[j].styles[k]);
	        var style=this.MFLayer[i].classes[j].styles[k];
					style.color=Object.extend(new Color(sid+"_color"),style.color);
				  style.outlinecolor=Object.extend(new Color(sid+"_outlinecolor"),style.outlinecolor);
				  style.backgroundcolor=Object.extend(new Color(sid+"_backgroundcolor"),style.backgroundcolor);
				
	      }
	    }
	  }    */
	},
	
	loadLayer:function(i){
    //alert("LOAD LAYER");
    var name=this.MFLayer[i].name;
    var result=this.launch("getLayer",{name:this.MFName,index:i});
	  var part=result.split("|");
	  if(part[0]!=""){
      alert("Erreur lors du chargement du layer!\n"+part[0]);
    }
	  //debug("load layer <br><br>"+result);

    //var tmp=document.createElement("div");
    //tmp.innerHTML=part[1];
    //alert(tmp.childNodes[0].id);
    this.MFLayer[i]=new Layer();
	  //this.MFLayer[i].html=tmp.childNodes[0];
	  this.MFLayer[i].html=part[1];
	  this.initLayer(this.MFLayer[i],i,name);
	  //$("td_layer_"+i+"_imgloading").style.visibility="hidden";
	  this.MFLayer[i].addListener(this.changeOccurs);
	  return this.MFLayer[i];
	},
	
	getLayer: function(i){

    /*if(this.MFLayer[i].state=="NOT LOADED"){
      return this.loadLayer(i);
    } */
	  return this.MFLayer[i];
	},
	
	modifyMF:function(){
	  this.serverState="MODIFY";
	  alert("on lance la modif de mapfile");
	  var rep=this.launch("modifyMF",{name:this.MFName,data:this.encodeJSON()});
	  debug("<br>modifyMF<br> "+rep);
	},
	
	modifyLayer:function(oLayer){
	  this.serverState="MODIFY";
	  alert("on lance la modif de layer");
	  //debug("MODIFY LAYER"+oLayer+"  "+oLayer.encodeJSON());
    var rep=this.launch("modifyLayer",{name:this.MFName,layer:oLayer.index,data:oLayer.encodeJSON()});
    //alert(rep);
    var part=rep.split("|");
    if(part[0]!=""){
      alert("Erreur lors de la modification du layer:\n"+part[0]);
    }
    //debug("REPONSE MODIFYLAYER "+part[1]+"<br><br>");
	  oLayer.state="LOADED";
	},
	
	modifyStyle:function(oStyle){
	  this.serverState="MODIFY";
	  //alert("on lance la modif de style");
	  var rep=this.launch("modifyStyle",{name:this.MFName,layer:oStyle.parent.parent.index,classe:oStyle.parent.index,style:oStyle.index,data:oStyle.encodeJSON()});
	  //alert(rep);
	},
	
	modifyClass:function(oClass){
	  this.serverState="MODIFY";
	  //alert("modifyClass\r\n"+oClass.encodeJSON());
	  var rep=this.launch("modifyClass",{name:this.MFName,layer:oClass.parent.index,classe:oClass.index,data:oClass.encodeJSON()});
	  //debug("modifyClass\r\n"+rep);
	},
	
	launch:function(requete,param,data){
    var parametre="requete="+requete;
    var file="lib/mapedit.php";
    for(var p in param){
      parametre+="&"+p+"="+encodeURIComponent(param[p]);
    }
    if(data){
      parametre+="&"+data;
    }
    //alert(parametre);
	  var ajaxRequest=new Ajax.Request(file,{method:'post',requestHeaders:["Content-Type", "application/x-www-form-urlencoded; charset=iso-8859-1"],parameters: parametre,onFailure:reportError,asynchronous:false}); 
    //alert(ajaxRequest.transport.responseText);
    return ajaxRequest.transport.responseText;
  },
  
  encodeJSON: function(){
    var data="{";
    var pref="";
    var e=$("mapfile_form").elements;
    var attr;
    for(var i=0;i<e.length;i++){
       var tmp=e[i].id.split("_");
       attr=tmp[1];
       if(attr=="extent"){
         //Rien on les ajoute a part
       }else{
         var v=parseInt(e[i].value);
         if(isNaN(v)) data+=pref+attr+":\""+e[i].value+"\"";
         else data+=pref+attr+":"+v;
         pref=",";
       }
       
    }
    var racine="mapfile_extent";
    data+=pref+"extent:"+Extent.encodeJSON($(racine+"_minx").value,$(racine+"_miny").value,$(racine+"_maxx").value,$(racine+"_maxy").value);
    data+=pref+"legend:"+this.legendObject.encodeJSON();
    data+=pref+"scalebar:"+this.scalebarObject.encodeJSON();
    data+=pref+"web:"+this.webObject.encodeJSON();
    data+=pref+"reference:"+this.referenceObject.encodeJSON();
    data+=pref+"outputformat:"+this.outputformatObject.encodeJSON();
    
		data+="}";
    debug(data);
    return data;
  },
  
  initGUI:function(arg){
    if(arg=="parameters"){
      var self=this;
    //alert($("layer_"+this.index+"_form").elements.length);
      var e=$("mapfile_form").elements;
      for(var i=0;i<e.length;i++){
       //alert(e[i].id);
       //debug(e[i].id);
        eval("$("+e[i].id+").onchange=function(e){self.onChange(\""+e[i].id+"\",this.value);};");
      }
      this.scalebarObject.addListener(this.changeOccurs);
      this.legendObject.addListener(this.changeOccurs);
      this.webObject.addListener(this.changeOccurs);
      this.referenceObject.addListener(this.changeOccurs);
      this.outputformatObject.addListener(this.changeOccurs);
      /*this.data.imagecolor.initGUI();
      this.data.imagecolor.addListener(this.changeOccurs);
      this.data.extent.initGUI();
      this.data.extent.addListener(this.changeOccurs);*/
    }
	},
	
	clearGUI:function(arg){
    //alert("MF clearGUI");
    if(arg=="parameters"){
      var e=$("mapfile_form").elements;
      for(var i=0;i<e.length;i++){
       //alert(e[i].id);
       debug(e[i].id);
        eval("$("+e[i].id+").onchange=null;");
      }
      /*this.data.imagecolor.clearGUI();
      this.data.imagecolor.removeListener(this.changeOccurs);
      this.scalebarObject.removeListener(this.changeOccurs);
      this.legendObject.removeListener(this.changeOccurs);
      this.referenceObject.removeListener(this.changeOccurs);
      this.webObject.removeListener(this.changeOccurs);
      this.data.extent.clearGUI();
      this.data.extent.removeListener(this.changeOccurs);*/
    }
	},
  
  _changeOccurs:function(e){
    alert("MAPFILE change occurs "+e);
    this.state="MODIFY";
  }
	
};

Layer=Class.create();

Layer.prototype={

	initialize:function(id){
	  var self=this;
    this.id=id;
    this.collapsed=false;
    this.state="LOADED";
    this.changeOccurs=function(e){self._changeOccurs(e);};
    this.initGUI=function(e){self._initGUI(e);};
    this.clearGUI=function(e){self._clearGUI(e);};

    Object.extend(this,new MFObject());
    this.onBlur=function(a,v){return;self._onBlur(a,v);};
    this.onChange=function(a,v){self.__onChange(a,v);};
    this.set=function(p,v){self.__set(p,v);};
    this.clearField=function(){self._clearField();};
    this.initField=function(){self._initField();};
    this.encodeJSON=function(){return self.__encodeJSON();};
    this.get=function(n){return self._get(n);};
    
    //this.data=new Object();
    this.classes=new Array();
    //alert((typeof this.classes));
    //this.data.classes=this.classes;
  },
  
  __set:function(p,v){
    //alert(p+"  "+v);
    if(p=="classitem"){
      if(v!="" && this[p]==""){
         //On ajoute l'icone pour l'ajout de classe
        $(this.id+"_newclass").style.visibility="visible";
      }else if(v=="" && this[p]!=""){
        //On ajoute l'icone pour l'ajout de classe
        $(this.id+"_newclass").style.visibility="hidden";
      }
    }
    this.state="MODIFY";
    this[p]=v;
    //alert(this.listener.length+"  "+p+"  "+arguments.callee.caller+"  "+this.listener[0]);
    this.notifyListener({object:"layer",attribute:p});
  },

  __onChange:function(attr,v){
    //alert("LAYER CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    var part=attr.split("_");
    var test=part[part.length-1];
    if(test=="offsite"){
      //TODO
    }else{
      //var tmp=parseInt(v);
      //if(isNaN(tmp))this.data[part[part.length-1]]=v;
      //else this.data[part[part.length-1]]=tmp;
    }
    //alert(this.data.toSource());
    this.notifyListener({field:new Array("layer",test),value:v,index:this.index});
  },

  getHTML:function(){
  	return this.html;
  },

  loadClass:function(i){
    //alert("LOAD CLASS");
    var result=this.parent.launch("getClass",{name:this.parent.MFName,layer:this.index,classe:i});
    //alert(result);
    this.classes[i]=new LayerClass(this,"layer_"+this.index+"_classe_"+i);
    this.classes[i].html=result;
    this.classes[i].parent=this;
    this.classes[i].index=i;
    this.classes[i].id="layer_"+this.index+"_classe_"+i;
    this.classes[i].addListener(this.changeOccurs);
    return this.classes[i];
  },

  getClass:function(i){
    return this.classes[i];
  },

  initState:function(){
    this.state="LOADED";
    for(var i=0;i<this.classes.length;i++){
      this.classes[i].initState();
    }
  },

  __encodeJSON:function(){
    var data="{";
    var pref="";
    alert(this.id);
    var e=$(this.id+"_form").elements;
    var attr;
    for(var i=0;i<e.length;i++){
       var tmp=e[i].id.split("_");
       attr=tmp[tmp.length-1];
       if(attr=="red" || attr=="green" || attr=="blue" || attr=="hexa"){
         //offsite TODO
       }else{
         var v=parseInt(e[i].value);
         if(isNaN(v)) data+=pref+attr+":\""+e[i].value+"\"";
         else data+=pref+attr+":"+v;
       }
       pref=",";
    }
    data+=pref+"classes:[";
    pref="";
    var i=0;
    while($(this.id+"_classe_"+i)!=null){
      data+=pref+this.classes[i].encodeJSON();
      pref=",";
      i++;
    }
    data+="]";
    data+="}";
    return data;
  },

  _changeOccurs:function(e){
    //alert("LAYER CHANGE OCCURS "+e.toSource()+"  "+this.listener.length+"  "+this.listener[0]);
    //return;
    //alert(" LAYER CHANGE "+e.toSource());
    this.state="MODIFY";
    this.notifyListener({field:new Array("layer",e.attribute),attribute:e.attribute,index:this.index});
  },

  _initGUI:function(){
    //alert("LAYER initGUI");
    var self=this;
    //alert("layer_"+this.index+"_form");//$("layer_"+this.index+"_form").elements.length);
    //alert(this.id);
		var e=$(this.id+"_form").elements;
    for(var i=0;i<e.length;i++){
       //alert(e[i].id);
       eval("$("+e[i].id+").onchange=function(e){alert(e);self.onChange(\""+e[i].id+"\",this.value);};");
    }
    var i=0;
    //alert(this.id+"_class_"+i+"  "+$(this.id+"_classe_"+i));
    while($(this.id+"_classe_"+i)!=null){
      this.classes[i]=new LayerClass(this,this.id+"_classe_"+i);
      this.classes[i].index=i;
      this.classes[i].initGUI();
      this.classes[i].addListener(this.changeOccurs);
      i++;
    }
    /*this.initField();
    this.offsite.initGUI();
    this.offsite.addListener(this.changeOccurs);
    if(this.classes){
      for(var i=0;i<this.classes.length;i++){
        this.classes[i].addListener(this.changeOccurs);
      }
    }*/
  },

  _clearGUI:function(){
    //alert("LAYER clearGUI");
    /*this.clearField();
    this.offsite.clearGUI();
    this.offsite.removeListener(this.changeOccurs);
    if(this.classes){
      for(var i=0;i<this.classes.length;i++){
        this.classes[i].removeListener(this.changeOccurs);
        //this.classes[i].clearGUI();
      }
    } */
  }

};

LayerClass=Class.create();

LayerClass.prototype={
  
	initialize:function(parent,id){
    var self=this;
    this.parent=parent;
    this.state="LOADED";
    this.id=id;
    this.changeOccurs=function(e){self._changeOccurs(e)};
    this.initGUI=function(){self._initGUI()};
    this.clearGUI=function(){self._clearGUI()};
    this.data=new Object();
    Object.extend(this,new MFObject());
    this.onChange=function(a,v){self.__onChange(a,v);};
    this.set=function(p,v){self._set(p,v);};
    this.clearField=function(){self._clearField();};
    this.initField=function(){self._initField();};
    this.encodeJSON=function(){return self.__encodeJSON();};
    
    this.styles=new Array();
    this.label=new LabelObject(this.id+"_label");
  },
  
  initState:function(){
    this.state="LOADED";
    for(var i=0;i<this.styles.length;i++){
      this.styles[i].state="LOADED";
    }
  },
  
  __onChange:function(attr,v){
    //alert("CLASS CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    var part=attr.split("_");
    var test=part[part.length-2];
    if(test=="label"){
      //TODO
    }else{
      var tmp=parseInt(v);
      if(isNaN(tmp))this.data[part[part.length-1]]=v;
      else this.data[part[part.length-1]]=tmp;
    }
    //alert(this.data.toSource());
    this.notifyListener({attribute:"classe",index:this.index});
  },
  
  /*getStyle:function(index){
    return this.styles[index];
  },*/
  
  loadStyle:function(i){
    //alert("LOAD STYLE");
    var result=this.parent.parent.launch("getStyle",{name:this.parent.parent.MFName,layer:this.parent.index,classe:this.index,style:i});
    this.styles[i]=new ClassStyle(i,this,"layer_"+this.parent.index+"_classe_"+this.index+"_style_"+i);
    this.styles[i].html=result;
    this.styles[i].parent=this;
    this.styles[i].index=i;
    this.styles[i].id="layer_"+this.parent.index+"_classe_"+this.index+"_style_"+i;
    this.styles[i].addListener(this.changeOccurs);
    return this.styles[i];
  },
  
  getStyle:function(i){
    //alert(this.parent);
    /*if(!this.styles[i]){
      return this.loadStyle(i);
    }*/
    return this.styles[i];
  },

  
  getHTML:function(){
    return this.html;
  },
  
  __encodeJSON:function(){
    var data="{";
    var pref="";
    var e=$(this.id+"_form").elements;
    var attr;
    for(var i=0;i<e.length;i++){
       var tmp=e[i].id.split("_");
       attr=tmp[tmp.length-1];
       if(tmp[4]=="label"){
         //label TODO
       }else{
         var v=parseInt(e[i].value);
         if(isNaN(v)) data+=pref+attr+":\""+e[i].value+"\"";
         else data+=pref+attr+":"+v;
       }
       pref=",";
    }
    if($(this.label.id+"_form"))data+=pref+"label:"+this.label.encodeJSON();
    data+=pref+"styles:[";
    pref="";
    var i=0;
    while($(this.id+"_style_"+i)!=null){
      data+=pref+this.styles[i].encodeJSON();
      pref=",";
      i++;
    }
    
    data+="]";
    data+="}";
    return data;
  },
  
   _changeOccurs:function(e){
    //alert(this.toSource());
    //alert("LAYER CLASS change OCURRS "+e.toSource()+"  "+this.listener.length);
    //return;
    //alert("CLASS CHANGE "+this.id+"  "+e.toSource());
    
		this.state="MODIFY";
   // this.notifyListener({attribute:"classe",index:this.index});
    if(e.field && e.field[0]=="layer"){
      
      if(e.field[1]=="labelitem"){
        if(e.value!=""){
          $(this.id+"_label").style.display="block";
        }else{
         $(this.id+"_label").style.display="none"; 
        }
      }
    }else{
      this.state="MODIFY";
      this.notifyListener({attribute:"classe",index:this.index});
    }  
  },
  
  _initGUI:function(){
    //alert("LAyerClass initGUI()");
    var self=this;
    //alert(this.id+"_form"+"  "+$(this.id+"_form"));
    //alert($(this.id+"_form").elements.length);
    var e=$(this.id+"_form").elements;
    for(i=0;i<e.length;i++){
       eval("$("+e[i].id+").onchange=function(e){self.onChange(\""+e[i].id+"\",this.value);};");
    }
    
    //On initialise les styles
    var i=0;
    while($(this.id+"_style_"+i)){
      this.styles[i]=new ClassStyle(i,this,this.id+"_style_"+i);
      this.styles[i].index=i;
      this.styles[i].initGUI();
      this.styles[i].addListener(this.changeOccurs);
      i++;
    }
    //alert($(this.parent.id+"_labelitem").value);
    //Si labelitem est renseigne on initialise le label
		if($(this.parent.id+"_labelitem").value!=""){
      if($(this.id+"_label")!=null){
        this.label.initGUI();
        this.label.addListener(this.changeOccurs);
      }
    }
		//Listener sur le layer parent pour le cas du labelitem
    this.parent.addListener(this.changeOccurs); 

  },
  
  _clearGUI:function(){
     //alert("LAyerClass clearGUI()"+this.listener.length);
     this.clearField();
     //this.label.removeListener(this.changeOccurs);
     for(var i=0;i<this.styles.length;i++){
       this.styles[i].removeListener(this.changeOccurs);
     }
    //alert("LAyerClass clearGUI()"+this.listener.length);
  }
  
};

ClassStyle=Class.create();

ClassStyle.prototype={
  
	initialize:function(index,parent,id){
    var self=this;
		this.index=index;
    this.parent=parent;
    this.id=id;
    this.collapsed=false;
    this.changeOccurs=function(e){self._changeOccurs(e);};
    this.data=new Object();
    Object.extend(this,new MFObject());
    this.onChange=function(a,v){self.__onChange(a,v);};
    this.set=function(p,v){self._set(p,v);};
    this.clearField=function(){self._clearField();};
    this.initField=function(){self._initField();};
    this.encodeJSON=function(){return self.__encodeJSON();};
  },
  
  getHTML:function(){
    return this.html;
  },
  
  
  
  __onChange:function(attr,v){
    alert("STYLE CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    var part=attr.split("_");
    var last=part[part.length-1];
    if(last=="blue" || last=="red" || last=="green"){
      if(!this.data[part[part.length-2]]) this.data[part[part.length-2]]=new Color();
      eval("this.data[part[part.length-2]]."+last+"=parseInt(v);");
    }else{
      var tmp=parseInt(v);
      if(isNaN(tmp))this.data[part[part.length-1]]=v;
      else this.data[part[part.length-1]]=tmp;
    }
    //alert(this.data.toSource());
    this.notifyListener({attribute:"style",index:this.index});
  },
  
  _changeOccurs:function(e){
    //alert("class style CHANGE OCCURS "+e.attribute);
    this.state="MODIFY";
    this.notifyListener({attribute:"style",index:this.index});
  },
  
  initGUI:function(){
    //alert("INIT GUI "+this.id+"_form");
    var self=this;
    //alert(this.id+"_form"+"  "+$(this.id+"_form"));
    //alert($(this.id+"_form").elements.length);
    var e=$(this.id+"_form").elements;
    for(i=0;i<e.length;i++){
      var attr=e[i].id.split("_");
      var last=attr[attr.length-1];
       if(last!="red" && last!="green" && last!="blue")
         eval("$("+e[i].id+").onchange=function(e){self.onChange(\""+e[i].id+"\",this.value);};");
    }
    this.color=new Color(this.id+"_color");
    this.outlinecolor=new Color(this.id+"_outlinecolor");
    this.backgroundcolor=new Color(this.id+"_backgroundcolor");
     //alert("INIT GUI STYLE");
     try{
       this.color.initGUI();
       this.color.addListener(this.changeOccurs);
       this.outlinecolor.initGUI();
       this.outlinecolor.addListener(this.changeOccurs);
       this.backgroundcolor.initGUI();
       this.backgroundcolor.addListener(this.changeOccurs);
    }catch(e){
    alert(e);
    }
   
    /*this.initField();
  
    this.color.initGUI();
    this.outlinecolor.initGUI();
    this.backgroundcolor.initGUI();
    this.color.addListener(this.changeOccurs);
    this.outlinecolor.addListener(this.changeOccurs);
    this.backgroundcolor.addListener(this.changeOccurs); */
    
  },
  
  clearGUI:function(){
    //this.clearField();
    //this.color.clearGUI();
    //this.outlinecolor.clearGUI();
    //this.backgroundcolor.clearGUI();
    //this.color.removeListener(this.changeOccurs);
    //this.outlinecolor.removeListener(this.changeOccurs);
    //this.backgroundcolor.removeListener(this.changeOccurs);
  },
  
  __encodeJSON:function(){
   //alert("STYLE "+this.id);
   var data="{";
	 data+="color:"+Color.encodeJSON($(this.id+"_color_red").value,$(this.id+"_color_green").value,$(this.id+"_color_blue").value);     
	 data+=",outlinecolor:"+Color.encodeJSON($(this.id+"_outlinecolor_red").value,$(this.id+"_outlinecolor_green").value,$(this.id+"_outlinecolor_blue").value);     
	 data+=",backgroundcolor:"+Color.encodeJSON($(this.id+"_backgroundcolor_red").value,$(this.id+"_backgroundcolor_green").value,$(this.id+"_backgroundcolor_blue").value);     
	 data+=",size:"+$(this.id+"_size").value;
	 data+=",symbol:\""+$(this.id+"_symbol").value+"\"";
	 data+="}";
	 
	 //TODO maxsize offset
	 return data; 
    
  }

};

LabelObject=Class.create();

LabelObject.prototype={
  
	initialize:function(id){
	  var self=this;
	  this.id=id;
	  this.expand=false;
	  
	  this.initGUI=function(){self._initGUI();};
	  this.clearGUI=function(){self._clearGUI();};
	  this.changeOccurs=function(e){self._changeOccurs(e);};
	  this.onChange=function(a,v){self.__onChange(a,v);};
	  
	  Object.extend(this,new MFObject());
    this.set=function(p,v){self._set(p,v);};
    this.encodeJSON=function(){return self.__encodeJSON();};

	},

  __encodeJSON:function(){
    var data="{";
    var pref="";
    var e=$(this.id+"_form").elements;
    var attr;
    for(var i=0;i<e.length;i++){
      var tmp=e[i].id.split("_");
      attr=tmp[tmp.length-1];
      if(attr=="red" || attr=="green" || attr=="blue" || attr=="hexa"
			|| attr=="font" || attr=="zl" || attr=="px"){
        //on rajoute les couleurs apres
        //hexa est le champ nécessaites  au colorpicker on le garde pas
        //zl et px sont les deux input pour la taille suivant le type
        //Suivant le type on envoie pas la font et on envoi pas la meme size
        
      }else{
        var v=parseInt(e[i].value);
        if(isNaN(v)) data+=pref+attr+":\""+e[i].value+"\"";
        else data+=pref+attr+":"+v;
        pref=",";
      }
     
    }
    
    
    if($(this.id+"_type").value.toUpperCase()=="TRUETYPE"){
      //On envoie la font et la taille en pixel
      data+=",font:\""+$(this.id+"_font").value+"\"";
      data+=",size:\""+$(this.id+"_size_px").value+"\"";
    }else{
      //On envoie taille suivant la zone de liste
      data+=",size:\""+$(this.id+"_size_zl").value+"\"";
    }
    
    data+=",color:"+Color.encodeJSON($(this.id+"_color_red").value,$(this.id+"_color_green").value,$(this.id+"_color_blue").value);     
	  data+=",outlinecolor:"+Color.encodeJSON($(this.id+"_outlinecolor_red").value,$(this.id+"_outlinecolor_green").value,$(this.id+"_outlinecolor_blue").value);     
	  data+=",backgroundcolor:"+Color.encodeJSON($(this.id+"_backgroundcolor_red").value,$(this.id+"_backgroundcolor_green").value,$(this.id+"_backgroundcolor_blue").value);     
	  data+=",backgroundshadowcolor:"+Color.encodeJSON($(this.id+"_backgroundshadowcolor_red").value,$(this.id+"_backgroundshadowcolor_green").value,$(this.id+"_backgroundshadowcolor_blue").value);     
	  data+=",shadowcolor:"+Color.encodeJSON($(this.id+"_shadowcolor_red").value,$(this.id+"_shadowcolor_green").value,$(this.id+"_shadowcolor_blue").value);     
    data+="}";
    return data;
  },
  
  _initGUI:function(){
    //alert("initGUI label");
    var self=this;
    if($(this.id+"_form")){
      var e=$(this.id+"_form").elements;
      for(i=0;i<e.length;i++){
         eval("$("+e[i].id+").onchange=function(e){self.onChange(\""+e[i].id+"\",this.value);};");
      }
    }
    
  
    
    this.color=new Color(this.id+"_color");
    this.outlinecolor=new Color(this.id+"_outlinecolor");
    this.backgroundcolor=new Color(this.id+"_backgroundcolor");
    this.backgroundshadowcolor=new Color(this.id+"_backgroundshadowcolor");
    this.shadowcolor=new Color(this.id+"_shadowcolor");
     //alert("INIT GUI STYLE");
     try{
       this.color.initGUI();
       this.color.addListener(this.changeOccurs);
       this.outlinecolor.initGUI();
       this.outlinecolor.addListener(this.changeOccurs);
       this.backgroundcolor.initGUI();
       this.backgroundcolor.addListener(this.changeOccurs);
       this.backgroundshadowcolor.initGUI();
       this.backgroundshadowcolor.addListener(this.changeOccurs);
       this.shadowcolor.initGUI();
       this.shadowcolor.addListener(this.changeOccurs);
    }catch(e){
      alert("Label (initGUI) - "+e);
    }
    
  },
  
  _clearGUI:function(){
    //TODO clear GUI
  },
  
  _changeOccurs:function(e){
    //alert("LABEL change occurs "+e.toSource());
    this.state="MODIFY";
    this.notifyListener({attribute:"label"});
  },
  
  __onChange:function(attr,v){
    alert("LABEL CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    var part=attr.split("_");
    var last=part[part.length-1];
    if(last=="type"){
      //si le type est truetype, on peut choisir la font
      //et la taille en pixel
      if(v.toLowerCase()=="truetype"){
        $(this.id+"_font").disabled=false;
        $(this.id+"_size_zl").style.display="none";
        $(this.id+"_size_px").style.display="block";
        $(this.id+"_size_px").value=1;
      }else{
        //sinon on desactive le choix de la font et 
        //le choix de la taille se fait par une zone de liste
        $(this.id+"_font").disabled=true;
        $(this.id+"_size_zl").style.display="block";
        $(this.id+"_size_px").style.display="none";
        //$(this.id+"_size_px").value=1;
      }
      
    }
  
    this.notifyListener({attribute:"label"});
  }
  
}

function hex2rgb(hex){
  var r=0,g=0,b=0;
  var t=new Array();
  //debug(hex+" "+hex.length)
  for(var i=0;i<hex.length;i++){
    var v=parseInt(hex.charAt(i));
    if(v.toString()=='NaN'){
      v=hex.charCodeAt(i)-55;
    }
    t[i]=v;
  }
  var R=t[0]*16+t[1], G=t[2]*16+t[3], B=t[4]*16+t[5];
  return {r:R,g:G,b:B};
}




Color=Class.create();

Color.encodeJSON=function(r,v,b){
  var data="{";
  data+="red:"+r;
  data+=",green:"+v;
  data+=",blue:"+b;
  data+="}";
  return data;
}

Color.prototype={
  
	initialize:function(id){
    var self=this;
    this.id=id;
    
    this.initGUI=function(){self._initGUI();};
    this.clearGUI=function(){return;self._clearGUI();};
    this.openColorPicker=function(){self._openColorPicker();};
    //alert(this.id);
		/*this.red=$(this.id+"_red").value;
    this.green=$(this.id+"_green").value;
    this.blue=$(this.id+"_blue").value;*/
      
    Object.extend(this,new MFObject());
    this.onChange=function(a,v){self.__onChange(a,v);};
    this.set=function(p,v){self._set(p,v);};
    
    this.reset=function(a){self._reset(a);};
    this.openEvent=function(a){self._openEvent(a);};
    this.closeEvent=function(a){ self._closeEvent(a);};
    this.updateEvent=function(a){self._updateEvent(a);};
    this.closeColorPicker=function(a){self._closeColorPicker(a);};
    this.manageButton=function(){self._manageButton();};
    this.ok=false;
    this.chexa="";
  },
  
  _initGUI:function(){
    //alert("INIT GUI COLOR");
    try{
      var self=this;
      
			this.red=$(this.id+"_red").value;
      this.green=$(this.id+"_green").value;
      this.blue=$(this.id+"_blue").value;
			this.pcolor=new Control.ColorPicker(this.id+"_hexa",{IMAGE_BASE:"images/colorpicker/",onOpen:this.openEvent,onClose:this.closeEvent,swatch:this.id+"_button",onUpdate:this.updateEvent});
      //Event.observe((this.id+"_img"),'click',this.openColorPicker,true);
      $(this.id+"_hexa").value=YAHOO.util.Color.rgb2hex( this.red,this.green,this.blue);
      this.pcolor.field.value=YAHOO.util.Color.rgb2hex( this.red,this.green,this.blue);
      this.manageButton();
      //Clique sur l'image pour ré-initiliser
      Event.observe(this.id+"_img","click",this.reset,true);
     
		  eval("$('"+this.id+"_red').onchange=function(e){self.onChange(\"red\",this.value);};");
      eval("$('"+this.id+"_green').onchange=function(e){self.onChange(\"green\",this.value);};");
      eval("$('"+this.id+"_blue').onchange=function(e){self.onChange(\"blue\",this.value);};");
    }catch(e){
      alert("color(initGUI) - "+e);
    }
  },
  
  _clearGUI:function(){
    Event.stopObserving(this.id+"_img","click",self.reset,true);
    //Event.stopObserving((this.id+"_img"),'click',this.openColorPicker,true);
  },
  
  _reset:function(e){
    this.set("red",-1);
    this.set("green",-1);
    this.set("blue",-1);
    $(this.id+'_red').value=-1;
    $(this.id+'_green').value=-1;
    $(this.id+'_blue').value=-1;
    this.manageButton();
  },
  
  _manageButton:function(){
    if(this.red!=-1 && this.blue!=-1 && this.green!=-1){
     // alert(this);
        $(this.id+'_button').style.backgroundColor="rgb("+this.red+","+this.green+","+this.blue+")";
        $(this.id+'_button').style.backgroundImage="";
    }else{
      $(this.id+'_button').style.backgroundImage="url(images/deleteClass.png)";
      $(this.id+'_button').style.backgroundRepeat="no-repeat";
      $(this.id+'_button').style.backgroundColor="";
    }
  },
  
  _openEvent:function(a){
    //debug("OPEN "+$(this.id+"_hexa").value);
    this.ok=true;
    $('colorpicker-value-input').style.fontSize=9;
    $('colorpicker-value-input').style.height=15;
    $(this.id+'_button').style.backgroundImage="";
    Event.observe(document,'mousedown',this.closeColorPicker,true);

    //On verifie la position
    if (window.document.documentElement && window.document.documentElement.scrollTop) {
      st= documentElement.scrollTop;
    } else if (window.document.body) {
      st = window.document.body.scrollTop;
    }
    var top=parseInt($('colorpicker').style.top);
    var height=parseInt($('colorpicker').offsetHeight);
    var tmp=top-st;
    //alert((tmp+height)+"  "+innerHeight);
    if(tmp+height>window.innerHeight){
      $('colorpicker').style.top=top-((tmp+height)-window.innerHeight);
    }

  },
  
  _closeEvent:function(a){
    //debug("CLOSE 1 "+$(this.id+"_hexa").value);
    Event.stopObserving(document,'mousedown',this.closeColorPicker,true);
    
    if(this.ok && this.chexa!=""){
      var c=hex2rgb(this.chexa);
      //debug(this.id+"  "+this.chexa+"  "+c.r+"  "+c.g+"  "+c.b);
     
      if(this.red!=c.r){
        $(this.id+'_red').value=c.r;
        this.set("red",c.r);
      }
      if(this.green!=c.green){
        $(this.id+'_green').value=c.g;
        this.set("green",c.g);
      }
      if(this.blue!=c.b){
        $(this.id+'_blue').value=c.b;
        this.set("blue",c.b);
      }
      this.manageButton();
      
    }else if($(this.id+'_button')){//Si le color picker est ouvert
      /*$(this.id+'_button').style.backgroundColor="rgb("+this.red+","+this.green+","+this.blue+")";
      $(this.id+"_hexa").value=YAHOO.util.Color.rgb2hex( this.red,this.green,this.blue);
      this.pcolor.field.value=YAHOO.util.Color.rgb2hex( this.red,this.green,this.blue);
      this.manageButton();*/
    }
    this.ok=false;
    this.chexa="";
    //debug("CLOSE 2 "+$(this.id+"_hexa").value);
  },
  
   _updateEvent:function(a){
      this.chexa=a;
      //debug( "argh "+$('colorpicker-value-input').style.height);
  },
  
  __onChange:function(attr,v){
      this.set(attr,v);
      $(this.id+'_button').style.backgroundColor="rgb("+this.red+","+this.green+","+this.blue+")";
      $(this.id+"_hexa").value=YAHOO.util.Color.rgb2hex( this.red,this.green,this.blue);
      this.pcolor.field.value=YAHOO.util.Color.rgb2hex( this.red,this.green,this.blue);
      this.notifyListener({attr:this.id});
  },
  
  _closeColorPicker:function(event){
    //debug("EVENT");
    var element=Event.element(event,"div");
    var pref=element.id.split("-")[0];
    var prefp=element.parentNode.id.split("-")[0];
    if(pref!="colorpicker" && prefp!="colorpicker" && pref!=this.id+"_button" && prefp!=this.id+"_button"){
      this.ok=false;
      this.pcolor.close();
  	}
  }
}




/******************************************************************/
WebObject=Class.create();

WebObject.prototype={
  initialize:function(id){
    var self=this;
	  this.id=id;
	  this.expand=false;
	  this.state="LOADED";
	  
	  //this.changeOccurs=function(e){self._changeOccurs(e);};
	  //Object.extend(this,data);
	  Object.extend(this,new MFObject());
    this.onChange=function(a,v){self.__onChange(a,v);};
    this.set=function(p,v){self._set(p,v);};
    this.initGUI=function(){self._initGUI();};
    this.clearField=function(){self._clearField();};
    this.initField=function(){self._initField();};
    this.encodeJSON=function(){return self.__encodeJSON();};
  },
  
  __encodeJSON:function(){
    var data="{";
    var pref="";
    var e=$(this.id+"_form").elements;
    var attr;
    for(var i=0;i<e.length;i++){
       var tmp=e[i].id.split("_");
       attr=tmp[tmp.length-1];
       if(attr=="extent"){
         //Rien on les ajoute a part
       }else{
         var v=parseInt(e[i].value);
         if(isNaN(v)) data+=pref+attr+":\""+e[i].value+"\"";
         else data+=pref+attr+":"+v;
         pref=",";
       }
       
    }
		data+="}";
    /*var data="{";
    data+="imagepath:\""+this.imagepath+"\"";
    data+=",imageurl:\""+this.imageurl+"\"";
    data+=",log:\""+this.log+"\"";
    data+=",minscale:"+this.minscale;
    data+=",maxscale:"+this.maxscale;
    data+=",queryformat:\""+this.queryformat+"\"";
  	data+="}";*/
  	//alert(data);
    return data;
  },
  
  
  _initGUI:function(){
  //alert(arguments.callee.caller);
    //this.initField();
    var self=this;
    //alert(this.id);
		var e=$(this.id+"_form").elements;
    for(var i=0;i<e.length;i++){
       //alert(e[i].id);
       eval("$("+e[i].id+").onchange=function(e){alert(e);self.onChange(\""+e[i].id+"\",this.value);};");
    }
  },
  
  __onChange:function(attr,v){
    //alert("MAPFILE CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    this.notifyListener({attribute:"web"});
  },
  
  clearGUI:function(){
    this.clearField();
  }
};


/******************************************************************/
ReferenceObject=Class.create();

ReferenceObject.prototype={
  
	initialize:function(id){
	  var self=this;
	  this.id=id;
	  this.expand=false;
	  this.state="LOADED";
	  
	  this.changeOccurs=function(e){self._changeOccurs(e);};
	 // Object.extend(this,data);
	  Object.extend(this,new MFObject());
    this.onBlur=function(a,v){self._onBlur(a,v);};
    this.set=function(p,v){self._set(p,v);};
    this.clearField=function(){self._clearField();};
    this.onChange=function(a,v){self.__onChange(a,v);};
    this.initField=function(){self._initField();};
    this.encodeJSON=function(){return self.__encodeJSON();};
	  
  },
  
  __encodeJSON:function(){
    //debug("YOUPI "+this.toSource());
    var data="{";
    var pref="";
    var e=$(this.id+"_form").elements;
    var attr;
    for(var i=0;i<e.length;i++){
      if(attr!="extent"){
        var tmp=e[i].id.split("_");
        attr=tmp[tmp.length-1];
        var v=parseInt(e[i].value);
        if(isNaN(v)) data+=pref+attr+":\""+e[i].value+"\"";
        else data+=pref+attr+":"+v;
        pref=",";
      }
    }
    var racine=this.id+"_extent";
    data+=pref+"extent:"+Extent.encodeJSON($(racine+"_minx").value,$(racine+"_miny").value,$(racine+"_maxx").value,$(racine+"_maxy").value);

		data+="}";
  	return data;
  },

  
  initGUI:function(){
    //TODO
    //alert("initGUI reference");
    var self=this;
		var e=$(this.id+"_form").elements;
    for(var i=0;i<e.length;i++){
       //alert(e[i].id);
       eval("$("+e[i].id+").onchange=function(e){self.onChange(\""+e[i].id+"\",this.value);};");
    }
  },
  
  clearGUI:function(){
    //TODO
  },
  
  __onChange:function(attr,v){
    alert("Reference CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    this.notifyListener({attribute:"reference"});
  },
  
  _changeOccurs:function(e){
    this.state="MODIFY";
    this.notifyListener({attribute:"reference"});
  }
  

};
/******************************************************************/
ScalebarObject=Class.create();

ScalebarObject.prototype={
  
	initialize:function(id){
	  var self=this;
	  this.id=id;
	  this.expand=false;
	  this.state="LOADED";
	  
	  this.changeOccurs=function(e){self._changeOccurs(e);};
	 // Object.extend(this,data);
	  Object.extend(this,new MFObject());
	  this.onChange=function(a,v){self.__onChange(a,v);};
    //this.onBlur=function(a,v){self._onBlur(a,v);};
    this.set=function(p,v){self._set(p,v);};
    this.clearField=function(){self._clearField();};
    this.initField=function(){self._initField();};
    this.encodeJSON=function(){return self.__encodeJSON();};
	  this.label=new LabelObject(this.id+"_label");
	 	 
  },
  
  __encodeJSON:function(){
    //debug("YOUPI "+this.toSource());
    var data="{";
    var pref="";
    var e=$(this.id+"_form").elements;
    var attr;
    for(var i=0;i<e.length;i++){
       var tmp=e[i].id.split("_");
       attr=tmp[tmp.length-1];
       //alert(tmp[3]);
       if(tmp[2]=="label" || tmp[2]=="color" 
			 || tmp[2]=="outlinecolor" || tmp[2]=="backgroundcolor"  || tmp[2]=="imagecolor"
			 || tmp[2]=="button" || tmp[2]=="chexa"){
         //label TODO
       }else{
         var v=parseInt(e[i].value);
         if(isNaN(v)) data+=pref+attr+":\""+e[i].value+"\"";
         else data+=pref+attr+":"+v;
       }
       pref=",";
    }
    
		//alert(this.label.toSource());
		//alert(this.label.id+"  "+$(this.label.id+"_form"));
    if($(this.label.id+"_form"))data+=pref+"label:"+this.label.encodeJSON();
    data+=",outlinecolor:"+Color.encodeJSON($(this.id+"_outlinecolor_red").value,$(this.id+"_outlinecolor_green").value,$(this.id+"_outlinecolor_blue").value);     
	  data+=",imagecolor:"+Color.encodeJSON($(this.id+"_imagecolor_red").value,$(this.id+"_imagecolor_green").value,$(this.id+"_imagecolor_blue").value);     
	  data+=",backgroundcolor:"+Color.encodeJSON($(this.id+"_backgroundcolor_red").value,$(this.id+"_backgroundcolor_green").value,$(this.id+"_backgroundcolor_blue").value);     
	  data+=",color:"+Color.encodeJSON($(this.id+"_color_red").value,$(this.id+"_color_green").value,$(this.id+"_color_blue").value);     
	  //alert(Color.encodeJSON($(this.id+"_color_red").value,$(this.id+"_color_green").value,$(this.id+"_color_blue").value));
    data+="}";
  	return data;
  },
  
  _changeOccurs:function(e){
    this.state="MODIFY";
    this.notifyListener({attribute:"scalebar"});
  },
  
  initGUI:function(){
    var self=this;
    //alert(this.id);
		var e=$(this.id+"_form").elements;
    for(var i=0;i<e.length;i++){
       //alert(e[i].id);
       eval("$("+e[i].id+").onchange=function(e){alert(e);self.onChange(\""+e[i].id+"\",this.value);};");
    }
    this.outlinecolor=new Color(this.id+"_outlinecolor");
    this.imagecolor=new Color(this.id+"_imagecolor");
    this.color=new Color(this.id+"_color");
    this.backgroundcolor=new Color(this.id+"_backgroundcolor");
    
    try{
       this.outlinecolor.initGUI();
       this.outlinecolor.addListener(this.changeOccurs);
       this.imagecolor.initGUI();
       this.imagecolor.addListener(this.changeOccurs);
       this.backgroundcolor.initGUI();
       this.backgroundcolor.addListener(this.changeOccurs);
       this.color.initGUI();
       this.color.addListener(this.changeOccurs);
       this.label.initGUI();
       this.label.addListener(this.changeOccurs);
    }catch(e){
      alert(e);
    }
  },
   
  
  clearGUI:function(){
    /*this.clearField();
  
    this.color.clearGUI();
    this.outlinecolor.clearGUI();
    this.backgroundcolor.clearGUI();
    this.imagecolor.clearGUI();
    this.color.removeListener(this.changeOccurs);
    this.outlinecolor.removeListener(this.changeOccurs);
    this.backgroundcolor.removeListener(this.changeOccurs);
    this.imagecolor.removeListener(this.changeOccurs);
    this.label.removeListener(this.changeOccurs);*/
  },
  
  __onChange:function(attr,v){
    //alert("legend CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    this.notifyListener({attribute:"scalebar"});
  }
  
}

/******************************************************************/
LegendObject=Class.create();

LegendObject.prototype={

  initialize:function(id){
    var self=this;
    this.id=id;
    this.state="LOADED";
    this.expand=false;
    this.initGUI=function(){self._initGUI();};
    this.clearGUI=function(){self._clearGUI();};
    this.changeOccurs=function(e){self._changeOccurs(e);};
    Object.extend(this,new Listener());
    
		this.onChange=function(a,v){self.__onChange(a,v);};
    this.set=function(p,v){self._set(p,v);};
    this.clearField=function(){self._clearField();};
    this.initField=function(){self._initField();};
    this.encodeJSON=function(){return self.__encodeJSON();};
    this.label=new LabelObject(this.id+"_label");
  },

  __onChange:function(attr,v){
    //alert("legend CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    this.notifyListener({attribute:"legend"});
  },

  __encodeJSON:function(){
  
    var data="{";
    var pref="";
    var e=$(this.id+"_form").elements;
    var attr;
    for(var i=0;i<e.length;i++){
       var tmp=e[i].id.split("_");
       attr=tmp[tmp.length-1];
       if(tmp[4]=="label"){
         //label TODO
       }else{
         var v=parseInt(e[i].value);
         if(isNaN(v)) data+=pref+attr+":\""+e[i].value+"\"";
         else data+=pref+attr+":"+v;
       }
       pref=",";
    }
    //alert(this.label.toSource());
		//alert(this.label.id+"  "+$(this.label.id+"_form"));
    if($(this.label.id+"_form"))data+=pref+"label:"+this.label.encodeJSON();
    
    //alert(this.label.encodeJSON());
    data+=",outlinecolor:"+Color.encodeJSON($(this.id+"_outlinecolor_red").value,$(this.id+"_outlinecolor_green").value,$(this.id+"_outlinecolor_blue").value);     
	  data+=",imagecolor:"+Color.encodeJSON($(this.id+"_imagecolor_red").value,$(this.id+"_imagecolor_green").value,$(this.id+"_imagecolor_blue").value);     
	 
    data+="}";
  	return data;
  },
  
  _initGUI:function(){
  //alert(arguments.callee.caller);
    //this.initField();
    var self=this;
    //alert(this.id);
		var e=$(this.id+"_form").elements;
    for(var i=0;i<e.length;i++){
       //alert(e[i].id);
       eval("$("+e[i].id+").onchange=function(e){alert(e);self.onChange(\""+e[i].id+"\",this.value);};");
    }
    
    this.outlinecolor=new Color(this.id+"_outlinecolor");
    this.imagecolor=new Color(this.id+"_imagecolor");
    try{
       this.outlinecolor.initGUI();
       this.outlinecolor.addListener(this.changeOccurs);
       this.imagecolor.initGUI();
       this.imagecolor.addListener(this.changeOccurs);
       this.label.initGUI();
       this.label.addListener(this.changeOccurs);
    }catch(e){
      alert(e);
    }
  },
  
  _clearGUI:function(){
    //this.clearField();
    this.outlinecolor.clearGUI();
		this.imagecolor.clearGUI();
		this.outlinecolor.removeListener(this.changeOccurs);
		this.imagecolor.removeListener(this.changeOccurs);
    this.label.removeListener(this.changeOccurs);
  },
  
  _changeOccurs:function(e){
    //alert("Legend change occurs "+e.toSource());
    this.notifyListener({attribute:"legend"});
  }
}


Extent=Class.create();

Extent.encodeJSON=function(minx,miny,maxx,maxy){
  var data="{";
  data+="minx:"+minx;
  data+=",miny:"+miny;
  data+=",maxx:"+maxx;
  data+=",maxy:"+maxy;
  data+="}";
  return data;
}

Extent.prototype={
  initialize:function(id){
	  var self=this;
	  this.id=id;
	  this.state="LOADED";
	  
	  this.changeOccurs=function(e){self._changeOccurs(e);};
	  Object.extend(this,new MFObject());

    this.set=function(p,v){self._set(p,v);};
    this.clearField=function(){self._clearField();};
    this.initField=function(){self._initField();};
  },
  
  _changeOccurs:function(e){
    this.state="MODIFY";
    this.notifyListener({attribute:"extent"});
  },
  
  initGUI:function(){
    this.initField();
  },
  
  clearGUI:function(){
    this.clearField();
  }
}


/******************************************************************/
OutputformatObject=Class.create();

OutputformatObject.prototype={

  initialize:function(id){
    var self=this;
    this.id=id;
    this.state="LOADED";
    this.expand=false;
    this.initGUI=function(){self._initGUI();};
    this.clearGUI=function(){self._clearGUI();};
    Object.extend(this,new Listener());

		this.onChange=function(a,v){self.__onChange(a,v);};
    this.set=function(p,v){self._set(p,v);};
    this.clearField=function(){self._clearField();};
    this.initField=function(){self._initField();};
    this.encodeJSON=function(){return self.__encodeJSON();};
  },

  __onChange:function(attr,v){
    //alert("outputformat CHANGE "+attr+"  "+v);
    this.state="MODIFY";
    this.notifyListener({attribute:"outpuformat"});
  },

  __encodeJSON:function(){

    var data="[";
    var p1="";
    var p2="";
    var e=$(this.id+"_form").elements;
    var attr;

    var current=0
    var obj="{";
    //alert(e.length);
    for(var i=0;i<e.length;i++){
       var tmp=e[i].id.split("_");
       var index=parseInt(tmp[tmp.length-2]);
       //debug(tmp.toSource());
       //debug(index);
       if(index!=current){
         obj+="}";
         current=index;
         data+=p1+obj;
         p1=",";
         p2="";
         obj="{";
       }
       attr=tmp[tmp.length-1];
       var v=parseInt(e[i].value);
       if(isNaN(v)) obj+=p2+attr+":\""+e[i].value+"\"";
       else obj+=p2+attr+":"+v;
       p2=",";
    }
    if(obj.length>1)data+=p1+obj+"}";
    data+="]";
    
    //alert(data);
  	return data;
  },

  _initGUI:function(){
    //debug("####<br>outputformat initGUI");
  //alert(arguments.callee.caller);
    //this.initField();
    var self=this;
    //alert(this.id);
		var e=$(this.id+"_form").elements;
		//debug(e.length);
    for(var i=0;i<e.length;i++){
       //alert(e[i].id);
       //debug(i+"  "+e[i].id+"  "+$(e[i].id).id);
       eval("$(\""+e[i].id+"\").onchange=function(e){self.onChange(\""+e[i].id+"\",this.value);};");
    }
    
    //eval("$(\"mapfile_outputformat_0_name\").onchange=function(e){alert(e);self.onChange(\"mapfile_outputformat_0_name\",this.value);};");

  },

  _clearGUI:function(){
    //TODO
  }
}
