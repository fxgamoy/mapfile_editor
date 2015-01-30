var isIE = navigator.appVersion.match(/MSIE/) == "MSIE";

DialogWindow=Class.create();

DialogWindow.prototype={

	initialize: function(id,width,height,parameters){
	  this.id=id;	
		this.title="<div style=\"width:100%;height:23px;background-image:url(./images/window.png);background-repeat:repeat-x;text-align:center;font-size:11px;font-weight:500;z-index:1020;padding-top:4px;\">&nbsp;</div>";
		this.width=width;
		this.height=height;
		this.background="#D5DFF4";
		this.visible=false;
		if(parent.frames.length>0)
		  this.frames=new Array(parent.frames.length);
		else
		  this.frames=new Array(1);
		//alert(parameters.ok.label);
		this.ok=null;
		this.cancel=null;
		this.drag=false;
		this.modal=false;
		var self=this;
		this.close=function(){self.hide();};
		if(parameters)
		{
		  if(parameters.ok){
		    var l="Ok";
		    if(parameters.ok.label)
		      l=parameters.ok.label;
		    this.ok=new Object({label:l,click:parameters.ok.click});
		  }
		  if(parameters.cancel){
		    var l="Ok";
		    if(parameters.cancel.label)
		      l=parameters.cancel.label;
		    this.cancel=new Object({label:l,click:parameters.cancel.click});
		  }
		  if(parameters.drag){
		    this.drag=true;
		  }
		  if(parameters.close){
		    var self=this;
		    this.close=function(){parameters.close(self);};
		  }
		  
		  if(parameters.modal){
		    this.modal=parameters.modal;
		  }
		}
		this.onScroll=function(e){self._onScroll(e);};
		this.resizeFunction=function(e){self._resizeFunction(e);};
		this.onChange=function(e){self._onChange(e);};
		this.pressKey=function(e){self._pressKey(e);};
		this.unload=function(e){self._unload(e);};
		
	},

	setTitle:function(title){
    cursor="";
    if(this.drag){
      cursor="cursor:move;";
    }
		this.title="<tr id=\"eventDrag\" style=\"width:"+this.width+";height:23px;"+cursor+"\"  ><td colspan=2 style=\"background-image:url(./images/window.png);background-repeat:repeat-x;width:"+this.width+";font-size:11px;text-align:center;font-weight:500;z-index:1020;padding-top:4px;\"  colspan=2>"+title+"</td></tr>";
	},

	setContent:function(content){
	  if(this.visible){
	    $(this.id+"Content").innerHTML=content;
	    return;
	  }
		this.inner=content;
		//alert(window.scrollMaxY+"  "+document.body.offsetHeight);
		//On met au depart la propriete visibility a hidden,
		//quand on fera apparaitre la fenetre, on determinera d'abord
		//l'emplacement du div en fonction de sa hauteur
		//et ensuite on le remet visible.
		this.content="<div id=\""+this.id+"\" style=\"position:absolute;visibility:hidden;width:"+this.width+"px;text-align:left;z-index:1010;background-color:"+this.background+";border-style:none ridge ridge ridge;border-width:0 1 3 1;border-color:#D0D0D0;\">";
		this.content+="<table id=\"dialogTable\" cellspacing=\"0\">";
		this.content+=this.title;
		this.content+="<tr><td id=\""+this.id+"Content\" colspan=3>";
		this.content+=this.inner;
		this.content+="</td></tr>";
		
		this.content+="<tr><td  align=\"center\" style=\"position:relative;text-align:center;padding-bottom:10px;\"  colspan=3>";
		if(this.ok!=null){
		  this.content+="<span style=\"height:50px;text-align:center;margin:5px;\">";
	    this.content+="<input id=\""+this.id+"Ok\" type=\"button\" value=\""+this.ok.label+"\"/ >";
 	    this.content+="</span>";
		}
		if(this.cancel!=null){ 
		  this.content+="<span style=\"height:50px;text-align:center;margin:5px;\">";
	    this.content+="<input id=\""+this.id+"Cancel\" type=\"button\" value=\""+this.cancel.label+"\">";
 	    this.content+="</span>";
		}
		this.content+="</td></tr>";
		this.content+="</table>";
		this.content+="<img id=\""+this.id+"Close\" src=\"images/icq_dnd.png\" style=\"position:absolute;top:3;left:3;\" width=16 height=16 border=0 onmouseover=\"this.style.cursor='pointer';\" class=\"png\">";
		
		this.content+="</div>";
	},
	
	show:function(){
		//document.body.style.margin="0px";
		//document.body.style.textAlign="left";
		if(this.modal){
  		DialogWindow.block();
  		if(parent.frames.length>0){
	      for(var i=0;i<parent.frames.length;i++){
		      Event.observe(parent.frames[i],"beforeunload",this.unload,true);
  		  }
  	  }
		}
		new Insertion.Bottom(document.body,this.content);
		this.oldScroll=DialogWindow.getScroll();
		var offset=this.getOffset();
		$(this.id).style.top=offset.y;
		$(this.id).style.left=offset.x;
		$(this.id).style.visibility="visible";
	
		this.visible=true;
		
		//Event.observe(window,"resize",this.resizeFunction,true);
		Event.observe(window, "scroll", this.onScroll,true);
		Event.observe(this.id+"Close", "click", this.close,true);
		//Event.isLeftClick(e)
	  if(this.drag){
      this.dragDialog=new Draggable(this.id, {starteffect:null,zindex:1010,change:this.onChange} );;
    }
		if(this.cancel!=null){
		   var self=this;
		   this.cancelFunction=function(){self.cancel.click(self);};
		   Event.observe(this.id+"Cancel", "click", this.cancelFunction,true);
		}
		
		if(this.ok!=null){
		  var self=this;
		  this.okFunction=function(){self.ok.click(self);};
		  Event.observe(this.id+"Ok", "click", this.okFunction,true);
		}
		Event.observe(document,"keypress",this.pressKey,true);
	},
	
	hide:function(){
	  //Debug.display("hide "+(parent.frames.length>0));
	  if(this.modal){
	    DialogWindow.unBlock();
	    if(parent.frames.length>0){
	      for(var i=0;i<parent.frames.length;i++){
		      Event.stopObserving(parent.frames[i],"beforeunload",this.unload,true);
  		  }
  	  }
	  }
		document.body.removeChild(document.getElementById(this.id));
		this.visible=false;
	//Event.stopObserving(window,"resize",this.resizeFunction,true);
		Event.stopObserving(window, "scroll", this.onScroll,true);
		Event.stopObserving(document,"keypress",this.pressKey,true); 
	},
	
	setBackground: function(b){
		this.background=b;
		if(this.visible){
			document.getElementById(this.id).style.background=this.background;
		}else{
			this.setContent(this.inner);
		}
	},
	
	_resizeFunction:function(){
	  
	},
	
	_onScroll:function(e){
	  var scroll=DialogWindow.getScroll();
	  var dx=scroll.left-this.oldScroll.left;
	  var dy=scroll.top-this.oldScroll.top;
	  x=dx+parseInt($(this.id).style.left);
	  y=dy+parseInt($(this.id).style.top);
	  $(this.id).style.top=y;
    $(this.id).style.left=x;
    this.oldScroll=scroll;
	  return {x:x,y:y};
	},
	
	_pressKey:function(NSEvent){
    if(NSEvent.keyCode==13 && this.ok!=null){
      this.okFunction(this);
    }else if(NSEvent.keyCode==27){
      this.close(this);
    }
	},
	
	_onChange:function(e){
    var inner=DialogWindow.getInner();
		var scroll=DialogWindow.getScroll(); 
  	if(parseInt($(this.id).style.left)<scroll.left){
      $(this.id).style.left=scroll.left;
    }else if(inner.width+scroll.left<parseInt($(this.id).style.left)+parseInt($(this.id).style.width)){
      $(this.id).style.left=inner.width-parseInt($(this.id).style.width)+scroll.left;
    }
    
    if(parseInt($(this.id).style.top)<scroll.top){
      $(this.id).style.top=scroll.top;
    }else if(inner.height+scroll.top<parseInt($(this.id).style.top)+parseInt($(this.id).offsetHeight)){
      $(this.id).style.top=inner.height-parseInt($(this.id).offsetHeight)+scroll.top;	
    } 
  },
	
	_unload:function(){
	  this.hide();
	},
	
	getOffset:function(){
	  var inner=DialogWindow.getInner();
	  var scroll=DialogWindow.getScroll();
	  //alert(scroll.top+" - "+this.oldScroll.top);
	  var x, y;
	  x=scroll.left+(inner.width-$(this.id).offsetWidth)/2;
	  y=scroll.top+(inner.height-$(this.id).offsetHeight)/2;
	  return {x:x,y:y};
	}

};

