Listener=Class.create();

Listener.prototype={
  
  initialize: function(){
		this.listener=new Array();
	},
	
  addListener:function(f){
    //alert("add "+f+"  "+this.listener.length);
    for(i=0;i<this.listener.length;i++){
		  if(this.listener[i]==f){
 	      return;
 	    }
 		}
 		this.listener[this.listener.length]=f;
 	},
 	
  removeListener:function(f){
    //Debug.display("remove "+arguments.callee.caller+"  "+this.listener.length);
 	  //Debug.display("remove "+arguments.callee.caller);
 	  //Debug.display("remove "+f+this.listener.length+"<br>---");
		var ind=this.listener.length;
 	  var i;
 	  var find=false;
 	  //Debug.display("removeListener "+this.listener.length);
 	  for(i=0;i<this.listener.length;i++){
 	    //Debug.display(i+" "+this.listener[i]+"<br>---");
		  if(this.listener[i]==f){
		    //Debug.display(i+" "+this.listener[i]+"<br>---");
 	      ind=i;
 	      i=this.listener.length;
 	      find=true;
 	    }
 		}
 		for(i=ind;i<this.listener.length-1;i++){
		  this.listener[i]=this.listener[i+1];
		  //Debug.display(i+" "+this.listener[i]+"<br>---");
 	     
 		}
 		if(this.listener.length>0 && find)
   		this.listener.length--;
 		//this.listener[this.listener.length]=f;
 	},
 	
 	removeAllListener:function(f){
 	  //Debug.display("remove all");
 	  //alert(all);
 	  this.listener.length=0;
 	},
 	
 	notifyListener:function(e){
 	  //Debug.display("notifyListener "+this.listener.length);
 	 // Debug.display(arguments.callee.caller);
 	 //Debug.display("NOTIFY "+e.object+"  "+e.cmd+"  "+e.step+"  "+this.listener.length);
		var i;
		var tmp=this.listener.clone();
 		for(i=0;i<tmp.length;i++){
 		  //Debug.display(i+"  "+e.object+"  "+e.cmd+"  "+e.step+"  "+e.type+"<br>"+this.listener[i]+"<br><br>");
			//Debug.display(i+"  "+tmp[i]+"<br>------");
			tmp[i](e);
 		}
 	}
 	
};

//Extension pour la classe array a mettre autre part
Array.prototype.clone=function(t){
  var i=0;
  var tmp=new Array();
  for(i=0;i<this.length;i++){
    tmp[i]=this[i];
  }
  return tmp;
}