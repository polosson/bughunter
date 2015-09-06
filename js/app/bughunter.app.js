// bughunter.app.js
'use strict';

/**
 * Déclaration de l'appli bughunter
 */
var bughunter = angular.module('bughunter', ['ui.bootstrap', 'ngSanitize']);


/* * * * * * * * * * * * *  DIRECTIVES ET SERVICES GÉNÉRIQUES, ET CONFIGURATION * * * * * * * * * * * * * * */

/**
 * Intercepteur de requetes / réponses Ajax
 * Pour affichage du spinner loading
 */
bughunter.config(function($httpProvider) {
	$httpProvider.interceptors.push(function($q) {
		return {
			'request': function(config) {
				$('#loadAjax').show();
				return config;
			},
			'response': function(response) {
				$('#loadAjax').hide();
				return response;
			}
		};
	});
});

/**
 * Service des messages
 */
bughunter.service('msgSrv', function($timeout){

	return {
		showMsg: showMsg,
		hideMsg: hideMsg
	};
	/**
	 * Affiche le message (il disparait automatiquement après 2 sec pour success, 5 sec pour error)
	 * @param STRING msg Le message à afficher
	 * @param STRING type Le type de message ("error", ou "success") pour la couleur
	 */
	function showMsg (msg,type) {
		console.log(type+' :', msg);
		$('#msg').html(msg).addClass('msg_'+type).show();
		var time = 2000;
		if (type === "error")
			time = 5000;
		$timeout(function(){
			hideMsg();
		}, time);
	}
	/**
	 * Cache le message (fade out 0.8 sec)
	 */
	function hideMsg () {
		$('#msg').fadeOut(800);
	}
});

/**
 * Appui sur entrée pour éxécuter une fonction
 */
bughunter.directive('hitenter', function() {
	return function(scope, element, attrs) {
		element.bind("keydown keypress", function(event) {
			if (event.keyCode === 13) {
				scope.$apply(function(){
					scope.$eval(attrs.hitenter, {'event': event});
				});
				event.preventDefault();
			}
		});
	};
});

/**
 * Filtre qui enlève la chaine "http://" devant un URL
 */
bughunter.filter('formaturl', function(){
	return function(url){
		var reghttp = /^http\:\/\//i;
		return url.replace(reghttp, '');
	};
});