<?php
/*  Copyright Géomatika, 2002-2007
 * Contact, infos : fx gamoy, fx.gamoy@geomatika.fr
 * -----------------------------------------------------------
 * This script is developed by geomatika.fr.  Visit us at www.geomatika.fr
 *
 */
 
 $endline="\n";
 $nbline=0;
 
  function parseLine($buffer){
    $trimbuf1=trim($buffer);
    $trimbuf=str_replace("'","",$trimbuf1);
    $testline=array();
    $testline[0]=$trimbuf;
    $index=strpos($trimbuf," ");
    if($index){
      $testline[0]=substr($trimbuf,0,$index);
      $testline[1]=substr($trimbuf,$index+1);
      $testline[1]=trim($testline[1]);
      //Si l'attribut est une chaine
      //On a qu'un seul attribut
      if($testline[1][0]=="\""){
        $testline[1]=str_replace('"','',$testline[1]);
        return $testline;
      }
      $i=1;
      $chaine=$testline[1];
      $flag=true;
      //Sinon on decoupe suivant les espaces
      while($flag){
        $index=strpos($chaine," ");
        //il reste plus d'un attribut
				//(exemple les couleurs)
        if($index){
          $testline[$i]=substr($chaine,0,$index);
          //Si c'est un nombre
          //TODO gerer les float
          $assign=sscanf($testline[$i],"%d",&$test)."  ";  	 
			    if($assign!=0) $testline[$i]=$test;
          $chaine=substr($chaine,$index+1);
          $chaine=trim($chaine);
          $i++;
        }else{ 
          //On est sur le dernier attribut
          $testline[$i]=$chaine;
          //$assign=sscanf($testline[$i],"%f",&$test2)."  "; 
          $assign=sscanf($testline[$i],"%f",&$test)."  "; 
          $test=(int)$test;
					//echo $chaine."  ".$test2."  ".$test."<br>"; 	 
			    if($assign!=0) $testline[$i]=$test;
			    $flag=false;
        }
      }
    }
    return $testline;
  }
 
 function readMetaData($fd,$obj){
   global $nbline;
    while(!feof($fd)){
      $nbline++;
      $buffer = fgets($fd);
      $trimbuf1=trim(str_replace('"','',$buffer));
      $trimbuf=str_replace("'","",$trimbuf1);
      $testline=explode(" ",$trimbuf);
      if (count($testline)==1){
        switch($testline[0]){
          case "END":return;break;
        }
      }else{

      }

    }
    return true;
  }
 

 //TODO
 function readLabel($fd,$label){
    while(!feof($fd)){
     global $nbline;
     $nbline++;
      $buffer = fgets($fd);
      $trimbuf1=trim(str_replace('"','',$buffer));
      $trimbuf=str_replace("'","",$trimbuf1);
      $testline=explode(" ",$trimbuf);
     
      switch($testline[0]){
        case "COLOR":
				  $color=new color_obj(array("",intval($testline[1]),intval($testline[2]),intval($testline[3])));
          $label->color=$color;break;
        case "BACKGROUNDCOLOR":
				  $color=new color_obj(array("",intval($testline[1]),intval($testline[2]),intval($testline[3])));
          $label->backgroundcolor=$color;break;
        case "BACKGROUNDSHADOWCOLOR":
				  $color=new color_obj(array("",intval($testline[1]),intval($testline[2]),intval($testline[3])));
          $label->backgroundshadowcolor=$color;break;
        case "SHADOWCOLOR":
				  $color=new color_obj(array("",intval($testline[1]),intval($testline[2]),intval($testline[3])));
          $label->shadowcolor=$color;break;
        case "OUTLINECOLOR":
				  $color=new color_obj(array("",intval($testline[1]),intval($testline[2]),intval($testline[3])));
          $label->outlinecolor=$color;break;
        case "OFFSET":
				  $label->offsetx=intval($testline[1]);
				  $label->offsety=intval($testline[2]);break;
				case "BACKGROUNDSHADOWSIZE":
				  $label->backgroundshadowsizex=intval($testline[1]);
				  $label->backgroundshadowsizey=intval($testline[2]);break;
				case "SHADOWSIZE":
				  $label->shadowsizex=intval($testline[1]);
				  $label->shadowsizey=intval($testline[2]);break;
        case "END":return true;break;
        default:
				  $assign=sscanf($testline[1],"%d",&$test)."  ";  
					if($assign!=0) $testline[1]=$test;
					$label->set(strtolower($testline[0]),$testline[1]);
      }
      
    }
  }


  
  
  
  function readLayer($fd,&$layer){
    //echo "<br>LAYER <br>";
   $numclass=0;
    while(!feof($fd)){
      global $nbline;
      $nbline++;
      $buffer = fgets($fd);
      $testline=parseLine($buffer);
      //print_r($testline);
      //echo $testline[0]."<br>";
      if (count($testline)==1){
       
        switch($testline[0]){
          case "METADATA":readMetaData($fd,&$layer);break;
          case "CLASS":
            //echo "<br>".$layer->name."<br>";
            $tclass=new class_obj($numclass);
            $restmp=readClass($fd,&$tclass);
            if(!$restmp){
              return false;
            }
            //print_r($tclass);
            $layer->class[$numclass]=$tclass;
            $numclass++;break;
          case "END":$layer->numclasses=$numclass;
					  return true;break;
        }
      }else{
        //echo $testline[0]."  ".$testline[1]."<br>";
        $layer->set(strtolower($testline[0]),$testline[1]);
      }

    }
    return false;
  }
  
   function readClass($fd,$class){
    $numstyles=0;
    while(!feof($fd)){
    global $nbline;
       $nbline++;
      $buffer = fgets($fd);
      $testline=parseLine($buffer);
      //echo "CLASS ".$testline[0]."  ".count($testline)."<br>";
      if (count($testline)==1){
        switch($testline[0]){
          case "METADATA":readMetaData($fd,&$class);break;
          case "LABEL":$label=new label_obj;
            readLabel($fd,&$label);
					  $class->label=$label;break;
					case "STYLE": 
            $tstyle=new style_obj;
            //echo $numstyles."<br>";
            readStyle($fd,&$tstyle);
            $class->style[$numstyles]=$tstyle;
            $numstyles++;break;
          case "END":$class->numstyles=$numstyles;return true;break;
        }
      }else{
        if($testline[0]=="COLOR"){
          global $error;
          //print_r($testline);
          $error="L'attribut color doit declare dans l'attribut style (ligne $nbline).";
          return false;
				}
        $class->set(strtolower($testline[0]),$testline[1]);
      }

    }
    
    return true;
  }
  
  
   function readStyle($fd,$style){
     while(!feof($fd)){
     global $nbline;
     $nbline++;
      $buffer = fgets($fd);
      $trimbuf1=trim(str_replace('"','',$buffer));
      $trimbuf=str_replace("'","",$trimbuf1);
      $testline=explode(" ",$trimbuf);
      //echo "STYLE ".$testline[0]."  ".count($testline)."<br>";
      if (count($testline)==1){
        switch($testline[0]){
          case "END":return;break;
        }
      }else{
        //echo $testline[0]."<br>";
        switch($testline[0]){
          case "COLOR":
            //echo "yep 1 ".print_r($testline,true)."<br>";
            $color=new color_obj(array("",intval($testline[1]),intval($testline[2]),intval($testline[3])));
            $style->color=$color;
						//echo "yep 2 ".print_r($style->color,true)."<br>";
						break;
            
          case "OUTLINECOLOR":
            $color=new color_obj(array("",intval($testline[1]),intval($testline[2]),intval($testline[3])));
            $style->outlinecolor=$color;break;
          case "OFFSET":
            $style->offsetx=intval($testline[1]);
            $style->offsety=intval($testline[2]);
            break;
          case "BACKGROUNDCOLOR":
            $color=new color_obj(array("",intval($testline[1]),intval($testline[2]),intval($testline[3])));
            $style->backgroundcolor=$color;break;
          default: 
				  $assign=sscanf($testline[1],"%d",&$test)."  ";  
			    if($assign!=0) $testline[1]=$test;
			  	$style->set(strtolower($testline[0]),$testline[1]);
        }
      }
    }
  }

class mcd
{
    /***  (private).*/
    var $prop;

  function mcd()
  {
	 $this->prop = array();
  }
	function connect()
	{
	//  $dbname = 'cdsm';
    $port = '5432';
    $host = 'localhost';
		$dbconn = pg_connect("host=$host port=$port dbname=isigeo34 user=postgres password=jtgacdt");
    if($dbconn)
     	return $dbconn;
   	else
    	return false;
  }

  function restoreprop($obj)
  {
    $testconn=$this->connect();
    $this->prop = array();
    if($testconn)
    {
      $query="select * from mapfile_reference WHERE (\"object\"='".$obj."')";
    //  echo $query;
      $result=pg_query($testconn,$query) or die("echec de la requete ".$query);
        while ($line=pg_fetch_assoc($result))
        {
          array_push($this->prop,$line);
          }
    }
	return true;
  }
  
  function getProperties($obj,$field){
    $testconn=$this->connect();
    //$this->prop = array();
    if($testconn)
    {
      $query="select * from mapfile_reference WHERE (\"object\"='".$obj."' AND nom='$field')";
      $result=pg_query($testconn,$query) or die("echec de la requete ".$query);
				if(pg_num_rows($result)>0){
				  return pg_fetch_assoc($result);
				}
    }
	  return NULL;
  }
  
