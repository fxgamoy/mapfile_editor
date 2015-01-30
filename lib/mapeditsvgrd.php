<?php
  session_start();
  //header("Content-Type: text/xml");
   require_once("JSON.php");
   require_once("libJSON.php");
   require_once("libdicomf.php");
    require_once("libmapfile.php");
  require("../globprefs.php");
  
	$http_form_vars = count( $_POST ) > 0 ? $_POST : 
                                    ( count($_GET) > 0 ? $_GET : array("") );
  

  //print_r($http_form_vars);
  if($http_form_vars["requete"]=="getMapFileList"){
    $cible=REPMAP;
  	$dir=opendir($cible);
  	$line=array();
//echo "ajout";
  	while($filename=readdir($dir))
  	{
    	if($filename != '.' && $filename != '..')
    	{
				$valtest=$cible."/".$filename;
				// on prend les fichiers ne débutant pas par tmp_ et terminant par l'extension .map
      	if(!is_dir($valtest) && substr($filename,0,4)!="tmp_" && substr(strrchr($filename,"."),1)=="map")
      	{
        	array_push($line,$filename);
				}
    	}
  	}
  	closedir($dir);
				$i=0;
		//$query="select * from geoset";
    //$result=pg_query($link,$query) or die("echec de la requete ".$query1);
	  echo "[";
	  //for($i=0;$i<pg_num_rows($result);$i++){
			$nbrep=count($line);
			foreach ($line as $name) {
      //$line=pg_fetch_assoc($result);
      echo "{";
      //echo "name:\"".$line["NOM"]."\"";
      echo "name:\"".$name."\"";
      //echo ",libelle:\"".htmlentities($line["DESCRIPTION"])."\"";
      echo ",libelle:\"\"";
      if($i==count($line)-1) echo "}";
      //if($i==pg_num_rows($result)-1) echo "}";
      else echo "},";
      $i++;
    }
    echo "]";
	
	}else if($http_form_vars["requete"]=="save"){
	  $oMS=new MapSession_RW;
    $oMS->readMapFile($_SESSION["tmpMF"]);
		$oMS->writeMapFile(REPMAP.$http_form_vars["name"]);
  }else if($http_form_vars["requete"]=="saveAs"){
    $name=split("[.]",$http_form_vars["name"]);
    $name=$name[0];
    
    $oMS=new MapSession_RW;
    $oMS->readMapFile($_SESSION["tmpMF"]);
		$oMS->writeMapFile(REPMAP.$name.".map");
		$_SESSION["tmpMF"]=REPMAP."tmp_".$name.".map";
		echo $name.".map";
  }else if($http_form_vars["requete"]=="moveLayerUp"){
    //print_r($http_form_vars);
    //On utilise pas la mapsession ici, au cas certaines variables du mapfile
    //ne soit pas valide comme les extents
    //print_r($http_form_vars);
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$lindex);
    $oMapObj->moveLayerUp($lindex);
    $oMapObj->save($_SESSION["tmpMF"]);
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    $content=getAllLayer($oMapObj);
    
    echo $content;
	}else if($http_form_vars["requete"]=="moveLayerDown"){
    //print_r($http_form_vars);
    //On utilise pas la mapsession ici, au cas certaines variables du mapfile
    //ne soit pas valide comme les extents
    //print_r($http_form_vars);
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$lindex);
    $oMapObj->moveLayerDown($lindex);
    $oMapObj->save($_SESSION["tmpMF"]);
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    $content=getAllLayer($oMapObj);
    
    echo $content;
	}else if($http_form_vars["requete"]=="newLayer"){
    //print_r($http_form_vars);
    //On utilise pas la mapsession ici, au cas certaines variables du mapfile
    //ne soit pas valide comme les extents
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    $oLayer=ms_newLayerObj($oMapObj);
    $oClass=ms_newClassObj($oLayer);
    $oLayer->set("type",0);
    $content=getAllLayer($oMapObj);
    $oMapObj->save($_SESSION["tmpMF"]);
    echo $content;
	}else if($http_form_vars["requete"]=="deleteLayer"){
	  //echo "deleteLayer<br>";
	// echo $_SESSION["tmpMF"];
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$lindex);
    $oLayer=$oMapObj->getLayer($lindex);
    $oLayer->set("status",MS_DELETE);  
    $oMapObj->save($_SESSION["tmpMF"]);
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    
    $layer="[";
    for($i=0;$i<$oMapObj->numlayers;$i++){
      $oLayer=$oMapObj->getLayer($i);
      $layer.="{";
      $layer.="name:\"".htmlentities($oLayer->name)."\"";
      $layer.=",index:".$i;
      if($i==$oMapObj->numlayers-1) $layer.="}";
      else $layer.="},";
    }
    $layer.="]";
    
    echo $layer;
    
	}else if($http_form_vars["requete"]=="newClass"){
    //print_r($http_form_vars);
    //On utilise pas la mapsession ici, au cas certaines variables du mapfile
    //ne soit pas valide comme les extents
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$index);
    $oLayer=$oMapObj->getLayer($index);
    $oClass=ms_newClassObj($oMapObj->getLayer($index));
    //echo $oLayer->numclasses."<br>";
    $content=layerObjToJSON($oMapObj->getLayer($index));
    $oMapObj->save($_SESSION["tmpMF"]);
    echo $content;
	}else if($http_form_vars["requete"]=="deleteClass"){
	  //echo "deleteClass<br>";
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$lindex);
    sscanf($http_form_vars["classe"],"%d",$cindex);
    $oLayer=$oMapObj->getLayer($lindex);
    $oClass=$oLayer->getClass($cindex);
    $oClass->set("status",MS_DELETE);  
    $oMapObj->save($_SESSION["tmpMF"]);
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    $oLayer=$oMapObj->getLayer($lindex);
    $content=layerObjToJSON($oLayer);
    echo $content;
	}else if($http_form_vars["requete"]=="newStyle"){
    //print_r($http_form_vars);
    //On utilise pas la mapsession ici, au cas certaines variables du mapfile
    //ne soit pas valide comme les extents
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$layer);
    sscanf($http_form_vars["classe"],"%d",$class);
    $oLayer=$oMapObj->getLayer($layer);
    $oClass=$oLayer->getClass($class);
    
		//echo $oLayer->numclasses."<br>";
    $oStyle=ms_newStyleObj($oClass);
    $content=layerObjToJSON($oLayer);
    $oMapObj->save($_SESSION["tmpMF"]);
    echo $content;
	}else if($http_form_vars["requete"]=="deleteStyle"){
	  //echo "deleteClass<br>";
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$lindex);
    sscanf($http_form_vars["classe"],"%d",$cindex);
    sscanf($http_form_vars["style"],"%d",$sindex);
    $oLayer=$oMapObj->getLayer($lindex);
    $oClass=$oLayer->getClass($cindex);
    $oClass->deleteStyle($sindex);  
    $oMapObj->save($_SESSION["tmpMF"]);
    $oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
    $oLayer=$oMapObj->getLayer($lindex);
    $content=layerObjToJSON($oLayer);
    echo $content;
	}else if($http_form_vars["requete"]=="newMFIsigeo"){
    //print_r($http_form_vars);
    $name=split("[.]",$http_form_vars["name"]);
    $name=$name[0];
    touch(REPMAP.$name.".map");
    $fp=fopen(REPMAP.$name.".map","w");
    fwrite($fp,"MAP\r\nEND");
    fclose($fp);
    $MF=ms_newMapObj(REPMAP.$name.".map");
    $MF->setExtent(-180,-90,180,90);
    $MF->setSize(100,100);
    $content=objectToJSON($MF);
    $MF->save(REPMAP.$name.".map");
    
    $_SESSION["tmpMF"]=REPMAP."tmp_".$name.".map";
    $MF->save($_SESSION["tmpMF"]);
    
    echo $name.".map|".$content;
	}else if($http_form_vars["requete"]=="openMFIsigeo"){
    //echo "open";
    //echo REPMAP.$http_form_vars["name"].".map";
    $oMS=new MapSession_RW;
    $oMS->readMapFile(REPMAP.$http_form_vars["name"]);
		$oMS->writeMapFile(REPMAP."tmp_".$http_form_vars["name"]);
		$_SESSION["tmpMF"]=REPMAP."tmp_".$http_form_vars["name"];
    
	  require_once("JSON.php");
    $json = new Services_JSON();
    //print_r(get_class_vars(get_class($oMS->oMap)));
    $param=mapObjToJSON($oMS->oMap);
    //echo $param;
  
    $layer="[";
    for($i=0;$i<$oMS->oMap->numlayers;$i++){
      $oLayer=$oMS->oMap->getLayer($i);
      $layer.="{";
      $layer.="name:\"".htmlentities($oLayer->name)."\"";
      $layer.=",index:".$i;
      if($i==$oMS->oMap->numlayers-1) $layer.="}";
      else $layer.="},";
    }
    $layer.="]";
    
    
    /** On fait la liste des symbol **/
    $_SESSION["symbolListe"]="";
		for($i=0;$i<$oMS->oMap->getNumSymbols();$i++){
		  $oSymbol=$oMS->oMap->getsymbolobjectbyid($i);
      $_SESSION["symbolListe"].=$oSymbol->name."~".$i;
      if($i!=$oMS->oMap->getNumSymbols()-1){
        $_SESSION["symbolListe"].=",";
      }
    }
    
     /** On fait la liste des font **/
    $_SESSION["fontListe"]="";
    $pref="";
    if($oMS->oMap->fontsetfilename!=NULL){
      $filename=REPMAP.$oMS->oMap->fontsetfilename;
      $fp=fopen($filename,"r");
      $cpt=0;
      while(!feof($fp)){
        $chaine=fgets($fp,4096);
        $chaine=substr($chaine,0,strlen($chaine)-1);
        $parts=split("[ ]",$chaine);
        //echo $parts[0]." ".strlen($parts[0])."\n";
        $tmp=$pref."".$parts[0];
        //echo "argh ".$tmp."\n";
        $_SESSION["fontListe"].=$tmp."~".$cpt;
        $cpt++;
        $pref=",";
      }
      fclose($fp);
    }

    //$tmp=get_class($oMS->oMap);
    //$tmp=$oMS->oMap->numoutputformats."  ".$oMS->oMap->outputformatlist;
    echo "|".$http_form_vars["name"]."|".$param."|".$layer."|".$tmp;
   
  }else if($http_form_vars["requete"]=="getLayer"){
    //echo "open";
    require_once("JSON.php");
    $json = new Services_JSON();
    $oMS=new MapSession_RW;
    $oMS->readMapFile($_SESSION["tmpMF"]);
    sscanf($http_form_vars["index"],"%d",$index);
    
    $oLayer=$oMS->oMap->getLayer($index);
    //echo get_class($oLayer)."<br>";
    
    echo layerObjToJSON($oLayer);
		  
  }else if($http_form_vars["requete"]=="modifyMF"){
	  require_once("JSON.php");
    print_r($http_form_vars);
    $oMS=new MapSession_RW;
    //echo "<br>".$_SESSION["tmpMF"]."<br>";
    $oMS->readMapFile($_SESSION["tmpMF"]);
    $tmp=str_replace("\\\"", "\"", $http_form_vars["data"]);
    $tmp=str_replace("\\'", "'", $tmp);
    $tmp=html_entity_decode($tmp);

    $json = new Services_JSON();
    $input = $json->decode($tmp); 
    
    $vars=get_object_vars($input);
		//print_r($input);
	  
	  while (list($k, $v) = each($vars)) {
      $tmp=sprintf("%s",$v);
      //echo "<br>$k  ".$v;
      //echo $tmp."  ";
      //echo $eval."  #".$v."#  ".($v!=NULL)."  bouh".($v!="Object")."\r\n";
			if($k!="zl" && $k!="keysizex" && $k!="keysizey" && $k!="keyspacingx" && $k!="keyspacingy" && $k!="collapsed" && $k!="state" && $k!="mappath" && $k!="querymap")
			  if(gettype($v)!="resource"){
          if($tmp==NULL){
         
          }else if($tmp!="Object"){
            
            if(hasZL("ms_map_obj",$k)){
              $v=getZLValue("ms_map_obj",$k,$v);
            }
            
            if(gettype($v)=="string"){
              if($k=='symbolsetfilename'){
                $tmp=array();
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

                }
                
              }else if($k=='fontsetfilename'){
                $oMS->oMap->setFontSet($v);
              }else if($k=='imagetype'){
                $oMS->oMap->selectOutputFormat($v);
              }else
                $oMS->oMap->set($k,$v);
            }else{
              $oMS->oMap->set($k,$v);
            }
           
          }else{//si c'est un objet
            if($k=="scalebar"){
              modifyScalebar($oMS->oMap->scalebar,$v);
            }else if($k=="legend"){
              modifyLegend($oMS->oMap->legend,$v);
            }else if($k=="web"){
							modifyWeb($oMS->oMap->web,$v);
            }else if($k=="outputformat"){
							modifyOutputformat($oMS->oMap->outputformat,$v);
            }else if($k=="reference"){
							modifyReferenceMapObj($oMS->oMap->reference,$v);
            }else if($k=="extent"){
              $oMS->oMap->setExtent($v->minx,$v->miny,$v->maxx,$v->maxy);
            }else if($k=="imagecolor"){
              $oMS->oMap->imagecolor->setRGB($v->red,$v->green,$v->blue);
            }
          }
          
      }
    }

    $oMS->writeMapFile($_SESSION["tmpMF"]);

	}else if($http_form_vars["requete"]=="modifyLayer"){
    
    require_once("JSON.php");
    echo "<br>MODIFYLAYER<br>";
    print_r($http_form_vars);
    $oMS=new MapSession_RW;
    $oMS->readMapFile($_SESSION["tmpMF"]);
    //echo $http_form_vars["layer"];
    sscanf($http_form_vars["layer"],"%d",$index);
    $oLayer=$oMS->oMap->getLayer($index); 
    
    /**/
    /*$fp=fopen("logmodif.txt","a");
    $content=$oLayer->name."\r\n";
    $content.=print_r($http_form_vars,true);
    fwrite($fp,print_r($http_form_vars,true));
    fclose($fp);*/
    /**/
    
    $tmp=str_replace("\\\"", "\"", $http_form_vars["data"]);
    $tmp=str_replace("\\'", "'", $tmp);
    $tmp=html_entity_decode($tmp);

    $json = new Services_JSON();
    $input = $json->decode($tmp); 
    modifyLayer($oLayer,$input);
    $tmp=$oMS->oMap->getLayer($index); 
    echo $tmp->name."END<br>";
    echo $_SESSION["tmpMF"];
    $oMS->writeMapFile($_SESSION["tmpMF"]);
	}else if($http_form_vars["requete"]=="modifyClass"){
    require_once("JSON.php");
    //echo "<br><br>HIHIHIHI<br><br>";
    print_r($http_form_vars);
    $oMS=new MapSession_RW;
    $oMS->readMapFile($_SESSION["tmpMF"]);
    //echo $http_form_vars["layer"]."  ".$http_form_vars["classe"]."  ".$http_form_vars["style"]."\r\n";
    sscanf($http_form_vars["layer"],"%d",$index);
    $oLayer=$oMS->oMap->getLayer($index); 
    sscanf($http_form_vars["classe"],"%d",$index);
    $oClass=$oLayer->getClass($index); 
    
    $tmp=str_replace("\\\"", "\"", $http_form_vars["data"]);
    $tmp=str_replace("\\'", "'", $tmp);
    
    $json = new Services_JSON();
    $input = $json->decode($tmp); 
    //print_r($input);
   
     /*$fp=fopen("logmodif.txt","a");
    fwrite($fp,print_r($input,true));
    fwrite($fp,"----------------------");
    fclose($fp);*/

    modifyClass($oClass,$input);
    
		sscanf($http_form_vars["layer"],"%d",$index);
   $oLayer=$oMS->oMap->getLayer($index); 
    sscanf($http_form_vars["classe"],"%d",$index);
    $oClass=$oLayer->getClass($index); 
     print_r($oClass->label);
     echo "<br><br>";
		//print_r($oClass);
    //print_r($oClass->label);
   $oMS->writeMapFile($_SESSION["tmpMF"]);
   
   
	}else if($http_form_vars["requete"]=="modifyStyle"){

    require_once("JSON.php");
    print_r($http_form_vars);
    
    $oMS=new MapSession_RW;
    $oMS->readMapFile($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$index);
    $oLayer=$oMS->oMap->getLayer($index); 
    sscanf($http_form_vars["classe"],"%d",$index);
    $oClass=$oLayer->getClass($index); 
    sscanf($http_form_vars["style"],"%d",$index);
    $oStyle=$oClass->getStyle($index); 
    
    $tmp=str_replace("\\\"", "\"", $http_form_vars["data"]);
    $tmp=str_replace("\\'", "'", $tmp);
    
    $json = new Services_JSON();
    $input = $json->decode($tmp); 
    
    /*$fp=fopen("logmodif.txt","a");
    fwrite($fp,print_r($input,true));
    fwrite($fp,"----------------------");
    fclose($fp);*/
    
    modifyStyle($oStyle,$input);
    /*$oLayer=$oMS->oMap->getLayer($index);
    $oClass=$oLayer->getClass(0); 
    $oStyle=$oClass->getStyle(0); 
		print_r($oStyle);
		echo $http_form_vars["name"]; */
    
    $oMS->writeMapFile($_SESSION["tmpMF"]);
	}
	
	
	
  
?>