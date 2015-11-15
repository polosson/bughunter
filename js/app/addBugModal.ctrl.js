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

bughunter.controller('addBugModalCtrl', function($scope, $modalInstance, $http, FileUploader, passConf){
	$scope.priorities = angular.copy(passConf.priorities);
	$scope.labels	  = angular.copy(passConf.labels);
	$scope.devs		  = angular.copy(passConf.devs);
	var lang		  = angular.copy(passConf.lang);
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
	/**
	 * Screenshots upload
	 */
	$scope.uploadDone = true;
	$scope.uploader = new FileUploader({
		url: "actions/adminBug.php",
		autoUpload: false,
		formData: [{action: 'uploadImg', bugID: 'newBug'}],
		filters: [{
			name: 'isImage',
			fn: function(item){
				var isImage = item.type.match(/image.*/);
				$('#imageFileWarning').css({'background':'none', 'font-weight': 'normal'});
				if (!isImage)
					$('#imageFileWarning').css({'background-color':'#FF0', 'font-weight': 'bold'});
				return isImage;
			}
		}],
		onAfterAddingFile: function(){
			$scope.uploadDone = false;
		},
		onBeforeUploadItem: function(){
			$scope.ajaxMsg = lang.Uploading;
		},
		onCompleteItem: function(item, R){
			$scope.bug.img.push(R.img);
		},
		onCompleteAll: function(item, R){
			$scope.uploadDone = true;
			$scope.ajaxMsg = "";
		}
	});

	$scope.submitNewBug = function(){
		$scope.ajaxMsg = "";
		if ($scope.bug.title.length < 5) {
			$scope.ajaxMsg = lang.Err_title_too_short;
			return;
		}
		if ($scope.bug.title.length > 90) {
			$scope.ajaxMsg = lang.Err_title_too_long;
			return;
		}
		if ($scope.bug.description.length < 5) {
			$scope.ajaxMsg = lang.Err_descr_too_short;
			return;
		}
		if ($scope.uploader.getNotUploadedItems().length > 0) {
			$scope.ajaxMsg = lang.Err_pending_upload;
			return;
		}
		if ($scope.uploadDone === false) {
			$scope.ajaxMsg = lang.Err_upload_progress;
			return;
		}
		$scope.ajaxMsg = lang.Submitting;
		$http({
			url: 'actions/adminBug.php',
			method: 'POST',
			data: {action:'addBug', bugInfos: $scope.bug}
		}).then(
			function(R){
				$scope.ajaxMsg = R.data.message;
				if (R.data.error === 'OK')
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
		$scope.uploader.cancelAll();
		if ($scope.bug.img.length > 0) {
			if (!confirm(lang.Warning_abort_new_bug))
				return;
		}
		$modalInstance.dismiss();
	};
});