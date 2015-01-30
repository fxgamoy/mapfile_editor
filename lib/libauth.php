<?php
session_start();
include_once( "../globprefs.php" );
	$blnret=false;
	$ControleUtilisateurs=false;
	$_SESSION["QUI_CLE"] = 0;
	$_SESSION["QUI_PROFIL"] = 0;
	$query="SELECT * FROM utilisateur";
	$result=pg_query($link, $query);

	while ($line=pg_fetch_assoc($result))
	{
		if ($line["NOM_UTILISATEUR"]==$_POST["utilisateur"] && $line["MOT_DE_PASSE"]==$_POST["motdepasse"])
		{
			$ControleUtilisateurs=true;
			$blnret=true;
			$_SESSION["QUI_U"]=$line["NOM_UTILISATEUR"];
			$_SESSION["QUI_P"]=$line["MOT_DE_PASSE"];
			$_SESSION["QUI_CLE"]=$line["CLE"];
			$_SESSION["DXF"]=$line["DXF"];
			$_SESSION["NATURE"]=$line["NATURE"];
			$_SESSION["MIF"]=$line["MIF"];
			$_SESSION["NOMPRO"]=$line["NOMPRO"];
			$_SESSION["MATCAD"]=$line["MATCAD"];
			$_SESSION["domain"]=$line["domain"];
			$_SESSION["UTIL"]=$line["COMMENTAIRE"];
			$_SESSION["LOOK"]=$line["LOOK"];
			if($line["LOGO1"]!="")
			$_SESSION["LOGO1"]="logos/".$_SESSION["domain"]."/".$line["LOGO1"];
			if($_SESSION["LOOK"]!="")
			$_SESSION["css"]="css/".$_SESSION["domain"]."/".$_SESSION["LOOK"].".css";
			else
			$_SESSION["css"]='css/geomatika/intranet.css';
			$_SESSION["CLIENT"]=$line["CLIENT"];
			if(isset($_POST["w"]) && $_POST["w"]!="")
				$_SESSION["w"]=$_POST["w"];
			if(isset($_POST["h"]) && $_POST["h"]!="")
				$_SESSION["h"]=$_POST["h"];
      if(isset($_POST["bureau"]) && $_POST["bureau"]!="")
				$_SESSION["bureau"]=$_POST["bureau"];
		}
	}
	if ($blnret)
		echo "ok";
	else
		echo "false";

?>