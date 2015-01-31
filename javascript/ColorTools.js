/* 
* @Product: IsiGeo
* @Author: GEOMATIKA SARL
* @Date creation:   2014-06-06 10:32:20
* @Last Modified by:   Geomatika
* @Last Modified time: 2014-11-19 12:12:17
* @Contact:   support@geomatika.fr
* @web:  http://www.geomatika.fr/
*/

/**
 * Class: ColorTools
 * Fourni quelque raccourci pour les colorPicker
 * root depend de la ou on l'envoie 
 * ( rien si on se trouve à la racine; ../../ si on est dans un plug)
 *
 * Dependences : Groupe de script 
 * isigeo_jq
 * isigeoCSS						
 */
 function ColorTools(root) {
	ColorTools.start='';
	ColorTools.end='';
	ColorTools.mode="RANGE";
	ColorTools.root=root;
	ColorTools.colorPickerImgBase=ColorTools.root+"extras/jquery_colorpicker/1.0.9/images/ui-colorpicker.png";
	ColorTools.colorPickerImgNone=ColorTools.root+"images/pipette_16.png";
 };





/**
 * Function: hue2RGB
 */
ColorTools.prototype.hue2RGB=function(v1,v2,vH){
	if ( vH < 0 ) {vH += 1;}
  if ( vH > 1 ) {vH -= 1;}
  if ( ( 6 * vH ) < 1 ){ return ( v1 + ( v2 - v1 ) * 6 * vH );}
  if ( ( 2 * vH ) < 1 ) {return ( v2 );}
  if ( ( 3 * vH ) < 2 ) {return ( v1 + ( v2 - v1 ) * ( ( 2 / 3 ) - vH ) * 6 );}
  return ( v1 );
};

/**
 * Function: random
 */
