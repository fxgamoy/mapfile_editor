<?php

  function gErrorHandler($errno,$errstr,$errfile,$errline,$errcontext){
    global $error;
    if($errno!=8)
      $error.=$errno." - ".$errstr."- $errfile - $errline\n";
  }

  function getAllLayer($oMapObj){
	  $layer="[";
   for($i=0;$i<count($oMapObj->layers);$i++)
   {
    $layer.="{";
    //print_r($_SESSION["mapobj"]->layers[$i]);
    $layer.="name:\"".htmlentities($oMapObj->layers[$i]->name)."\"";
      $layer.=",index:".$i;
      if($i==count($oMapObj->layers)-1) $layer.="}";
      else $layer.="},";
   }
   $layer.="]";
   return $layer;
	}
	
	function modifyColor(&$oColor,$data){
	  //echo (isset($data))."  ".print_r($data,true)."<br>";
	  if(isset($data)){
	  
	    if($data->red=="")$data->red=0;
	    if($data->green=="")$data->green=0;
	    if($data->blue=="")$data->blue=0;
	    
	    if(!isset($oColor)){
	      $oColor=new color_obj(array());
	    }
	    /*echo "red ".$data->red."  ".($data->red!=-1)."  ".($data->red=="")."<br>";
	    echo "green ".$data->green."  ".($data->green!=-1)."  ".($data->green=="")."<br>";
	    echo "blue ".$data->blue."  ".($data->blue!=-1)."  ".($data->blue=="")."<br>";
	  */
	    if($data->red!=-1 && $data->green!=-1 && $data->blue!=-1){
	      $oColor->setRGB($data->red,$data->green,$data->blue);
	    }else{
	     
	      $oColor=NULL;
				//echo "<br>UNSET 2 ".print_r($oColor,true)."<br>";
	    }
	  }else{
	    $oColor=NULL;
	  }
	}
	
	function modifyLayer(&$oLayer,$data){
	  //Modification
	 // echo "OUUUUUUUPSSEEEU <br>";
	 //print_r($data);
	  $vars=get_object_vars($data);
		//print_r($input);
	  
	  while (list($k, $v) = each($vars)) {
		  if($k!="classes"){
			 $oLayer->set($k,$v); 
			}    
       
    }
     //echo "NUMCLASSES ".$oLayer->numclasses."<br>";
     //print_r($oLayer->class);
    for($i=0;$i<$oLayer->numclasses;$i++){
      //$oClass=$oLayer->getclass($i);
      $oClass=$oLayer->class[$i];
      
        $exp=false;
        if($oLayer->classitem!="" && $oLayer->classitem!=null)
          $exp=true;
          
         //echo "<br>TEST 1<br>";
		  
		 	//print_r($oLayer);
		 		//echo "<br>CLASS $i<br>"; 
				modifyClass($oLayer->class[$i],$data->classes[$i],$exp,$oLayer->labelitem);
				//echo "<br>TEST 1<br>";
		  
		 	//print_r($oLayer);
		 		//echo "<br>END<br>";
      //echo "<br>TEST 1<br>";
			//print_r($oLayer->class[$i]->style[0]);
    }
     //print_r($oLayer);
	  /*print_r($data);
	  $vars=get_object_vars($data);
		//print_r($input);
	  
	  //if(hasZL("LAYER","type")){
		  //$v=getZLValue("LAYER","type",$data->type);
		  //$oLayer->set("type",$v);
		//}else{
			//$oLayer->set("type",$data->type);
		//}
    //$oLayer->set("connectiontype",$data->connectiontype);
    //$oLayer->set("connection",$data->connection);
    //$oLayer->set("minscale",$data->minscale);
    //$oLayer->set("maxscale",$data->maxscale);
    */
	}
	
	function modifyClass(&$oClass,$data,$exp=false,$labelitem=""){
	  //Modification
	 // echo "MODIFY CLASS<br>";
	  //print_r($data);
	  // echo "<br>";
	  
	  $oClass->set("name",$data->name);
    $oClass->set("title",$data->title);
    $oClass->set("minscale",$data->minscale);
    $oClass->set("maxscale",$data->maxscale);
    $oClass->set("template",$data->template);
		$oClass->set("status",$data->status); 
    //TODO explression
    /*if($exp){
      if($data->expression[0]!="(" && $data->expression[0]!="/"){
        $oClass->setExpression("\"".$data->expression."\"");
      }else{
        $oClass->setExpression($data->expression);
      }
    }*/
    //TODO
    if($labelitem!=""){
      //echo "<br>MODIFY LABEL<br>";
      //print_r($data->label);
      //echo "<br>OBJECT <br>";
      //print_r($oClass->label);
      if($oClass->label!=null)
        modifyLabel($oClass->label,$data->label);
      //print_r($oClass->label);
    }
    //print_r($oClass->label);
    if($data->keyimage!=null)
      $oClass->set("keyimage",$data->keyimage);
    //echo "NUMSTYLES ".$oClass->numstyles."<br>";
    for($i=0;$i<$oClass->numstyles;$i++){
      $oStyle=$oClass->style[$i];
			//echo "STYLE $i <br>";
     // echo "STYLE $i ".print_r($data->styles[$i],true)."<br>";
      //echo "AVANT ".print_r($oClass->style[$i],true)."<br>";
      modifyStyle($oClass->style[$i],$data->styles[$i]);
       //echo "APRES ".print_r($oClass->style[$i],true)."<br>";
    }
    
	}
	
	function modifyStyle(&$oStyle,$data){
	  //echo "<br>MODIFYSTYLE<br> ";
	 
	  //print_r($data);
	  //echo "<br><br>";
	  $oStyle->set("size",$data->size);
	  /*$oStyle->set("offsetx",$data->offsetx);
	  $oStyle->set("offsety",$data->offsety);
	  $oStyle->set("minsize",$data->minsize);
	  $oStyle->set("maxsize",$data->maxsize);*/
	  
	  modifyColor($oStyle->color,$data->color);
	  modifyColor($oStyle->outlinecolor,$data->outlinecolor);
	  modifyColor($oStyle->backgroundcolor,$data->backgroundcolor);
	
    $oStyle->set("symbol",$data->symbol);

	}
	
	function modifyScalebar(&$oScalebar,$data){
    //print_r($data->label);
	  //$v=getZLValue("ms_scalebar_obj","units",$data->units);
	  $oScalebar->set("status",$data->status);
	  $oScalebar->set("units",$data->units);
	  $oScalebar->set("width",$data->width);
	  $oScalebar->set("height",$data->height);
	  $oScalebar->set("intervals",$data->intervals);
	  
	  modifyColor($oScalebar->color,$data->color);
	  modifyColor($oScalebar->backgroundcolor,$data->backgroundcolor);
	  modifyColor($oScalebar->outlinecolor,$data->outlinecolor);
	  modifyColor($oScalebar->imagecolor,$data->imagecolor);
	  
	  modifyLabel($oScalebar->label,$data->label);
	}
	
	
	
	function modifyLegend(&$oLegend,$data){
	  //echo "MODIFY LEGEND<br>";
	  //print_r($data);
	  $oLegend->set("width",$data->width);
	  $oLegend->set("height",$data->height);
	  $oLegend->set("keysizex",$data->keysizex);
	  $oLegend->set("keysizey",$data->keysizey);
	  $oLegend->set("keyspacingx",$data->keyspacingx);
	  $oLegend->set("keyspacingy",$data->keyspacingy);
	  
		//TODO status
		//$v=getZLValue("ms_legend_obj","status",$data->status);
	  $oLegend->set("status",$v);
	  $oLegend->set("position",$data->position);
	  $oLegend->set("transparent",$data->transparent);
	  $oLegend->set("interlace",$data->interlace);
    $oLegend->set("template",$data->template);

    //echo "<br>LEGEND LABEL<br>";
    //print_r($data->label);
    modifyLabel(&$oLegend->label,$data->label);
    //echo "<br>END<br>";
	  //print_r(&$oLegend->label);
	  modifyColor($oLegend->outlinecolor,$data->outlinecolor);
	  modifyColor($oLegend->imagecolor,$data->imagecolor);

	}
	
	function modifyWeb(&$oWeb,$data){
	
	  //print_r($data);
	  $vars=get_object_vars($data);
		//print_r($input);
	  
	  while (list($k, $v) = each($vars)) {
	    
			if($k!="zl" && $k!="collapsed" && $k!="state"){
			  if(gettype($v)!="resource"){
          if($v==NULL){
         
          }else if(gettype($v)!="Object"){
          
            if(hasZL("ms_web_obj",$k)){
              $v=getZLValue("ms_web_obj",$k,$v);
            }
            //echo $k."  ".$v."<br>";
            if(gettype($v)=="string"){
              $oWeb->set($k,$v);
            }else{
              $oWeb->set($k,$v);
            }
          
          }else{//si c'est un objet
            if($k=="extent"){
            
            }
          }
        }
      }
    }

	}
	
	function modifyOutputformat(&$o,$data){
	  //echo "<br>MODIFY Outputformat<br>";
	  //echo print_r($data,true)."<br>";

	  //echo print_r($o,true)."<br>";
		//print_r($input);
	  for($i=0;$i<count($data);$i++){
      $vars=get_object_vars($data[$i]);
	    while (list($k, $v) = each($vars)) {
	    //echo "you $k  $v ".gettype($v)."<br>";
			  if($k!="zl" && $k!="collapsed" && $k!="state"){
			    if(gettype($v)!="resource"){
            if($v==NULL){
         
            }else if(gettype($v)!="Object"){
          
              if(hasZL("ms_outputformat_obj",$k)){
                $v=getZLValue("ms_outputformat_obj",$k,$v);
              }
              //echo $k."  ".$v."<br>";
              if(gettype($v)=="string"){
                $o[$i]->set($k,$v);
              }else{
                $o[$i]->set($k,$v);
              }
            }
          }
        }
      }
    }
	}
	
	function modifyLabel(&$o,$data){
	  $vars=get_object_vars($data);
	  
	  while (list($k, $v) = each($vars)) {
	    //echo $k."  ".gettype($v)."<br>";
		 if(gettype($v)!="resource"){
        if($v==NULL){
       
        }else if(gettype($v)!="object"){
          if($k=="font"){
            if(strtoupper($data->type)=="TRUETYPE"){
              $o->set($k,$v);
            }
          }else{
            if(hasZL("ms_label_obj",$k)){
              $v=getZLValue("ms_label_obj",$k,$v);
            }
            $o->set($k,$v);
          }
        }else{
        
          if($k=="color"){
            if($data->color->red!=-1 && $data->color->green!=-1 && $data->color->blue!=-1){
              if(!isset($o->color)) $o->color=new color_obj(array("",0,0,0)); 
              $o->color->setRGB($data->color->red,$data->color->green,$data->color->blue);
            }else
             unset($o->color);
          }else if($k=="outlinecolor"){
					  if($data->outlinecolor->red!=-1 && $data->outlinecolor->green!=-1 && $data->outlinecolor->blue!=-1){
              if(!isset($o->outlinecolor)) $o->outlinecolor=new color_obj(array("",0,0,0));
						  $o->outlinecolor->setRGB($data->outlinecolor->red,$data->outlinecolor->green,$data->outlinecolor->blue);
            }else
              unset($o->outlinecolor);
				  }else if($k=="shadowcolor"){
				    if($data->shadowcolor->red!=-1 && $data->shadowcolor->green!=-1 && $data->shadowcolor->blue!=-1){
              if(!isset($o->shadowcolor)) $o->shadowcolor=new color_obj(array("",0,0,0));
              $o->shadowcolor->setRGB($data->shadowcolor->red,$data->shadowcolor->green,$data->shadowcolor->blue);
            }else
              unset($o->shadowcolor);
					}else if($k=="backgroundcolor"){
            if($data->backgroundcolor->red!=-1 && $data->backgroundcolor->green!=-1 && $data->backgroundcolor->blue!=-1){
						  if(!isset($o->backgroundcolor)) $o->backgroundcolor=new color_obj(array("",0,0,0));
              $o->backgroundcolor->setRGB($data->backgroundcolor->red,$data->backgroundcolor->green,$data->backgroundcolor->blue);
            }else
              unset($o->backgroundcolor);
					}else if($k=="backgroundshadowcolor"){
					  if($data->backgroundshadowcolor->red!=-1 && $data->backgroundshadowcolor->green!=-1 && $data->backgroundshadowcolor->blue!=-1){
              if(!isset($o->backgroundshadowcolor)) $o->backgroundshadowcolor=new color_obj(array("",0,0,0));
              $o->backgroundshadowcolor->setRGB($data->backgroundshadowcolor->red,$data->backgroundshadowcolor->green,$data->backgroundshadowcolor->blue);
            }else
              unset($o->backgroundshadowcolor);
					}
        } 
        
      }
    }
	}
	
