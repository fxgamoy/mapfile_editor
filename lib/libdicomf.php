<?php
 
   
  function hasZL($level,$name){
	  $l=pg_connect("host=localhost port=5432 dbname=isigeo34 user=postgres password=jtgacdt");
    $select="SELECT * FROM mapfile_reference WHERE \"object\"='".$level."' AND nom='".$name."'";
    //echo $select."  ";
		$result=pg_query($l,$select);
    if(pg_num_rows($result)>0){
      $line=pg_fetch_assoc($result);
      if($line["type"]=="ZL"  || $line["type"]=="ZLS"){
        return true;
      }
    }
    return false;
	}
	
	function getZLValue($level,$name,$libelle){
	  $l=pg_connect("host=localhost port=5432 dbname=isigeo34 user=postgres password=jtgacdt");
    $select="SELECT * FROM mapfile_reference WHERE \"object\"='".$level."' AND nom='".$name."'";
    $result=pg_query($l,$select);
    if(pg_num_rows($result)>0){
      $line=pg_fetch_assoc($result);
      if($line["type"]=="ZL"){
        $ZL=$line["zl"];
        
        $select="SELECT * FROM \"$ZL\" WHERE libelle='$libelle'";
        //echo "<br>".$select."  ";
        $result=pg_query($l,$select);
        $line2=pg_fetch_assoc($result);
        return $line2["value"];
      }else if($line["type"]=="ZLS"){
        $ZL=$line["zl"];
        $tmp=split(",",$_SESSION[$ZL]);
        for($i=0;$i<count($tmp);$i++){
          $tmp2=split("~",$tmp[$i]);
          if($libelle==$tmp2[0]){
            return $tmp2[1];
          }
        }
      }
    }
    return false;
	}
	
	function getZLLibelle($level,$name,$value){
	  $l=pg_connect("host=localhost port=5432 dbname=isigeo34 user=postgres password=jtgacdt");
    $select="SELECT * FROM mapfile_reference WHERE \"object\"='".$level."' AND nom='".$name."'";
    //echo $select;
    $result=pg_query($l,$select);
    if(pg_num_rows($result)>0){
      $line=pg_fetch_assoc($result);
      if($line["type"]=="ZL"){
        $ZL=$line["zl"];
        
        $select="SELECT * FROM \"$ZL\" WHERE value='".intval($value)."'";
        //echo $select."  ";
        $result2=pg_query($l,$select);
        if(pg_num_rows($result2)>0){
        $line2=pg_fetch_assoc($result2);
        return $line2["libelle"];
        }
      }else if($line["type"]=="ZLS"){
        $ZL=$line["zl"];

        $tmp=split(",",$_SESSION[$ZL]);

        for($i=0;$i<count($tmp);$i++){
          $tmp2=split("~",$tmp[$i]);
          if($value==$tmp2[1]){
            return $tmp2[0];
          }
        }
      }
    }
    return false;
	}
	
	function getProperties($level,$name){
	  $l=pg_connect("host=localhost port=5432 dbname=isigeo34 user=postgres password=jtgacdt");
    $select="SELECT * FROM mapfile_reference WHERE \"object\"='".$level."' AND nom='".$name."'";
    $result=pg_query($l,$select);
    $line=pg_fetch_assoc($result);
    return $line;
      
	}
	
	function getZL($level,$name){
	  $l=pg_connect("host=localhost port=5432 dbname=isigeo34 user=postgres password=jtgacdt");
    $select="SELECT * FROM mapfile_reference WHERE \"object\"='".$level."' AND nom='".$name."'";
    //echo $select."<br>";
    $result=pg_query($l,$select);
    $return=array();
    //echo pg_num_rows($result);
    if(pg_num_rows($result)>0){
      $line=pg_fetch_assoc($result);
      if($line["type"]=="ZL"){
        $ZL=$line["zl"];
        
        $select="SELECT * FROM \"$ZL\"";
        $result=pg_query($l,$select);
        $i=0;
				while(($line2=pg_fetch_assoc($result))){
          $return[$i]=$line2["libelle"];
          $i++;
        }
        
      }else if($line["type"]=="ZLS"){
        $ZL=$line["zl"];
        $tmp=split(",",$_SESSION[$ZL]);
        for($i=0;$i<count($tmp);$i++){
          $tmp2=split("~",$tmp[$i]);
          $return[$i]=$tmp2[0];
        }
      }
    }
    return $return;
	}
?>