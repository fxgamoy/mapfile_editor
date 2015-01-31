/* Copyright(c)  Géomatika, 2002-2015
 * Isigeo v4.2
 * Contact, infos : fx gamoy, fx.gamoy@geomatika.fr
 * -------------------------------------------------
 * This script is developed by geomatika.fr.
 * Visit us at http://www.geomatika.fr/
 */

/**
 *
 *	IsiAlert.js
 *
 *	Class de création d'alert belle et responsive pour Isigeo
 *
 *	Cas d'utilisation: ExportSIG
 */
(function(){

	//Active le mode strict du compilateur JS du navigateur si compatible(permet de remonter plus d'avertissements très utiles sur le code JS)
	// "use strict";

	//Création du namespace de la classe IsiAlert
	StudioManager = {

		/**
		 *	Attribut activated
		 *	True ou false si alerte active ou pas
		 */
		activated:false,

		/**
		 *	Attribut hidden
		 *	True ou false si alerte cachée ou non
		 */
		hidden:false,

		/**
		 *
		 *	Méthode d'ouverture de la IsiAlert
		 *
		 *	Arguments:
		 *	@param: contentOptions - { object } - Un objet avec les attributs: title, subTitle, html, style
		 *	@param: closeButtonOptions - { object } - Un objet avec les attributs: style, imgSrc, hover, callback pour configurer le bouton de fermeture
		 *	@param: callback - { function } - Une fonction à executer à l'ouverture
		 */
		create:function(contentOptions, closeButtonOptions, createCallback){

			//Si on à déjà une alerte en cours, on la détruit d'abord
			if(self.activated){
				self.destroy();
			}

			//On vérifie les arguments
			if( (typeof arguments[2] != 'undefined' && typeof arguments[2] != 'function') || typeof arguments[0] != 'object'){
				throw { 
					name:"IsiAlert impossible.", 
					level:"Show Stopper", 
					message:"Merci de renseigner des arguments valides. Deux objets javascript pour les deux premiers, une fonction pour le 3eme", 
					htmlMessage:"",
					toString:function(){return this.name + ": " + this.message;} 
				};
			}

			//Creation du contenu HTML de la IsiAlert
			self.contentHtml = '<div id="isigeo-isialert">';

			//Mise en place du titre et du sous titre
			
			if(typeof contentOptions.title != 'undefined'){
				self.contentHtml += '<h3 id="isigeo-isialert-title">'+contentOptions.title+'</h3>';
			}

			if(typeof contentOptions.subTitle != 'undefined'){
				self.contentHtml += '<h4 id="isigeo-isialert-subtitle">'+contentOptions.subTitle+'</h4>';
			}

			self.contentHtml += '<div id="isigeo-isialert-content" ';

			//Le style du conteneur
			if(typeof contentOptions.style != 'undefined'){
				self.contentHtml += 'style="'+contentOptions.style+'"';
			}

			self.contentHtml += '>';

			//Le contenu HTML de l'alerte
			if(typeof contentOptions.html != 'undefined'){
				self.contentHtml += contentOptions.html;
			}

			self.contentHtml += '</div>';

			//Création du bouton de fermeture
			self.closeButton = '<img id="isigeo-isialert-quit" ';
			
			if(typeof closeButtonOptions != 'undefined' && closeButtonOptions !== null && closeButtonOptions !== false){

				//Le style du bouton de fermeture
				if(typeof closeButtonOptions.style != 'undefined'){
					self.closeButton += 'style="'+closeButtonOptions.style+'"';					
				}else{
					self.closeButton += 'style="position:absolute;width:2em;left:31.9em;top:.4em;cursor:pointer;" ';	
				}

				//Le hover du bouton de fermeture
				if(typeof closeButtonOptions.hover != 'undefined'){
					self.closeButton += ' title="'+closeButtonOptions.hover+'" alt="'+closeButtonOptions.hover+'"';
				}else{
					self.closeButton += 'alt="Quitter" ';
				}

				//L'image du bouton de fermeture
				if(typeof closeButtonOptions.imgSrc != 'undefined'){
					self.closeButton += 'src="'+closeButtonOptions.imgSrc+'"';
				}else{
					self.closeButton += 'src="images/export_close.png" ';	
				}

			}else{
				//Style et image du bouton par défaut si aucune options
				self.closeButton += 'style="position:absolute;width:2em;left:31.9em;top:.4em;cursor:pointer;" ';
				self.closeButton += 'src="images/export_close.png" ';
				self.closeButton += 'alt="Quitter" ';
			}

			self.closeButton += '>';

			//Lancement d'alertify
			alertify.prompt(self.contentHtml+self.closeButton);

			//Suppression de balise inutiles
			$jq('alertify-message').remove();

			$jq('.alertify-buttons').css({
				visibility:'hidden',
				display:'none'
			});

			$jq('.alertify-text-wrapper').remove();

			$jq('#alertify').css({
				'height' : 'auto',
				'display' : 'block'
			});

			//On cache la balise nav d'alertify
			$jq('.alertify-buttons').css({
				'visibility':'hidden',
				'display':'none'
			});

			//Ajout d'un event sur le bouton de sortie pour créer un evenement de sortie maison
			$jq('#isigeo-isialert-quit').click(function(e){
				//Tu stop l'evenement d'origine
				e.preventDefault();

				//Et tu me lance ma propre function de fermeture
				if(typeof closeButtonOptions != 'undefined' && typeof closeButtonOptions.callback != 'undefined'){
					self.destroy(closeButtonOptions.callback);
				}else{
					self.destroy();
				}


			}).tooltip({
				open: function(event, ui){
					$jq(ui.tooltip).css('z-index', 99999);
				}
			});

			self.activated = true;

			//Execution du callback d'ouverture de l'alerte
			if(typeof createCallback == 'function'){
				try{
					createCallback();
				}catch(e){
					return;
				}
			}

		},

		/**
		 *
		 *	Méthode de fermeture de la IsiAlert
		 *
		 *	Arguments:
		 *	@param: callback - { function } - Une fonction à executer a la fermeture
		 */
		destroy:function(destroyCallback){

			//Pour ce plaisantin de internet explorer
			if(isIE){

				$jq('#alertify-cover').remove();
				$jq('#alertify').remove();

			}else{
				$jq('#alertify-ok').click();
				$jq('#alertify-cover').remove();
			}

			self.activated = false;

			if(typeof destroyCallback == 'function'){

				try{
					destroyCallback();
				}catch(e){
					return;
				}

			}

		},

		/**
		 *	hide
		 *	Permet de cacher la IsiAlert
		 */
		hide:function(){
			$jq('#alertify-cover').hide();
			$jq('#alertify').hide();

			self.hidden = true;
		},

		/**
		 *	show
		 *	Remontre à nouveau la IsiAlert en l'état
		 */
		show:function(){
			$jq('#alertify-cover').show();
			$jq('#alertify').show();

			self.hidden = false;
		},
		/**
		 *
		 *	Méthode de fermeture de la IsiAlert
		 *
		 *	Arguments:
		 *	@param: callback - { function } - Une fonction à executer a la fermeture
		 */
		initInterface:function(){
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
		},

		pourvoir:function(){
			alert("okok");
		},
		/**
		 *
		 *	Méthode de mise à jour du contenu HTML de l'alerte
		 *
		 *	Arguments:
		 *	@param: contentOptions - { object } - Un objet contenant les differentes options du contenu HTML à changer
		 *	@param: updateContentCallback - { function } - le callback à executer une fois la mise à jour faite
		 */
		updateContent:function(contentOptions, updateContentCallback){
			//Faire le traitement Jquery pour mettre à jour l'alert selon les options
			if(typeof contentOptions.title != 'undefined'){
				$jq('#isigeo-isialert-title').html(contentOptions.title);
			}

			if(typeof contentOptions.subTitle != 'undefined'){
				$jq('#isigeo-isialert-subtitle').html(contentOptions.subTitle);
			}

			if(typeof contentOptions.html != 'undefined'){
				$jq('#isigeo-isialert-content').html(contentOptions.html);
			}

			if(typeof updateContentCallback == 'function'){
				updateContentCallback();
			}
		}

	};

	//Permet d'acceder aux attributs de la classe dans toutes ses methodes comme en php ::self
	var self = StudioManager;

})();