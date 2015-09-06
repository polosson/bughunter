// bughunter.app.js
'use strict';

/**
 * Déclaration de l'appli bughunter
 */
var bughunter = angular.module('bughunter', ['ui.bootstrap', 'ngSanitize']);


/* * * * * * * * * * * * *  DIRECTIVES GÉNÉRIQUES ET CONFIGURATION * * * * * * * * * * * * * * */

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