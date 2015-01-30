<?php
  session_start();
  //header("Content-Type: text/xml");
   require_once("JSON.php");
   require_once("libJSON.php");
   require_once("libdicomf.php");
   require_once("libmapfile.php");
   require_once("libfiche.php");
   require("../globprefs.php");
  
	$http_form_vars = count( $_POST ) > 0 ? $_POST : 
                                    ( count($_GET) > 0 ? $_GET : array("") );
  $error="";
  $debug="";

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
      echo "name:\"".$name."\"";
      //echo ",libelle:\"".htmlentities($line["DESCRIPTION"])."\"";
      echo ",libelle:\"\"";
      if($i==count($line)-1) echo "}";
      //if($i==pg_num_rows($result)-1) echo "}";
      else echo "},";
      $i++;
    }
    echo "]";
	
	}
  else if($http_form_vars["requete"]=="save")
  {
	  /*$oMS=new MapSession_RW;
    $oMS->readMapFile($_SESSION["tmpMF"]);
		$oMS->writeMapFile(REPMAP.$http_form_vars["name"]);*/
		if (!copy($_SESSION["tmpMF"], REPMAP.$http_form_vars["name"]))
      echo "La copie du fichier $file n'a pas réussi...\n";
  }
  else if($http_form_vars["requete"]=="saveAs")
  {
    $name=split("[.]",$http_form_vars["name"]);
    $name=$name[0];
    
    $oMS=new MapSession_RW;
    $oMS->readMapFile($_SESSION["tmpMF"]);
		$oMS->writeMapFile(REPMAP.$name.".map");
		$_SESSION["tmpMF"]=REPMAP."tmp_".$name.".map";
		echo $name.".map";
  }else if($http_form_vars["requete"]=="moveLayerUp"){
    //print_r($http_form_vars);
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$index);
    $_SESSION["mapobj"]->moveLayerUp($index);
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    $content=getAllLayer($_SESSION["mapobj"]);
    echo "|".$content;

	}else if($http_form_vars["requete"]=="moveLayerDown"){
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$index);
    $_SESSION["mapobj"]->moveLayerDown($index);
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    $content=getAllLayer($_SESSION["mapobj"]);
    echo "|".$content;
	}else if($http_form_vars["requete"]=="newLayer"){
    //print_r($http_form_vars);
    //On utilise pas la mapsession ici, au cas certaines variables du mapfile
    //ne soit pas valide comme les extents
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    $_SESSION["mapobj"]->newLayer();
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    $content=getAllLayer($_SESSION["mapobj"]);
    
    echo "|".$content;
	}else if($http_form_vars["requete"]=="deleteLayer"){
	  //echo "deleteLayer<br>";
	// echo $_SESSION["tmpMF"];
	
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    sscanf($http_form_vars["layer"],"%d",$index);
    $_SESSION["mapobj"]->deleteLayer($index);
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    $content=getAllLayer($_SESSION["mapobj"]);

    echo "|".$content;
	
    /*$oMapObj=ms_newMapObj($_SESSION["tmpMF"]);
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
    
    echo $layer; */
    
	}else if($http_form_vars["requete"]=="newOutputformat"){
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
   // echo $_SESSION["tmpMF"];
    $of=$_SESSION["mapobj"]->map->addOutputformat();
     print_r($_SESSION["mapobj"]->map->outputformat);
    //$oLayer->newClass();
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    $content=getOutputformatSheet($_SESSION["mapobj"]->map->outputformat,"mapfile_outputformat");
    echo "|".$content;
  }else if($http_form_vars["requete"]=="newClass"){
    //print_r($http_form_vars);
    sscanf($http_form_vars["layer"],"%d",$index);
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    $_SESSION["mapobj"]->layers[$index]->newClass();
    //$oLayer->newClass();
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    $content=getLayerSheet($_SESSION["mapobj"]->layers[$index],$index);
    
    echo "|".$content;
	}else if($http_form_vars["requete"]=="deleteClass"){

  	sscanf($http_form_vars["layer"],"%d",$index);
	  sscanf($http_form_vars["classe"],"%d",$class);
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    $_SESSION["mapobj"]->layers[$index]->deleteClass($class);
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    $content=getLayerSheet($_SESSION["mapobj"]->layers[$index],$index);

    echo "|".$content;

	}else if($http_form_vars["requete"]=="newStyle"){
		sscanf($http_form_vars["layer"],"%d",$index);
	  sscanf($http_form_vars["classe"],"%d",$class);
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    $_SESSION["mapobj"]->layers[$index]->class[$class]->newStyle();
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    $content=getLayerSheet($_SESSION["mapobj"]->layers[$index],$index);
    
    echo "|".$content;
	}else if($http_form_vars["requete"]=="deleteStyle"){
	  //echo "deleteClass<br>";
	  //echo "DELETE STYLE<br>";
    print_r($http_form_vars);
  	sscanf($http_form_vars["layer"],"%d",$index);
	  sscanf($http_form_vars["classe"],"%d",$class);
    sscanf($http_form_vars["style"],"%d",$style);
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    $_SESSION["mapobj"]->layers[$index]->class[$class]->deleteStyle($style);
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    $content=getLayerSheet($_SESSION["mapobj"]->layers[$index],$index);

    echo "|".$content;
	}
  else if($http_form_vars["requete"]=="newMFIsigeo")
  {
    //print_r($http_form_vars);
    $name=split("[.]",$http_form_vars["name"]);
    $name=$name[0];
    $_SESSION["tmpMF"]=REPMAP."tmp_".$name.".map";
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->map->initDefault();
    $_SESSION["mapobj"]->writemapfile(REPMAP.$name.".map");
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
		
    //$content=objectToJSON($MF);
    $content=getMapParametersSheet($_SESSION["mapobj"]->map);
   
    echo "|".$name.".map|".$content;
	}
  else if($http_form_vars["requete"]=="openMFIsigeo")
  {
   /* $oMS=new MapSession_RW;
    $oMS->readMapFile(REPMAP.$http_form_vars["name"]);
    echo $oMS->oMap->getNumSymbols()."<br>";
    for($i=0;$i<$oMS->oMap->getNumSymbols();$i++){
      $symbol=$oMS->oMap->getsymbolobjectbyid($i);
      echo "$i $symbol->name <br>";
    }*/
  
    $_SESSION["mapobj"]=new mapobject;
    $result=$_SESSION["mapobj"]->readParameters(REPMAP.$http_form_vars["name"]);
    if(!$result){
      echo $error;
      return;
    }
    $file = REPMAP.$http_form_vars["name"];
    $newfile = REPMAP."tmp_".$http_form_vars["name"];
    if (!copy($file, $newfile))
      echo "La copie du fichier $file n'a pas réussi...\n";
    else
     $_SESSION["tmpMF"]=$newfile ;

    //On fait la liste des image type
    $_SESSION["imagetypeListe"]="[{name:\"\",index:null}";
    for($i=0;$i<count($_SESSION["mapobj"]->map->outputformat);$i++){
       $tmp=$_SESSION["mapobj"]->map->outputformat[$i];
       $_SESSION["imagetypeListe"].=",{name:\"".$tmp->name."\",index:".$i."}";
    }
    $_SESSION["imagetypeListe"].="]";
    //echo "YEP ".$_SESSION["imagetypeListe"];
    
    require_once("JSON.php");
    $json = new Services_JSON();
    //genere la fiche des parametres du mapfile
    $mapsheet=getMapParametersSheet($_SESSION["mapobj"]->map);
  
    //sert a generer la page de layers...
    $layer="[";
     for($i=0;$i<count($_SESSION["mapobj"]->layers);$i++)
     {
       $layer.="{";
       //print_r($_SESSION["mapobj"]->layers[$i]);
       $layer.="name:\"".htmlentities($_SESSION["mapobj"]->layers[$i]->name)."\"";
       $layer.=",index:".$i;
       if($i==count($_SESSION["mapobj"]->layers)-1) $layer.="}";
       else $layer.="},";
     }
     $layer.="]";

     //Lecture des symbol du fichier symbolset
		 $_SESSION["mapobj"]->loadSymbolSet();
     // On fait la liste des symbol
     $i=0;
     $_SESSION["symbolListe"]="[";
     foreach($_SESSION["mapobj"]->symbols as $oSymbol)
     {
       if($oSymbol->name!=NULL && $oSymbol->name!="")
         $_SESSION["symbolListe"].=$pref."{name:\"".$oSymbol->name."\",index:".($i+1)."}";
       else
         $_SESSION["symbolListe"].=$pref."{name:\"".$i."\",index:".($i+1)."}";
       
			 $pref=",";
       $i++;
     }
     $_SESSION["symbolListe"].="]";
     //echo $_SESSION["symbolListe"];
   
     //On fait la liste des font
    $_SESSION["fontListe"]="[";
    $pref="";
    if($_SESSION["mapobj"]->map->fontset!=NULL){
      $filename=REPMAP.$_SESSION["mapobj"]->map->fontset;
      $fp=fopen($filename,"r");
      $cpt=0;
      while(!feof($fp)){
        $chaine=fgets($fp,4096);
        $chaine=substr($chaine,0,strlen($chaine)-1);
        $parts=split("[ ]",$chaine);
        //echo $parts[0]." ".strlen($parts[0])."\n";
        if($parts[0]!="")
        {
        $tmp=$pref."".$parts[0];
        //echo "argh ".$tmp."\n";
        $_SESSION["fontListe"].=$pref."{name:\"".$tmp."\",index:".$cpt."}";
        $cpt++;
         $pref=",";
        }


      }
      fclose($fp);
    }
    $_SESSION["fontListe"].="]";
    $tmp="";
    

     /*$fp=@fopen("TEST","w");
     fwrite($fp,$mapsheet);
     fclose($fp);*/
     
    echo "|".$http_form_vars["name"]."|".$mapsheet."|".$layer."|".$tmp;
   
  }else if($http_form_vars["requete"]=="getLayer"){
    //echo "open";
    //print_r($http_form_vars);
    require_once("JSON.php");
    $json = new Services_JSON();

    sscanf($http_form_vars["index"],"%d",$index);

    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    $oLayer=$_SESSION["mapobj"]->layers[$index];
    $html=getLayerSheet($oLayer,$index);
    //$schema=getLayerSchema($oLayer,$index);
    echo "|".$html;
  }else if($http_form_vars["requete"]=="getClass"){
    //echo "open";
    //print_r($http_form_vars);
    sscanf($http_form_vars["layer"],"%d",$idlayer);
    sscanf($http_form_vars["classe"],"%d",$index);

   $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters(REPMAP.$http_form_vars["name"]);
    $oLayer=$_SESSION["mapobj"]->layers[$idlayer];
    $oClass=$oLayer->class[$index];
    //print_r($oLayer);
    //echo "<br>";
    echo getClassSheet($idlayer,$oClass,$index);
  }
  else if($http_form_vars["requete"]=="getStyle"){
    //echo "open";
    //print_r($http_form_vars);
    sscanf($http_form_vars["layer"],"%d",$layer);
    sscanf($http_form_vars["classe"],"%d",$class);
     sscanf($http_form_vars["style"],"%d",$style);

    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters(REPMAP.$http_form_vars["name"]);
    $oLayer=$_SESSION["mapobj"]->layers[$layer];
    $oClass=$oLayer->class[$class];
    $oStyle=$oClass->style[$style];
    //print_r($oLayer);
    //echo "<br>";
    echo getStyleSheet($layer,$class,$oStyle,$style);
  }
  else if($http_form_vars["requete"]=="modifyMF")
  {
	  require_once("JSON.php");
    //print_r($http_form_vars);
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);
    $tmp=str_replace("\\\"", "\"", $http_form_vars["data"]);
    $tmp=str_replace("\\'", "'", $tmp);
    $tmp=html_entity_decode($tmp);

    $json = new Services_JSON();
    $input = $json->decode($tmp); 
    /*print_r($tmp);
    echo "<br>INPUT<br> ";
    print_r($input);*/
    /*echo "<br>AVANT<br>";
    print_r($_SESSION["mapobj"]->map);
    echo "<br>FIN<br>";*/
    //print_r($_SESSION["mapobj"]->layers[0]);
    modifyMapParameters(&$_SESSION["mapobj"]->map,$input);
    
		//echo "<br>APRES<br>";
     //print_r($_SESSION["mapobj"]->layers[0]);
    //print_r($_SESSION["mapobj"]->map);*/
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
	}else if($http_form_vars["requete"]=="modifyLayer"){
    set_error_handler(gErrorHandler);

    require_once("JSON.php");
    $debug.="<br>MODIFYLAYER<br>";
    //$debug.=print_r($http_form_vars,true);
    $_SESSION["mapobj"]=new mapobject;
    $_SESSION["mapobj"]->readParameters($_SESSION["tmpMF"]);

    sscanf($http_form_vars["layer"],"%d",$index);
    $oLayer=$_SESSION["mapobj"]->layers[$index];
    
    $tmp=str_replace("\\\"", "\"", $http_form_vars["data"]);
    $tmp=str_replace("\\'", "'", $tmp);
    $tmp=html_entity_decode($tmp);

    $json = new Services_JSON();
    $input = $json->decode($tmp);
    modifyLayer($_SESSION["mapobj"]->layers[$index],$input);
    $_SESSION["mapobj"]->writemapfile($_SESSION["tmpMF"]);
    if($error!=""){
      echo $error;
    }
    echo "|DEBUG - ".$debug;
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