  function getLevel($obj,$field){
    $prop=$this->getProperties($obj,$field);
    return $prop["level"];
  }
  
  function restorezl($obj)
  {
    $testconn=$this->connect();
    $this->prop = array();
    if($testconn)
    {
      $query="select * from mapfile_reference WHERE (\"object\"='".$obj."' AND (type='ZL' OR type='ZLS'))";
    //  echo $query;
      $result=pg_query($testconn,$query) or die("echec de la requete ".$query);
        while ($line=pg_fetch_assoc($result))
        {
          array_push($this->prop,$line);
          }
    }
	return true;
  }
  function toogleprop($prop)
  {
    $i=0;
    //echo print_r($this->prop,true)."\n";
   // echo $prop;
    foreach ($this->prop as $testprop)
    {
      if (isset($testprop["nom"]) && $testprop["nom"]==$prop)
      {
        $this->prop[$i]["vu"]=1;
       // echo $prop;
        break;
      }
    $i++;
    }
  }
  
  function hasZL($name){
    foreach ($this->prop as $testprop)
    { 
      if (isset($testprop["nom"]) && $testprop["nom"]==$name)
      {
        return $testprop;
      }
    }
    return false;
	}
	
	function getZL($prop){
	  $l=pg_connect("host=HOST port=PORT dbname=DBNAME user=USER password=PASSWORD");

    if($prop["type"]=="ZL"){
      $ZL=$prop["zl"];

      $select="SELECT * FROM \"$ZL\"";
      $result=pg_query($l,$select);
      $i=0;
			while(($line2=pg_fetch_assoc($result))){
        $return[$i]=$line2["libelle"];
        $i++;
      }
    }else if($prop["type"]=="ZLS"){
      $ZL=$prop["zl"];
      require_once("JSON.php");
      $json = new Services_JSON();
      //$tmp=split(",",$_SESSION[$ZL]);
      $tmp=$json->decode($_SESSION[$ZL]);
      for($i=0;$i<count($tmp);$i++){
        //$tmp2=split("~",$tmp[$i]);
        $return[$i]=$tmp[$i]->name;
      }
    }
    return $return;
	}
  
// end mcd class
}

class mapobject extends mcd
{
  var $layers;
  var $symbols;
  var $map;
  var $memobj;
  var $fileName;
    /***  (private).*/
  function mapobject()
  {
    $this->layers = array();
    $this->symbols = array();
    $this->memobj = array();
    $this->map = new mapfile_obj();
   // $this->map->layer=new layer_obj();
  }
  
  function loadSymbolSet(){
    
    if($this->map->symbolset!=""){
    
    }
    $base=dirname($this->fileName);
    //echo dirname($this->fileName)."<br>";
    //echo "<br>".$this->map->symbolset."<br>";
    $fd=@fopen($base."/".$this->map->symbolset,"r");
    if($fd==NULL){
      return;
    }
    while (!feof($fd))
    {
      $buffer = fgets($fd);
      $test=parseline($buffer);
      if(strtoupper($test[0])=="SYMBOL"){
        $symbol=new symbol_obj;
				$symbol->load($fd);
				$symbol->inmapfile=false;
				$this->symbols[count($this->symbols)]=$symbol;
				//print_r($symbol);
      }
      //echo print_r($test,true)."<br>";
    }
    fclose($fd);
  }
  
  function loadFontSet(){
    $this->font=array();
  }
  
  function moveLayerUp($index){
    $tmp=array_splice($this->layers,$index);
    $t1=array_pop($this->layers);
    $t2=array_shift($tmp);
    $tmp2=array($t2,$t1);
    $this->layers=array_merge($this->layers,$tmp2,$tmp);
  }
  
  function moveLayerDown($index){
    $tmp=array_splice($this->layers,$index+1);
    $t1=array_pop($this->layers);
    $t2=array_shift($tmp);
    $tmp2=array($t2,$t1);
    $this->layers=array_merge($this->layers,$tmp2,$tmp);
  }
  function deleteLayer($index){
    $tmp=array_splice($this->layers,$index,1);
  }
  
