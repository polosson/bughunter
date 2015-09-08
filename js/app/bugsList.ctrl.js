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
// bugsList.ctrl.js
'use strict';

/**
 * BUGS LIST controller
 */
bughunter.controller("bugsCtrl", function($scope, $http, $modal, msgSrv, config, countBugs){
	$scope.bugsList		= [];
	$scope.search	 = {'title':"", 'priority':"", 'FK_label_ID':"", 'FK_dev_ID':""};
	$scope.orderProp = 'priority';
	$scope.orderRev  = true;
	$scope.bugsType	 = 0;

	$scope.config	= config.data;

	getBugsList();

	$scope.resetFilter = function(){
		$scope.search = {'title':"", 'priority':"", 'FK_label_ID':"", 'FK_dev_ID':""};
	};

	function getBugsList () {
		var type = $scope.bugsType;
		$http({
			'url': 'actions/getBugsList.php?type='+type
		}).then(
			function(R){
				if (R.data.error === "OK") {
					$scope.bugsList		= R.data.bugsList;
					countBugs.updateCountType(type, R.data.bugsList.length);
				}
				else msgSrv.showMsg(R.data.error, 'error');
			},
			function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
		);
	}

	$scope.$on('showbugsAlive', function(){
		$scope.bugsList	= [];
		$scope.bugsType	= 0;
		getBugsList();
	});
	$scope.$on('showbugsKilled', function(){
		$scope.bugsList	= [];
		$scope.bugsType	= 1;
		getBugsList();
	});

	$scope.openBug = function(bug){
		var modalInstance = $modal.open({
			templateUrl: 'pages/bugModal.php?v='+ new Date().getTime(),
			controller: 'bugModalCtrl',
			backdrop: 'static',
			size: 'lg',
			windowClass: '',
			resolve: {
				passConf:	function() { return $scope.config; },
				bug:		function() { return bug; }
			}
		});
		modalInstance.result.then(function (R) {
			console.log(R);
		});
	};

	$scope.openAddBug = function(){
		var modalInstance = $modal.open({
			templateUrl: 'pages/addBugModal.php?v='+ new Date().getTime(),
			controller: 'addBugModalCtrl',
			backdrop: 'static',
			size: 'lg',
			windowClass: '',
			resolve: {
				passConf: function() { return $scope.config; }
			}
		});
		modalInstance.result.then(function (R) {
			console.log(R);
			msgSrv.showMsg(R.message, 'success');
			$scope.bugsList.push(R.bug);
			console.log($scope.bugsList);
		});
	};

	$scope.$on('bugKilled', function(e, bugId){
		$scope.killBug(bugId);
	});

	$scope.getLabelColor = function(labelID){
		var zeLabel = $.grep($scope.config.labels, function(e){ return e.id === labelID; });
		if (!zeLabel[0] || zeLabel[0].id === 0)
			return '#DDDDDD';
		return zeLabel[0].color;
	};

	$scope.killBug = function(bugId){
		var zeBug = $.grep($scope.bugsList, function(e){ return e.id === bugId; });
		zeBug[0].closed = '1';
		countBugs.bugWasKilled();
	};

	$scope.deleteBug = function(bugId){
		var zeBug = $.grep($scope.bugsList, function(e){ return e.id === bugId; });
		var bugTitle = zeBug[0].title;
		if (!confirm("Permanently remove the bug titled\n\n      \""+bugTitle+"\"\n\nfrom database? Are you sure?"))
			return;
		zeBug[0].removed = true;
		countBugs.bugWasRemoved();
	};
});