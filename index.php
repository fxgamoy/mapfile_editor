<?php
/* Copyright(c)  Géomatika, 2002-2011
 * Isigeo v4.2
 * Contact, infos : fx gamoy, fx.gamoy@geomatika.fr
 * -----------------------------------------------------------
 * This script is developed by geomatika.fr.
 * Visit us at http://www.geomatika.fr/
 */
session_start();
ini_set('session.bug_compat_warn', 0);
require( "../globprefs.php" );
include(APP_PATH."/manage_session.php");
include(APP_PATH."/wrapper/class.menu.php");
include(APP_PATH."/admin/wrapper/class.admin.php");
include(APP_PATH."/admin/wrapper/class.bounds.php");
include( APP_PATH."/admin/wrapper/class.domain.php" );
include( APP_PATH."/admin/wrapper/class.mapfile.php" );
include( APP_PATH."/admin/wrapper/class.envcarto.php" );
include(APP_PATH."/admin/wrapper/class.user.php" );
include( APP_PATH."/wrapper/class.trace.php" );
include( "./adminprefs.php" );
include(APP_PATH."lib/libcarte.php");
include_once(APP_PATH."lib/libpg.php");
include(APP_PATH."lib/libmenu.php");
include(APP_PATH."lib/JSON.php");
  include("lib/libtemplate.php");