  function readParameters($szMapFile)
  {
    $this->fileName=$szMapFile;
    global $nbline;
// check to see if the map file exists
    if ( !file_exists($szMapFile) )
      return false;
    $fp=@fopen($szMapFile,"r");
    $adrobj="";
    if ($fp) {
    $level=0;
    $numlayers=0;
     $nbobj=0;
    while (!feof($fp))
    {
      $nbline++;
      $buffer = fgets($fp);
      $temp=fopen("test.txt","a");
      //fwrite($temp,print_r($testline,true));
      fwrite($temp,$buffer);
      fclose($temp);
      $testline=parseLine($buffer);
      //echo $testline[0]."<br>";
      
      if (count($testline)==1)
      {
        switch(trim(strtoupper($testline[0])))
        {
          case "MAP":
          $nbobj++;
               $obj="map";
               $adrobj="map";
               $adrclass=$this->map;
               $memobj[$nbobj]=$obj;
               array_push($this->memobj,array($obj,$level));
               $this->restoreprop($obj);
               break;
          case "OUTPUTFORMAT":
            $nbobj++;
            $level++;
            $adrobj="map|outputformat";

            $obj="outputformat";
            $memobj[$nbobj]=$obj;
            array_push($this->memobj,array($obj,$level));
            if(!isset($this->map->outputformat)){
             $this->map->outputformat=array();
            }
            $this->map->outputformat[count($this->map->outputformat)]=new outputformat_obj;
            $adrclass=$this->map->outputformat[count($this->map->outputformat)-1];
						$this->restoreprop($obj);
            break;

         case "LEGEND":
         $nbobj++;
         $level++;
           $obj="legend";
           $adrobj="map|legend";
           $adrclass=$this->map->legend;
           $memobj[$nbobj]=$obj;
           array_push($this->memobj,array($obj,$level));
           $this->map->legend=new legend_obj;
            $this->restoreprop($obj);
            break;
         case "REFERENCE":
           $this->map->reference=new reference_obj;
           if(!$this->map->reference->read($fp)){
             $error="Erreur lors de la lecture de l'objet reference\n".$error;
             return false;
           }

            //$this->restoreprop($obj);
            break;
         case "SCALEBAR":
         $nbobj++;
         $level++;
           $obj="scalebar";
           $adrobj="map|scalebar";
            $adrclass=$this->map->scalebar;
            $memobj[$nbobj]=$obj;
            array_push($this->memobj,array($obj,$level));
           $this->map->scalebar=new scalebar_obj;
            $this->restoreprop($obj);
            break;
         case "WEB":
         $nbobj++;
         $level++;
           $obj="web";
           $adrobj="map|web";
            $adrclass=$this->map->web;
            $memobj[$nbobj]=$obj;
            array_push($this->memobj,array($obj,$level));
           $this->map->web=new web_obj;
            $this->restoreprop($obj);
            break;
         case "LABEL":
         $nbobj++;
         $level++;
           $obj="label";
            $memobj[$nbobj]=$obj;
           // echo $adrobj;
            $tmpobj=explode("|",$adrobj);
            $oldobj=$tmpobj[count($tmpobj)-1];
           // echo $oldobj;
            array_push($this->memobj,array($obj,$level));
           if($oldobj=="scalebar")
           {
           $adrobj="map|scalebar|label";
           $this->map->scalebar->label=new label_obj;
           }
           else if($oldobj=="legend")
           {
              $adrobj="map|legend|label";
           $this->map->legend->label=new label_obj;
           }
            /*else if($oldobj=="class" && $numlayers>=0)
            {
               $adrobj="map|layer|class|label";
           $this->layers[$numlayers]->class[$numclass]->label=new label_obj;
           }  */
            $this->restoreprop($obj);
           break;
         case "SYMBOL":
            $symbol=new symbol_obj;
				    $symbol->load($fp);
				    $symbol->inmapfile=true;
				    $this->symbols[count($this->symbols)]=$symbol;
           break;
         case "LAYER":
           
            $tlayer=new layer_obj;
            //echo "yep";
            if(!readLayer($fp,$tlayer)){
              return false;
            }
            $this->layers[$numlayers]=$tlayer;
            $numlayers++;
           break;
          
           case "METADATA":
             readMetaData($fp,NULL);
             break;
           case "END":
             $level=$level-1;
             //echo "END $obj<br>";
             if($obj=="map")
             {
              $adrclass=$this->map;
              $this->map=$this->addmissing($obj,$adrclass);
             }
              else if($obj=="legend")
              {
             $adrclass=$this->map->legend;
             $this->map->legend=$this->addmissing($obj,$adrclass);
           }
            else if($obj=="web")
              {
             $adrclass=$this->map->web;
             $this->map->web=$this->addmissing($obj,$adrclass);
             }
             else if($obj=="outputformat")
             {
             $adrclass=$this->map->outputformat[count($this->map->outputformat)-1];
             $this->map->outputformat[count($this->map->outputformat)-1]=$this->addmissing($obj,$adrclass);
             }
             else if($obj=="reference")
             {
             $adrclass=$this->map->reference;
             $this->map->reference=$this->addmissing($obj,$adrclass);
             }
             else if($obj=="label")
             {
             //  echo $oldobj;
                if($oldobj=="scalebar")
                {
                $adrclass=$this->map->scalebar->label;
                $this->map->scalebar->label=$this->addmissing($obj,$adrclass);
                }
                else if($oldobj=="legend")
                {
                $adrclass=$this->map->legend->label;
                $this->map->legend->label=$this->addmissing($obj,$adrclass);
                }
             }

             $testlevel=explode("|",trim($adrobj));
             if(count($testlevel)>1)
             {
             $idlevel=count($testlevel)-2;
             $obj=$testlevel[$idlevel];

            }
            $q=0;
            $adrobj="";
            $nbrep=count($testlevel);
            foreach ($testlevel as $test)
            {
              if($q<$nbrep-1)
              {
                if($q==$nbrep-2)
                $adrobj.=$test;
                else
              $adrobj.=$test."|";
              }
              $q++;
            }


             break;
           case "":
             break;
             case "#":
             break;
          default:
         //   $level++;
         //si l'option n'est pas referencée
         // on avance jusqu'au END ki le concerne
            $obj="";
        }

       // echo $level."|".$memobj[$level]."<br>";
      }// if(count($testline)==1
      else if(count($testline)>1 && substr(trim($testline[0]),0,1)!="#")
      {
        //echo $testline[0]." $obj 2<br>";
        if($obj=="map")
        {
          switch(strtoupper($testline[0]))
          {
            case "EXTENT":
              $this->map->extent=new rect_obj($testline);
              break;
            case "IMAGECOLOR":
             $this->map->imagecolor=new color_obj($testline);
             break;
           case "SIZE":
            
             $this->map->width=$testline[1];
             $this->map->height=$testline[2];
             break;
           default:
            $tmpname=strtolower($testline[0]);
            $this->map->$tmpname=$testline[1];
         }
         //$this->test.=$testline[0]."|".$testline[1]."@@@";
       }
        else if($obj=="outputformat")
        {
          switch(strtoupper($testline[0]))
          {
            case "FORMATOPTION":
              $outputformat=$this->map->outputformat[count($this->map->outputformat)-1];
              if(!isset($outputformat->formatoption)) $outputformat->formatoption=array();
              $outputformat->formatoption[count($outputformat->formatoption)]=$testline[1];
              $this->map->outputformat[count($this->map->outputformat)-1]=$outputformat;
              break;
            default:
            $tmpname=strtolower($testline[0]);
            $this->map->outputformat[count($this->map->outputformat)-1]->$tmpname=$testline[1];
          }
        }
        else if($obj=="legend")
        {
          switch(strtoupper($testline[0]))
          {
            case "IMAGECOLOR":
              $this->map->legend->imagecolor=new color_obj($testline);
          break;
          case "OUTLINECOLOR":
              $this->map->legend->outlinecolor=new color_obj($testline);
          break;
        case "KEYSIZE":
             $this->map->legend->keysizex=$testline[1];
             $this->map->legend->keysizey=$testline[2];
             break;
           case "KEYSPACING":
             $this->map->legend->keyspacingx=$testline[1];
             $this->map->legend->keyspacingy=$testline[2];
             break;
           default:
            $assign=sscanf($testline[1],"%d",&$test)."  ";  
					 
						  if($assign!=0){
						    $testline[1]=$test;
						  }
						$tmpname=strtolower($testline[0]);
            $this->map->legend->$tmpname=$testline[1];
         }
         //$this->test.=$testline[0]."|".$testline[1]."@@@";
        }
        else if($obj=="reference")
        {
          switch(strtoupper($testline[0]))
          {
            case "EXTENT":
              $this->map->reference->extent=new rect_obj($testline);
              break;
            case "COLOR":
              $this->map->reference->color=new color_obj($testline);
              break;
            case "OUTLINECOLOR":
          $this->map->reference->outlinecolor=new color_obj($testline);
          break;
        case "SIZE":
          $this->map->reference->width=$testline[1];
          $this->map->reference->height=$testline[2];
          break;
        default:
          $assign=sscanf($testline[1],"%d",&$test)."  ";  
					 
						  if($assign!=0){
						    $testline[1]=$test;
						  }
          $tmpname=strtolower($testline[0]);
          $this->map->reference->$tmpname=$testline[1];
          }
         //$this->test.=$testline[0]."|".$testline[1]."@@@";
        }
        else if($obj=="scalebar")
        {
          switch(strtoupper($testline[0]))
          {
            case "IMAGECOLOR":
              $this->map->scalebar->imagecolor=new color_obj($testline);
              break;
            case "COLOR":
              $this->map->scalebar->color=new color_obj($testline);
              break;
        case "BACKGROUNDCOLOR":
          $this->map->scalebar->backgroundcolor=new color_obj($testline);
          break;
        case "OUTLINECOLOR":
          $this->map->scalebar->outlinecolor=new color_obj($testline);
          break;
        case "SIZE":
             $this->map->scalebar->width=$testline[1];
             $this->map->scalebar->height=$testline[2];
             break;
           default:
            $tmpname=strtolower($testline[0]);
            $this->map->scalebar->$tmpname=$testline[1];
         }
         //$this->test.=$testline[0]."|".$testline[1]."@@@";
        }
        else if($obj=="web")
        {
          //echo  "WEB ".$testline[0]."  ".$testline[1]."<br>";
          switch(strtoupper($testline[0]))
          {
            default:
              $tmpname=strtolower($testline[0]);
              $this->map->web->$tmpname=$testline[1];
          }
        }
        else if($obj=="label")
        {
          switch(strtoupper($testline[0]))
          {
        case "COLOR":
          if ($memobj[$nbobj-1]== "scalebar")
            $this->map->scalebar->label->color=new color_obj($testline);
          else if ($memobj[$nbobj-1]== "legend")
            $this->map->legend->label->color=new color_obj($testline);
          //else if ($memobj[$nbobj-1]== "class")
            //$this->layers[$numlayers]->class[$numclass]->label->color=new color_obj($testline);
					break;
        case "BACKGROUNDCOLOR":
          if ($memobj[$nbobj-1]== "scalebar")
            $this->map->scalebar->label->backgroundcolor=new color_obj($testline);
          else if ($memobj[$nbobj-1]== "legend")
            $this->map->legend->label->backgroundcolor=new color_obj($testline);
          //else if ($memobj[$nbobj-1]== "class")
            //$this->layers[$numlayers]->class[$numclass]->label->backgroundcolor=new color_obj($testline);
					break;
        case "BACKGROUNDSHADOWCOLOR":
          if ($memobj[$nbobj-1]== "scalebar")
            $this->map->scalebar->label->backgroundshadowcolor=new color_obj($testline);
          else if ($memobj[$nbobj-1]== "legend")
            $this->map->legend->label->backgroundshadowcolor=new color_obj($testline);
          //else if ($memobj[$nbobj-1]== "class")
            //$this->layers[$numlayers]->class[$numclass]->label->backgroundshadowcolor=new color_obj($testline);
					break;
        case "SHADOWCOLOR":
          if ($memobj[$nbobj-1]== "scalebar")
            $this->map->scalebar->label->shadowcolor=new color_obj($testline);
          else if ($memobj[$nbobj-1]== "legend")
            $this->map->legend->label->shadowcolor=new color_obj($testline);
          //else if ($memobj[$nbobj-1]== "class")
            //$this->layers[$numlayers]->class[$numclass]->label->shadowcolor=new color_obj($testline);
					break;
        case "OUTLINECOLOR":
          if ($memobj[$nbobj-1]== "scalebar")
            $this->map->scalebar->label->outlinecolor=new color_obj($testline);
          else if ($memobj[$nbobj-1]== "legend")
            $this->map->legend->label->outlinecolor=new color_obj($testline);
          //else if ($memobj[$nbobj-1]== "class")
            //$this->layers[$numlayers]->class[$numclass]->label->outlinecolor=new color_obj($testline);
            	break;
        case "OFFSET":
          if ($memobj[$nbobj-1]== "scalebar")
          {
             $this->map->scalebar->label->offsetx=$testline[1];
             $this->map->scalebar->label->offsety=$testline[2];
           }
           else if ($memobj[$nbobj-1]== "legend")
           {
             $this->map->legend->label->offsetx=intval($testline[1]);
             $this->map->legend->label->offsety=intval($testline[2]);
           }
           break;

           //  $this->scalebar->height=$testline[2]
         default:
           //echo $testline[0]."  ".$testline[1]."<br>";
          
					  $assign=sscanf($testline[1],"%d",&$test)."  ";  
					 
						  if($assign!=0){
						    $testline[1]=$test;
						  }
					
						//echo $testline[1]."<br><br>";
           $tmpname=strtolower($testline[0]);
       //$this->$memobj[$level-1]->label->$tmpname=$testline[1];
           if($memobj[$nbobj-1]=="scalebar")
             $this->map->scalebar->label->$tmpname=$testline[1];
           else if($memobj[$nbobj-1]=="legend")
             $this->map->legend->label->$tmpname=$testline[1];
           // else if($nbobj[$nbobj-1]=="class")
             //$this->layers[$numlayers]->class[$numclass]->label->$tmpname=$testline[1];
       }
         //$this->test.=$testline[0]."|".$testline[1]."@@@";
        }//label
        
        else if($obj=="symbol")
        {
          if(strtoupper($testline[0])=="NAME")
          {
            $line=array($numsymbols, $testline[1]);
            array_push($this->symbols,$line);
           // print_r($this->layers);
          }
        }

         $this->toogleprop(strtolower($testline[0]));
      } // if(count($testline)>1)

      // print_r($this->prop);
      } //while (!feof($fp)
      fclose($fp);
    }
 //   print_r($memobj);
// return success
    return true;
// end readMapFile function
  }
  function addmissing($obj,$adrclass)
  {
  //  $arval=get_object_vars($adrclass));
  //  print_r( $adrclass);
    $this->restoreprop($obj);
      //print_r( $this->prop);
    if($obj!="")
    {
    $testprop=array();
    foreach ($this->prop as $testprop)
    {
      $tmp=$testprop["nom"];
       $testval=$adrclass->$tmp;
     if($testval=="")
     {
          $adrclass->$tmp="titi";
      }

    /*  if (intval($testprop["vu"])==1)
      ;
      else
      {
         if($obj=="legend")
        echo $obj;
        $tmp=$testprop["nom"];
       if($obj=="map")
       $this->map->$tmp="";
       else if ($obj=="reference")
       $this->map->reference->$tmp="";
       else if ($obj=="legend")
       {
        // echo $tmp;
       $this->map->legend->$tmp="toto";
       }
       // $this->prop[$i]["vu"]=1;
      // echo $testprop["nom"]."\n";
        */

    } //foreach

    return $adrclass;
    }

  }
  function writemapfile($szMapFile)
  {
     //print_r($this->map);
     global $endline;
     $fp=@fopen($szMapFile,"w");
     fwrite($fp,"MAP\n");


     if(isset($this->map->name)) fwrite($fp,"  NAME \"".$this->map->name."\"".$endline);
     if(isset($this->map->extent))$this->map->extent->write($fp,"EXTENT");
     if(isset($this->map->fontset)) fwrite($fp,"  FONTSET \"".$this->map->fontset."\"".$endline);
     //if(isset($this->map->imagecolor)) fwrite($fp,"  IMAGECOLOR ".$this->map->imagecolor->red." ".$this->map->imagecolor->green." ".$this->map->imagecolor->blue.$endline);
     if(isset($this->map->imagecolor))$this->map->imagecolor->write($fp,"  ","imagecolor");
     if(isset($this->map->imagetype)) fwrite($fp,"  IMAGETYPE ".$this->map->imagetype.$endline);
     if(isset($this->map->interlace)) fwrite($fp,"  INTERLACE ".$this->map->interlace.$endline);
     if(isset($this->map->symbolset)) fwrite($fp,"  SYMBOLSET \"".$this->map->symbolset."\"".$endline);
     if(isset($this->map->shapepath)) fwrite($fp,"  SHAPEPATH \"".$this->map->shapepath."\"".$endline);
     if(isset($this->map->width) && $this->map->width!=""
		 && isset($this->map->height) && $this->map->height!="") {
		   fwrite($fp,"  SIZE ".$this->map->width." ".$this->map->height.$endline);
     }
		 if(isset($this->map->status)) fwrite($fp,"  STATUS ".$this->map->status.$endline);
     if(isset($this->map->units)) fwrite($fp,"  UNITS ".$this->map->units.$endline);
		 if(isset($this->map->outputformat)){
       //echo  "yep ".count($this->map->outputformat);
		   for($i=0;$i<count($this->map->outputformat);$i++){
		     $tmp=$this->map->outputformat[$i];
		     $tmp->write($fp);
		   }
		 }
     if(isset($this->map->legend))$this->map->legend->write($fp);
     if(!isset($this->map->reference)) echo "ERROR - NOT REFERENCE";
     else $this->map->reference->write($fp);
      
     if(!isset($this->map->web)) echo "ERROR - NOT WEB";
     else $this->map->web->write($fp);
     if(isset($this->map->scalebar)) $this->map->scalebar->write($fp);
     
     for($i=0;$i<count($this->symbols);$i++){
       //echo $i."<br>";
       if($this->symbols[$i]->inmapfile)
         $this->symbols[$i]->write($fp,"  ");
     }
     
     //echo "WRITE LAYER<br>";
     //echo  count($this->layers)."<br>";
     for($i=0;$i<count($this->layers);$i++){
       //echo $i."<br>";
       $this->layers[$i]->write($fp);
     }
     
     

     fwrite($fp,"END$endline");
     fclose($fp);


  }
	function getlayer($index)
	{
	  $layertmp=$this->layers[$index];
    $alayer= get_object_vars($layertmp);
	  $returnclass=new layer_obj;
    while (list($k, $v) = each($alayer))
	  {
	// echo $k;
	// echo $v;
	  if($k!="class" && $k!="style")
	  {
		  $returnclass->$k=$v;
	  }
	 
    }
    return $returnclass;
  }

