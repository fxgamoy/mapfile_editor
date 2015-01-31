<?php
/*  Copyright Géomatika, 2002-2007
 * Contact, infos : fx gamoy, fx.gamoy@geomatika.fr
 * -----------------------------------------------------------
 *
 * This script is developed by geomatika.fr.  Visit us at www.geomatika.fr
 *
 */
$_SESSION["domain"]="demo";
define( "APP_PATH", "d:/web/github/mapfile_editor/" );
define( "TMP_DIR", "d:/mapimage/" );
define( "REPMAP", "d:/mapserver_data/".$_SESSION["domain"]."/");
define( "REPDATA", "d:/mapserver_data/".$_SESSION["domain"]."/data/" );
define( "REPBASE","d:/mapserver_data/" );
define( "VIEWER","http://proto3.2.geomatika.fr/contrib/prototype/index.phtml");
define( "LEVEL","1");


//$link = pg_connect("host=localhost port=5432 dbname=applisig user=postgres password=jtgacdt");
// include the utilties file for misc functions
//include_once( APP_PATH."wrapper/utilities.php" );
include_once( APP_PATH."wrapper/map_session.php" );
//include_once( APP_PATH."wrapper/map_navigator.php" );
//include_once( APP_PATH."wrapper/map_query.php" );
include_once( APP_PATH."wrapper/class.JavaScriptPacker.php" );
include_once( APP_PATH."wrapper/class.compress.php" );
require(APP_PATH."lib/groupe_def.php");

include_once "wrapper/map_mcd.php";
$oMapMcd =  new mcd;
//include_once( APP_PATH."wrapper/map_infobulle.php" );
// create a new map session object
$oMapSession = new MapSession_R;
// set the temp directory for the map session
//$oMapSession->setTempDir( TMP_DIR );

// set the maximum extents to limit navigation
//$oMapSession->setMaxExtents( MAX_EXT_MINX, MAX_EXT_MINY, MAX_EXT_MAXX,MAX_EXT_MAXY );

// create a new map navigator object
//$oMapNavigator = new MapNavigator( $oMapSession );
//$oMapQuery =  new MapQuery( $oMapSession );
	$oCompress=new compress();
	$revision=$oCompress->getRevision(); 
	define("REVISION", $revision); 
?>