if(!isset($_GET["page"])||$_GET["page"]=="" )
{
  $_GET["page"]="404";

}
if($_SESSION["NATURE"]!="ADMIN" && $_SESSION["NATURE"]!="EXPERT"){
    $_GET["page"]="403";
    //echo $_GET["page"];
}
//echo $_GET["page"];
$oUserSession->restore();
$css=$oUserSession->val["css"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Console d'administration</title>
<meta name="language" content="fr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="distribution" content="Global" />
<meta name="email" content="webmaster@geomatika.fr" />
<meta name="author" content="fx gamoy" />
<meta name="publisher" content="GEOMATIKA" />
<meta name="copyright" content="Copyright geomatika©2012" />

<link rel="shortcut icon" href="/favicon.ico" />

<link rel="stylesheet" type="text/css" href="../css/intranet.css">
<link rel="stylesheet" type="text/css" href="../css/dialog.css">
<link rel="stylesheet" type="text/css" href="../extras/jquery_ui/1.10.4/css/smoothness/jquery-ui-1.10.4.custom.min.css">
<?php
if($css!='' && $css!='css/intranet.css'){
	echo "<link href=\"".$css."\" rel=\"stylesheet\" type=\"text/css\">";
}
?>
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="../css/tablesort.css">
<link rel="stylesheet" type="text/css" href="../css/dialog.css">
<link rel="stylesheet" type="text/css" href="../css/completion.css">
<link rel="stylesheet" type="text/css" href="../extras/chosen/chosen/chosen-isigeo.min_0.10.css">
<!--link rel="stylesheet" type="text/css" href="../extras/jquery_autocomplete/1.1.5/styles.css"-->
<link rel="stylesheet" type="text/css" href="extras/jquery_autocomplete/1.2.11/content/styles.css">
<link rel="stylesheet" type="text/css" href="extras/jquery_colorpicker/1.0.9/jquery.colorpicker.css">
<link rel="stylesheet" type="text/css" href="css/style.css">


<?php
if($_GET["page"]!="404"){
  echo "<script src=\"javascript/".$_GET["page"]."GUI.js\" type=\"text/javascript\"></script>";
}
require(APP_PATH."/admin/maintenance/groupe_def.php");
$oCompress->rootDir="../";
$oCompress->version=REVISION;
$oCompress->load($isigeo_base);
$oCompress->load($isigeo);
?>
<script src="javascript/templateGUI.js" type="text/javascript"></script>


<style type="text/css">
.autocompletion {border: 1px solid #848484;}
.autocompletion li{margin-left:0px;}

</style>
<?php
	$oCompress->load($isigeo_jq);
?>
<script language="JavaScript" type="text/javascript">
DialogWindow.IMAGE_PATH="..";

Event.observe(window, 'load', function(){
	templateGUI.start('<?php if(isset($_REQUEST["page"])){echo $_REQUEST["page"]; }?>');
 $jq( "#mapfile_editor" ).tabs();
 $jq( "#header_accordion" ).accordion();
 $jq( "#layer_accordion" ).accordion({
collapsible:true,
 heightStyle: "content"
});
 $jq( ".class_accordion" ).accordion({
collapsible:true,
heightStyle: "content"
});
 
  $jq( ".label_accordion" ).accordion({
heightStyle: "content",
active:true
});
 $jq( ".style_accordion" ).accordion({
heightStyle: "content",
collapsible:true,
active:true
});


});
</script>
<script type="text/javascript" src="../extras/chosen/chosen/chosen.jquery-modified_0.10.js"></script>

<script src="../extras/highcharts/js/highcharts.js" type="text/javascript"></script>
<script src="../extras/highcharts/js/modules/exporting.js" type="text/javascript"></script>
</head>
<body>
<div id="bandeau" class="bandeau" style="height:55px">
<div style="background-color:#737373;margin-left:auto; margin-right:auto;width:300px;">
	<div id="titrepage" class="fdOnglet	corpswhite" style="padding-top:10px;width:300px;margin-left:auto; margin-right:auto; text-align:center;">
	.: Console d'administration :.<br/><br/>
	</div></div>

<div id="chxDomaine" style="margin-top:-35px;">

    </div>

</div>

<div class="ficheonglet" style="overflow:auto;text-align:left;">

<div id="menu" style="width:100%;padding:0px;margin:0px;">
<table class="fondmenu" style="width:100%;margin:0px;padding:0px;border-collapse:collapse;" border="0">  <tr><td style="width:26px;"><a href="template.phtml" target="_self"><img src="../images/home_22.png" width="22" height="22" border=0 /></a></td>
<td class="corpserror">

</tr></table>
</div>


<div style="width:98%;padding:0px;margin:0px 1%;">
<form name="main" style="margin:0px;padding:0px">


<!-- contenu de la page -->
<?php
    //print_r($_GET);
if($_GET["page"]!='404'){
  
    echo "<div id=\"content\"></div>";
    echo "<script>";
    echo "Event.observe(window, 'load', function(){";
      
      echo "studioGUI.start('".$domain."')});";

    echo "</script>";

}
else {


  echo "<div id=\"container_content\">";
  ?>
  <br />
 <div id="mapfile_editor">
<ul>
<li><a href="#mapfile_header">Param&egrave;tres g&eacute;n&eacute;raux</a></li>
<li><a href="#mapfile_layers">Gestion des couches</a></li>
</ul>
<div id="mapfile_header">

<div id="header_accordion">

<h3>Chemins</h3>
<div>
<p>
Les chemins de l'entete
</p>
</div>
<h3>Barre d'echelle</h3>
<div>
<p>
Les paramètres de l'echelle
</p>
</div>
<h3>L&eacute;gende</h3>
<div>
<p>
Les paramètres de la légende
</p>
</div>

</div>


</div> <!--  mapfile_header   -->
<div id="mapfile_layers">

<div id="layer_accordion">

<h3>Layer 1</h3>
<div>
<p>
Param&egrave;tres g&eacute;n&eacute;raux du Layer
</p>


<div id="class_accordion_1" class="class_accordion">
<h3>Class 1</h3>
<div>
<p>
Param&egrave;tres g&eacute;n&eacute;raux de la classe
</p>
<div id="style_accordion_1" class="style_accordion">
<h3>Style 1</h3>
<div>
<p>
Mon Style 1
</p>
</div>
<h3>Style 2</h3>
<div>
<p>
Mon Style 2
</p>
</div>
</div><!-- style_accordion  -->


<div id="label_accordion_1" class="label_accordion">
<h3>Label 1</h3>
<div>
<p>
Mon Label
</p>
</div>
</div><!-- label_accordion  -->


</div> <!-- class_accordion  -->
</div>

</div> <!-- class_accordion  -->


<!-- layer1  -->

<h3>Layer 2</h3>
<div>
<p>
Param&egrave;tres g&eacute;n&eacute;raux du Layer
</p>


<div id="class_accordion_2" class="class_accordion">
<h3>Class 1</h3>
<div>
<p>
Param&egrave;tres g&eacute;n&eacute;raux de la classe
</p>
<div id="style_accordion_2"  class="style_accordion">
<h3>Style 1</h3>
<div>
<p>
Mon Style 1
</p>
</div>
</div><!-- style_accordion  -->


<div id="label_accordion_2" class="label_accordion">
<h3>Label 1</h3>
<div>
<p>
Mon Label
</p>
</div>
</div><!-- label_accordion  -->


</div> <!-- class_accordion  -->
</div>

</div> <!-- class_accordion  -->
</div> <!-- layer1  -->
</div> <!-- layer1  -->




</div><!-- layer_accordion  -->
</div> <!-- mapfile_layers  -->
</div> <!-- mapfile_editor  -->
  <?
  echo "</div>"; // container_content
  $otrace->addTrace($oUserSession, 89);
}
?>
<br>

</form>
</div>		
<?php
if($_GET["page"]=='mobile'){
	echo '<script>$jqm.mobile.initializePage();</script>';
}
?>
</body>
</html>