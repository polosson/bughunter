// menu.ctrl.js
'use strict';

bughunter.controller('settingsCtrl', function($scope, $rootScope, $http, $modalInstance, config){
	$scope.config  = config.data;
	$scope.ajaxMsg = "";

	$scope.closeSettingsModal = function(){
		$modalInstance.dismiss();
	};
});