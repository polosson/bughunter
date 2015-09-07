// menu.ctrl.js
'use strict';

bughunter.controller('settingsCtrl', function($scope, $rootScope, $http, $modalInstance, config){
	$scope.config  = config.data;
	$scope.ajaxMsg = "";
	$scope.editLabel = false;
	$scope.editDev   = false;
	$scope.projTypes = ['open-source', 'private'];

	$scope.initEdit = function(type, id){
		var item = $.grep($scope.config[type], function(e){ return e.id === id; });
		console.log('initEdit', type, item[0]);
		if (type === "labels")
			$scope.editLabel = angular.copy(id);
		if (type === "devs")
			$scope.editDev   = angular.copy(id);
	};
	$scope.saveEdit = function(type, id){
		var item = $.grep($scope.config[type], function(e){ return e.id === id; });
		console.log('save', type, item[0]);

	};
	$scope.cancelEdit = function(type, id){
		var item = $.grep($scope.config[type], function(e){ return e.id === id; });
		console.log('cancel', type, item[0]);
		$scope.editLabel = false;
		$scope.editDev   = false;
	};

	$scope.deleteItem = function(type, id){
		var item = $.grep($scope.config[type], function(e){ return e.id === id; });
		if (!confirm("Remove '"+(item[0].name || item[0].pseudo)+"' from "+type+"? Sure?"))
		console.log('delete', type, item[0]);

	};

	$scope.changePassword = function(){
		var pws = $('.passwInput');
		var pw1 = $(pws.get(0)).val();
		var pw2 = $(pws.get(1)).val();
		$scope.ajaxMsg = "";
		if (pw1 === "" || pw1.length < 4)
			return;
		if (pw2 !== pw1) {
			$scope.ajaxMsg = "Warning! Passwords don't match in both inputs. Please retry.";
			return;
		}
		if (!confirm("Do you really want to update main password??"))
			return;
		$('.passwInput').val('');
	};

	$scope.closeSettingsModal = function(){
		$modalInstance.dismiss();
	};
});