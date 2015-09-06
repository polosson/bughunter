// bugsList.ctrl.js
'use strict';

/**
 * Controleur de la liste des bugs et des filtres
 */
bughunter.controller("bugsCtrl", function($scope, $rootScope, $http, $modal){
	$scope.modeAdmin	= false;
	$scope.bugsList		= [];
	$scope.priorities	= [];
	$scope.labels		= [];
	$scope.devs			= [];
	$scope.search	 = {'title':"", 'priority':"", 'FK_label_ID':"", 'FK_dev_ID':""};
	$scope.orderProp = 'priority';
	$scope.orderRev  = true;
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
				$scope.bugsList		= R.data.bugsList;
				$scope.priorities	= R.data.priorities;
				$scope.labels		= R.data.labels;
				$scope.devs			= R.data.devs;
				$rootScope.$broadcast('updateBugCount', {'type':type, 'count':R.data.bugsList.length});
			},
			function(errMsg) { console.log("error", errMsg); $('#msg').html(errMsg).addClass('msg_error').show(); }
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
	$scope.$on('modeAdminSet', function(){
		$scope.modeAdmin	= true;
	});
	$scope.$on('modeAdminUnset', function(){
		$scope.modeAdmin	= false;
	});

	$scope.openBug = function(bug){
		var modalInstance = $modal.open({
			templateUrl: 'pages/bugModal.php?v='+ new Date().getTime(),
			controller: 'bugModalCtrl',
			backdrop: 'static',
			size: 'lg',
			windowClass: '',
			resolve: {
				modeAdmin:	function() { return $scope.modeAdmin; },
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