  function newLayer(){
    $this->layers[count($this->layers)]=new layer_obj;
  }

// end mapobject class
}

/************************************************
 * Class définissant les fonctions communes
 * a chaque objet contenu dans le mapfile
 ************************************************/
class _mapfile_attribute
{
  function _mapfile_attribute(){
  
  }
  
  function set($name,$value)
  {
    $this->$name=$value;
  }
  
  function getInputField($id,$name,$size=30,$disabled=false){
	  $content="";
  	$type=get_class($this);
		//echo "<br>INTERFACE ".$type."<br>";
		$level=$_SESSION["mapobj"]->getLevel($type,$name);
		//echo "$name $level ".($level=='1')."<br>";
		if(!$level || $level=="1" || $level==LEVEL){
		  $_SESSION["mapobj"]->restorezl($type);
		  //echo "yep ".$_SESSION["mapobj"]->hasZL($name)."<br>";
      if(($prop=$_SESSION["mapobj"]->hasZL($name))){
        $ZL=$_SESSION["mapobj"]->getZL($prop);
        $content.=$this->getHTMLZl($id,$name,$ZL,$disabled);
      }else{
        $content.="<td><span class=\"data_label\">$name</span>:</td><td> <input  id=\"".$id."_$name\" size=\"$size\" type=\"text\" class=\"data_label\" value=\"".$this->$name."\"></td>";
      }
		}
    return $content;
	}
	
	function getHTMLZl($id,$name,$ZL,$d){
    $disabled="";
    if($d) $disabled="disabled=true";
    $content="";
    
  	$content.="<td><span class=\"data_label\">".$name.":</span></td>";
    $content.="<td><select ".$disabled." id=\"".$id."_".$name."\" class=\"data_label\">";
    for($i=0;$i<count($ZL);$i++){
      if($ZL[$i]==$this->$name)
        $content.="<option SELECTED value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
      else
        $content.="<option value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
    }
    $content.="</select></td>";
   
    return $content;  
  }
  
  function writeString($fp,$p,$attr){
    global $endline;
    if($this->$attr!="" && $this->$attr!=null)
      fwrite($fp,$p.strtoupper($attr)." \"".$this->$attr."\"".$endline);
  }
  
  function writeBoolean($fp,$p,$attr){
    global $endline;
    if($this->$attr!="" && $this->$attr!=null)
      fwrite($fp,$p.strtoupper($attr)." ".strtoupper($this->$attr).$endline);
  }
  
  function writeNumber($fp,$p,$attr){
    global $endline;
    if($this->$attr!="" && $this->$attr!=null)
      fwrite($fp,$p.strtoupper($attr)." ".$this->$attr.$endline);
  }

}
/*************************************
 * Différents objet du mapfile
 *************************************/
class mapfile_obj extends _mapfile_attribute
{
  function mapfile_obj()
  {
    $this->extent=new rect_obj(array());
  }
  
  function setExtent($minx,$miny,$maxx,$maxy){
    $this->extent->set("minx",$minx);
    $this->extent->set("miny",$miny);
    $this->extent->set("maxx",$maxx);
    $this->extent->set("maxy",$maxy);
  }
  
  function set($name,$value)
  {
    $this->$name=$value;
  }
  
  function initDefault(){
    $this->extent=new rect_obj(array("",0,0,0,0));
    $this->web=new web_obj;
    $this->web->initDefault();
    $this->legend=new legend_obj;
    $this->legend->initDefault();
    $this->scalebar=new scalebar_obj;
    $this->scalebar->initDefault();
    $this->reference=new reference_obj;
    $this->reference->initDefault();
    
		$this->outputformat=array();
    $o=new outputformat_obj;
    $o->initDefault();
    $this->outputformat[0]=$o;
  }
  
  function addOutputformat(){
    $o=new outputformat_obj;
    $o->initDefault();
    $this->outputformat[count($this->outputformat)]=$o;
    return $o;
  }
}

class label_obj extends _mapfile_attribute
{
  function label_obj()
  {
  }

  function initDefault(){
    $this->type="BITMAP";
    $this->size="SMALL";
		$this->backgroundshadowcolor=new color_obj(array());
    $this->shadowcolor=new color_obj(array());
    $this->outlinecolor=new color_obj(array());
    $this->backgroundcolor=new color_obj(array());
    $this->color=new color_obj(array());
  }

