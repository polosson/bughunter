// alive.ctrl.js
'use strict';

bughunter.controller("bugsCtrl", function($scope, $rootScope, $http){
	$scope.bugsList		= [];
	$scope.priorities	= [];
	$scope.listKilled	= false;

	function getBugsList (type) {
		if (!type) type = 0;
		$http({
			'url': 'actions/getBugsList.php?type='+type
		}).then(
			function(R){
				$scope.bugsList		= R.data.bugsList;
				$scope.priorities	= R.data.priorities;
				$rootScope.$broadcast('updateBugCount', {'type':type, 'count':R.data.bugsList.length});
			},
			function(errMsg) { console.log("error", errMsg); $('#msg').html(errMsg).addClass('msg_error'); }
		);
	}

	getBugsList();

	$scope.$on('showbugsAlive', function(){
		$scope.bugsList		= [];
		getBugsList(0);
		$scope.listKilled	= false;
	});
	$scope.$on('showbugsKilled', function(){
		$scope.bugsList		= [];
		getBugsList(1);
		$scope.listKilled	= true;
	});

});