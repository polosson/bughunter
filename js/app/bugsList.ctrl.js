// bugsList.ctrl.js
'use strict';

/**
 * Controleur de la liste des bugs et des filtres
 */
bughunter.controller("bugsCtrl", function($scope, $rootScope, $http, $modal, msgSrv, config){
	$scope.bugsList		= [];
	$scope.priorities	= [];
	$scope.labels		= [];
	$scope.devs			= [];
	$scope.search	 = {'title':"", 'priority':"", 'FK_label_ID':"", 'FK_dev_ID':""};
	$scope.orderProp = 'priority';
	$scope.orderRev  = true;

	$scope.config	= config.data;

	getBugsList();

	$scope.resetFilter = function(){
		$scope.search = {'title':"", 'priority':"", 'FK_label_ID':"", 'FK_dev_ID':""};
	};

	function getBugsList (type) {
		if (!type) type = 0;
		$http({
			'url': 'actions/getBugsList.php?type='+type
		}).then(
			function(R){
				if (R.data.error === "OK") {
					$scope.bugsList		= R.data.bugsList;
					$scope.priorities	= R.data.priorities;
					$scope.labels		= R.data.labels;
					$scope.devs			= R.data.devs;
					$rootScope.$broadcast('updateBugCount', {'type':type, 'count':R.data.bugsList.length});
				}
				else msgSrv.showMsg(R.data.error, 'error');
			},
			function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
		);
	}

	$scope.$on('showbugsAlive', function(){
		$scope.bugsList		= [];
		getBugsList(0);
	});
	$scope.$on('showbugsKilled', function(){
		$scope.bugsList		= [];
		getBugsList(1);
	});

	$scope.openBug = function(bug){
		var modalInstance = $modal.open({
			templateUrl: 'pages/bugModal.php?v='+ new Date().getTime(),
			controller: 'bugModalCtrl',
			backdrop: 'static',
			size: 'lg',
			windowClass: '',
			resolve: {
				modeAdmin:	function() { return $scope.config.authAdmin; },
				priorities: function() { return $scope.priorities; },
				labels:		function() { return $scope.labels; },
				devs:		function() { return $scope.devs; },
				bug:		function() { return bug; }
			}
		});
		modalInstance.result.then(function (R) {
			console.log(R);
		});
	};

	$scope.getLabelColor = function(labelID){
		var zeLabel = $.grep($scope.labels, function(e){ return e.id === labelID; });
		if (zeLabel[0].id == 0)
			return '#DDDDDD';
		return zeLabel[0].color;
	};

});