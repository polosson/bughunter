// menu.ctrl.js
'use strict';

/**
 * Controleur du menu principal
 */
bughunter.controller("menuCtrl", function($scope, $rootScope, $modal, $http){
	$scope.modeAdmin   = false;
	$scope.page		   = 'alive';
	$scope.countKilled = startCountKilled;
	$scope.countAlive  = startCountAlive;

	$scope.showPage = function(zepage){
		$scope.page = zepage;
		if (zepage == "alive")
			$rootScope.$broadcast('showbugsAlive');
		if (zepage == "killed")
			$rootScope.$broadcast('showbugsKilled');
	};

	$scope.$on('updateBugCount', function(e, count){
		if (count.type === 0)
			$scope.countAlive  = count.count;
		else
			$scope.countKilled = count.count;
	});

	$scope.connectModal = function(){
		var modalInstance = $modal.open({
			templateUrl: 'pages/loginModal.php?v='+ new Date().getTime(),
			controller: 'loginModalCtrl',
			backdrop: 'static',
		});
		modalInstance.result.then(function (R) {
			if (R.AUTH === 'OK') {
				$scope.modeAdmin   = true;
				$rootScope.$broadcast('modeAdminSet');
			}
		});
	};

	$scope.disconnect = function(){
		if (!confirm("Quit admin mode? Sure?"))
			return;
		$http({
			'url': 'actions/deconx.php'
		}).then(
			function(){
				$scope.modeAdmin   = false;
				$rootScope.$broadcast('modeAdminUnset');
			},
			function(errMsg) { console.log(errMsg); $('#msg').html(errMsg).addClass('msg_error').show(); }
		);
	};
});

/**
 * Controleur de la modale de connexion
 */
bughunter.controller("loginModalCtrl", function($scope, $modalInstance, $http, $timeout){
	$scope.password = '';
	$scope.message	= '';

	$scope.connect = function(){
		if ($scope.password === '')
			return;
		$scope.message	= 'Vérification en cours...';
		$http({
			'url': 'actions/conx.php',
			'data': {'passw': $scope.password},
			'method': 'POST'
		}).then(
			function(R){
				$scope.message = R.data.message;
				if (R.data.auth === 'OK') {
					$('#msg').html(R.data.message).addClass('msg_success').show();
					$timeout(function(){ $('#msg').fadeOut(600); }, 2000);
					$modalInstance.close({'AUTH':'OK'});
				}
			},
			function(errMsg) { console.log(errMsg); $('#msg').html(errMsg).addClass('msg_error').show(); }
		);
	};

	$scope.closeLoginModal = function(){
		$modalInstance.dismiss();
	};
});