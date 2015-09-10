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
// menu.ctrl.js
'use strict';

/**
 * SETTINGS controller
 */
bughunter.controller('settingsCtrl', function($scope, $timeout, $modalInstance, ajaxBug, config){
	$scope.config	 = config.data;
	$scope.ajaxMsg	 = "";
	$scope.newLabel	 = {name:'', color:'#'};
	$scope.newDev	 = {pseudo:'', mail:''};
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

	$scope.addLabel = function(){
		$('.settings-message').removeClass('text-success').addClass('text-danger');
		$scope.ajaxMsg = "Saving new label...";
		if ($scope.newLabel.name === '')		{ $scope.ajaxMsg = "New label miss a name!"; return; }
		if ($scope.newLabel.name.length > 10)	{ $scope.ajaxMsg = "New label name is too long! (max 10 characters)"; return; }
		if ($scope.newLabel.color === '')		{ $scope.ajaxMsg = "New label miss a color!"; return; }
		if (!checkColor($scope.newLabel.color)) { $scope.ajaxMsg = "New label color is not valid!"; return; }
		ajaxBug.addLabel($scope.newLabel).then(
			function(R){
				$scope.config.labels.push(R.newLabel);
				$('.settings-message').removeClass('text-danger').addClass('text-success');
				$scope.newLabel	 = {name:'', color:'#'};
				$scope.ajaxMsg = R.message;
				$timeout(function(){ $scope.ajaxMsg = ""; }, 4000);
			},
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
	};

	$scope.addDev = function(){
		$('.settings-message').removeClass('text-success').addClass('text-danger');
		$scope.ajaxMsg = "Saving new Dev...";
		if ($scope.newDev.pseudo === '')	 { $scope.ajaxMsg = "New dev miss a pseudo!"; return; }
		if ($scope.newDev.pseudo.length < 3) { $scope.ajaxMsg = "New dev pseudo too short! (min 3 characters)"; return; }
		if (!check_email($scope.newDev.mail)){ $scope.ajaxMsg = "New dev email is not valid!"; return; }
		ajaxBug.addDev($scope.newDev).then(
			function(R){
				$scope.config.devs.push(R.newDev);
				$('.settings-message').removeClass('text-danger').addClass('text-success');
				$scope.newDev	= {pseudo:'', mail:''};
				$scope.ajaxMsg	= R.message;
				$timeout(function(){ $scope.ajaxMsg = ""; }, 4000);
			},
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
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
		ajaxBug.updatePW(pw1).then(
			function(R){ $scope.ajaxMsg = R.message+" Reloading..."; window.location = "./"; },
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
		$('.passwInput').val('');
	};

	$scope.resetBughunter = function(){
		if (!confirm("Reset all current bughunter informations?\n\n"
				+"This includes project infos, main password, and all bugs.\n"
				+"You should create a backup of the database before...\n\n"
				+"Continue anyway?\n\n"))
			return;
	};

	$scope.closeSettingsModal = function(){
		$modalInstance.dismiss();
	};
});