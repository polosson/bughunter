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
bughunter.controller('bugModalCtrl', function($scope, $modalInstance, $rootScope, $timeout, ajaxBug, passConf, bug){
	$scope.editInfos  = false;
	$scope.editDescr  = false;
	$scope.editComment= false;
	$scope.modeAdmin  = angular.copy(passConf.authAdmin);;
	$scope.priorities = angular.copy(passConf.priorities);
	$scope.labels	  = angular.copy(passConf.labels);
	$scope.devs		  = angular.copy(passConf.devs);
	$scope.bug		  = angular.copy(bug);
	$scope.newComment = "";
	if ($scope.bug.closed === '1')
		$scope.modeAdmin = false;

	/**
	 * Bug informations
	 */
	$scope.initEdit = function(){ $scope.editInfos = true; };
	$scope.initUpdDescr = function(){ $scope.editDescr  = true; };

	$scope.saveBug = function(){
		$('#ajaxBugMsg').html("Updating bug...").removeClass('text-info text-danger text-success').addClass('text-info').show();
		ajaxBug.saveModBug($scope.bug).then(
			function(R) {
				$('#ajaxBugMsg').html(R.message).removeClass('text-info').addClass('text-success').show();
				$timeout(function(){ $('#ajaxBugMsg').fadeOut(600); }, 3000);
				$scope.editInfos  = false;
				$scope.editDescr  = false;
				$rootScope.$broadcast('bugChanged', R.bug);
				bug = angular.copy($scope.bug);
			},
			function(errMsg) { $('#ajaxBugMsg').html(errMsg).removeClass('text-info').addClass('text-danger').show(); }
		);
	};
	$scope.cancelEdit = function(){
		$scope.bug = angular.copy(bug);
		$scope.editInfos  = false;
		$scope.editDescr  = false;
	};

	$scope.killBug = function(){
		if (!confirm("Kill this bug? Sure?"))
			return;
		$scope.bug.closed = '1';
		$scope.modeAdmin = false;
		$rootScope.$broadcast('bugKilled', $scope.bug.id);
		$('#ajaxBugMsg').html('Bug closed.').removeClass('text-danger text-success').addClass('text-success').show();
		$timeout(function(){ $('#ajaxBugMsg').fadeOut(600); }, 3000);
	};

	/**
	 * Bug comments
	 */
	$scope.initUpdComment = function(idComm){ $scope.editComment  = idComm; };

	$scope.saveUpdComment = function(idx){
		ajaxBug.saveComment($scope.bug.id, $scope.bug.comment[idx]).then(
			function(R) {
				$('#ajaxBugMsg').html(R.message).removeClass('text-info').addClass('text-success').show();
				$timeout(function(){ $('#ajaxBugMsg').fadeOut(600); }, 3000);
				$scope.editComment  = false;
				bug = angular.copy($scope.bug);
			},
			function(errMsg) { $('#ajaxBugMsg').html(errMsg).removeClass('text-info').addClass('text-danger').show(); }
		);
	};
	$scope.cancelUpdComment = function(idx){
		$scope.editComment  = false;
		$scope.bug.comment[idx] = angular.copy(bug.comment[idx]);
	};
	$scope.deleteComment = function(idx){
		if (!confirm("Delete this comment? Sure?"))
			return;
		ajaxBug.delComment($scope.bug.id, $scope.bug.comment[idx].id).then(
			function(R) {
				$('#ajaxBugMsg').html(R.message).removeClass('text-info').addClass('text-success').show();
				$timeout(function(){ $('#ajaxBugMsg').fadeOut(600); }, 3000);
				$scope.bug.comment.splice(idx, 1);
				$rootScope.$broadcast('bugChanged', $scope.bug);
				bug = angular.copy($scope.bug);
			},
			function(errMsg) { $('#ajaxBugMsg').html(errMsg).removeClass('text-info').addClass('text-danger').show(); }
		);
	};
	$scope.addComment = function(){
		if ($scope.newComment === "") return;
		$('#ajaxBugMsg').html("Adding comment to bug...").removeClass('text-info text-danger text-success').addClass('text-info').show();
		if (typeof $scope.bug.comment === 'undefined')
			$scope.bug.comment = [];
		if ($scope.newComment.length < 3) {
			$('#ajaxBugMsg').html("Comment too short. 3 characters minimum.").removeClass('text-info').addClass('text-danger').show();
			return;
		}
		ajaxBug.addComment($scope.bug.id, $scope.newComment).then(
			function(R) {
				$('#ajaxBugMsg').html(R.message).removeClass('text-info').addClass('text-success').show();
				$timeout(function(){ $('#ajaxBugMsg').fadeOut(600); }, 3000);
				$scope.bug.comment.push(R.newComment);
				bug = angular.copy($scope.bug);
				$rootScope.$broadcast('bugChanged', $scope.bug);
				$scope.newComment = "";
			},
			function(errMsg) { $('#ajaxBugMsg').html(errMsg).removeClass('text-info').addClass('text-danger').show(); }
		);
	};

	/**
	 * OTHER FUNCTIONS
	 */
	$scope.closeBugModal = function(){
		$modalInstance.dismiss();
	};

	$scope.getLabelColor = function(labelID){
		var zeLabel = $.grep($scope.labels, function(e){ return e.id === labelID; });
		if (zeLabel[0].id == 0) return '#DDDDDD';
		return zeLabel[0].color;
	};

	$scope.nl2br = function(text){
		return nl2br(text);
	};
});