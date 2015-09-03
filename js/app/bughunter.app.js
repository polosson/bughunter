// bughunter.app.js
'use strict';

/**
 * Déclaration de l'appli bughunter
 */
var bughunter = angular.module('bughunter', ['ui.bootstrap']);


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
 * Controleur du menu principal
 */
bughunter.controller("menuCtrl", function($scope, $rootScope, $http){
	$scope.page		   = 'alive';
	$scope.countKilled = startCountKilled;
	$scope.countAlive  = startCountAlive;

	$scope.showPage = function(zepage){
		$scope.page = zepage;
		if (zepage == "alive")
			$rootScope.$broadcast('showbugsAlive');
		if (zepage == "killed")
			$rootScope.$broadcast('showbugsKilled');
	};

	$scope.$on('updateBugCount', function(e, count){
		if (count.type === 0)
			$scope.countAlive  = count.count;
		else
			$scope.countKilled = count.count;
	});

	$scope.disconnect = function(){
		$http({
			'url': 'actions/deconx.php'
		}).then(
			function(){ window.location = 'index.php'; },
			function(errMsg) { console.log(errMsg); $('#msg').html(errMsg).addClass('msg_error'); }
		);
	};
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