ColorTools.prototype.random=function(nb){
	var color=[];
	var oldH = [];
	var amplitudeH = 360 / nb;
	for(var i=0;i<nb;i++){
		var h = Math.floor(Math.random() * nb);
		var s = Math.floor(Math.random() * 100) + 40;
		var v = Math.floor(Math.random() * 100) + 40;
		while($jq.inArray(h,oldH) != -1){
			h = Math.floor(Math.random() * nb);
		}
		oldH.push(h);
		h = h * amplitudeH;
		var rgb =this.hsvToRgb(h,s,v);
		color[i]=this.rgb2hex(rgb[0],rgb[1],rgb[2]);
	}
	return color;
};
// http://www.javascripter.net/faq/rgbtohex.htm
ColorTools.prototype.rgb2hex=function(r, g, b){
	var toHex=function(n) {
		n = parseInt(n,10);
		if (isNaN(n)){ return "00";}
		n = Math.max(0,Math.min(n,255));
		return "0123456789ABCDEF".charAt((n-n%16)/16)
			+ "0123456789ABCDEF".charAt(n%16);
	}; // Avec un ; c'est mieux Benoit !!
  return "#"+toHex(r)+toHex(g)+toHex(b); // Avec un ; c'est mieux Benoit !!
};
ColorTools.prototype.hex2rgb=function(hex){
	if (hex[0]=="#"){
		hex=hex.substr(1);
	}
	if (hex.length==3) {
		var temp=hex; hex='';
		temp = /^([a-f0-9])([a-f0-9])([a-f0-9])$/i.exec(temp).slice(1);
		for (var i=0;i<3;i++){
			hex+=temp[i]+temp[i];
		}
	}

	var triplets = /^([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i.exec(hex).slice(1);

	return {
		r: parseInt(triplets[0],16),
		g: parseInt(triplets[1],16),
		b: parseInt(triplets[2],16)
	};
};
ColorTools.prototype.gradationColor=function(d,f,nb){
    //On recup les couleurs
    if(d.length>6){d=d.substr(1,6);}
		if(f.length>6){f=f.substr(1,6);}
		var deb=this.hex2rgb(d);
    var fin=this.hex2rgb(f);


    var ratio=1/(nb-1);
    var rd=fin.r-deb.r;
    var gd=fin.g-deb.g;
    var bd=fin.b-deb.b;
    var color=[];
    color[0]=this.rgb2hex(deb.r,deb.g,deb.b);
    for(var i=1;i<nb-1;i++){
      var r=deb.r+rd*(i*ratio);
      var g=deb.g+gd*(i*ratio);
      var b=deb.b+bd*(i*ratio);
      color[i]={r:r,g:g,b:b};
      color[i]=this.rgb2hex(color[i].r,color[i].g,color[i].b);
    }
    color[nb-1]=this.rgb2hex(fin.r,fin.g,fin.b);
		return color;
		
};

/**
 * Function: getColorChooser
 * Renvoie le code html pour la création d'un colorpicker
 *
 * Parameters:
 * idinput - {String} Identifiant de l'input contenant la couleur
 * color - {String} valeur de la couleur en hexa
 * mode = différentes présentation du calendrier
 * onOk = function appelée lorsque l'on clique sur ok
 * Returns:
 * {Object} Contient un champs html,
 * et un champs init avec le javascript qui initialise
 */
ColorTools.prototype.getColorChooser=function(inputId,color,mode,onOk){
	var result={};
	var init="";
	var eventColor='';
	if(typeof onOk != 'undefined' && onOk!=""){
		eventColor+="close:function(event, color) {";
		/*if(color.formatted != ''){  \
			var colorSend = '#'+color.formatted; \
			$jq('#divColorpicker').find('img').attr('src',Proprio.colorPickerImgBase); \
			$jq('#divColorpicker').find('img').attr('title','Couleur des parcelles sur la carte'); \
		} \
		else{ \
			var colorSend = ''; \
			 $jq('#divColorpicker').find('img').attr('src',Proprio.colorPickerImgNone); \
			 $jq('#divColorpicker').find('img').attr('title','Aucune couleur associée'); \
		} \
		 $jq('#divColorpicker').find('img').css({'cursor':'pointer'});";*/
			eventColor+=onOk+";";
			eventColor+="},";
			//console.log(eventColor);
	}



	//console.log(mode);
	if(mode=='full') {
		init+="$jq('#"+inputId+"').colorpicker({ \
			regional:'fr', \
			showOn: 'button', \
			buttonColorize: true, \
			buttonImageOnly :true, \
			buttonImage:ColorTools.colorPickerImgBase, \
			showCancelButton: true,";
			init+=eventColor;
			init+="showNoneButton: true, \
			swatchesWidth  : 200, \
			parts: ['header','swatches','preview','footer','map','bar','rgb','hsv'], \
			part:{map:{ size: 128 },bar:{ size: 128 }}, \
			layout: { \
				swatches:		[0, 0, 1, 1], \
				map:		[1, 0, 1, 1], \
				bar:		[2, 0, 1, 1], \
				rgb:		[0, 1, 1, 1], \
				hsv:		[1, 1, 1, 1], \
				preview:		[2, 1, 1, 1] \
			} \
		});";	
	}
	else if (mode=='jqmobile') {
		init+="$jq('#"+inputId+"').colorpicker({ \
			regional:'fr', \
			showOn: 'button', \
			buttonColorize: true, \
			buttonImageOnly :true, \
			buttonImage:ColorTools.colorPickerImgBase, \
			showCancelButton: true, \
			showNoneButton: false, \
			parts:'draggable' \
		});";
	}
	else {
		init+="$jq('#"+inputId+"').colorpicker({ \
			regional:'fr', \
			showOn: 'button', \
			buttonColorize: true, \
			buttonImageOnly :true, \
			buttonImage:ColorTools.colorPickerImgBase, \
			showCancelButton: true, \
			showNoneButton: true, \
			parts:'draggable' \
		});";
	}

	result.init=init;

	return result;
};

/**
 * Function: setColor
 * Remet à zero un champ colorpicker, et met à jour le bouton.
 *
 * Parameters:
 * idinput - {String} Identifiant de l'input
 * idbutton - {String} Identifiant du bouton
 */
ColorTools.prototype.setColor=function(idinput,idbutton,color){
  try{	
		var selectorOwner=$jq("input#"+idbutton).next("div.colorPicker-picker");
		if(color=="" || color=="-1"|| color=="undefined" || typeof(color)=='undefined'){
			color='-1';
			selectorOwner.css({
				"background-repeat": "no-repeat"
				,"background": "url('plugins/stat/images/nullcolor2.png')"
			});
		}
		else if(color && color.length==6){
			color="#"+color;
			selectorOwner.css({"background":'none'});
			selectorOwner.css("background-color", color);
		}
		selectorOwner.prev("input").val(color).change();

  }catch(e){
    alert("SetCOlor Error - " +idinput+"  "+idbutton+"\n"+e.message);
  }
};

/**
 * HSV to RGB color conversion
 *
 * H runs from 0 to 360 degrees
 * S and V run from 0 to 100
 *
 * Ported from the excellent java algorithm by Eugene Vishnevsky at:
 * http://www.cs.rit.edu/~ncs/color/t_convert.html
 */
 ColorTools.prototype.hsvToRgb=function(h, s, v){
	var r, g, b;
	var i;
	var f, p, q, t;
	 
	// Make sure our arguments stay in-range
	h = Math.max(0, Math.min(360, h));
	s = Math.max(0, Math.min(100, s));
	v = Math.max(0, Math.min(100, v));
	 
	// We accept saturation and value arguments from 0 to 100 because that's
	// how Photoshop represents those values. Internally, however, the
	// saturation and value are calculated from a range of 0 to 1. We make
	// That conversion here.
	s /= 100;
	v /= 100;
	 
	if(s == 0) {
	// Achromatic (grey)
	r = g = b = v;
	return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
	}
	 
	h /= 60; // sector 0 to 5
	i = Math.floor(h);
	f = h - i; // factorial part of h
	p = v * (1 - s);
	q = v * (1 - s * f);
	t = v * (1 - s * (1 - f));
	 
	switch(i) {
	case 0:
	r = v;
	g = t;
	b = p;
	break;
	 
	case 1:
	r = q;
	g = v;
	b = p;
	break;
	 
	case 2:
	r = p;
	g = v;
	b = t;
	break;
	 
	case 3:
	r = p;
	g = q;
	b = v;
	break;
	 
	case 4:
	r = t;
	g = p;
	b = v;
	break;
	 
	default: // case 5:
	r = v;
	g = p;
	b = q;
	}
	 
	return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
}
/**
 * Function: update
 * Met à jour la couleur du bouton si la couleur est != de -1.
 *
 * Parameters:
 * idinput - {String} Identifiant de l'input
 * idbutton - {String} Identifiant du bouton
 */
ColorTools.prototype.update=function(idinput,idbutton){
  if($jq(idinput).value!=-1){
	  $jq(idbutton).style.backgroundImage="";
	}
};
/**
 * Function: getPUPosition
 * Fonction appelé à l'affichage du colorPicker pour indiquer sa position.
 *
 * Parameters:
 * e - {Event}
 */
ColorTools.prototype.getPUPosition=function(e){
  var element=Event.element(e);
  element.style.position="relative";
  var x=element.offsetLeft;
  var y=element.offsetTop;
  var px=Event.pointerX(e);
  var py=Event.pointerY(e);
  var cp=$jq('colorpicker');

  xr=0;
  yr=Event.pointerY(e)-$jq('colorpicker').offsetHeight;
  return [xr,yr];
};