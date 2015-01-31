<?php
/* 
* @Author: Geomatika
* @Date:   2014-06-06 10:32:22
* @Last Modified by:   Geomatika
* @Last Modified time: 2015-01-31 14:39:17
*/
 //Va contenir tous les objets
$groupe_js=array();
//===========================================================
//===========================================================
// isigeo_draw

//===========================================================
// isigeo_base
//===========================================================
$isigeo_base=new stdclass();
//Chemin du fichier compressé
$isigeo_base->out='javascript/build/';
$isigeo_base->name='isigeo_base';
$isigeo_base->delete=false;//pour modules internet
$isigeo_base->version=true;
$isigeo_base->yui=true;
$isigeo_base->format='js';
$isigeo_base->src[]='extras/geomatika/scripta.js';
$isigeo_base->src[]='extras/geomatika/saveSWF.js';
$isigeo_base->src[]='extras/geomatika/Component/Accordion.js';
$isigeo_base->src[]='extras/geomatika/Component/DialogWindow.js';
$isigeo_base->src[]='extras/geomatika/FieldManager.js';
$isigeo_base->src[]='extras/geomatika/Tree/GNode.js';
$isigeo_base->src[]='extras/geomatika/Tree/GTree.js';
$isigeo_base->src[]='extras/geomatika/Tree/DataManager.js';
$isigeo_base->src[]='extras/geomatika/Tree/DataTree.js';
$isigeo_base->src[]='javascript/LayerTree.js';
$isigeo_base->src[]='extras/geomatika/Tree/TreeExplorer.js';
$isigeo_base->src[]='javascript/menu_bureau.js';
$isigeo_base->src[]='javascript/maputil.js';
$isigeo_base->src[]='javascript/MapTab.js';
$isigeo_base->src[]='javascript/ToolsGUI.js';
$isigeo_base->src[]='javascript/Frame.js';
$isigeo_base->src[]='javascript/requeteurGUI.js';
$isigeo_base->src[]='javascript/queryGUI.js';
$isigeo_base->src[]='javascript/TabFooter.js';
$isigeo_base->src[]='javascript/DataGUI.js';
$isigeo_base->src[]='javascript/mcd.js';
$isigeo_base->src[]='javascript/menu.js';
$isigeo_base->src[]='javascript/JSUtils.js';
$isigeo_base->src[]='javascript/debug.js';
$isigeo_base->src[]='javascript/filterGUI.js';
$isigeo_base->src[]='javascript/utils.js';
$isigeo_base->src[]='javascript/SaisieRapide.js';
$isigeo_base->src[]='plugins/shortcut/javascript/Shortcut.js';
$isigeo_base->src[]='extras/jquery_tooltipster/js/jquery.tooltipster.min.js';
$isigeo_base->src[]='extras/geomatika/Component/IsiAlert.js';
$groupe_js['isigeo_base']=$isigeo_base;

//===========================================================
// isigeo_epsg
//===========================================================
$isigeo_epsg=new stdclass();
$isigeo_epsg->out='javascript/build/';
$isigeo_epsg->name='isigeo_epsg';
$isigeo_epsg->delete=false;
$isigeo_epsg->version=true;
$isigeo_epsg->yui=true;
$isigeo_epsg->format='js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG27561.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG27562.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG27563.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3942.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3943.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3944.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3945.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3946.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3947.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3948.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3949.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3950.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG2154.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG3857.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG4171.js';
$isigeo_epsg->src[]='extras/proj4js/lib/defs/EPSG32622.js';
$groupe_js['isigeo_epsg']=$isigeo_epsg;


//===========================================================
// isigeo_extras
//===========================================================
$isigeo_extras=new stdclass();
$isigeo_extras->out='javascript/build/';
$isigeo_extras->name='isigeo_extras';
$isigeo_extras->delete=false;
$isigeo_extras->version=true;
$isigeo_extras->yui=true;
$isigeo_extras->format='js';
$isigeo_extras->src[]='extras/jquery_validation/1.13/dist/jquery.validate.min.js';
$isigeo_extras->src[]='extras/jquery_validation/1.13/dist/additional-methods_isigeo.js';
$isigeo_extras->src[]='extras/jquery_validation/1.13/dist/localization/messages_fr.js';
$isigeo_extras->src[]='extras/chosen/chosen/chosen.jquery-modified_0.10.js';
$isigeo_extras->src[]='javascript/libchosen.js';
$isigeo_extras->src[]='extras/alertify/alertify.min.js';
// $isigeo_extras->src[]='extras/ckeditor/ckeditor.js';// Ne Passe pas à cause de sa différence d'encodage et des chemins
// $isigeo_extras->src[]='extras/ckeditor/adapters/jquery.js';
/* $isigeo_extras->src[]='extras/highcharts/js/highcharts.js';
$isigeo_extras->src[]='extras/highcharts/js/modules/exporting.js'; */
$groupe_js['isigeo_extras']=$isigeo_extras;