/*
 * Fonction statique de dialogWindow
 */

DialogWindow.nbBlock=0;

DialogWindow.getScroll=function(){
  var st,sl;
  if (window.document.documentElement && window.document.documentElement.scrollTop) {
    st= documentElement.scrollTop;
    sl= documentElement.scrollLeft;
  } else if (window.document.body) {
    st = window.document.body.scrollTop;
    sl = window.document.body.scrollLeft;
  }
  return {top:st,left:sl};
}
	
DialogWindow.getInner=function(){
  var w,h;
  if(isIE){
    w=window.document.body.clientWidth;
    h=window.document.body.clientHeight;
  }else{
    w=window.innerWidth
    h=window.innerHeight;
  }
  return {width:w,height:h};
}

DialogWindow.block=function(){
  if(DialogWindow.nbBlock==0){
    if(parent.frames.length>0){
	    for(i=0;i<parent.frames.length;i++){
        var sel=parent.frames[i].document.getElementsByTagName("select");
        for(j=0;j<sel.length;j++){
          sel[j].disabled=true;
        }
  		  var tmp=DialogWindow.createDivBlock();
  		  new Insertion.Bottom(parent.frames[i].document.body,tmp);
  		}
  	}else{   //si il n'y a pas de frame
  	  var sel=document.getElementsByTagName("select");
      for(j=0;j<sel.length;j++){
        sel[j].disabled=true;
      }
  	  var tmp=DialogWindow.createDivBlock();
  	  new Insertion.Bottom(document.body,tmp); 
    }
  }
	DialogWindow.nbBlock++;
}