  function write($p,$fd){
    global $endline;
    fwrite($fd,$p."LABEL".$endline);
    $input=get_object_vars($this);
    $_SESSION["mapobj"]->restorezl("label_obj");
	  while (list($k, $v) = each($input)) {
	    //On recupere la valeur
      //$v=$this->$k;
      //echo $k."  ".$v."<br>";
      //Si le champ n'est pas une ressource
      //echo $k."  ".gettype($v)."<br>";//"  ".get_class($v)."<br>";
			if(gettype($v)!="resource" && $k!="offsetx" && $k!="offsety" 
			   &&  $k!="shadowsizex" && $k!="shadowsizey"
			   &&  $k!="backgroundshadowsizex" && $k!="backgroundshadowsizey")
      {
        if($k=="font"){
          if(strtoupper($this->type)=="TRUETYPE"){
             fwrite($fd,$p.strtoupper($k)." \"".$v."\"".$endline);
          }
         
        }
        else if($k=="angle")
				{
          fwrite($fd,$p."  ".strtoupper($k)." ".$v.$endline);
        }
				else if(gettype($v)=="string")
        {
          //Si il y a une zone de liste, c'est une constante donc sans guillemet
          //if($_SESSION["mapobj"]->hasZl($k)) fwrite($fd,$p."  ".strtoupper($k)." ".$v.$endline);
          //else fwrite($fd,$p."  ".strtoupper($k)." \"".$v."\"".$endline);
          $this->writeString($fd,$k,$v,$p);
				}
        else if(gettype($v)=="integer" || gettype($v)=="double")
        {
          fwrite($fd,$p."  ".strtoupper($k)." ".$v.$endline);
        }
				else if(gettype($v)=="object")
				{ 
				  
          /*if($k=="color")   fwrite($fd,$p."  COLOR ".$this->color->red." ".$this->color->green." ".$this->color->blue.$endline);
          if($k=="backgroundcolor")   fwrite($fd,$p."  BACKGROUNDCOLOR ".$this->backgroundcolor->red." ".$this->backgroundcolor->green." ".$this->backgroundcolor->blue.$endline);
          if($k=="backgroundshadowcolor")   fwrite($fd,$p."  BACKGROUNDSHADOWCOLOR ".$this->backgroundshadowcolor->red." ".$this->backgroundshadowcolor->green." ".$this->backgroundshadowcolor->blue.$endline);
          if($k=="shadowcolor")   fwrite($fd,$p."  SHADOWCOLOR ".$this->shadowcolor->red." ".$this->shadowcolor->green." ".$this->shadowcolor->blue.$endline);
          if($k=="outlinecolor")   fwrite($fd,$p."  OUTLINECOLOR ".$this->outlinecolor->red." ".$this->outlinecolor->green." ".$this->outlinecolor->blue.$endline);
          */
          if($k=="color")  $this->color->write($fd,$p."  ","color");
          if($k=="backgroundcolor")  $this->backgroundcolor->write($fd,$p."  ","backgroundcolor");
          if($k=="backgroundshadowcolor")  $this->backgroundshadowcolor->write($fd,$p."  ","backgroundshadowcolor");
          if($k=="shadowcolor")  $this->shadowcolor->write($fd,$p."  ","shadowcolor");
          if($k=="outlinecolor")  $this->outlinecolor->write($fd,$p."  ","outlinecolor");


        }
      }
    } //fin while général
  
    if(isset($this->shadowsizex))fwrite($fd,$p."  OFFSET ".$this->offsetx." ".$this->offsety.$endline);
    if(isset($this->backgroundshadowsizex)) fwrite($fd,$p."  BACKGROUNDSHADOWSIZE ".$this->backgroundshadowsizex." ".$this->backgroundshadowsizey.$endline);
    if(isset($this->shadowsizex)) fwrite($fd,$p."  SHADOWSIZE ".$this->shadowsizex." ".$this->shadowsizex.$endline);
    fwrite($fd,$p."END".$endline.$endline);
  }
  
  function writeString($fd,$k,$v,$p){
    global $endline;
    $_SESSION["mapobj"]->restorezl("label_obj");
    if($v!=NULL && $v!=""){
      //echo $k."  ".$_SESSION["mapobj"]->hasZl($k)."<br>";
      if($_SESSION["mapobj"]->hasZl($k)) fwrite($fd,$p."  ".strtoupper($k)." ".$v.$endline);
      else fwrite($fd,$p."  ".strtoupper($k)." \"".strtoupper($v)."\"".$endline);
      //fwrite($fd,$p."  $k \"".$v."\"".$endline);
    }
  }
  
  function getToggle($id,$display="block"){
	  $content="<div id=\"".$id."\" style=\"display:$display\" class=\"mapfile_object\"  onmouseover=\"$('".$id."').className='mapfile_objectover'\" onmouseout=\"$('".$id."').className='mapfile_object'\">";
    $content.="<table style=\"width:90%;margin:0px;padding:0px;\">";
    $content.="<tr><td class=\"data_label\">";
		$content.="<a href=\"javascript:toggleLabel('".$id."')\">&nbsp;label</a>";
    $content.="</td></tr></table></div>";
    return $content;
	}
	
	function getSheet($id){
	  $content="<div id=\"".$id."_attr\" style=\"display:none\" class=\"mapfile_object_attr\">";
  	$content.="<form id=\"".$id."_form\">";
		$content.="<br><table style=\"width:90%;margin:0px;padding:0px;\"><tr>";
  	$disabled="";
    $_SESSION["mapobj"]->restorezl("label_obj");
     $content.="<tr>";
	  $disabled=false;
    if($this->type=="BITMAP") $disabled=true;
    $content.=$this->getInputField($id,"font",10,$disabled);
    $content.=$this->getSizeInput($id);
    $content.=$this->getInputField($id,"type");
    $content.="</tr>";
    
		$content.="</tr></table>";
    $content.="<table style=\"width:90%;margin:0px;padding:0px;\"><tr>";
    $content.=$this->getInputField($id,"position");
    $content.=$this->getInputField($id,"buffer");
		$content.=$this->getInputField($id,"antialias");

		$content.="</tr></table>";

  	$content.="<table style=\"width:90%;margin:0px;padding:0px;\"><tr>";
    $content.="<td class=\"data_label\">minsize :</td><td><input id=\"".$id."_minsize\" size=3 type=\"text\" value=\"".$this->minsize."\" class=\"data_label\"></td>";
    $content.="<td class=\"data_label\">maxsize :</td><td><input id=\"".$id."_maxsize\" size=3 type=\"text\" value=\"".$this->maxsize."\" class=\"data_label\"></td>";
    $content.="<td class=\"data_label\">offsetx :</td><td><input id=\"".$id."_offsetx\" size=3 type=\"text\" value=\"".$this->offsetx."\" class=\"data_label\"></td>";
    $content.="<td class=\"data_label\">offsety :</td><td><input id=\"".$id."_offsety\" size=3 type=\"text\" value=\"".$this->offsety."\" class=\"data_label\"></td>";
		$content.="</tr></table>";
		
		$content.="<table style=\"width:90%;margin:0px;padding:0px;\"><tr>";
    $content.="<td class=\"data_label\">shadowsizex :</td><td><input id=\"".$id."_shadowsizex\" size=3 type=\"text\" value=\"".$this->shadowsizex."\" class=\"data_label\"></td>";
    $content.="<td class=\"data_label\">shadowsizey :</td><td><input id=\"".$id."_shadowsizey\" size=3 type=\"text\" value=\"".$this->shadowsizey."\" class=\"data_label\"></td>";
    $content.="<td class=\"data_label\">backgroundshadowsizex :</td><td><input id=\"".$id."_backgroundshadowsizex\" size=3 type=\"text\" value=\"".$this->backgroundshadowsizex."\" class=\"data_label\"></td>";
    $content.="<td class=\"data_label\">backgroundshadowsizey :</td><td><input id=\"".$id."_backgroundshadowsizey\" size=3 type=\"text\" value=\"".$this->backgroundshadowsizey."\" class=\"data_label\"></td>";
		$content.="</tr></table>";
		
		$content.="<table style=\"width:90%;margin:0px;padding:0px;\"><tr>";
    
    
	  $content.=$this->getInputField($id,"partials");
	  $content.=$this->getInputField($id,"force");
    $content.="<td class=\"data_label\">mindistance :</td><td><input id=\"".$id."_mindistance\" size=20 type=\"text\" value=\"".$this->mindistance."\" class=\"data_label\"></td>";
    $content.="<td class=\"data_label\">minfeaturesize :</td><td><input id=\"".$id."_minfeaturesize\" size=20 type=\"text\" value=\"".$this->minfeaturesize."\" class=\"data_label\"></td>";
		$content.="</tr></table>";
		$content.="<table style=\"width:90%;margin:0px;padding:0px;\"><tr>";
    //$content.="<td class=\"data_label\">angle :</td><td><input id=\"".$id."_angle\" size=3 type=\"text\" value=\"".$this->angle."\" class=\"data_label\"></td>";
    $content.=$this->getInputField($id,"angle");
		$content.=$this->getInputField($id,"wrap");
    $content.=$this->getInputField($id,"encoding");
 
		$content.="</tr></table>";
		$content.="<div class=\"arrondif\">";
		$content.="<table style=\"width:90%;margin:0px;padding:0px;\"><tr>";
    $content.="<td class=\"data_label\">color : <br>".getHTMLColor($id."_color",$this->color)."</td>";
    $content.="<td class=\"data_label\">outlinecolor : <br>".getHTMLColor($id."_outlinecolor",$this->outlinecolor)."</td>";
    $content.="<td class=\"data_label\">shadowcolor : <br>".getHTMLColor($id."_shadowcolor",$this->shadowcolor)."</td>";
   	$content.="</tr></table>";
   	$content.="<table style=\"width:90%;margin:5px;padding:5px;\"><tr>";
    $content.="<td class=\"data_label\">backgroundcolor : <br>".getHTMLColor($id."_backgroundcolor",$this->backgroundcolor)."</td>";
    $content.="<td class=\"data_label\">backgroundshadowcolor : <br>".getHTMLColor($id."_backgroundshadowcolor",$this->backgroundshadowcolor)."</td>";
   	$content.="</tr></table></div>";

  	$content.="<br></form></div>";
	  return $content;
	}
	
