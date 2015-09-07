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