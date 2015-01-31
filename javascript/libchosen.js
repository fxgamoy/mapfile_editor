/**
* Function: chosen
* Va gérer les effets sur les ZL
*/
function chosen(){
 var select, selects, _i, _len, _results;
	selects = $jq(".chzn-select");
	for (_i = 0, _len = selects.length; _i < _len; _i++) {
		select = $jq(selects[_i]);
		if(select.is(':visible')){
			if(select.is('[required]')){ // pour savoir si il possède l'attribut
				if(!select.is('[data-placeholder]')){
					select.attr( "data-placeholder", "Choix obligatoire...");
				}
				select.chosen({no_results_text: "Pas de résultat pour"});
			}
			else{
				if(!select.is('[data-placeholder]')){
					select.attr( "data-placeholder", "Choix...");
				}
				select.chosen({allow_single_deselect:true, no_results_text: "Pas de résultat pour"});
			}
		}
	}
}

/**
* Function: chosen
* Va gérer les effets sur les ZL
*/
function chosen_jq(){
	$jq(".chzn-select").chosen();
	$jq(".chzn-select-deselect").chosen({allow_single_deselect:true});
}