DialogWindow.createDivBlock=function(){
  var height=document.body.scrollHeight;
  var width=document.body.scrollWidth;
  var tmp="<div  id=\"modalDialog\" style=\"display: block; position: absolute;";
  tmp+="background-color: #666666;filter:alpha(opacity=60);-moz-opacity: 0.6;";
  tmp+="opacity: 0.6;top: 0pt; left: 0pt; z-index: 1000; width: "+width+"; height: "+height+";\" ></div>";
  return tmp;
};

DialogWindow.unBlock=function(){
	if(DialogWindow.nbBlock==1){
	  if(parent.frames.length>0){
		  for(i=0;i<parent.frames.length;i++){
        var sel=parent.frames[i].document.getElementsByTagName("select");
        for(j=0;j<sel.length;j++){
          //alert(sel[j]);
          sel[j].disabled=false;
        }
			  var tmp=parent.frames[i].document.getElementById("modalDialog");
			  parent.frames[i].document.body.removeChild(tmp);
		  }
		}else{
		  var sel=document.getElementsByTagName("select");
      for(j=0;j<sel.length;j++){
        sel[j].disabled=false;
      }
			var tmp=document.getElementById("modalDialog");
			document.body.removeChild(tmp);
		}
	}
	DialogWindow.nbBlock--;
};



	
	