	function getSizeInput($id){
	  
		$content.="<td class=\"data_label\">size : </td>";
   	$_SESSION["mapobj"]->restorezl("label_obj");
		
    if(($prop=$_SESSION["mapobj"]->hasZL("size"))){
      $ZL=$_SESSION["mapobj"]->getZL($prop);
      $display="none";
			if($this->type=="BITMAP") {
		    $display="block";
		  }
      $content.="<td><select style=\"display:$display;\" id=\"".$id."_size_zl\" class=\"data_label\">";
      //echo "<br>SIE ".strtolower($this->size)."<br>";
			for($i=0;$i<count($ZL);$i++){
        //echo "<br>SIE ".$ZL[$i]." ".strtolower($this->size)."<br>";
			
        if($ZL[$i]==strtoupper($this->size))
          $content.="<option SELECTED value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
        else
          $content.="<option value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
      }
      $content.="</select></td>";
      
    }
    $v="";
    $display="none";
    if($this->type=="TRUETYPE") {
		  $v=$this->size;
		  $display="block";
		}
    $content.="<td><input style=\"display:$display;\" id=\"".$id."_size_px\" size=\"10\" type=\"text\" class=\"data_label\" value=\"".$v."\"></td>";
  
		return $content;
	}
  

}
class outputformat_obj extends _mapfile_attribute
{
  function outputformat_obj()
  {
    $this->formatoption=array();
  }

  function initDefault(){
    $this->name="pnggeo";
    $this->mimetype="image/png";
    $this->driver="GD/PNG";
    $this->extension="png";
    $this->imagemode="RGBA";
    $this->transparent="FALSE";
  }

  function write($fp){
     global $endline;
     fwrite($fp,"  OUTPUTFORMAT\n");
     $this->writeString($fp,"    ","name");
     $this->writeString($fp,"    ","mimetype");
     $this->writeString($fp,"    ","driver");
     $this->writeString($fp,"    ","extension");
     $this->writeString($fp,"    ","imagemode");
     $this->writeBoolean($fp,"    ","transparent");
     
     if(isset($this->formatoption)){
       for($i=0;$i<count($this->formatoption);$i++){
         fwrite($fp,"    FORMATOPTION \"".$this->formatoption[$i]."\"".$endline);
       }
     }
     
     //fwrite($fp,"    FORMATOPTION \"".$this->map->shapepath."\"".$endline);
     //fwrite($fp,"    FORMATOPTION \"".$this->);
     fwrite($fp,"  END$endline$endline");
  }
  
  function writeString($fp,$p,$name){
    global $endline;
    if(isset($this->$name))
      fwrite($fp,$p.strtoupper($name)." \"".$this->$name."\"".$endline);
  }
  
  function writeBoolean($fp,$p,$name){
    global $endline;
    if(isset($this->$name))
      fwrite($fp,$p.strtoupper($name)." ".strtoupper($this->$name).$endline);
  }
  
  function getInputField($id,$name,$size=30){
	  $content="";
  	$type=get_class($this);
		//echo "<br>INTERFACE ".$type."<br>";
		$level=$_SESSION["mapobj"]->getLevel($type,$name);
		//echo "$name $level ".($level=='1')."<br>";
		if(!$level || $level=="1" || $level==LEVEL){
		  $_SESSION["mapobj"]->restorezl($type);
		  //echo "yep ".$_SESSION["mapobj"]->hasZL($name)."<br>";
      if(($prop=$_SESSION["mapobj"]->hasZL($name))){
        $ZL=$_SESSION["mapobj"]->getZL($prop);
        $content.=$this->getHTMLZl($id,$name,$ZL);
      }else{
        $content.="<td> <input  id=\"".$id."_$name\" size=\"$size\" type=\"text\" class=\"data_label\" value=\"".$this->$name."\"></td>";
      }
		}
    return $content;
	}
	
	function getHTMLZl($id,$name,$ZL){
    $disabled="";
    if($d) $disabled="disabled=true";
    $content="";

  	//$content.="<td><span class=\"data_label\">".$name.":</span></td>";
    $content.="<td><select ".$disabled." id=\"".$id."_".$name."\" class=\"data_label\">";
    for($i=0;$i<count($ZL);$i++){
      if($ZL[$i]==$this->$name)
        $content.="<option SELECTED value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
      else
        $content.="<option value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
    }
    $content.="</select></td>";

    return $content;
  }
  
}
class legend_obj extends _mapfile_attribute
{

  
  function legend_obj()
  {
  }
  
  function initDefault(){
    $this->label=new label_obj;
    //$this->label->initDefault();
    $this->imagecolor=new color_obj(array());
    $this->imagecolor->initDefault();
    $this->outlinecolor=new color_obj(array());
    $this->outlinecolor->initDefault();
  }

  function write($fp){
     global $endline;
     fwrite($fp,"  LEGEND\n");
     if(isset($this->imagecolor))$this->imagecolor->write($fp,"    ","imagecolor");
     if(isset($this->outlinecolor))$this->outlinecolor->write($fp,"    ","outlinecolor");
		 $this->writeBoolean($fp,"    ","interlace");
		 
		 if($this->keysizex!=""  && $this->keysizex!=null
		 && $this->keysizey!=""  && $this->keysizey!=null)
		   fwrite($fp,"    KEYSIZE ".$this->keysizex." ".$this->keysizey.$endline);
		   
		 if($this->keyspacingx!=""  && $this->keyspacingx!=null
		 && $this->keyspacingy!=""  && $this->keyspacingy!=null)  
       fwrite($fp,"    KEYSPACING ".$this->keyspacingx." ".$this->keyspacingy.$endline);
     
		 //Ecrire le label
     if(isset($this->label)) $this->label->write("    ",$fp);
     
     if($this->position!=""  && $this->position!=null)fwrite($fp,"    POSITION ".$this->position.$endline);
     $this->writeBoolean($fp,"    ","status");
     $this->writeBoolean($fp,"    ","transparent");
     fwrite($fp,"    TEMPLATE \"".$this->template."\"".$endline);
     fwrite($fp,"  END$endline$endline");
  }
  
  function set($name,$value)
  {
    $this->$name=$value;
  }
 
}
class reference_obj  extends _mapfile_attribute
{
  function reference_obj()
  {
    $this->extent=new rect_obj(array("",0,0,0,0));
  }

  function initDefault(){
     $this->image="";
     $this->width=160;
     $this->height=77;
     $this->extent=new rect_obj(array("",0,0,0,0));
  }

  function read($fd){
    while(!feof($fd)){
      global $nbline;
      $nbline++;
      $buffer = fgets($fd);
      $testline=parseLine($buffer);
      //print_r($testline);
      //echo $testline[0]."<br>";
      if (count($testline)==1){
        switch($testline[0]){
          case "END":$layer->numclasses=$numclass;
					  return true;break;
        }
      }else{
        //echo "READ REFERENCE ".$testline[0]."  ".$testline[1]."<br>";
        if(strtolower($testline[0])=="size"){
          $this->set("width",$testline[1]);
          $this->set("height",$testline[2]);
        }else if(strtolower($testline[0])=="extent"){
          $this->setExtent($testline[1],$testline[2],$testline[3],$testline[4]);
        }else if(strtolower($testline[0])=="color")
          $this->setColor($testline[1],$testline[2],$testline[3]);
        else if(strtolower($testline[0])=="outlinecolor")
          $this->setOutlineColor($testline[1],$testline[2],$testline[3]);
        else
          $this->set(strtolower($testline[0]),$testline[1]);
      }

    }
    //print_r($this);
    return false;
  }

  function setExtent($minx,$miny,$maxx,$maxy){
    $this->extent->set("minx",$minx);
    $this->extent->set("miny",$miny);
    $this->extent->set("maxx",$maxx);
    $this->extent->set("maxy",$maxy);
  }
  
  function setColor($r,$g,$b){
    $this->color=new color_obj(array("",$r,$g,$b));
  }
  
  function setOutlineColor($r,$g,$b){
    $this->outlinecolor=new color_obj(array("",$r,$g,$b));
  }

  function write($fd){
    global $endline;
    global $debug;
    $debug.="REFERENCE<br>";
    fwrite($fd,"  REFERENCE".$endline);
    $input=get_object_vars($this);
    $_SESSION["mapobj"]->restorezl("reference_obj");
	  while (list($k, $v) = each($input)) {
	    //On recupere la valeur
      //$v=$this->$k;
      //echo $k."  ".$v."<br>";
      //Si le champ n'est pas une ressource
      //echo gettype($v)."<br>";//"  ".get_class($v)."<br>";
      if($k!="width" && $k!="height")
			if(gettype($v)!="resource")
      {
        $debug.=$k."  ".gettype($v)."<br>";
        if(gettype($v)=="string")
        {
          if($_SESSION["mapobj"]->hasZl($k)) fwrite($fd,"    ".strtoupper($k)." ".$v.$endline);
          else fwrite($fd,"    ".strtoupper($k)." \"".$v."\"".$endline);
          //fwrite($fd,"    ".strtoupper($k)." \"".$v."\"".$endline);
        }
        else if(gettype($v)=="integer" || gettype($v)=="double")
        {
          fwrite($fd,"    ".strtoupper($k)." ".$v.$endline);
        }
				else if(gettype($v)=="object")
				{
          //if($k=="color")   fwrite($fd,"    COLOR ".$this->color->red." ".$this->color->green." ".$this->color->blue.$endline);
          if($k=="color")   $this->color->write($fd,"    ","color");
          if($k=="outlinecolor")   $this->outlinecolor->write($fd,"    ","outlinecolor");
          if($k=="extent")   $this->extent->write($fd,"EXTENT");
        }
      }
    } //fin while général
    fwrite($fd,"  SIZE ".$this->width." ".$this->height.$endline);
    fwrite($fd,"  END".$endline.$endline);
  }
}
class scalebar_obj extends _mapfile_attribute
{
  function scalebar_obj()
  {
  }
  
