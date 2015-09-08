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
bughunter.controller('installCtrl', function($scope, $http){
	$scope.sql = { host:'localhost', user:'', pass:'', dbnm:'bughunter' };

	$scope.launchInstall = function(){
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

		$http({
			url: "actions/doInstall.php",
			data: {sql: $scope.sql},
			'method': 'POST'
		}).then(
			function(R){
				console.log(R.data);
				if (R.data.error == 'OK'){
					$('#msg').html("Intallation successful! Reloading...").addClass('msg_success').show();
					window.location = './';
				}
				else
					$('#msg').html(R.data.error).addClass('msg_error').show();

			},
			function(errMsg){ $('#msg').html(errMsg).addClass('msg_error').show(); }
		);
	};
});