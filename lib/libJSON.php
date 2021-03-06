<?php
  function mapObjToJSON($object){
	  $pref="";
	  $suffixe="";
	  $prefZL="";
    $zl="";
    $json = new Services_JSON();
	  //On recupere l'ensemble de champs de l'objet
	  $input=get_object_vars($object);
	  $return="";
	  //$_SESSION["mapobj"]->restorezl("map");
	  //  print_r($_SESSION["mapobj"]->prop);
	  while (list($k, $v) = each($input)) {
	    //On recupere la valeur
	    //$eval='$v=$object->'.$k.';';
      //eval('$v=$object->'.$k.';');
      $tmp=sprintf("%s",$v);
      //echo $k."<br>";
      $value="null";
      //echo $k." ".$v."<br>";
      //echo $zl."<br>";
      //Si le champ n'est pas une ressource
      //echo $v ." $tmp  ".gettype($v)."  ".get_class($v)."<br>";
			if(gettype($v)!="resource"){
        if($tmp==NULL)
				{
          if(gettype($v)=="string"){
            $value="\"\"";
          }else if(gettype($v)=="integer" || gettype($v)=="double"){
            $value=-1;
          }else{
            $value="null";
          }
        }
				else if($tmp!="Object")
				{//si c'est un type primitif
          //echo $k."  ".$v.gettype($v)."  \r\n";
          if(gettype($v)=="string"){
            $value="\"$v\"";
          }else{
            $value="$v";
          }
          
        }
				else{//On lance la recursivite

          if($k=="web"){
            $value=objectToJSON($v);
          }else if($k=="legend"){
            //echo "***** LEGEND *****";
            $value=objectToJSON($v);
            //echo "***** /LEGEND *****";
          }else if($k=="outputformat"){
            //echo "<br><br>YOUPPPPIII<br>";
            //print_r($v->getOption);
            $value=outputformatObjToJSON($v);
            // echo "<br><br>END<br>";
          }else if($k=="reference"){
            //echo "REFERENCE JSON";
            $value=objectToJSON($v);
          }else if($k=="scalebar"){
            $value=objectToJSON($v);
          }else if($k=="extent"  || $k=="imagecolor"){
            $value=objectToJSON($v);
          }
        }
        
        //Si le champs est une zone de liste

        if(hasZL(get_class($object),$k)){
       //  if(count($_SESSION["mapobj"]->prop)>0)
       //  {

         // $value="\"".getZLLibelle(get_class($object),$k,$v)."\"";
         $value=$v;
        // print_r(get_class($object));
          $ZL=getZL(get_class($object),$k);
         // $ZL=getZL("map",$k);
         // print_r($ZL);
          $tmp=$json->encode($ZL);
          $zl.=$prefZL."\"$k\":".$tmp;
          
          $properties=getProperties(get_class($object),$k);
          $libelle=htmlentities($properties["fr"]);
          $prefZL=",";
		    }
		    
		    $return.=$pref."$k:$value".$suffixe;

        $pref=",";
      }
    } //fin while g�n�ral
    $return="{zl:{".$zl."},".$return."}";
  
    return $return;
	}
	
	function outputformatObjToJSON($object){
	  $return="{";
	  $return.="zl:[]";
	  $return.=",name:\"".$object->name."\"";
	  $return.=",mimetype:\"".$object->mimetype."\"";
	  $return.=",driver:\"".$object->driver."\"";
	  $return.=",extension:\"".$object->extension."\"";
	  $return.=",renderer:\"".$object->renderer."\"";
	  $return.=",imagemode:\"".$object->imagemode."\"";
	  $return.=",transparent:\"".$object->transparent."\"";
	  $return.=",formatoption:\"".$object->formatoption."\"";
    $return.="}";
    return $return;
	}
	
	function layerObjToJSON($oLayer,$i){
	  $json = new Services_JSON();
//	  print_r($oLayer );
	  $oLayertmp=$_SESSION["mapobj"]->getlayer($i);
	  $return=objectToJSON($oLayertmp);
    //On enleve les accolades
    $return=substr($return,1,count($return)-2);
    //sscanf($oLayer->numclasses,"%d",$nbclass);
    $nbclass=count($oLayer->class);
    //echo "youpi ".$nbclass."  ".$oLayer->numclasses."<br>";
    if($nbclass>0){
	    $pref="";
			$classes="classes:[";  
   
	    for($i=0;$i<$nbclass;$i++){
			//	$oClass=new class_obj;
			$oClass= $oLayer->getclass($i);
	      //$oClass=$oLayer->class[$i];
	     //
	    // print_r($oClass);
	      $tmp=objectToJSON($oClass);
	     // echo "<br><br>$tmp<br><br>";
	      $tmp=substr($tmp,1,count($tmp)-2);
	      // echo "<br><br>$tmp<br><br>";
	      $classes.="$pref{index:$i,".$tmp;
	      if($oClass->expression==NULL)
	      $classes.=",expression:\"\"";
	      else $classes.=",expression:\"".$oClass->expression."\"";
	      //$nbstyle=$oClass->numstyles;
	      $nbstyle=count($oLayer->class[$i]->style);
	      $styles="styles:[";  
        if($nbstyle>0){
          $prefStyle="";
			    
	        for($j=0;$j<$nbstyle;$j++){
            $oStyle= $oLayer->class[$i]->getstyle($j);
             //print_r($oStyle);
	          //$oStyle=$oClass->getStyle($j);
	          //$oStyle=$oLayer->class[$i]->style[$j];
	          $styles.=$prefStyle.objectToJSON($oStyle);
            
	          $prefStyle=",";
	        }
	        
	        
	      }
	      $styles.="]";
	      $classes.=",".$styles."}";
	      //$classes.="}";
	      $pref=",";
	    }
	    $classes.="]";
	    //echo $classes."<br>";
	  }
	  $return="{".$return.",".$classes."}";
	  return $return;
	}
	
  function objectToJSON($object){
	  $pref="";
	  $suffixe="";
	  $prefZL="";
    $zl="";
	  $json = new Services_JSON();
	  //On recupere l'ensemble de champs de l'objet
	  $input=get_object_vars($object);
	  //echo "<br><br>";

		//$return="{".$suff;
	 $return="";
	  while (list($k, $v) = each($input)) {
	    //On recupere la valeur
	    //$eval='$v=$object->'.$k.';';
      //eval('$v=$object->'.$k.';');
      $tmp=sprintf("%s",$v);
      //echo $k."<br>";
      $value="null";
      
			if(gettype($v)!="resource"){
        if($tmp==NULL){
          if(gettype($v)=="string"){
            $value="\"\"";
          }else if(gettype($v)=="integer" || gettype($v)=="double"){
            $value=-1;
          }else{
            $value="null";
          }
        }
				else if($tmp!="Object")
				{//si c'est un type primitif
          //echo $k."  ".$v.gettype($v)."  \r\n";
          if(gettype($v)=="string"){
            $value="\"".htmlentities($v)."\"";
          }
					else
					{
            $value="$v";
          }
          
        }
				else
				{//On lance la recursivite
          if($k=="label")
            $value=labelObjToJSON($v);
          else
            $value=objectToJSON($v);
        }
        

        //Si le champs est une zone de liste
        //echo get_class($object)."  ".$k."  ".hasZL(get_class($object),$k)."<br>";
        if(hasZL(get_class($object),$k)){
          $value="\"".getZLLibelle(get_class($object),$k,$v)."\"";
          $ZL=getZL(get_class($object),$k);
          //print_r($ZL);
          $tmp=$json->encode($ZL);
          $zl.=$prefZL."\"$k\":".$tmp;
          
          $properties=getProperties(get_class($object),$k);
          $libelle=htmlentities($properties["fr"]);
          $prefZL=",";
		    }
		    
		    $return.=$pref."$k:$value".$suffixe;

        $pref=",";
      }
    }
    if($return!="") $return="{zl:{".$zl."},".$return."}";
    else $return="{zl:{".$zl."}}";
    //echo "<br><br>".$return."<br><br>";
    return $return;
	}
	
	function labelObjToJSON($object){
	  $pref="";
	  $suffixe="";
	  $prefZL="";
    $zl="";
	  $json = new Services_JSON();
	  //On recupere l'ensemble de champs de l'objet
	  $input=get_object_vars($object);
	  //echo "<br><br>";

		//$return="{".$suff;
	  $return="";
	  
	  while (list($k, $v) = each($input)) {
	    //On recupere la valeur
	    //$eval='$v=$object->'.$k.';';
      //eval('$v=$object->'.$k.';');
      $tmp=sprintf("%s",$v);
      $value="null";
      //echo $k."  ".$v."  ".gettype($v)."<br>";
			if(gettype($v)!="resource"){
        if($tmp==NULL){
          if(gettype($v)=="string"){
            $value="\"\"";
          }else if(gettype($v)=="integer" || gettype($v)=="double"){
            $value=-1;
          }else{
            $value="null";
          }
        }else if($tmp!="Object"){//si c'est un type primitif
          //echo $k."  ".$v.gettype($v)."  \r\n";
          if(gettype($v)=="string"){
            $value="\"$v\"";
          }else{
            $value="$v";
          }

        }else{//On lance la recursivite

            $value=objectToJSON($v);
        }

        //Si le champs est une zone de liste
        //echo get_class($object)."  ".$k."  ".hasZL(get_class($object),$k)."<br>";
        if(hasZL(get_class($object),$k)){
          if($k=="size"){
            //TODO g�rer les diff�rents cas
            /*if($object->type==MS_BITMAP)
              $value="\"".getZLLibelle(get_class($object),$k,$v)."\"";
            else*/
            $value="\"".$object->size."\"";
            $ZL=getZL(get_class($object),$k);
            //print_r($ZL);
            $tmp=$json->encode($ZL);
            $zl.=$prefZL."\"$k\":".$tmp;

            $properties=getProperties(get_class($object),$k);
            $libelle=htmlentities($properties["fr"]);
            $prefZL=",";
          }else{
            $value="\"".getZLLibelle(get_class($object),$k,$v)."\"";
            $ZL=getZL(get_class($object),$k);
            //print_r($ZL);
            $tmp=$json->encode($ZL);
            $zl.=$prefZL."\"$k\":".$tmp;

            $properties=getProperties(get_class($object),$k);
            $libelle=htmlentities($properties["fr"]);
            $prefZL=",";
          }
		    }

		    $return.=$pref."$k:$value".$suffixe;

        $pref=",";
      }
    }
    $return="{zl:{".$zl."},".$return."}";
    //echo "<br><br>".$return."<br><br>";
    return $return;
	}
	

?>