  function initDefault(){
    $this->width=150;
    $this->height=3;
    $this->intervals=3;
    $this->color=new color_obj(array("",-1,-1,-1));
    $this->outlinecolor=new color_obj(array("",-1,-1,-1));
    $this->backgroundcolor=new color_obj(array("",-1,-1,-1));
    $this->imagecolor=new color_obj(array("",-1,-1,-1));
    $this->label=new label_obj;
    
  }
  
   function write($fp){
     global $endline;
     fwrite($fp,"  SCALEBAR\n");
     $this->writeBoolean($fp,"    ","status");
     $this->writeBoolean($fp,"    ","units");
     if(isset($this->imagecolor))$this->imagecolor->write($fp,"    ","imagecolor");
     if(isset($this->outlinecolor))$this->outlinecolor->write($fp,"    ","outlinecolor");
		 if(isset($this->backgroundcolor))$this->backgroundcolor->write($fp,"    ","backgroundcolor");
		 if(isset($this->color))$this->color->write($fp,"    ","color");
		 
		 $this->writeBoolean($fp,"    ","interlace");
		 if($this->width!=""  && $this->width!=null
		 && $this->height!=""  && $this->height!=null)  
       fwrite($fp,"    SIZE ".$this->width." ".$this->height.$endline);
     
		 //Ecriture du label
     if(isset($this->label)) $this->label->write("    ",$fp);
     
     $this->writeBoolean($fp,"    ","intervals");
     fwrite($fp,"  END$endline$endline");
  }
}
class web_obj extends _mapfile_attribute
{
  function web_obj()
  {
  }

  function initDefault(){
    $this->imagepath="d:/mapimage/";
    $this->imageurl="/tmp/";
    $this->log="/tmp/log.txt";
    $this->minscale=500;
    $this->maxscale=400000;
    $this->queryformat="text/html";
    
  }
  function write($fd){
    global $endline;
    fwrite($fd,"  WEB\n");
    fwrite($fd,"    IMAGEPATH \"".$this->imagepath."\"".$endline);
    fwrite($fd,"    IMAGEURL \"".$this->imageurl."\"".$endline);
    fwrite($fd,"    LOG \"".$this->log."\"".$endline);
    fwrite($fd,"    MINSCALE ".$this->minscale.$endline);
    fwrite($fd,"    MAXSCALE ".$this->maxscale.$endline);
    fwrite($fd,"    QUERYFORMAT \"".$this->queryformat."\"".$endline);
    fwrite($fd,"  END$endline$endline");
  }
}
class layer_obj extends _mapfile_attribute
{
  //var $numclass;
  //var $class;
  
  function layer_obj()
  {
    $this->class=array();

  }
  function addlayer($champs,$prop)
  {
    $this->$champs=$prop;
  }
  
  function get($name)
  {
    return $this->$name;
  }
  
  function newClass(){
    $this->class[count($this->class)]=new class_obj;
  }
  
  function getclass($index)
	{
	  //echo "GETCLASS ".$index."<br>";
	  $classtmp=$this->class[$index];
	  //print_r( $classtmp);
    $alayer= get_object_vars($classtmp);
	  $returnclass=new class_obj();
    while (list($k, $v) = each($alayer))
	  {
	    if($k!="style")
	    {
		    $returnclass->$k=$v;
	    }
    }
    return $returnclass;
  }
  
  function write($fd){
    global $endline;
    fwrite($fd,"  LAYER".$endline);
    $input=get_object_vars($this);
    $_SESSION["mapobj"]->restorezl("layer_obj");
    //echo "<br>".$this->name."<br>";
	  while (list($k, $v) = each($input)) {
      //echo "LAYER ".$k."  ".gettype($v)."<br>";
      
      if($k!="numclasses")
			if(gettype($v)!="resource")
      {
        if(gettype($v)=="string")
        {
        
         $this->writeString($fd,$k,$v,"  ");
        
         // fwrite($fd,"    ".strtoupper($k)." \"".$v."\"".$endline);
        }
        else if(gettype($v)=="integer" || gettype($v)=="double")
        {
          fwrite($fd,"    ".strtoupper($k)." ".$v.$endline);
        }
				else if(gettype($v)=="object")
				{
          if($k=="color")  $this->color->write($fd,"      ","color");
          if($k=="outlinecolor")  $this->outlinecolor->write($fd,"      ","outlinecolor");
        }
        
      }
    } //fin while général
    

    //ecriture des classes
    //echo "NUM CLASSE $k".count($this->class)."<br>";
    //echo "<br>".$this->name."<br>";
		for($i=0;$i<count($this->class);$i++){
      //echo $i."<br>";
      $this->class[$i]->write($fd,$this->classitem);
    }
         
    fwrite($fd,"  END".$endline.$endline);
  }
  
  function writeString($fd,$k,$v,$p){
    global $endline;
    $_SESSION["mapobj"]->restorezl("layer_obj");
    if($v!=NULL && $v!=""){
      //echo $k."  ".$_SESSION["mapobj"]->hasZl($k)."<br>";
      if($_SESSION["mapobj"]->hasZl($k)) fwrite($fd,$p."  ".strtoupper($k)." ".$v.$endline);
      else fwrite($fd,$p."  ".strtoupper($k)." \"".$v."\"".$endline);
      //fwrite($fd,$p."  $k \"".$v."\"".$endline);
    }
  }
  function deleteClass($index){
    //$this->style[count($this->style)]=new style_obj;
    //echo "<br>YEP $index ".print_r($this->style,true)."<br>";
    array_splice($this->class,$index,1);
    //print_r($this->style);
  }
 
}
class class_obj extends _mapfile_attribute
{
  function class_obj($i=0)
  {
    $this->index=$i;
    $this->status="ON";
    $this->style=array();
    $this->label=null;
    //$this->label=new label_obj;
  }

  function set($name,$value)
  {
    $this->$name=$value;
  }

  function newStyle(){
    $this->style[count($this->style)]=new style_obj;
  }
  
  function deleteStyle($index){
    //$this->style[count($this->style)]=new style_obj;
    //echo "<br>YEP $index ".print_r($this->style,true)."<br>";
    array_splice($this->style,$index,1);
    //print_r($this->style);
  }

  function getstyle($index)
	{
	  $styletmp=$this->style[$index];
    return $styletmp;
	}
	
	function write($fd,$classitem=""){
    global $endline;
    $p="    ";
    fwrite($fd,$p."CLASS".$endline);
    $input=get_object_vars($this);

	  while (list($k, $v) = each($input)) {
      //echo "LAYER ".$k."  ".gettype($v)."<br>";
      //echo "CLASS ".$k."  ".$v."  ".gettype($v)."<br>";
      if($k!="index" && $k!="numstyles")
			if(gettype($v)!="resource" && $k!="expression")
      {
        if($k=="status"){
          $this->writeBoolean($fd,$p."  ","status");
        }else if(gettype($v)=="string")
          $this->writeString($fd,$k,$v,$p);
        else if(gettype($v)=="integer" || gettype($v)=="double")
        {
          $this->writeNumber($fd,$p."  ",$k);
        }
				else if(gettype($v)=="object")
				{
          if($this->label!=null)
            $this->label->write("      ",$fd);
        }
        else if(gettype($v)=="array")
				{
           //ecriture des style
           //echo "CLASS ".$i."<br>";
           for($i=0;$i<count($this->style);$i++){
             //echo $i."<br>";
             $this->style[$i]->write($fd);
           }
        }
      }
    } //fin while général
    if(isset($classitem) && $classitem!=""){
      if(!isset($this->expression)) $this->expression="";
      if($this->expression[0]=="/" || $this->expression[0]=="("){
        fwrite($fd,$p."  EXPRESSION ".strtoupper($this->expression).$endline);
		  }else{
			  fwrite($fd,$p."  EXPRESSION \"".strtoupper($this->expression)."\"".$endline);
			}
    }
    fwrite($fd,"    END".$endline.$endline);
  }
  
  function writeString($fd,$k,$v,$p){
    global $endline;
    if($v!=NULL && $v!="")
      fwrite($fd,$p."  ".strtoupper($k)." \"".$v."\"".$endline);
  }

}
class style_obj extends _mapfile_attribute
{
  function style_obj()
  {

  }
  
