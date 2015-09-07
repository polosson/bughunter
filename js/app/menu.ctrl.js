// menu.ctrl.js
'use strict';

/**
 * Controleur du menu principal
 */
bughunter.controller("menuCtrl", function($scope, $rootScope, $modal, $http, msgSrv, config, countBugs){
	$scope.config	  = config.data;
	$scope.page		  = 'alive';
	$scope.count	  = countBugs;

	// Check si la session admin est toujours active
	$http({
		'url': 'actions/checkConx.php'
	}).then(
		function(R){
			if (R.data.auth === 'authOK')
				config.data.authAdmin = true;
			else {
				if (R.data.error === "error")
					msgSrv.showMsg(R.data.message, 'error');
			}
		},
		function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
	);

	// Récupération de la config générale et du compte des bugs
	$http({
		'url': 'actions/getConfig.php'
	}).then(
		function(R){
			if (R.data.error === "OK") {
				countBugs.updateCount({
					killed: R.data.countKilled,
					alive: R.data.countAlive
				});
				config.data.priorities	= R.data.priorities;
				config.data.labels		= R.data.labels;
				config.data.devs		= R.data.devs;
				config.data.globalConf	= R.data.globalConf;
			}
			else msgSrv.showMsg(R.data.error, 'error');
		},
		function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
	);

	$scope.showPage = function(zepage){
		if (zepage === "settings") {
			$modal.open({
				templateUrl: 'pages/settings.php?v='+ new Date().getTime(),
				controller: 'settingsCtrl',
				backdrop: 'static',
				size: 'lg'
			});
		}
		else {
			$scope.page = zepage;
			if (zepage === "alive")
				$rootScope.$broadcast('showbugsAlive');
			if (zepage === "killed")
				$rootScope.$broadcast('showbugsKilled');
		}
	};

	$scope.connectModal = function(){
		var modalInstance = $modal.open({
			templateUrl: 'pages/loginModal.php?v='+ new Date().getTime(),
			controller: 'loginModalCtrl',
			backdrop: 'static'
		});
		modalInstance.result.then(function (R) {
			if (R.AUTH === 'OK') {
				msgSrv.showMsg(R.message, 'success');
				config.data.authAdmin = true;
			}
		});
	};

	$scope.disconnect = function(){
		if (!confirm("Quit admin mode? Sure?"))
			return;
		$http({
			'url': 'actions/deconx.php'
		}).then(
			function(R){
				msgSrv.showMsg(R.data.message, 'success');
				config.data.authAdmin = false;
			},
			function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
		);
	};
});

/**
 * Controleur de la modale de connexion
 */
bughunter.controller("loginModalCtrl", function($scope, $modalInstance, $http, msgSrv){
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
				if (R.data.auth === 'OK') {
					$modalInstance.close({AUTH:'OK', message:R.data.message});
				}
				else
					$scope.message = R.data.message;
			},
			function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
		);
	};

	$scope.closeLoginModal = function(){
		$modalInstance.dismiss();
	};
});