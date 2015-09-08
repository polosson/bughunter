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
// addBugModal.ctrl.js
'use strict';

bughunter.controller('addBugModalCtrl', function($scope, $modalInstance, $http, passConf){
	$scope.priorities = angular.copy(passConf.priorities);
	$scope.labels	  = angular.copy(passConf.labels);
	$scope.devs		  = angular.copy(passConf.devs);
	$scope.ajaxMsg	  = "";
	$scope.bug		  = {
		title: '',
		app_url: '',
		app_version: '',
		priority: '4',
		FK_label_ID: '0',
		FK_dev_ID: '0',
		description: '',
		img: []
	};

	$scope.submitNewBug = function(){
		$scope.ajaxMsg = "";
		if ($scope.bug.title.length < 5) {
			$scope.ajaxMsg = "Bug title too short. 5 characters minimum.";
			return;
		}
		if ($scope.bug.title.length > 90) {
			$scope.ajaxMsg = "Bug title too long. 90 characters maximum.";
			return;
		}
		if ($scope.bug.description.length < 5) {
			$scope.ajaxMsg = "Bug description too short. 5 characters minimum.";
			return;
		}
		$scope.ajaxMsg = "Submitting bug informations...";
		$http({
			url: 'actions/addBug.php',
			method: 'POST',
			data: {bugInfos: $scope.bug}
		}).then(
			function(R){
				$scope.ajaxMsg = R.data.message;
				$modalInstance.close({message: R.data.message, bug: R.data.bug});
			},
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
	};

	$scope.getLabelColor = function(labelID){
		var zeLabel = $.grep($scope.labels, function(e){ return e.id === labelID; });
		if (!zeLabel[0] || zeLabel[0].id == 0)
			return '#DDDDDD';
		return zeLabel[0].color;
	};

	$scope.closeAddBugModal = function(){
		$modalInstance.dismiss();
	};
});