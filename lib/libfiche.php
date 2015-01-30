<?php

  function getMapParametersSheet($map){
	  //print_r($map);
	  $_SESSION["mapobj"]->restorezl("map_obj");
	  $i=0;
	  $content="";
	  $content.="<div id=\"mapfile_attr\">";
	  $content.="<form id=\"mapfile_form\">";
    $left=20;
    
    $content.="<table border=\"0\" style=\"margin:0;padding:0;\">";
		//$content.="<a href=\"javascript:gethelp('map','name')\" border=\"0\"><img src=\"images/help.png\" width=\"12px\" height=\"12px\" class=\"png\" border=\"0\"></a>";
		//$content.="&nbsp;&nbsp;Name : <input size=32 id=\"mapfile_name\" type=\"text\"  class=\"data_label\" value=\"".$map->name."\">&nbsp;&nbsp;&nbsp;";
    $content.=$map->getInputField("mapfile","name",10);
    $content.=$map->getInputField("mapfile","status");
    $content.="</table>";

    $content.="<hr size=1>";
    
    $content.="<div class=\"arrondif\">";
    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
    $content.="<tr>";
    $content.="<td class=\"data_label\"><a href=\"javascript:gethelp('map','width')\" border=\"0\"><img src=\"images/help.png\" width=\"12px\" height=\"12px\" class=\"png\" border=\"0\"></a>";
		$content.="&nbsp;&nbsp;Width ( largeur ) : <input size=12 id=\"mapfile_width\" type=\"text\"  class=\"data_label\" value=\"".$map->width."\"></td>";
    $content.="<td class=\"data_label\"><a href=\"javascript:gethelp('map','height')\" border=\"0\"><img src=\"images/help.png\" width=\"12px\" height=\"12px\" class=\"png\" border=\"0\"></a>";
		$content.="&nbsp;&nbsp;Height ( hauteur ) : <input size=12 id=\"mapfile_height\" type=\"text\"  class=\"data_label\" value=\"".$map->height."\"></td></tr>";
    $content.="</table>";
    
    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
    $content.="<tr><td class=\"data_label\">Shapepath : <input size=24 id=\"mapfile_shapepath\" type=\"text\"  class=\"data_label\" value=\"".$map->shapepath."\"></td>";
    $content.="<td class=\"data_label\">symbolset : <input size=24 id=\"mapfile_symbolsetfilename\" type=\"text\"  class=\"data_label\" value=\"".$map->symbolset."\"></td>";
    $content.="<td class=\"data_label\">fontset : <input size=24 id=\"mapfile_fontsetfilename\" type=\"text\"  class=\"data_label\" value=\"".$map->fontset."\"></td></tr>";
    $content.="</table>";
    
    //$content.="<td class=\"data_label\">imagetype : <input size=12 id=\""+this.id+"_imagetype\" type=\"text\"  class=\"data_label\" value=\""+this.data["imagetype"]+"\"></td>";

    /*$content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
    $content.="<tr><td class=\"data_label\">imagecolor : "+this.data.imagecolor.getHTML();+"</td>";
    $content.="<td class=\"data_label\">maxsize : <input size=12 id=\""+this.id+"_maxsize\" type=\"text\"  class=\"data_label\" value=\""+this.data["maxsize"]+"\"></td></tr>";
    $content.="</table>";*/
    
    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
    $content.="<tr><td class=\"data_label\">transparent : <input size=24 id=\"mapfile_transparent\" type=\"text\"  class=\"data_label\" value=\"".$map->transparent."\"></td>";
    
    $content.=$map->getInputField("mapfile","units");
    $content.=$map->getInputField("mapfile","imagetype");

		//interlace deprecié
		//$content.="<td class=\"data_label\">interlace : <input size=6 id=\"mapfile_interlace\" type=\"text\"  class=\"data_label\" value=\"".$map->interlace."\"></td></tr>";
    $content.="</tr></table>";
    $content.="</div>";
    if($map->extent)$content.=$map->extent->getSheet("mapfile");
    $content.="</form>";
    
    //Pour l'objet web voir comment on fait
		$content.="<div id=\"web_object\" class=\"mapfile_object\" onmouseover=\"$('web_object').className='mapfile_objectover'\" onmouseout=\"$('web_object').className='mapfile_object'\">";
		$content.="<a href=\"javascript:toggleWebObject();\">Web</a>";
		$content.="</div>";
		$content.=getWebSheet($map->web,"mapfile_web");
		if(isset($map->reference)){
		  $content.="<div id=\"reference_object\" class=\"mapfile_object\" onmouseover=\"$('reference_object').className='mapfile_objectover'\" onmouseout=\"$('reference_object').className='mapfile_object'\">";
		  $content.="<a href=\"javascript:toggleReferenceObject();\">Reference</a>";
		  $content.="</div>";
		  $content.=getReferenceSheet($map->reference,"mapfile_reference");
		}
		if(isset($map->scalebar)){
      $content.="<div id=\"scalebar_object\" class=\"mapfile_object\" onmouseover=\"$('scalebar_object').className='mapfile_objectover'\" onmouseout=\"$('scalebar_object').className='mapfile_object'\">";
		  $content.="<a href=\"javascript:toggleScalebarObject();\">Scalebar</a>";
		  $content.="</div>";
		  $content.=getScalebarSheet($map->scalebar,"mapfile_scalebar");
		}
	  $content.="<div id=\"legend_object\" class=\"mapfile_object\" onmouseover=\"$('legend_object').className='mapfile_objectover'\" onmouseout=\"$('legend_object').className='mapfile_object'\">";
		$content.="<a href=\"javascript:toggleLegendObject();\">Legend</a>";
		$content.="</div>";
		$content.=getLegendSheet($map->legend,"mapfile_legend");
		$content.="<div id=\"outputformat_object\" class=\"mapfile_object\" onmouseover=\"$('outputformat_object').className='mapfile_objectover'\" onmouseout=\"$('outputformat_object').className='mapfile_object'\">";
		$content.="<a href=\"javascript:toggleOutputformatObject();\">outputformat</a>";
		$content.="</div>";
	
		$content.=getOutputformatSheet($map->outputformat,"mapfile_outputformat");
	  $content.="<br><br></div>";
    return $content;
	}
	
	function getScalebarSheet($scalebar,$id){
	  $content="<div id=\"".$id."_attr\"  style=\"display:none;\" class=\"mapfile_object_attr\">";
    $content.="<form id=\"".$id."_form\">";
    
    $content.="<table style=\"margin:5px;padding:5px\">";
		$content.="<tr>".$scalebar->getInputField($id,"status");
		$content.=$scalebar->getInputField($id,"units");
    $content.="</table>";
		$content.="<hr size=1>";
    
		$content.="<table style=\"margin:5px;padding:5px\">";
		$content.="<tr><td class=\"data_label\">width: <input size=25   id=\"".$id."_width\" type=\"text\" value=\"".$scalebar->width."\" class=\"data_label\"></td>";
    $content.="<td class=\"data_label\">height : <input size=10 id=\"".$id."_height\" type=\"text\" value=\"".$scalebar->height."\" class=\"data_label\"></td>";
    $content.="<td class=\"data_label\">intervals : <input size=10 id=\"".$id."_intervals\" type=\"text\" value=\"".$scalebar->intervals."\" class=\"data_label\"></td></tr>";
    $content.="</table>";

   
  	//TODO color
    $content.="<table style=\"margin:5px;padding:5px\">";
    $content.="<tr><td class=\"data_label\">color : </td><td>".getHTMLColor($id."_color",$scalebar->color)."</td></tr>";
    $content.="<tr><td class=\"data_label\">outlinecolor : </td><td>".getHTMLColor($id."_outlinecolor",$scalebar->outlinecolor)."</td></tr>";
    $content.="<tr><tr><td class=\"data_label\">backgroundcolor : </td><td>".getHTMLColor($id."_backgroundcolor",$scalebar->backgroundcolor)."</td></tr>";
    $content.="<tr><td class=\"data_label\">imagecolor : </td><td>".getHTMLColor($id."_imagecolor",$scalebar->imagecolor)."</td></tr>";
    $content.="</table>";
  	$content.="</form>";
  	//TODO label
		$content.=$scalebar->label->getToggle($id."_label");
		$content.=$scalebar->label->getSheet($id."_label");
  	$content.="</div>";
  	return $content;
	}
	
	function getReferenceSheet($reference,$id){
	  $left=20;
    //print_r($reference);
    $content="<div id=\"".$id."_attr\"  style=\"display:none;\" class=\"mapfile_object_attr\">";
    $content.="<form id=\"".$id."_form\">";
  	$content.="<table style=\"margin:5px;padding:5px\"><tr><td class=\"data_label\"> image: <input size=25   id=\"".$id."_image\" type=\"text\" value=\"".$reference->image."\"></td>";
    $content.="<td class=\"data_label\">width : <input size=10   id=\"".$id."_width\" type=\"text\" value=\"".$reference->width."\"></td>";
    $content.="<td class=\"data_label\"> height: <input size=10 id=\"".$id."_height\" type=\"text\" value=\"".$reference->height."\"></td></tr>";
    $content.="</table>";
    //extent
     if(isset($reference->extent))$content.=$reference->extent->getSheet($id);

  	//TODO couleur
    /*$content.="<table style=\"margin:5px;padding:5px\">";
		$content.="<tr><td class=\"data_label\">color : </td><td>"+this.color.getHTML()+"</td>";
    $content.="<td class=\"data_label\">outlinecolor : </td><td>"+this.outlinecolor.getHTML()+"</td></tr>";
    $content.="</table>";*/
  	$content.="</form></div>";
  	return $content;        
	}
	
	
	
	function getWebSheet($web,$id){
	  $content="<div id=\"".$id."_attr\"  style=\"display:none;\" class=\"mapfile_object_attr\">";
    $content.="<form id=\"".$id."_form\">";
		$content.="<table style=\"width:95%\"><tr>";
    $content.=$web->getInputField($id,"imagepath");
    $content.=$web->getInputField($id,"imageurl");
    $content.=$web->getInputField($id,"log");
   	$content.="</tr>";
    $content.="</table>";
    $content.="<table style=\"width:95%\"><tr>";
    $content.=$web->getInputField($id,"minscale",15);
    $content.=$web->getInputField($id,"maxscale",15);
		$content.="</tr>";
  	$content.="<tr>";
  	$content.=$web->getInputField($id,"queryformat",10);
  	$content.="</tr>";
    $content.="</table>";
     $content.="</form></div>";
  	return $content;        
	}
	
	function getLegendSheet($legend,$id){
	  $content="<div id=\"".$id."_attr\"  style=\"display:none;\" class=\"mapfile_object_attr\">";
    $content.="<form id=\"".$id."_form\">";
	
	
	  //$content="<div id=\"legend_object_attr\"  class=\"mapfile_object_attr\">";
    $content.="<table style=\"width:95%;margin:0px;pading:0px;\">";
    $content.="<tr><td class=\"data_label\">width:</td><td><input size=10  class=\"data_label\" id=\"".$id."_width\"  type=\"text\" value=\"".$legend->width."\"></td></tr>";
    $content.="<tr><td class=\"data_label\">height:</td><td> <input size=10  class=\"data_label\" id=\"".$id."_height\"  type=\"text\" value=\"".$legend->height."\"></td></tr>";
    $content.="<tr><td class=\"data_label\">keysizex:</td><td> <input size=10  class=\"data_label\" id=\"".$id."_keysizex\"  type=\"text\" value=\"".$legend->keysizex."\"></td>";
    $content.="<td class=\"data_label\"> keysizey:</td><td> <input size=10  class=\"data_label\" id=\"".$id."_keysizey\"  type=\"text\" value=\"".$legend->keysizey."\"></td></tr>";
    $content.="<tr><td class=\"data_label\"> keyspacingx: </td><td><input size=10  class=\"data_label\" id=\"".$id."_keyspacingx\"  type=\"text\" value=\"".$legend->keyspacingx."\"></td>";
    $content.="<td class=\"data_label\"> keyspacingy:</td><td> <input size=10  class=\"data_label\" id=\"".$id."_keyspacingy\"  type=\"text\" value=\"".$legend->keyspacingy."\"></td></tr>";
    //$content.="<tr>"+this.getZlHTML("status")+"</tr>";
    
    //$content.="<tr>"+this.status+"</tr>";
     $_SESSION["mapobj"]->restorezl("legend_obj");
     
    $content.="<tr>".$legend->getInputField($id,"status")."</tr>"; 
    $content.="<tr>".$legend->getInputField($id,"position")."</tr>"; 
		$content.="<tr>".$legend->getInputField($id,"transparent",10)."</tr>"; 
		$content.="<tr>".$legend->getInputField($id,"interlace",10)."</tr>"; 
	  $content.="<tr><td class=\"data_label\"> template: </td><td><input size=10  class=\"data_label\" id=\"".$id."_template\"  type=\"text\" value=\"".$legend->template."\"></td></tr>";
   
  	$content.="<tr><td class=\"data_label\"> outlinecolor:</span></td><td colspan=3 class=\"data_label\">".getHTMLColor($id."_outlinecolor",$legend->outlinecolor)."</td></tr>";
  	$content.="<tr><td class=\"data_label\">imagecolor:</span></td><td colspan=3 class=\"data_label\">".getHTMLColor($id."_imagecolor",$legend->imagecolor)."</td></tr>";
  	$content.="</table>";
  	$content.="</form>";
		$content.=$legend->label->getToggle($id."_label");
		$content.=$legend->label->getSheet($id."_label");
		
  	$content.="</div>";
  	return $content;
	}
	
	function getOutputformatSheet($o,$id){
	  $content="<div id=\"".$id."_attr\" style=\"display:none;\" class=\"mapfile_object_attr\">";
    $content.="<form id=\"".$id."_form\">";
    $content.="<table>";
    $content.="<tr><td align=right colspan=7>";
    $content.="<a title=\"Ajouter un outputformat\" style=\"text-decoration:none;\" href=\"javascript:newOutputformat();\"><img border=0 src=\"images/addOutputformat.png\"/></a>";

    $content.="</td></tr>";
    $content.="<tr>";
		$content.="<td class=\"data_label\"> name</td>";
    $content.="<td class=\"data_label\"> mimetype</td>";
    $content.="<td class=\"data_label\"> driver</td>";
    $content.="<td class=\"data_label\"> extension</td>";
    //$content.="<td class=\"data_label\"> renderer</td>";
    $content.="<td class=\"data_label\"> imagemode</td>";
    $content.="<td class=\"data_label\"> transparent</td>";
	  $content.="</tr>";
	  //echo  "NB ".count($o);
	  for($i=0;$i<count($o);$i++){
      //$content.="<div id=\"".$id."_".$i."_attr\" class=\"mapfile_object_attr\">";
      $content.="<tr>";
		  //$content.="<td class=\"data_label\"><input size=10  id=\"".$id."_".$i."_name\" type=\"text\" value=\"".$o[$i]->name."\"></td>";

      $content.=$o[$i]->getInputField($id."_".$i,"name",10);
      $content.=$o[$i]->getInputField($id."_".$i,"mimetype",10);
      $content.=$o[$i]->getInputField($id."_".$i,"driver",10);
      $content.=$o[$i]->getInputField($id."_".$i,"extension",10);
      $content.=$o[$i]->getInputField($id."_".$i,"imagemode",10);
      $content.=$o[$i]->getInputField($id."_".$i,"transparent",10);
      $content.="</tr>";
		
	  }
	  $content.="</table>";  
	  $content.="</form>";
	  $content.="</div>";
	  return $content;
	}
  
	
	function getHTMLZL($libelle,$value,$id,$ZL){
    $content="<td><span class=\"data_label\">".$libelle." : </span></td>";
    $content.="<td><select id=\"".$id."_".$libelle."\" class=\"data_label\">";
    for($i=0;$i<count($ZL);$i++){
      if($ZL[$i]==$value)
        $content.="<option selected=\"true\" value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
      else
        $content.="<option value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
    }
    $content.="</select></td>";
    //return "";
    return $content;
  }

	//Fonction pour les fiches

	function getLayerSheet($layer,$index){
    $_SESSION["mapobj"]->restorezl("layer_obj");
    $id="layer_".$index;
    $content="<div id=\"layer_".$index."_attr\" class=\"layer_attr\">";
    $content.="<form id=\"layer_".$index."_form\">";

		$content.="<table style=\"margin:0;padding:0;\">";
    if(($prop=$_SESSION["mapobj"]->hasZL("status"))){
      $ZL=$_SESSION["mapobj"]->getZL($prop);
      //print_r($ZL);
      //echo $layer->status."  ".count($ZL);
      $content.="<tr>".getHTMLZL("status",$layer->status,$id,$ZL)."</tr>";

    }else
      $content.="<tr><td><span class=\"data_label\">status</span>:</td><td> <input  id=\"".$id."_status\" type=\"text\" value=\"".$layer->status."\"></td></tr>";

    $content.="</table>";
    $content.="<table style=\"width:100%;margin:0;padding:0;\"><tr><td>";
    $content.="<hr size=\"1\"/>";
		$content.="</td></tr></table>";
		$content.="<div class=\"arrondi\">";
		$content.="<table style=\"width:98%;margin:0;padding:0;\">";
		if(($prop=$_SESSION["mapobj"]->hasZL("connectiontype"))){
      $ZL=$_SESSION["mapobj"]->getZL($prop);
      $content.="<tr>".getHTMLZL("connectiontype",$layer->connectiontype,$id,$ZL)."</tr>";
    }else
      $content.="<tr><td><span class=\"data_label\">connectiontype</span>:</td><td> <input  id=\"".$id."_connectiontype\" type=\"text\" value=\"".$layer->connectiontype."\"></td></tr>";

    if(($prop=$_SESSION["mapobj"]->hasZL("type"))){
      $ZL=$_SESSION["mapobj"]->getZL($prop);
      $content.="<tr>".getHTMLZL("type",$layer->type,$id,$ZL)."</tr>";
    }else
      $content.="<tr><td><span class=\"data_label\">type</span>:</td><td> <input  id=\"".$id."_type\" type=\"text\" value=\"".$layer->type."\"></td></tr>";

		$content.="</table>";

    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
  	$content.="<tr><td><span class=\"data_label\">connection</span> : </td>";
		$content.="<td><input size=\"80\"  id=\"".$id."_connection\" type=\"text\" value=\"".$layer->connection."\" /></td></tr>";
    $content.="<tr><td><span class=\"data_label\">data</span> : </td><td> <input size=\"80\" id=\"".$id."_data\"  class=\"data_label\" type=\"text\" value=\"".$layer->data."\" /></td></tr>";
    $content.="</table>";

    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
    $content.="<tr><td><span class=\"data_label\">groupe : </span></td><td> <input size=\"30\" id=\"".$id."_group\" type=\"text\"  class=\"data_label\" value=\"".$layer->group."\" /></td>";
    $content.="<td><span class=\"data_label\">name : </span></td><td> <input size=\"30\" id=\"".$id."_name\" type=\"text\"  class=\"data_label\" value=\"".$layer->name."\" /></td></tr>";
    
    //$prop=$_SESSION["mapobj"]->getProperties("layer_obj","template");
    //print_r($prop);
		$content.="<tr>";
		$content.=$layer->getInputField($id,"template");
		$content.=$layer->getInputField($id,"requires");
	  $content.="</tr>";
		$content.="</table><table id=\"table_".$index."2_5\" border=\"0\" style=\"width:98%;margin:0;padding:0%;\">";
    $content.=$layer->getInputField($id,"classitem");
		$content.=$layer->getInputField($id,"labelitem");
		$content.=$layer->getInputField($id,"filteritem");
		$content.="</tr></table>";

    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
    $content.="<tr><td class=\"data_label\">Minscale : <br/><input size=\"8\" id=\"".$id."_minscale\" type=\"text\"  class=\"data_label\" value=\"".$layer->minscale."\" /></td>";
    $content.="<td class=\"data_label\">Maxscale : <br/><input size=\"8\" id=\"".$id."_maxscale\" type=\"text\"  class=\"data_label\" value=\"".$layer->maxscale."\" /></td>";
    $content.="<td class=\"data_label\">Labelminscale :<br/><input size=\"8\" id=\"".$id."_labelminscale\" type=\"text\"  class=\"data_label\" value=\"".$layer->labelminscale."\" /></td>";
    $content.="<td class=\"data_label\">Labelmaxscale : <br/><input size=\"8\" id=\"".$id."_labelmaxscale\" type=\"text\"  class=\"data_label\" value=\"".$layer->labelmaxscale."\" /></td>";
    $content.=$layer->getInputField($id,"symbolscale",5);
		
		$content.="</table>";
    $content.="</div>";
		$content.="<table id=\"table_".$index."4\" border=\"0\" style=\"width:98%;margin:0;padding:0;\"><tr>";
    $content.=$layer->getInputField($id,"transparency");
		$content.=$layer->getInputField($id,"styleitem");
		
		$content.="</tr></table>";

    $content.="<table id=\"table_".$index."5\" border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
    
		$level=$_SESSION["mapobj"]->getLevel("layer_obj","offsite");
		if(!$level || $level=="1" || $level==LEVEL)
		  $content.="<tr><td  class=\"data_label\">offsite : ".getHTMLColor($id."_offsite",$layer->offsite)."</td></tr>";

    $content.="</td></tr></table>";

		$content.="<table id=\"table_".$index."5\" border=\"0\" style=\"width:98%;margin:0;padding:0;\"><tr>";
    $content.=$layer->getInputField($id,"tolerance",10);
    $content.=$layer->getInputField($id,"toleranceunits",10);
    $content.="</tr></table>";
    $content.="<table id=\"table_".$index."5\" border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
    $content.="<tr>";
    
    $_SESSION["mapobj"]->restorezl("layer_obj");
    if(($prop=$_SESSION["mapobj"]->hasZL("sizeunits"))){
      $ZL=$_SESSION["mapobj"]->getZL($prop);
      $content.=getHTMLZL("sizeunits",$layer->sizeunits,$id,$ZL);
    }else
      $content.="<td><span class=\"data_label\">Size Units</span>:</td><td> <input  id=\"".$id."_sizeunits\" type=\"text\" value=\"".$layer->sizeunits."\"></td>";

	  $content.=$layer->getInputField($id,"maxfeatures",10);
    $content.=$layer->getInputField($id,"transform",10);
		$content.="</tr></table>";
   	$content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\"><tr>";
    $content.=$layer->getInputField($id,"labelcache",10);
    $content.=$layer->getInputField($id,"postlabelcache",10);
		$content.="</tr><tr>";
		$content.=$layer->getInputField($id,"labelsizeitem",10);
    $content.=$layer->getInputField($id,"labelangleitem",10);
    $content.=$layer->getInputField($id,"labelrequires",10);
		$content.="</tr></table>";

		$content.="<table border=\"0\" style=\"margin:0;\"><tr>";
		$content.=$layer->getInputField($id,"num_processing",10);
		
		$content.="</tr></table>";
	  $content.="</form>";
		$content.="<hr size=1/>";
    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
    $content.="<tr><td style=\"width:120px;text-align:right;\"><span id=\"".$id."_newclass\" style=\"visibility:".$visibility."\">";
		$content.="&nbsp;&nbsp;<a style=\"display:inline;\" href=\"javascript:newClass(".$index.")\" title=\"Ajoutez une classe\"><img border=\"0\" src=\"images/addClass.png\" /></a></span></td>";
    $content.="</tr>";
		$content.="<tr><td>";
		//echo " SHEET NUMCLASS ".($layer->class)."<br>";
	//	echo " SHEET ".($layer->name)."<br>";
    if($layer->class && count($layer->class)>0){
      $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\"><tr><td class=\"data_label\"><b>Classes</b></td></tr>";
      for($i=0;$i<count($layer->class);$i++){
        $content.="<tr><td>";
      	$content.=getHTMLClassToggle($layer,$i,$id,$index);
      	$class=$layer->getClass($i);
      	$content.=getClassSheet($layer,$index,$layer->class[$i],$i);
      	$content.="</td></tr>";
      }
      $content.="</table>";

    }
		$content.="</td></tr></table>";

  	$content.="</div>";
  	return $content;
  }

  function getHTMLClassToggle($layer,$i,$id,$index){
    $name=$layer->class[$i]->name;
  	if($name==""){
      $name="Class ".($i+1);
    }
		$content="<div id=\"layer_".$index."_classe_".$i."\" class=\"layer_classe\"  onmouseover=\"$('layer_".$index."_classe_".$i."').className='layer_classe_over'\" onmouseout=\"$('layer_".$index."_classe_".$i."').className='layer_classe'\">";
    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
  	$content.="<tr><td><a  href=\"javascript:toggleClass(".$i.");\">".$name."</a>";
    $content.="</td><td style=\"text-align:right;\"><a style=\"display:inline;\" href=\"javascript:deleteClass(".$index.",".$i.");\"><img border=0 src=\"images/deleteClass.png\" />";
		//content+="&nbsp;&nbsp;<a style=\"display:inline;\" href=\"javascript:newStyle("+i+")\"><img border=0 src=\"images/addStyle.png\" /></a></td></tr></table>";
    $content.="</tr></table>";
    $content.="</div>";
		//content+="</td></tr>";
    return $content;
  }
  
  function getHTMLStyleToggle($layer,$class,$oStyle,$index){
    $id="layer_".$layer."_classe_".$class."_style_".$index;
    $content="<div id=\"".$id."\" class=\"layer_classe_style\" onmouseover=\"$('".$id."').className='layer_classe_style_over'\" onmouseout=\"$('".$id."').className='layer_classe_style'\">";
    $content.="<table width=100%>";
    $content.="<tr id=\"tr_".$layer."_".$class."_".$index."\" ><td width=99%><a style=\"display:block;\" href=\"javascript:toggleStyle(".$class.",".$index.")\">Style ".($index+1)."</a></td>";
    $content.="<td><a href=\"javascript:deleteStyle(".$layer.",".$class.",".$index.");\"><img border=0 src=\"images/deleteClass.png\"></a></td></tr>";
	  $content.="</table></div>";
    return $content;
  }
  
  function getClassSheet($oLayer,$layer,$class,$index){
     $id="layer_".$layer."_classe_".$index;
     $_SESSION["mapobj"]->restorezl("class_obj");

    $content="<div id=\"".$id."_attr\" style=\"display:none;\" class=\"layer_classe_attr\">";
    $content.="<form id=\"".$id."_form\">";
    $content.="<table border=\"0\" style=\"margin:0;padding:0;\">";
  	//$content.="<tr><td class=\"data_label\">status : </td>";
		//$content.="<td><input size=4  id=\"".$id."_status\" type=\"text\"  class=\"data_label\" value=\"".$class->status."\"></td></tr>";
		$content.=$class->getInputField($id,"status");
		
		$content.="</table>";
		
		$content.= "<hr size=\"1\"/>";
    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
  	$content.="<tr><td class=\"data_label\">name : </td>";
		$content.="<td><input size=32  id=\"".$id."_name\" type=\"text\"  class=\"data_label\" value=\"".$class->name."\"></td>";
    $content.="<td class=\"data_label\">title : </td><td> <input size=32 id=\"".$id."_title\"  class=\"data_label\" type=\"text\" value=\"".$class->title."\"></td></tr>";
    $content.="</table>";
    
    $content.="<table border=\"0\" style=\"width:98%;margin:0;padding:0;\">";
  	$content.="<tr><td class=\"data_label\">Minscale : </td>";
		$content.="<td><input size=32  id=\"".$id."_minscale\"  class=\"data_label\" type=\"text\" value=\"".$class->minscale."\"></td>";
    $content.="<td class=\"data_label\">Maxscale : </td><td> <input size=32 id=\"".$id."_maxscale\"  class=\"data_label\" type=\"text\" value=\"".$class->maxscale."\"></td></tr>";
    $content.="</table>";

    $content.="<table border=\"0\" style=\"margin:0;padding:0;\">";
  	$content.="<tr><td class=\"data_label\">Expression : </td>";
		$content.="<td><input size=64  id=\"".$id."_expression\" type=\"text\"  class=\"data_label\" value=\"".$class->expression."\"></td></tr>";
		$content.="</table>";
	  $content.="</form>";
    $content.= "<hr size=\"1\"/>";
    //alert($content);
    //TODO label
    //this.labelDisplay=false;
    if($oLayer->labelitem!=""){

      /*if(!isset($class->label)){
        $label=new label_obj();
        $label->initDefault();
        $class->label=$label;
      } */
      //On met pas le label, on le met que si
      //Il est défini
      if($class->label!=null){
        $content.=$class->label->getToggle($id."_label");
		    $content.=$class->label->getSheet($id."_label");
		  }
    }else{
      
		/*	if(isset($class->label)){
          
      }else{
        $label=new label_obj();
        $label->initDefault();
        $class->label=$label;
      } */
      if($class->label!=null){
        $content.=$class->label->getToggle($id."_label","none");
		    $content.=$class->label->getSheet($id."_label");
		  }
      //TODO mettre un label vierge au cas ou
      //Verifier quand meme que le label est pas defini
    }
    //Affichage des styles
    $content.="<div style=\"display:block;text-align:right;\" ><a href=\"javascript:newStyle(".$index.")\"><img border=0 src=\"images/addStyle.png\" /></a> </div>";
      
    if(count($class->style)>0){
       $content.="<table style=\"display:block;width=100%;\"  border=\"0\" style=\"margin:0;padding:0;\">";

  	   $content.="<tr><td class=\"data_label\"><b>Styles : </b></td></tr>";
       $content.="</table>";
       //echo "".($index)."<br>";
       for($j=0;$j<count($class->style);$j++){
         $content.=getHTMLStyleToggle($layer,$index,$class->style[$j],$j);
         $content.=getStyleSheet($layer,$index,$class->style[$j],$j);
        // alert(this.getHTMLStyleToggle(j));
       }

    }

  	$content.="</div>";
    return $content;
  }
  
  
   function getStyleSheet($layer,$class,$oStyle,$index){
     $id="layer_".$layer."_classe_".$class."_style_".$index;
     $_SESSION["mapobj"]->restorezl("class_obj");


    $content="<div id=\"".$id."_attr\" style=\"display:none;\" class=\"layer_classe_style_attr\">";
    $content.="<form id=\"".$id."_form\">";
    $content.="<table>";
    $content.="<tr>".$oStyle->getInputField($id,"symbol",10)."<tr>";
    $content.="</table>"; 
    $content.="<hr size=\"1\"/>";
    $content.="<table style=\"width:100%;margin:0;padding:0;\">";
   
    //echo print_r($oStyle->color,true)."<br>";
    $content.="<tr><td  class=\"data_label\">color : ".getHTMLColor($id."_color",$oStyle->color)."</td></tr>";
    $content.="<tr><td  class=\"data_label\">outlinecolor : ".getHTMLColor($id."_outlinecolor",$oStyle->outlinecolor)."</td></tr>";
    $content.="<tr><td  class=\"data_label\">backgroundcolor : ".getHTMLColor($id."_backgroundcolor",$oStyle->backgroundcolor)."</td></tr>";
    $content.="</table>";
    $content.="<hr size=\"1\"/>";
    $content.="<table>";
    $content.="<tr>".$oStyle->getInputField($id,"size",10)."<tr>";
    $content.="<tr>".$oStyle->getInputField($id,"offsetx",10);
    $content.=$oStyle->getInputField($id,"offsety",10);
    $content.=$oStyle->getInputField($id,"minsize",10);
    $content.=$oStyle->getInputField($id,"maxsize",10);
		$content.="</tr></table>";
    $content.="</form>";
    $content.="</div>";

    return $content;
  }
  
  function getHTMLColor($id,$oColor){
    if(!$oColor){
      $oColor=new color_obj(array());
      $oColor->red=-1;
		  $oColor->green=-1;
		  $oColor->blue=-1;
    }
  
    $content=" <input id=\"".$id."_red\"  size=5 class=\"data_label\" type=\"text\" value=\"".$oColor->red."\">";
    $content.=" <input  id=\"".$id."_green\" size=5 class=\"data_label\" type=\"text\" value=\"".$oColor->green."\">";
    $content.=" <input  id=\"".$id."_blue\"  size=5 class=\"data_label\" type=\"text\" value=\"".$oColor->blue."\">";
    $content.="&nbsp;&nbsp;<a style=\"display:inline;\" title=\"Ouvrir la palette de couleur\"><input name=\"Ouvrir la palette de couleur\" type=\"button\"   id=\"".$id."_button\"  ></a>";
    $content.="&nbsp;&nbsp;<img title=\"Ré-initialiser les champs\" style=\"position:relative;top:6;\" src=\"images/initcolor.png\" title=\"Ré-initialiser les couleurs\" onmouseover=\"this.style.cursor='pointer'\" id=\"".$id."_img\"  >";
    $content.=" <input  id=\"".$id."_hexa\"  size=5 class=\"data_label\" type=\"text\" style=\"visibility:hidden;\"><br>";

    return $content;
  }
?>