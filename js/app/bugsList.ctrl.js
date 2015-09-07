// bugsList.ctrl.js
'use strict';

/**
 * Controleur de la liste des bugs et des filtres
 */
bughunter.controller("bugsCtrl", function($scope, $http, $modal, msgSrv, config, countBugs){
	$scope.bugsList		= [];
	$scope.priorities	= [];
	$scope.labels		= [];
	$scope.devs			= [];
	$scope.search	 = {'title':"", 'priority':"", 'FK_label_ID':"", 'FK_dev_ID':""};
	$scope.orderProp = 'priority';
	$scope.orderRev  = true;
	$scope.bugsType	 = 0;

	$scope.config	= config.data;

	getBugsList();

	$scope.resetFilter = function(){
		$scope.search = {'title':"", 'priority':"", 'FK_label_ID':"", 'FK_dev_ID':""};
	};

	function getBugsList () {
		var type = $scope.bugsType;
		$http({
			'url': 'actions/getBugsList.php?type='+type
		}).then(
			function(R){
				if (R.data.error === "OK") {
					$scope.bugsList		= R.data.bugsList;
					$scope.priorities	= R.data.priorities;
					$scope.labels		= R.data.labels;
					$scope.devs			= R.data.devs;
					countBugs.updateCountType(type, R.data.bugsList.length);
				}
				else msgSrv.showMsg(R.data.error, 'error');
			},
			function(errMsg) { msgSrv.showMsg(errMsg, 'error'); }
		);
	}

	$scope.$on('showbugsAlive', function(){
		$scope.bugsList	= [];
		$scope.bugsType	= 0;
		getBugsList();
	});
	$scope.$on('showbugsKilled', function(){
		$scope.bugsList	= [];
		$scope.bugsType	= 1;
		getBugsList();
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

	$scope.$on('bugKilled', function(e, bugId){
		$scope.killBug(bugId);
	});

	$scope.getLabelColor = function(labelID){
		var zeLabel = $.grep($scope.labels, function(e){ return e.id === labelID; });
		if (zeLabel[0].id == 0)
			return '#DDDDDD';
		return zeLabel[0].color;
	};

	$scope.killBug = function(bugId){
		var zeBug = $.grep($scope.bugsList, function(e){ return e.id === bugId; });
		zeBug[0].closed = '1';
		countBugs.bugWasKilled();
	};

	$scope.deleteBug = function(bugId){
		var zeBug = $.grep($scope.bugsList, function(e){ return e.id === bugId; });
		var bugTitle = zeBug[0].title;
		if (!confirm("Permanently remove the bug titled\n\n      \""+bugTitle+"\"\n\nfrom database? Are you sure?"))
			return;
		zeBug[0].removed = true;
		countBugs.bugWasRemoved();
	};
});