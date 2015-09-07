// bugModal.ctrl.js
'use strict';

/**
 * Controleur de la modale de bug
 */
bughunter.controller('bugModalCtrl', function($scope, $modalInstance, $rootScope, modeAdmin, priorities, labels, devs, bug){
	$scope.editInfos  = false;
	$scope.editDescr  = false;
	$scope.editComment= false;
	$scope.modeAdmin  = angular.copy(modeAdmin);;
	$scope.priorities = angular.copy(priorities);
	$scope.labels	  = angular.copy(labels);
	$scope.devs		  = angular.copy(devs);
	$scope.bug		  = angular.copy(bug);
	$scope.bug.descriptionHtml = angular.copy(nl2br(bug.description));
	if ($scope.bug.closed === '1')
		$scope.modeAdmin = false;

	$scope.closeBugModal = function(){
		$modalInstance.dismiss();
	};

	$scope.getLabelColor = function(labelID){
		var zeLabel = $.grep($scope.labels, function(e){ return e.id === labelID; });
		if (zeLabel[0].id == 0)
			return '#DDDDDD';
		return zeLabel[0].color;
	};

	$scope.killBug = function(){
		if (!confirm("Kill this bug? Sure?"))
			return;
		$scope.bug.closed = '1';
		$scope.modeAdmin = false;
		$rootScope.$broadcast('bugKilled', $scope.bug.id);
	};

	$scope.initEdit = function(){
		$scope.editInfos  = true;
	};
	$scope.saveEdit = function(){
		$scope.editInfos  = false;
	};
	$scope.cancelEdit = function(){
		$scope.editInfos  = false;
	};

	$scope.initUpdDescr = function(){
		$scope.editDescr  = true;
	};
	$scope.saveUpdDescr  = function(){
		$scope.editDescr  = false;
	};
	$scope.cancelUpdDescr  = function(){
		$scope.editDescr  = false;
	};

	$scope.initUpdComment = function(idComm){
		$scope.editComment  = idComm;
	};
	$scope.saveUpdComment = function(){
		$scope.editComment  = false;
	};
	$scope.cancelUpdComment = function(){
		$scope.editComment  = false;
	};
	$scope.deleteComment = function(idComm){
		if (!confirm("Delete this comment? Sure?"))
			return;
	};
});