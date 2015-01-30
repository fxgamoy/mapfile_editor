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
<title>Console d'administration</title>
<meta name="language" content="fr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="distribution" content="Global" />
<meta name="email" content="webmaster@geomatika.fr" />
<meta name="author" content="fx gamoy" />

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
/*if($_GET["page"]!="404"){
  echo "<script src=\"javascript/".$_GET["page"]."GUI.js\" type=\"text/javascript\"></script>";
}*/
require(APP_PATH."/groupe_def.php");
$oCompress->rootDir="";
$oCompress->load($isigeo_jq); 
$oCompress->load($isigeo_extras);
echo "<script src=\"extras/jquery_layout/source/versions/jquery.layout-1.4.1.js\" type=\"text/javascript\"></script>";
?>

<style type="text/css">
.autocompletion {border: 1px solid #848484;}
.autocompletion li{margin-left:0px;}

</style>
<?php
//	$oCompress->load($isigeo_jq);
?>
<script language="JavaScript" type="text/javascript">
$jq(document).ready(function () {
    $jq('body').layout({ applyDemoStyles: true });
});
</script>

</head>
<body>
<div class="ui-layout-center">Center
    <p><a href="http://layout.jquery-dev.com/demos.html">Go to the Demos page</a></p>
    <p>* Pane-resizing is disabled because ui.draggable.js is not linked</p>
    <p>* Pane-animation is disabled because ui.effects.js is not linked</p>
</div>
<div class="ui-layout-north">North</div>
<div class="ui-layout-south">South</div>
<div class="ui-layout-east">East</div>
<div class="ui-layout-west">West</div>
</body>
</html>