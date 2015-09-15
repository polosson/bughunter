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
bughunter.controller("bugsCtrl", function($scope, $http, $modal, msgSrv, config, countBugs, ajaxBug){
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
		$modal.open({
			templateUrl: 'pages/bugModal.php?v='+ new Date().getTime(),
			controller: 'bugModalCtrl',
			backdrop: 'static',
			size: 'lg',
			windowClass: '',
			resolve: {
				passConf: function() { return $scope.config; },
				bug:	  function() { return bug; }
			}
		});
	};

	$scope.$on('bugKilled', function(e, bugId){
		$scope.killBug(bugId);
	});
	$scope.$on('bugChanged', function(e, bug){
		var zeBug = $.grep($scope.bugsList, function(e){ return e.id === bug.id; });
		$.each(bug, function(prop, val){
			zeBug[0][prop] = val;
		});
	});

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
			msgSrv.showMsg(R.message, 'success');
			countBugs.bugWasAdded();
			$scope.bugsList.push(R.bug);
		});
	};

	$scope.getLabelColor = function(labelID){
		var zeLabel = $.grep($scope.config.labels, function(e){ return e.id === labelID; });
		if (!zeLabel[0] || zeLabel[0].id === 0)
			return '#DDDDDD';
		return zeLabel[0].color;
	};

	$scope.killBug = function(bugId){
		var zeBug = $.grep($scope.bugsList, function(e){ return e.id === bugId; });
		ajaxBug.killBug(bugId).then(
			function(R) {
				zeBug[0].closed = '1';
				countBugs.bugWasKilled();
				msgSrv.showMsg(R.message, 'success');
			},
			function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
		);
	};

	$scope.deleteBug = function(bugId){
		var zeBug = $.grep($scope.bugsList, function(e){ return e.id === bugId; });
		var bugTitle = zeBug[0].title;
		if (!confirm("Permanently remove the bug titled\n\n      \""+bugTitle+"\"\n\nfrom database? Are you sure?"))
			return;
		ajaxBug.removeBug(bugId).then(
			function(R) {
				zeBug[0].removed = true;
				countBugs.bugWasRemoved();
				msgSrv.showMsg(R.message, 'success');
			},
			function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
		);
	};

	$scope.updateBug = function(bugId){
		var zeBug = $.grep($scope.bugsList, function(e){ return e.id === bugId; });
		ajaxBug.saveModBug(zeBug[0]).then(
			function(R) { msgSrv.showMsg(R.message, 'success'); },
			function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
		);
	};
});

/**
 * Filtering using ng-hide
 */
bughunter.controller('filtering', function($scope){

	$scope.filtered = function(){
		if (($scope.bugsType === 0 && $scope.bug.closed === '1') || $scope.bug.removed)
			return true;
		var search = $scope.search.title.toLowerCase();
		var title  = $scope.bug.title.toLowerCase();
		if (title.indexOf(search) === -1)
			return true;
		var fPrio = $scope.search.priority;
		if (fPrio !== "" && $scope.bug.priority !== fPrio)
			return true;
		var fLabel = $scope.search.FK_label_ID;
		if (fLabel !== "" && $scope.bug.FK_label_ID != fLabel)
			return true;
		var fDev = $scope.search.FK_dev_ID;
		if (fDev !== "" && $scope.bug.FK_dev_ID != fDev)
			return true;
		return false;
	};
});