  function write($fd){
    global $endline;
    $p="      ";
    fwrite($fd,$p."STYLE".$endline);
    $input=get_object_vars($this);

	  while (list($k, $v) = each($input)) {
      //echo "STYLE ".$k."  ".gettype($v)."<br>";
      //TODO symbol
      if($k!="offsetx" && $k!="offsety"){
        if($k=="symbol")
			  {
			    if($v!="" && $v!=NULL){
				    $assign=sscanf($v,"%d",&$test)."  ";  	 
			      if($assign==0) 
					    fwrite($fd,$p."  ".strtoupper($k)." \"".$v."\"".$endline); 
				    else //c'est l'index du symbol
				      fwrite($fd,$p."  ".strtoupper($k)." ".$v.$endline);
				  }
			  }
			  else if(gettype($v)!="resource")
        {
          if(gettype($v)=="string")
          {
            if($v!="" && $v!=NULL) fwrite($fd,$p."  ".strtoupper($k)." \"".$v."\"".$endline);
          }
          else if(gettype($v)=="integer" || gettype($v)=="double")
          {
            if($v!=NULL) fwrite($fd,$p."  ".strtoupper($k)." ".$v.$endline);
          }
				  else if(gettype($v)=="object")
				  {
				    
            if($k=="color"  && isset($this->color)){
             // echo $k."  ".print_r($this->color,true)."<br>";
						  $this->color->write($fd,"      ","color");
						}else if($k=="outlinecolor"  && isset($this->outlinecolor)){
						  //echo $k."  ".print_r($this->outlinecolor,true)."<br>";
						  $this->outlinecolor->write($fd,"      ","outlinecolor");
						}else if($k=="backgroundcolor" && isset($this->backgroundcolor)){
						 //echo $k."  ".print_r($this->backgroundcolor,true)."<br>";
						  $this->backgroundcolor->write($fd,"      ","backgroundcolor");
						}
          }
        }
      }
    } //fin while général
    if($this->offsetx){
		  fwrite($fd,$p."OFFSET ".$this->offsetx." ".$this->offsety.$endline);
		}
    fwrite($fd,$p."END".$endline.$endline);
  }

  function getHTMLZl($id,$name,$ZL,$d){
    $disabled="";
    if($d) $disabled="disabled=true";
    $content="";
  	$content.="<td><span class=\"data_label\">".$name.":</span></td>";
    $content.="<td><select ".$disabled." id=\"".$id."_".$name."\" class=\"data_label\">";
    $content.="<option value=\"0\"></option>";
		for($i=0;$i<count($ZL);$i++){
      //echo $ZL[$i]."  ".$this->$name."  ".$i."  ".(!strcmp($ZL[$i],$this->$name))."  ".($this->$name==$i)."<br>";
      //On test sur l'index et le nom
			if(!strcmp($ZL[$i],$this->$name)  || $this->$name==($i+1)){
        $content.="<option SELECTED value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
      }else{
        $content.="<option value=\"".$ZL[$i]."\">".$ZL[$i]."</option>";
      }
		}
    $content.="</select></td>";
   
    return $content;  
  }

}


class rect_obj
{
   var $minx;
   var $miny;
   var $maxx;
   var $maxy;

  function rect_obj($valobj)
  {
    $this->minx=$valobj[1];
    $this->miny=$valobj[2];
    $this->maxx=$valobj[3];
    $this->maxy=$valobj[4];
  }
  
  function getSheet($id){
    $content="<div class=\"arrondif\"><span class=\"data_label\"><b>Extent</b></span><br>";
    $content.="<table><tr>";
		$content.="<td><span class=\"data_label\"> minx: </span></td><td><input size=15 id=\"".$id."_extent_minx\" class=\"data_label\" type=\"text\" value=\"".$this->minx."\"></td>";
    $content.="<td><span class=\"data_label\"> miny:</span></td><td> <input size=15  id=\"".$id."_extent_miny\" class=\"data_label\" type=\"text\" value=\"".$this->miny."\"></td>";
    $content.="<td><span class=\"data_label\"> maxx:</span></td><td> <input size=15  id=\"".$id."_extent_maxx\" class=\"data_label\" type=\"text\" value=\"".$this->maxx."\"></td>";
    $content.="<td><span class=\"data_label\"> maxy:</span></td><td> <input size=15  id=\"".$id."_extent_maxy\" class=\"data_label\" type=\"text\" value=\"".$this->maxy."\"></td>";
    $content.="</tr></table>";
    $content.="</div>";
  	return $content;
  }
  
  function set($name,$value)
  {
    $this->$name=$value;
  }
  
  function setExtent($minx,$miny,$maxx,$maxy)
  {
    $this->minx=$minx;
    $this->miny=$miny;
    $this->maxx=$maxx;
    $this->maxy=$maxy;
  }
  
  function write($fd,$libelle){
    global $endline;
    if($this->minx=="") $this->minx=0;
    if($this->miny=="") $this->miny=0;
    if($this->maxx=="") $this->maxx=0;
    if($this->maxy=="") $this->maxy=0;
		fwrite($fd,"  ".strtoupper($libelle)." ".$this->minx." ".$this->miny." ".$this->maxx." ".$this->maxy.$endline);
  }
}
class color_obj
{
   var $red;
   var $green;
   var $blue;

  function color_obj($valobj)
  {
    $this->red = $valobj[1];
    $this->green = $valobj[2];
    $this->blue = $valobj[3];
  }
  
  function initDefault()
  {
    $this->red = -1;
    $this->green = -1;
    $this->blue = -1;
  }
  
  function set($name,$value)
  {
    $this->$name=$value;
  }
  
  function setRGB($r,$g,$b)
  {
    $this->red = $r;
    $this->green = $g;
    $this->blue = $b;
  }
  
  function write($fd,$p,$name){
    global $endline;
    global $debug;

    $c1=sscanf($this->red,"%d");
    $c2=sscanf($this->green,"%d");
    $c3=sscanf($this->blue,"%d");
    $debug.="$name ".$c1[0]."  ".$c2[0]."  ".$c3[0]."<br>";
		//if($c1[0]!=-1 && $c2[0]!=-1 && $c3[0]!=-1){
      $debug.="write $name ".$this->red."  ".$this->green."  ".$this->blue."<br>";
      fwrite($fd,$p.strtoupper($name)." ".$c1[0]." ".$c2[0]." ".$c3[0].$endline);
    //}
  }
}

class symbol_obj
{
  function symbol_obj(){
    $this->points=array();
    $this->style=array();
  }
  
  function load($fd){
    global $error;
    global $nbline;
    while (!feof($fd))
    {
      $nbline++;
      $buffer = fgets($fd);
      $test=parseline($buffer);
			switch(strtolower($test[0])){
        //case "":break;
        case "end":return true;
				case "name":
				  $this->name=$test[1];
          break;
        case "image":
				  $this->image=$test[1];
          break;
        case "type":
				  $this->type=$test[1];
          break;
        case "style":
          $this->readStyle($fd);
          break;
        case "transparent":
           $this->transparent=$test[1];
          break;
        case "points":
          $this->readPoint($fd);
          break;
        default:break;
      }
      //echo print_r($test,true)."<br>";
    }
    $error="Erreur de syntaxe dans le fichier des symbol";
    return false;
  }
  
  function readPoint($fd){
    global $error;
    global $nbline;
    while (!feof($fd))
    {
      $buffer = fgets($fd);
      $nbline++;
      $test=parseline($buffer);
      switch(strtolower($test[0])){
        case "end":return true;
        default:
				$this->points[count($this->points)]=array();
				$this->points[count($this->points)-1][0]=$test[0];
				$this->points[count($this->points)-1][1]=$test[1];
				break;
      }
      //echo print_r($test,true)."<br>";
    }
    $error="Erreur de syntaxe dans le fichier des symbol";
    return false;
  }
  
  function readStyle($fd){
    global $error;
    global $nbline;
    
    while (!feof($fd))
    {
      $buffer = fgets($fd);
      $nbline++;
      $test=parseline($buffer);
      switch(strtolower($test[0])){
        case "end":return true;
        default:
        for($i=0;$i<count($test);$i++){
          $this->style[$i]=$test[$i];  
        }
				break;
      }
      //echo print_r($test,true)."<br>";
    }
    $error="Erreur de syntaxe dans le fichier des symbol";
    return false;
  }
  
  function write($fd,$p){
    global $endline;
    fwrite($fd,$p."SYMBOL".$endline);  
		if(isset($this->name)) fwrite($fd,$p."  NAME \"".$this->name."\"".$endline);      
    if(isset($this->type)) fwrite($fd,$p."  TYPE ".strtoupper($this->type)."".$endline);      
    if(isset($this->filled)) fwrite($fd,$p."  FILLED \"".strtoupper($this->filled)."\"".$endline);      
    if(isset($this->transparent)) fwrite($fd,$p."  TRANSPARENT ".strtoupper($this->transparent).$endline);      
    if(isset($this->image)) fwrite($fd,$p."  IMAGE \"".$this->image."\"".$endline);      
    
    //ecriture des points
    if(count($this->points)>0)
      fwrite($fd,$p."  POINTS".$endline);      
		for($i=0;$i<count($this->points);$i++){
      fwrite($fd,$p."    ".$this->points[$i][0]."  ".$this->points[$i][1].$endline);    
    }
    if(count($this->points)>0)
      fwrite($fd,$p."  END".$endline);   
    
		//ecriture du style 
		if(count($this->style)>0){
      fwrite($fd,$p."  STYLE".$endline.$p." "); 
		}     
		for($i=0;$i<count($this->style);$i++){
      fwrite($fd," ".$this->style[$i]);    
    }
    if(count($this->points)>0){
      fwrite($fd,$endline.$p."  END".$endline); 
		}   
		  
		fwrite($fd,$p."END".$endline);    
  }
}

?>