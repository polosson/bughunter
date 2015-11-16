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
	$scope.bckpItem	 = {};
	$scope.editLabel = false;
	$scope.editDev   = false;
	$scope.projInfo	 = {
		project_name:  angular.copy(config.data.globalConf.project_name.value),
		project_type:  angular.copy(config.data.globalConf.project_type.value),
		git_repo:	   angular.copy(config.data.globalConf.git_repo.value),
		enable_notify: (config.data.globalConf.enable_notify.value == 1)
	};
	$scope.projTypes = ['open-source', 'private'];
	$scope.current_lang = config.data.globalConf.language.value;

	$scope.initEdit = function(type, id){
		var item = $.grep($scope.config[type], function(e){ return e.id === id; });
		$scope.bckpItem = angular.copy(item[0]);
		if (type === "labels")
			$scope.editLabel = id;
		if (type === "devs")
			$scope.editDev   = id;
	};
	$scope.saveEdit = function(type, id){
		$('.settings-message').removeClass('text-success').addClass('text-danger');
		$scope.ajaxMsg = "Saving "+type+"...";
		var item = $.grep($scope.config[type], function(e){ return e.id === id; });
		if (type === "labels") {
			if (item[0].name === '')		{ $scope.ajaxMsg = config.data.lang.Err_label_mod_name; return; }
			if (item[0].name.length > 10)	{ $scope.ajaxMsg = config.data.lang.Err_label_mod_too_long; return; }
			if (item[0].color === '')		{ $scope.ajaxMsg = config.data.lang.Err_label_mod_color; return; }
			if (!checkColor(item[0].color)) { $scope.ajaxMsg = config.data.lang.Err_label_mod_color; return; }
		}
		if (type === "devs") {
			if (item[0].pseudo === '')	   { $scope.ajaxMsg = config.data.lang.Err_dev_mod_pseudo; return; }
			if (item[0].pseudo.length < 3) { $scope.ajaxMsg = config.data.lang.Err_dev_mod_too_short; return; }
			if (item[0].id != 0 && !check_email(item[0].mail)){ $scope.ajaxMsg = config.data.lang.Err_dev_mod_email; return; }
		}
		ajaxBug.updateSetting(type, item[0]).then(
			function(R){
				$scope.editLabel = false;
				$scope.editDev   = false;
				$('.settings-message').removeClass('text-danger').addClass('text-success');
				$scope.ajaxMsg = R.message;
				$timeout(function(){ $scope.ajaxMsg = ""; }, 4000);
			},
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
	};
	$scope.cancelEdit = function(type, id){
		var item = $.grep($scope.config[type], function(e){ return e.id === id; });
		$.each($scope.bckpItem, function(prop, val){
			item[0][prop] = val;
		});
		$scope.editLabel = false;
		$scope.editDev   = false;
	};

	$scope.deleteItem = function(type, idx){
		var item = $scope.config[type][idx];
		if (!confirm("Remove '"+(item.name || item.pseudo)+"' from "+type+"? Sure?")) return;
		ajaxBug.removeSetting(type, item.id).then(
			function(R){
				$scope.config[type].splice(idx, 1);
				$('.settings-message').removeClass('text-danger').addClass('text-success');
				$scope.ajaxMsg = R.message;
				$timeout(function(){ $scope.ajaxMsg = ""; }, 4000);
			},
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
	};

	$scope.addLabel = function(){
		$('.settings-message').removeClass('text-success').addClass('text-danger');
		$scope.ajaxMsg = "Saving new label...";
		if ($scope.newLabel.name === '')		{ $scope.ajaxMsg = config.data.lang.Err_label_name; return; }
		if ($scope.newLabel.name.length > 10)	{ $scope.ajaxMsg = config.data.lang.Err_label_too_long; return; }
		if ($scope.newLabel.color === '')		{ $scope.ajaxMsg = config.data.lang.Err_label_color; return; }
		if (!checkColor($scope.newLabel.color)) { $scope.ajaxMsg = config.data.lang.Err_label_color; return; }
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
		if ($scope.newDev.pseudo === '')	 { $scope.ajaxMsg = config.data.lang.Err_dev_pseudo; return; }
		if ($scope.newDev.pseudo.length < 3) { $scope.ajaxMsg = config.data.lang.Err_dev_too_short; return; }
		if (!check_email($scope.newDev.mail)){ $scope.ajaxMsg = config.data.lang.Err_dev_email; return; }
		ajaxBug.addDev($scope.newDev).then(
			function(R){
				R.newDev.notify = 0;
				$scope.config.devs.push(R.newDev);
				$('.settings-message').removeClass('text-danger').addClass('text-success');
				$scope.newDev	= {pseudo:'', mail:''};
				$scope.ajaxMsg	= R.message;
				$timeout(function(){ $scope.ajaxMsg = ""; }, 4000);
			},
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
	};

	$scope.saveProject = function() {
		ajaxBug.updateSetting('projectInfo', $scope.projInfo).then(
			function(R){
				$scope.config.globalConf.project_name.value = angular.copy($scope.projInfo.project_name);
				$scope.config.globalConf.project_type.value = angular.copy($scope.projInfo.project_type);
				$scope.config.globalConf.git_repo.value		= angular.copy($scope.projInfo.git_repo);
				$scope.config.globalConf.enable_notify.value= angular.copy($scope.projInfo.enable_notify);
				$('.settings-message').removeClass('text-danger').addClass('text-success');
				$scope.ajaxMsg	= R.message;
				$timeout(function(){ $scope.ajaxMsg = ""; }, 4000);
			},
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
	};

	$scope.cancelProject = function() {
		$scope.projInfo	 = {
			project_name: angular.copy(config.data.globalConf.project_name.value),
			project_type: angular.copy(config.data.globalConf.project_type.value),
			git_repo:	  angular.copy(config.data.globalConf.git_repo.value),
			enable_notify: (config.data.globalConf.enable_notify.value == 1)
		};
	};

	$scope.changePassword = function(){
		var pws = $('.passwInput');
		var pw1 = $(pws.get(0)).val();
		var pw2 = $(pws.get(1)).val();
		$scope.ajaxMsg = "";
		if (pw1 === "" || pw1.length < 4)
			return;
		if (pw2 !== pw1) {
			$scope.ajaxMsg = config.data.lang.Err_PW_doesnt_match;
			return;
		}
		if (!confirm(config.data.lang.Confirm_PW_change))
			return;
		ajaxBug.updatePW(pw1).then(
			function(R){ $scope.ajaxMsg = R.message+" Reloading..."; window.location = "./"; },
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
		$('.passwInput').val('');
	};

	$scope.saveLanguage = function(){
		ajaxBug.updateLanguage($scope.current_lang).then(
			function(R){ $scope.ajaxMsg = R.message+" Reloading..."; window.location = "./"; },
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
	};

	$scope.getBackup = function(){
		ajaxBug.getBackup().then(
			function(R){ $scope.ajaxMsg = R.message; window.location = R.dumpfile; },
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
	};

	$scope.resetBughunter = function(){
		if (!confirm(config.data.lang.Confirm_reset))
			return;
		ajaxBug.resetAll().then(
			function(R){ $scope.ajaxMsg = R.message+" Reloading..."; window.location = './'; },
			function(errMsg){ $scope.ajaxMsg = errMsg; }
		);
	};

	$scope.closeSettingsModal = function(){
		$modalInstance.dismiss();
	};
});