var leftObject=parseInt(($jq(window).width()/2)-(450/2));
var topObject=parseInt(($jq(window).height()/2)-(22/2));
var div="<div class=\"jq_progress\" style=\"width:100%; height:100%;z-index:1011;background-color:rgb(250,250,250);position:absolute;top:0;left:0;\"></div>";
div+="<div class=\"jq_progress\"  style=\"position:absolute;z-index:1012;width:450px; top:"+topObject+"px; left:"+leftObject+"px;text-align:center;\"><div id=\"progressbar\" style=\"height:22px;\"></div></div>";//<span class=\"corpstexte corpserror\">Chargement en cours...</span><br/><br/>

$jq("body").prepend(div);

if($jq("div.jq_progress:first").length){
	$jq( "div.jq_progress:first" ).css({"opacity":0.80});
}

$jq( "#progressbar" ).progressbar({
	value: 5
});
var indice=0;
function progress(perc){

	if($jq( "#progressbar" ).length>0){
		if(perc && perc !== null){
			indice=perc;
		}
		else{
			indice+=15;
		}
		// console.log(indice);
		//alert('progress' + indice);
		$jq( "#progressbar" ).progressbar( "value" , indice );
		 if(indice>=100){
			$jq( "#progressbar" ).progressbar( "destroy" );
			$jq( ".jq_progress" ).remove();
		}
	}
}