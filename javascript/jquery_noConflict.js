var $jq=jQuery.noConflict();

/**
* On redefinis la fonction serialze de JQ pour quelle se comporte comme Proto
*
*/
jQuery.fn.extend({
	serialize: function() {
	 	var tab=this.serializeArray();
		// On gère les buttons avec name et value
		$jq(this).find(':button[name]').each( function(index, value){
			var obj={};
			obj.name=value.name;
			obj.value=value.value;
			tab.push(obj);
		});

		var param='';
		var crt='';
		$jq.each(tab, function(i, field){
			var value=field.value;
			value = encodeURIComponent(value);
			var multiple=$jq("[name='"+field.name+"']").is('[multiple]');
			if((crt !== field.name && multiple) || !multiple){
				param+='&'+field.name+"="+value;
			}
			else{
				param+=","+value;
			}
			crt=field.name;
		});
		return param;
	}
});

/**
* Override d'une fonction de JQ Ui
* Nous avons besoin de passé du html() et pas du text()
* https://gist.github.com/sgruhier/1086231
*
*/
 jQuery.widget("ui.dialog", jQuery.extend({}, jQuery.ui.dialog.prototype, {
	_title: function( title ) {
		if ( !this.options.title ) {
			title.html("&#160;");
		}
		title.html( this.options.title );
	}
}));

jQuery.widget("ui.tooltip", jQuery.extend({}, jQuery.ui.tooltip.prototype, {
	options: {
		content: function() {
			return $jq( this ).attr( "title" );
		},
		hide: true,
		// Disabled elements have inconsistent behavior across browsers (#8661)
		items: "[title]:not([disabled])",
		position: {
			my: "left top+15",
			at: "left bottom",
			collision: "flipfit flip"
		},
		show: true,
		tooltipClass: null,
		track: false,

		// callbacks
		close: null,
		open: null
	}
}));