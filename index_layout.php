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
require( "globprefs.php" );

/*
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
$css=$oUserSession->val["css"];*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Mapfile Editor</title>
<meta name="language" content="fr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="distribution" content="Global" />
<meta name="email" content="webmaster@geomatika.fr" />
<meta name="author" content="fx gamoy" />

<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" type="text/css" href="../extras/jquery_ui/1.10.4/css/smoothness/jquery-ui-1.10.4.custom.min.css">
<link rel="stylesheet" type="text/css" href="../css/completion.css">
<link rel="stylesheet" type="text/css" href="../extras/chosen/chosen/chosen-isigeo.min_0.10.css">
<!--link rel="stylesheet" type="text/css" href="../extras/jquery_autocomplete/1.1.5/styles.css"-->
<link rel="stylesheet" type="text/css" href="extras/jquery_autocomplete/1.2.11/content/styles.css">
<link rel="stylesheet" type="text/css" href="extras/jquery_colorpicker/1.0.9/jquery.colorpicker.css">
<?php
    require(APP_PATH."/groupe_def.php");
    $oCompress->rootDir="";
    $oCompress->load($isigeo_jq); 
    $oCompress->load($isigeo_extras);
?>
<script src="extras/jquery_layout/source/versions/jquery.layout-1.4.1.js" type="text/javascript"></script>

<script language="JavaScript" type="text/javascript">
$jq(document).ready(function () {
    $jq('body').layout({ 
        applyDemoStyles: true
         });
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

</head>
<body>
<div class="ui-layout-center">
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

            </div> <!--  mapfile_accordion   -->


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
</div>
<div class="ui-layout-north">North</div>
<div class="ui-layout-south">South</div>
<div class="ui-layout-east">East</div>
<div class="ui-layout-west">West</div>
</body>
</html>