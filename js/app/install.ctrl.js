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
// install.ctrl.js
'use strict';

/**
 * INSTALL controller
 */
bughunter.controller('installCtrl', function($scope, $http, $timeout){
	$scope.sql  = { host:'localhost', user:'', pass:'', dbnm:'bughunter' };
	$scope.conf = { password_access:'bhadmin', project_name:'', project_type:'open-source', git_repo:'' };
	$scope.admpwconfirm = 'bhadmin';
	$scope.step = 1;

	$scope.prevStep = function(){
		$scope.step -= 1;
	};
	$scope.skipStep = function(){
		$scope.step += 1;
	};

	$scope.stepInstall = function(){
		$('#msg').removeClass('msg_error, msg_success');
		var toSend = "";
		if ($scope.step === 1) {
			if ($scope.sql.host.length < 4) {
				$('#msg').html("MySQL host must be specified.").addClass('msg_error').show();
				return;
			}
			if ($scope.sql.user.length < 4){
				$('#msg').html("MySQL user must be specified.").addClass('msg_error').show();
				return;
			}
			if ($scope.sql.pass.length < 4){
				$('#msg').html("MySQL password must be specified.").addClass('msg_error').show();
				return;
			}
			if ($scope.sql.dbnm.length < 4){
				$('#msg').html("MySQL database name must be specified.").addClass('msg_error').show();
				return;
			}
			toSend = $scope.sql;
		}
		else if ($scope.step === 2) {
			toSend = [];
		}
		else if ($scope.step === 3) {
			if ($scope.conf.password_access !== $scope.admpwconfirm) {
				$('#msg').html("Passwords do not match in both inputs.").addClass('msg_error').show();
				return;
			}
			toSend = $scope.conf;
		}
		$http({
			url: "actions/doInstall.php",
			data: {step: $scope.step, infos: toSend},
			'method': 'POST'
		}).then(
			function(R){
				console.log(R.data);
				if (R.data.error == 'OK'){
					$('#msg').html(R.data.message).addClass('msg_success').show();
					$scope.step = R.data.nextStep;
					$timeout(function(){ $('#msg').fadeOut(800); }, 3000);
				}
				else
					$('#msg').html(R.data.error).addClass('msg_error').show();

			},
			function(errMsg){ $('#msg').html(errMsg).addClass('msg_error').show(); }
		);
	};
});