//	modifyReferenceMapObj
	function modifyReferenceMapObj(&$o,$data){
	  //echo "<br>ARGH<br>";
	  //echo "MODIFY REFERENCE ".print_r($data,true);
	  $vars=get_object_vars($data);
		//print_r($input);
	  
	  while (list($k, $v) = each($vars)) {
	    //echo gettype($v)."<br>";
			if($k!="zl" && $k!="collapsed" && $k!="state"){
			  if(gettype($v)!="resource"){
          if($v==NULL){
         
          }else if(gettype($v)!="object"){
          
            if(hasZL("ms_referencemap_obj",$k)){
              $v=getZLValue("ms_referencemap_obj",$k,$v);
            }
            //echo $k."  ".$v."<br>";
            if(gettype($v)=="string"){
              $o->set($k,$v);
            }else{
              $o->set($k,$v);
            }
          
          }else{
            //echo "<br>ARGH REFERENCE<br> ";
            //echo "$k<br>";
            //print_r($data->color);
            if($k=="extent"){
              $o->extent->setExtent($data->extent->minx,$data->extent->miny,$data->extent->maxx,$data->extent->maxy);
            }if($k=="color"){
              $o->color->setRGB($data->color->red,$data->color->green,$data->color->blue);
            }if($k=="outlinecolor"){
              $o->outlinecolor->setRGB($data->outlinecolor->red,$data->outlinecolor->green,$data->outlinecolor->blue);
            }
          } 
        }
      }
    }

	}
	

  function modifyMapParameters($map,$data){
    $vars=get_object_vars($data);
		//print_r($input);
	  
	  while (list($k, $v) = each($vars)) {
      //$tmp=sprintf("%s",$v);
      //echo "<br>$k  ".$v;
      //echo $tmp."  ";
      //echo $eval."  #".$v."#  ".($v!=NULL)."  bouh".($v!="Object")."\r\n";
		  if(gettype($v)!="resource"){
        if(gettype($v)!="object" && gettype($v)!="array"){
          if(gettype($v)=="string"){
            if($k=='symbolsetfilename'){
            //TODO recharger la liste des symbol
              /*$tmp=array();
              $cpt=0;
              for($i=0;$i<$oMS->oMap->getNumSymbols();$i++){
                $oSymbol=$oMS->oMap->getsymbolobjectbyid($i);
                if($oSymbol->inmapfile){
                  $tmp[$cpt]=$oSymbol;
                  $cpt++;
                }
              }
              $oMS->oMap->setSymbolSet($v);
              for($i=0;$i<count($tmp);$i++){
                //echo "youp ".$i."  ".print_r($tmp[$i],true)."<br>";
                $nId=ms_newsymbolobj($oMS->oMap, $tmp[$i]->name);
                $oSymbol = $oMS->oMap->getsymbolobjectbyid($nId);
                $oSymbol->set("inmapfile", MS_TRUE);
                $oSymbol->set("sizex", $tmp->sizex);
                $oSymbol->set("sizey", $tmp->sizey);
                if($tmp[$i]->getstylearray()!=NULL)
                  $oSymbol->setstyle($tmp[$i]->getstylearray());
                $oSymbol->setpoints($tmp[$i]->getpointsarray());
               }*/
              
            }else{
              $map->set($k,$v);
            }
          }else{
            $map->set($k,$v);
          }
         
        }else{//si c'est un objet
          if($k=="scalebar"){
            //echo "<br>MODIF SCALEBAR<br>";
						//echo print_r($v,true)."<br>";
						//echo print_r($map->scalebar,true)."<br>";
            modifyScalebar($map->scalebar,$v);
            //echo print_r($map->scalebar,true)."<br>";
          }else if($k=="legend"){
            modifyLegend($map->legend,$v);
          }else if($k=="web"){
            //echo "<br>MODIF WEB<br>";
						//echo print_r($v,true)."<br>";
					  modifyWeb($map->web,$v);
          }else if($k=="outputformat"){
					  modifyOutputformat($map->outputformat,$v);
          }else if($k=="reference"){
						modifyReferenceMapObj($map->reference,$v);
          }else if($k=="extent"){
            $map->setExtent($v->minx,$v->miny,$v->maxx,$v->maxy);
          }else if($k=="imagecolor"){
            $map->imagecolor->setRGB($v->red,$v->green,$v->blue);
          }
        }    
      }
    }
  }
  
  /******************************************/
  
  function getLayerSchema($oLayer){
    $json="{";
		$json.="offsite:{},";
		$json.="classes:[";
    $pref="";
    for($i=0;$i<count($oLayer->class);$i++){
      $json.=$pref.getClassSchema($oLayer->class[$i]);
      $pref=",";
    }
    $json.="]}";
    return $json;
  }
  
  function getClassSchema($oClass){
    $json="{";
		$json.="label:{},";
		$json.="numstyles:".count($oClass->style);
    $json.="}";
    return $json;
  }
  
?>