//===========================================================
// isigeo_internetMods
//===========================================================
/* $isigeo_internetMods=new stdclass();
$isigeo_internetMods->out='internet/javascript/';
$isigeo_internetMods->name='isigeo_internetMods';
$isigeo_internetMods->delete=false;
$isigeo_internetMods->version=false;
$isigeo_internetMods->yui=true;
$isigeo_internetMods->format='js';
$isigeo_internetMods->src[]='internet/javascript/Search.js';
$isigeo_internetMods->src[]='javascript/menu.js';
$isigeo_internetMods->src[]='extras/geomatika/Map/LegendGUI.js'	;
$isigeo_internetMods->src[]='internet/javascript/Search.js';
$isigeo_internetMods->src[]='internet/javascript/SearchGUI_2.js';
// $isigeo_internetMods->src[]='internet/javascript/BaseSwitcher.js';
$isigeo_internetMods->src[]='internet/javascript/GSelect.js';
$isigeo_internetMods->src[]='internet/javascript/CrossDomain.js';
$isigeo_internetMods->src[]='internet/javascript/MouseQuery.js';
$groupe_js['isigeo_internetMods']=$isigeo_internetMods; */
//===========================================================
// isigeo_internetCSS
//===========================================================
/* $isigeo_internetCSS=new stdclass();
$isigeo_internetCSS->out='internet/css/';
$isigeo_internetCSS->name='isigeo_internetCSS';
$isigeo_internetCSS->delete=false;
$isigeo_internetCSS->version=false;
$isigeo_internetCSS->yui=true;
$isigeo_internetCSS->format='css';
$isigeo_internetCSS->src[]='css/menu.css';
$isigeo_internetCSS->src[]='extras/openlayers/theme/default/style.css';
$isigeo_internetCSS->src[]='internet/css/search.css';
$isigeo_internetCSS->src[]='internet/css/GSelect.css';
$isigeo_internetCSS->src[]='internet/css/internet.css';
$groupe_js['isigeo_internetCSS']=$isigeo_internetCSS; */
//===========================================================
// isigeoCSS
//===========================================================
$isigeoCSS=new stdclass();
$isigeoCSS->out='css/';
$isigeoCSS->name='isigeo';
$isigeoCSS->delete=false;
$isigeoCSS->version=true;
$isigeoCSS->yui=true;
$isigeoCSS->format='css';
$isigeoCSS->src[]='css/template.css';
$isigeoCSS->src[]='css/menu.css';
$isigeoCSS->src[]='css/viewer.css';
$isigeoCSS->src[]='css/tablesort.css';
$isigeoCSS->src[]='css/accordion.css';
$isigeoCSS->src[]='css/calendar.css';
$isigeoCSS->src[]='css/completion.css';
$isigeoCSS->src[]='css/dialog.css';
$isigeoCSS->src[]='css/alertify.core.css';
$isigeoCSS->src[]='css/alertify.default.css';
$isigeoCSS->src[]='extras/jquery_colorpicker/1.0.9/jquery.colorpicker.css';
$isigeoCSS->src[]='extras/jquery_tooltipster/css/tooltipster.css';
$isigeoCSS->src[]='extras/jquery_tooltipster/css/themes/tooltipster-shadow.css';
//$isigeoCSS->src[]='extras/jquery_tooltipster/css/themes/tooltipster-punk.css';


$groupe_js['isigeoCSS']=$isigeoCSS;

//===========================================================
// isigeo_mobileJS_min
//===========================================================
$isigeo_mobileJS_min=new stdclass();
$isigeo_mobileJS_min->out='mobile/';
$isigeo_mobileJS_min->name='isigeo.min';
$isigeo_mobileJS_min->delete=false;
$isigeo_mobileJS_min->version=false;
$isigeo_mobileJS_min->yui=true;
$isigeo_mobileJS_min->format='js';
$isigeo_mobileJS_min->src[]='extras/jquery/jquery.min.js';
$isigeo_mobileJS_min->src[]='javascript/jquery_proto.js';
$isigeo_mobileJS_min->src[]='mobile/isigeo.js';
$groupe_js['isigeo_mobileJS_min']=$isigeo_mobileJS_min;
//===========================================================
// isigeo_jq
//===========================================================
$isigeo_jq=new stdclass();
//Chemin du fichier compressé
$isigeo_jq->out='javascript/build/';
$isigeo_jq->name='isigeo_jq';
$isigeo_jq->delete=false;
$isigeo_jq->version=true;
$isigeo_jq->yui=true;
$isigeo_jq->format='js';
$isigeo_jq->src[]='extras/jquery/jquery.min.js';
$isigeo_jq->src[]='extras/jquery/jquery-migrate.js';
$isigeo_jq->src[]='extras/jquery_ui/1.10.4/js/jquery-ui-1.10.4.custom.min.js';
$isigeo_jq->src[]='javascript/jquery_noConflict.js';
$isigeo_jq->src[]='extras/jquery_ui/1.10.4/development-bundle/ui/i18n/jquery.ui.datepicker-fr.js';
$isigeo_jq->src[]='javascript/jquery_progressBar.js';
$isigeo_jq->src[]='extras/jquery_json/jquery.json.js';
$isigeo_jq->src[]='extras/jquery_autocomplete/1.2.11/dist/jquery.autocomplete.min.js';
$isigeo_jq->src[]='extras/jquery_lightview/js/excanvas/excanvas.js';
$isigeo_jq->src[]='extras/jquery_lightview/js/spinners/spinners.min.js';
$isigeo_jq->src[]='extras/jquery_lightview/js/lightview/lightview.js';
$isigeo_jq->src[]='extras/jquery_colorpicker/1.0.9/jquery.colorpicker.js';
$isigeo_jq->src[]='extras/jquery_colorpicker/1.0.9/i18n/jquery.ui.colorpicker-fr.js';
$isigeo_jq->src[]='extras/jquery_scrollTo/jquery.scrollTo.js';
$isigeo_jq->src[]='extras/jquery_corner/jquery.corner.js';
$isigeo_jq->src[]='javascript/ColorTools.js';
$groupe_js['isigeo_jq']=$isigeo_jq;
?>