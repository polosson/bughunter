// alive.ctrl.js
'use strict';

/**
 * Controleur de la liste des bugs et des filtres
 */
bughunter.controller("bugsCtrl", function($scope, $rootScope, $http, $modal){
	$scope.bugsList		= [];
	$scope.priorities	= [];
	$scope.listKilled	= false;
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
				$rootScope.$broadcast('updateBugCount', {'type':type, 'count':R.data.bugsList.length});
			},
			function(errMsg) { console.log("error", errMsg); $('#msg').html(errMsg).addClass('msg_error'); }
		);
	}

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

	$scope.openBug = function(bug){
		var modalInstance = $modal.open({
			templateUrl: 'pages/bugModal.php?v='+ new Date().getTime(),
			controller: 'bugModalCtrl',
			backdrop: 'static',
			windowClass: '',
			resolve: { bug: function() { return bug; } }
		});
		modalInstance.result.then(function (R) {
			console.log(R);
		});
	};
});


/**
 * Controleur de la modale de bug
 */
bughunter.controller('bugModalCtrl', function($scope, $modalInstance, bug){
	console.log(bug);
	$scope.bug = angular.copy(bug);
	$scope.bug.description = angular.copy(nl2br(bug.description));

	$scope.closeBugModal = function(){
		$modalInstance.dismiss();
	};
});