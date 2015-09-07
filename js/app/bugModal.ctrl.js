/**
	Copyright (C) 2015  Azuk & Polosson

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Affero General Public License as
	published by the Free Software Foundation, either version 3 of the
	License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Affero General Public License for more details.

	You should have received a copy of the GNU Affero General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
// bugModal.ctrl.js
'use strict';

/**
 * BUG MODAL controller
 */
bughunter.controller('bugModalCtrl', function($scope, $modalInstance, $rootScope, passConf, bug){
	$scope.editInfos  = false;
	$scope.editDescr  = false;
	$scope.editComment= false;
	$scope.modeAdmin  = angular.copy(passConf.authAdmin);;
	$scope.priorities = angular.copy(passConf.priorities);
	$scope.labels	  = angular.copy(passConf.labels);
	$scope.devs		  = angular.copy(passConf.devs);
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